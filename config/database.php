<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    // 默认数据库
    'default' => 'mysql',
    // 各种数据库配置
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '192.168.50.205',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'root',
            'password'    => 'root',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => base_path().'/sqlite3/webman.db',
            'prefix'   => '',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => '127.0.0.1',
            'port'     => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => 'localhost',
            'port'     => 1433,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
        ],
    ],
];