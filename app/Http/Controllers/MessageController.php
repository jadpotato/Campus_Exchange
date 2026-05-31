<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    // 消息列表页
    public function index()
    {
        $uid = Auth::id();
        // 找到用户参与的所有交易
        $trades = Trade::where(function ($q) use ($uid) {
            $q->where('proposer_id', $uid)
              ->orWhere('responder_id', $uid);
        })
        ->with([
            'messages' => function ($q) {
                $q->latest()->take(1);
            },
            'item'
        ])
        ->get();

        return view('messages.index', compact('trades'));
    }

    // 对话详情页
    public function show(Trade $trade)
    {
        // 只有交易双方能访问
        if (!$trade->isParticipant(Auth::id())) {
            abort(403);
        }

        // 标记对方发来的消息为已读
        Message::where('trade_id', $trade->id)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        // 获取所有消息（按时间排序）
        $messages = Message::where('trade_id', $trade->id)
            ->oldest()
            ->with('sender')
            ->get();

        return view('messages.show', compact('trade', 'messages'));
    }

    // 发送消息（含联系方式过滤）
    public function store(Request $request)
    {
        $trade = Trade::findOrFail($request->trade_id);

        if (!$trade->isParticipant(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $content = $request->input('content');
        $content = $this->filterContactInfo($content);

        Message::create([
            'trade_id' => $trade->id,
            'sender_id' => Auth::id(),
            'content' => $content,
            'is_read' => false
        ]);

        return back();
    }

    // API：获取未读消息数量（轮询用）
    public function unread()
    {
        $uid = Auth::id();

        $count = Message::where('is_read', false)
            ->where('sender_id', '!=', $uid)
            ->whereHas('trade', function ($q) use ($uid) {
                $q->where('proposer_id', $uid)
                  ->orWhere('responder_id', $uid);
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    // 屏蔽手机号、微信、QQ等联系方式
    protected function filterContactInfo(string $text)
    {
        $patterns = [
            '/1[3-9]\d{9}/', // 手机号
            '/QQ[:：\s]*\d{5,12}/i',
            '/微信[:：\s]*[\w-]+/i',
            '/vx[:：\s]*[\w-]+/i',
            '/电话[:：\s]*\d+/i',
            '/tel[:：\s]*\d+/i',
            '/wx[:：\s]*[\w-]+/i',
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '***', $text);
        }

        return $text;
    }
}