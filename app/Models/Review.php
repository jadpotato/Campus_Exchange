<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // ✅ 你的字段：comment 代替 content
    protected $fillable = [
        'trade_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment'
    ];

    // 关联交易
    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    // 评价者
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // 被评价者
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    // 判断是否为好评
    public function isGood()
    {
        return $this->rating >= 4;
    }

    // 判断是否为中评
    public function isNeutral()
    {
        return $this->rating == 3;
    }

    // 判断是否为差评
    public function isBad()
    {
        return $this->rating <= 2;
    }
}