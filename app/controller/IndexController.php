<?php

namespace app\controller;

use app\exception\ErrorCode;
use app\model\Test;
use app\utils\ZLog;
use support\Cache;
use support\Log;
use support\Redis;
use support\Request;
use support\Response;
use Webman\Context;

class IndexController
{
    public function index(Request $request): Response
    {
        ZLog::info("This is the index action",array(
//            'requestId'=> Context::get('X-Request-Id'),
        ));

        $dataList = Test::where('id','>',10)->get();
        Redis::get("key");
        $dataList = Test::where('id','<>',10)->get();
        return jsonWithCode([
            'dataList'=>$dataList,
        ]);

    }

    public function view(Request $request): Response
    {
        return view('index/view', ['name' => (string)random_int(10,10000)]);
    }

    public function json(Request $request): Response
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
