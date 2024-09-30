<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "mysql",
            "host"    => "192.168.50.205",
            "name"    => "webman",
            "user"    => "root",
            "pass"    => "root",
            "port"    => "webman",
            "charset" => "utf8"
        ]
    ]
];