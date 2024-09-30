<?php

use PHPUnit\Framework\TestCase;

class TestConfig extends TestCase
{
    public function testConfig()
    {
        $config = config('app');

        self::assertIsArray($config);
        self::assertArrayHasKey('debug', $config);

        self::assertIsBool($config['debug']);

        self::assertIsBool($config['debug']);
    }

    public function testRedis()
    {
        $k = random_string();
        $v = random_string();
        \support\Redis::set($k,$v);

        $value = \support\Redis::get($k);
        self::assertEquals($v,$value);
    }

    public function testDatabase()
    {
        $testModel = new app\model\Test();
        $age = rand(1,100);
        $name = random_string();

        $testModel->name = $name;
        $testModel->age = $age;
        $testModel->save();
        $d1 = \app\model\Test::where('id', $testModel->id)->first();

        self::assertEquals($age,$d1->age);
        self::assertEquals($name,$d1->name);

    }
}
