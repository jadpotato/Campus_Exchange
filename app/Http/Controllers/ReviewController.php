<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // 评价提交页
    public function create(Trade $trade)
    {
        // 权限检查：只能评价自己参与的交易
        if (!$trade->isParticipant(Auth::id())) {
            abort(403, '无权评价此交易');
        }

        // 交易必须已完成
        if ($trade->status !== 'completed') {
            abort(403, '只能评价已完成的交易');
        }

        // 只能在交易完成后7天内评价
        if ($trade->updated_at->diffInDays() > 7) {
            abort(403, '评价已过期（交易完成后7天内可评价）');
        }

        // 只能评价一次
        $exists = Review::where('trade_id', $trade->id)
            ->where('reviewer_id', Auth::id())
            ->exists();
            
        if ($exists) {
            abort(403, '您已评价过此交易');
        }

        // 获取对方用户ID
        $revieweeId = $trade->getOppositeId(Auth::id());

        return view('reviews.create', compact('trade', 'revieweeId'));
    }

    // 保存评价（✅ 适配 comment 字段）
    public function store(Request $request, Trade $trade)
    {
        if (!$trade->isParticipant(Auth::id())) abort(403);
        if ($trade->status !== 'completed') abort(403);
        if ($trade->updated_at->diffInDays() > 7) abort(403);
        
        $exists = Review::where('trade_id', $trade->id)
            ->where('reviewer_id', Auth::id())
            ->exists();
            
        if ($exists) abort(403);

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500', // ✅ 改 comment
            'reviewee_id' => 'required|exists:users,id'
        ]);

        Review::create([
            'trade_id' => $trade->id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $validated['reviewee_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] // ✅ 改 comment
        ]);

        return redirect()->route('trades.show', $trade)->with('success', '评价提交成功');
    }

    // 用户评价列表页
    public function index(User $user)
    {
        $reviews = $user->receivedReviews()
            ->with(['reviewer', 'trade.item'])
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('user', 'reviews'));
    }
}