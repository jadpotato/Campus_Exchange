<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 保留你原来的所有中间件
        $middleware->web()->append([
            \App\Http\Middleware\CheckUserStatus::class,
            \App\Http\Middleware\DhtmlxCspMiddleware::class,
        ]);
        
        // ✅ 新增：角色中间件别名（用于路由中的 'role:user' / 'role:admin' 写法）
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();