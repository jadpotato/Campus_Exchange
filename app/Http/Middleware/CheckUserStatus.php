<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */ // 这一行是关键！
        $user = Auth::user();

        if ($user && $user->isBanned()) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => '您的账号已被永久封禁，如有疑问请联系管理员。'
            ]);
        }

        if ($user && $user->isSuspended()) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => '您的账号已被临时停权，如有疑问请联系管理员。'
            ]);
        }

        return $next($request);
    }
}