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

use Webman\Route;

Route::disableDefaultRoute();

Route::get("/",[\app\controller\IndexController::class,'index']);
Route::post("/log_test",[\app\controller\IndexController::class,'index']);
Route::get("/view",[\app\controller\IndexController::class,'view']);
Route::get("/json",[\app\controller\IndexController::class,'json']);



Route::fallback(function(){
    return response(
        "404 NOT FOUND",
        404
    );
});
