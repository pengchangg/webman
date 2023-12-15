<?php
/**
 * @author chang
 * @date 2023/12/15
 */

namespace app\middleware;

use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;
use RuntimeException;
use support\Context;
use support\Db;
use support\Log;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class RequestDebug implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function process(Request $request, callable $next): Response
    {
        static $initialized_db;

        $request->X_REQUEST_ID = uuid();

        // 请求开始时间
        $start_time = microtime(true);

        Context::get()->webmanRequestInfo = [];


        // 初始化数据库监听
        if (!$initialized_db) {
            $initialized_db = true;
            $this->initDbListen();
        }


        // 得到响应
        $response = $next($request);
        $time_diff = substr((microtime(true) - $start_time) * 1000, 0, 7);
        $requestInfo = [
            'header' => $request->header(),
            'get' => $request->get(),
            'post' => $request->post(),
            'ip' => $request->getRealIp(),
            'path' => $request->path(),
            'fullUrl' => $request->fullUrl(),
            'method' => $request->method(),
            'time' => $time_diff . 'ms',
        ];

        $requestInfo = array_merge($requestInfo, (Context::get()->webmanRequestInfo ?? []));


        // 判断业务是否出现异常
        $exception = null;
        if (method_exists($response, 'exception')) {
            $exception = $response->exception();
        }

        // 尝试记录异常
        $method = 'info';
        if ($exception && config('app.exception.enable', true) && !$this->shouldntReport($exception)) {
            $requestInfo = array_merge($requestInfo, ['exception' => $exception]);
            $method = 'error';
        }

        // 判断Db是否有未提交的事务
        $has_uncommitted_transaction = false;
        if (class_exists(Connection::class, false)) {
            if ($log = $this->checkDbUncommittedTransaction()) {
                $has_uncommitted_transaction = true;
                $method = 'error';
                $requestInfo = array_merge($requestInfo, ['checkDbUncommittedTransaction_ERROR' => $log]);
            }
        }

        call_user_func([Log::class, $method], sprintf('[RequestDebug X-REQUEST-ID: %s]', $request->X_REQUEST_ID), $requestInfo);

        if ($has_uncommitted_transaction) {
            throw new RuntimeException('Uncommitted transactions found');
        }

        $response->header('X-Request-Id', $request->X_REQUEST_ID);

        return $response;
    }

    /**
     * 初始化数据库日志监听
     *
     * @return void
     */
    protected function initDbListen()
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
            $dispatcher->listen(QueryExecuted::class, function (QueryExecuted $query) {
                $sql = trim($query->sql);
                if (strtolower($sql) === 'select 1') {
                    return;
                }
                $sql = str_replace("?", "%s", $sql);
                foreach ($query->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $query->bindings[$i] = $binding->format("'Y-m-d H:i:s'");
                    } else {
                        if (is_string($binding)) {
                            $query->bindings[$i] = "'$binding'";
                        }
                    }
                }
                try {
                    $sql = vsprintf($sql, $query->bindings);
                } catch (\Throwable $e) {
                }
                $tmp = array_merge((Context::get()->webmanRequestInfo ?? []));
                $tmp['db'][] = [
                    'sql' => $sql,
                    'connectionName' => $query->connectionName,
                    'time' => $query->time,
                ];
                Context::get()->webmanRequestInfo = $tmp;
            });
            $capsule->setEventDispatcher($dispatcher);
        } catch (\Throwable $e) {
            echo $e;
        }
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
            $reflect = new \ReflectionClass(Db::class);
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
                /* @var \Illuminate\Database\MySqlConnection $connection * */
                if (\in_array($connection->getConfig('driver'), ['mysql', 'pgsql', 'sqlite', 'sqlsrv'])) {
                    $pdo = $connection->getPdo();
                    if ($pdo && $pdo->inTransaction()) {
                        $connection->rollBack();
                        $logs .= "Uncommitted transaction found and try to rollback";
                    }
                }
            }
        } catch (Throwable $e) {
            echo $e;
        }
        return $logs;
    }


    /**
     * 判断是否需要记录异常
     *
     * @param Throwable $e
     * @return bool
     */
    protected function shouldntReport($e): bool
    {
        foreach (config('app.exception.dontReport', []) as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }
        return false;
    }

}