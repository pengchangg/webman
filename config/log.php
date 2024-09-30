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

use Monolog\Formatter\JsonFormatter;

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\Handler\RotatingFileHandler::class,
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    7, //$maxFiles
                    Monolog\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\JsonFormatter::class,
                    'constructor' => [
                        JsonFormatter::BATCH_MODE_JSON,
                        true,
                        true,
                        false
                    ],
                ],
            ],
            [
                'class' => Monolog\Handler\StreamHandler::class,
                'constructor' => [
                    'php://stdout',
                ],
                'formatter' => [
                    'class' => Monolog\Formatter\JsonFormatter::class,
                    'constructor' => [
                        JsonFormatter::BATCH_MODE_JSON,
                        true,
                        true,
                        false
                    ],
                ],
            ]
        ],
    ],
];
