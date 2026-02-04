<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            // 如果是 Web 请求，重定向到登录页面
            if ($request->is('web') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }

            // 重定向到我们自定义的登录页面路由
            return redirect()->guest(route('login'));
        });
    }
}
