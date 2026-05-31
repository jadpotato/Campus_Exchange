<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(User $user): View
    {
        // 预加载关联数据
        $user->load([
            'items' => function ($query) {
                $query->where('status', 'published')->latest()->take(6);
            },
            'receivedReviews' => function ($query) {
                $query->latest()->take(5);
            },
        ]);

        return view('users.show', compact('user'));
    }
}