<?php
/**
 * @author chang
 * @date 2023/12/15
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(base_path())->load();

return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "mysql",
            'host' => env('DB_HOST'),
            'name' => env('DB_NAME'),
            'user' => env('DB_USER'),
            'pass' => env('DB_PASSWORD'),
            'port' => env('DB_PORT'),
            "charset" => "utf8"
        ]
    ]
];