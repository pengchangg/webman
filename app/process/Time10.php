<?php

namespace app\process;

use support\Log;
use Workerman\Timer;

class Time10
{
    public function onWorkerStart()
    {
        Timer::add(10, function () {
//            $count = Test::count();
            Log::info("Server.onWorkerStart.timer.10",[
            ]);
        });
    }
}