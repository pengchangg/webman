<?php

namespace app\controller;

use support\Cache;
use support\Db;
use support\Redis;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        static $readme;
        if (!$readme) {
            $readme = file_get_contents(base_path('README.md'));
        }
        return $readme;
    }

    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

    public function redis(Request $request)
    {
        $keys = Redis::keys("*");

        return json(['code' => 0, 'msg' => 'ok','data'=>$keys]);
    }


    public function db()
    {
        $insert = Db::table('post')->insert(['title' => 'test', 'content' => 'test','add_column'=>'test']);
        $keys = Redis::keys("*");
        $ret = Db::table('post')->where('id', 1)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return json(['code' => 0, 'msg' => 'ok',
            'data'=>[
                'ret'=>$ret,
                'insert'=>$insert,
                $keys
            ]
        ]);
    }

}
