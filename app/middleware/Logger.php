<?php

namespace app\middleware;

use app\utils\ZLog;
use DateTime;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\MySqlConnection;
use Illuminate\Events\Dispatcher;
use Illuminate\Redis\Events\CommandExecuted;
use ReflectionClass;
use RuntimeException;
use support\Db;
use support\Redis;
use Throwable;
use Webman\Context;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use function in_array;

class Logger implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        static $initialized_db;
        $requestId = uuid();

        // 请求开始时间
        $start_time = microtime(true);

        // 记录ip 请求等信息
        $logs = [
            'client_id' => $request->getRealIp(),
            'method' => $request->method(),
            'url' =>trim( $request->fullUrl(),"/")
        ];

        // 初始化数据库监听
        if (!$initialized_db) {
            $initialized_db = true;
            $this->initDbListen($requestId);
        }

        Context::set('X-Request-Id', $requestId);

        // 得到响应
        $response = $handler($request);

        $err = $response->exception();
        if ($err instanceof Exception) {
            //这个统一异常时的接口响应
            $trace = [$err->getMessage(), $err->getFile(), $err->getLine(), $err->getTraceAsString()];
            $log['exception'] = json_encode($trace, JSON_UNESCAPED_UNICODE);
        }

        $time_diff = substr((microtime(true) - $start_time) * 1000, 0, 7);

        $logs['time']  = $time_diff;

        $logs['all_params'] = $request->all();


        // 判断Db是否有未提交的事务
        $has_uncommitted_transaction = false;
        if (class_exists(Connection::class, false)) {
            if ($log = $this->checkDbUncommittedTransaction()) {
                $has_uncommitted_transaction = true;
                $logs['transaction'] = $log;
            }
        }


        /**
         * 初始化redis监听
         * 注意：由于redis是延迟监听，所以第一个请求不会记录redis具体日志
         */
         $this->tryInitRedisListen($requestId);

        if ($has_uncommitted_transaction) {
            throw new RuntimeException('Uncommitted transactions found');
        }

        $response->header('X-Request-Id', $requestId);
//        $logs['response_body'] = $response->rawBody();
        ZLog::info('request',$logs);
        return $response;
    }

    /**
     * 初始化数据库日志监听
     *
     * @return void
     */
    protected function initDbListen($request_id)
    {
        if (!class_exists(QueryExecuted::class)) {
            return;
        }
        try {
            $capsule = $this->getCapsule();
            if (!$capsule) {
                return;
            }
            $dispatcher = $capsule->getEventDispatcher();
            if (!$dispatcher) {
                $dispatcher = new Dispatcher(new Container);
            }
            $dispatcher->listen(QueryExecuted::class, function (QueryExecuted $query) use ($request_id) {
                $sql = trim($query->sql);
                if (strtolower($sql) === 'select 1') {
                    return;
                }
                $sql = str_replace("?", "%s", $sql);
                foreach ($query->bindings as $i => $binding) {
                    if ($binding instanceof DateTime) {
                        $query->bindings[$i] = $binding->format("'Y-m-d H:i:s'");
                    } else {
                        if (is_string($binding)) {
                            $query->bindings[$i] = "'$binding'";
                        }
                    }
                }
                $log = $sql;
                try {
                    $log = vsprintf($sql, $query->bindings);
                } catch (Throwable $e) {}
                ZLog::info('EXEC SQL',[
                    'sql' => $log,
                    'connection' => $query->connectionName,
                    'time' => $query->time,
                ]);
            });
            $capsule->setEventDispatcher($dispatcher);
        } catch (Throwable $e) {
            echo $e;
        }
    }

    /**
     * 尝试初始化redis日志监听
     *
     * @param string $requestId
     * @return array
     */
    protected function tryInitRedisListen(string $requestId): array
    {
        static $listened_names = [];
        if (!class_exists(CommandExecuted::class)) {
            return [];
        }
        $new_names = [];
        try {
            foreach (Redis::instance()->connections() ?: [] as $connection) {
                /* @var \Illuminate\Redis\Connections\Connection $connection */
                $name = $connection->getName();
                if (isset($listened_names[$name])) {
                    continue;
                }
                $connection->listen(function (CommandExecuted $command) use ($requestId) {
                    foreach ($command->parameters as &$item) {
                        if (is_array($item)) {
                            $item = implode('\', \'', $item);
                        }
                    }
                    if (count($command->parameters) > 0 && $command->parameters[0] != 'ping') {
                        ZLog::info('REDIS COMMAND',[
                            'connection' => $command->connectionName,
                            'command' => $command->command,
                            'parameters' => $command->parameters,
                            'time'=>$command->time
                        ]);
                    }
                });
                $listened_names[$name] = $name;
                $new_names[] = $name;
            }
        } catch (Throwable $e) {
            echo $e;
        }
        return $new_names;
    }


    /**
     * 获得Db的Manager
     *
     * @return mixed
     */
    protected function getCapsule()
    {
        static $capsule;
        if (!$capsule) {
            $reflect = new ReflectionClass(Db::class);
            $property = $reflect->getProperty('instance');
            $property->setAccessible(true);
            $capsule = $property->getValue();
        }
        return $capsule;
    }

    /**
     * 检查Db是否有未提交的事务
     *
     * @return string
     */
    protected function checkDbUncommittedTransaction(): string
    {
        $logs = '';
        try {
            foreach ($this->getCapsule()->getDatabaseManager()->getConnections() as $connection) {
                /* @var MySqlConnection $connection * */
                if (in_array($connection->getConfig('driver'), ['mysql', 'pgsql', 'sqlite', 'sqlsrv'])) {
                    $pdo = $connection->getPdo();
                    if ($pdo && $pdo->inTransaction()) {
                        $connection->rollBack();
                        $logs .= "[ERROR]\tUncommitted transaction found and try to rollback";
                    }
                }
            }
        } catch (Throwable $e) {
            echo $e;
        }
        return $logs;
    }

}
