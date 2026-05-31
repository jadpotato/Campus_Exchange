<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'trade_id',
        'sender_id',
        'content',
        'is_read'
    ];

    // 发送者关联
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // 交易关联
    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}