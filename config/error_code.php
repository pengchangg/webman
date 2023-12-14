<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode 类文件
    "root_path" => app_path(), // 当前代码根目录
    "system_number" => 201, // 系统标识
    "start_min_number" => 10000 // 错误码生成范围 例如 10000-99999
];
