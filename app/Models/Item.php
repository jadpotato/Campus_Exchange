<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    // 物品状态常量
    public const STATUS_PENDING = 'pending';    // 待审核
    public const STATUS_PUBLISHED = 'published'; // 已发布
    public const STATUS_REJECTED = 'rejected';  // 已拒绝
    public const STATUS_SOLD = 'sold';          // 已售出


    /**
     * 可批量赋值字段（完全匹配你现有数据库表结构）
     */
    protected $fillable = [
        'user_id',
        'title',
        'category',
        'item_condition',
        'trade_type',
        'price',
        'desired_item', // 保留你原有的字段名
        'description',
        'photos',
        'view_count', // 保留你原有的字段名
        'status',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'photos' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * 关联：发布物品的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联：物品的交易
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * 关联：收藏该物品的用户
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * 访问器：第一张图片的URL
     */
    public function getFirstPhotoUrlAttribute()
    {
        if ($this->photos && count($this->photos) > 0) {
            return asset('storage/' . $this->photos[0]);
        }
        return asset('images/default-item.png');
    }

    /**
     * 访问器：交易模式中文名称
     */
    public function getTradeTypeTextAttribute()
    {
        return match ($this->trade_type) {
            'sell' => '现金出售',
            'exchange' => '以物换物',
            'free' => '免费赠送',
            default => '未知',
        };
    }

    /**
     * 访问器：状态中文名称
     */
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending_approval' => '待审核',
            'published' => '发布中',
            'locked' => '已锁定',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'unpublished' => '已下架',
            default => '未知',
        };
    }

    /**
     * 检查物品是否可以编辑
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['pending_approval', 'published', 'unpublished']);
    }

    /**
     * 检查物品是否可以发起交易
     */
    public function canBeTraded()
    {
        return $this->status === 'published';
    }
}