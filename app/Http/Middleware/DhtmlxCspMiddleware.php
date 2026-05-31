<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DhtmlxCspMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 只对后台和测试页面应用宽松策略
        if ($request->is('admin*') || $request->is('test-dhtmlx')) {
            // ✅ 完全移除 CSP 限制（开发环境专用）
            $response->headers->remove('Content-Security-Policy');
            
            // 添加最宽松的 CSP
            $response->headers->set('Content-Security-Policy', 
                "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                "script-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                "style-src * 'unsafe-inline' data: blob:; " .
                "img-src * data: blob:; " .
                "font-src * data: blob:; " .
                "connect-src *;"
            );
        }

        return $response;
    }
}