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

        // 关键：整行无换行，避免 header 换行报错
        $csp = "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; script-src * 'unsafe-inline' 'unsafe-eval' data: blob:; style-src * 'unsafe-inline' data: blob:; img-src * data: blob:; font-src * data: blob:; connect-src *;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}