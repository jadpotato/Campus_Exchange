<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('unread_message_count')) {
    function unread_message_count()
    {
        if (!Auth::check()) return 0;

        return \App\Models\Message::where('is_read', false)
            ->where('sender_id', '!=', Auth::id())
            ->whereHas('trade', function ($q) {
                $q->where('proposer_id', Auth::id())
                  ->orWhere('responder_id', Auth::id());
            })
            ->count();
    }
}