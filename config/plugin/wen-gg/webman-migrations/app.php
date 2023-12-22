<?php

return [
  'enable' => true,

  //
  'migrations' => [
    'table_storage' => [
      'table_name' => 'migrations',
      'version_column_name' => 'version',
      'version_column_length' => 1024,
      'executed_at_column_name' => 'executed_at',
      'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
      'database\migrations' => base_path() . '/database/migrations',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
  ],
  'migrations_db' => [
    'driver' => call_user_func(function (string $driver) {
      $arr = [
        'mysql' => 'pdo_mysql',
        'sqlite' => 'pdo_sqlite',
      ];
      return $arr[$driver];
    }, env('DB_DRIVER', 'sqlite')),
    'host' => env('DB_HOST', '127.0.0.1'),
    'dbname' => env('DB_NAME', '/home/chang/dev/webman/webman.sqlite'),
    'user' => env('DB_USER', 'root'),
    'password' => env('DB_PASSWORD', 'root'),
  ],
];
