<?php

namespace app\exception;

use support\Log;
use Throwable;
use Webman\Exception\ExceptionHandlerInterface;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 *  全局异常处理器
 */
class Handler implements ExceptionHandlerInterface
{

    public function report(Throwable $exception)
    {
    }

    public function render(Request $request, Throwable $exception): Response
    {
        Log::error('exception.report', [
            'trace' => $exception->getTrace(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'requestId' => $request->requestId,
        ]);
        $msg = '系统出小差了';
        if (!$request->expectsJson()) {
            return jsonWithCode(500, [
                'msg' => $msg
            ]);
        }
        return \response($msg, 500);
    }
}