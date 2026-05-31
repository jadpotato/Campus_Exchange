<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * 处理传入请求
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role 允许的角色：admin|user
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 检查用户角色
        if (Auth::user()->role !== $role) {
            // 普通用户访问管理员页面 → 跳转到首页
            if ($role === 'admin') {
                return redirect()->route('home')->with('error', '你没有管理员权限');
            }
            
            // 管理员访问普通用户页面 → 跳转到后台首页
            if ($role === 'user') {
                return redirect()->route('admin.index')->with('error', '管理员不能访问普通用户页面');
            }
        }

        return $next($request);
    }
}