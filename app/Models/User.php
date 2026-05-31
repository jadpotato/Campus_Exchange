<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    // use SoftDeletes

    /**
     * 可批量赋值的字段
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'avatar',
        'free_time',
    ];

    /**
     * 应该隐藏的字段
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 字段类型转换
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'free_time' => 'array', // JSON字段自动转为数组
        'rating_avg' => 'decimal:1',
    ];

    /**
     * 关联：用户发布的物品
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * 关联：用户发起的交易
     */
    public function proposedTrades()
    {
        return $this->hasMany(Trade::class, 'proposer_id');
    }

    /**
     * 关联：用户响应的交易
     */
    public function respondedTrades()
    {
        return $this->hasMany(Trade::class, 'responder_id');
    }

    /**
     * 关联：用户收到的评价
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * 关联：用户发出的评价
     */
    public function writtenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * 关联：用户的收藏
     */
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites')->withTimestamps();
    }

    /**
     * 检查用户是否为活跃状态
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * 检查用户是否被停权
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * 检查用户是否被封号
     */
    public function isBanned()
    {
        return $this->status === 'banned';
    }
    
    /**
     * 头像访问器：优先使用上传头像，无则使用默认头像
     */
    public function getAvatarUrlAttribute()
    {
        // 有自定义头像 → 显示上传的
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // 无头像 → 显示默认头像
        return asset('images/default_avatar.png');
    }
}