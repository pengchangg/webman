<?php

namespace app;

use support\Log;
use Workerman\Timer;
use support\Db;

class Task
{

    public function onWorkerStart()
    {
        // 每隔10秒检查一次数据库是否有新用户注册
        Timer::add(3, function(){
            Log::info("TaskTest",[
                'time'=>time(),
                'date'=>date("Y-m-d H:i:s")
            ]);
        });
    }

}
