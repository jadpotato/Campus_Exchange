<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id','proposer_id','responder_id','offer_item_id',
        'trade_type','price','status','cancel_reason','cancelled_by',
        'appoint_time','appoint_location' // 新增预约字段
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'appoint_time' => 'datetime'
    ];

    // 关联
    public function item() { return $this->belongsTo(Item::class); }
    public function proposer() { return $this->belongsTo(User::class, 'proposer_id'); }
    public function responder() { return $this->belongsTo(User::class, 'responder_id'); }
    public function offerItem() { return $this->belongsTo(Item::class, 'offer_item_id'); }

    // 判断参与者
    public function isParticipant(int $userId) {
        return $userId == $this->proposer_id || $userId == $this->responder_id;
    }

    // 状态机：允许的状态流转（核心！禁止非法跳转）
    public function canChangeTo(string $newStatus) {
        $allowed = [
            'negotiating' => ['pending_confirm', 'cancelled'],
            'pending_confirm' => ['waiting_pickup', 'cancelled'],
            'waiting_pickup' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];
        return in_array($newStatus, $allowed[$this->status] ?? []);
    }

    // 获取对方用户ID
    public function getOppositeId(int $userId) {
        return $this->proposer_id == $userId ? $this->responder_id : $this->proposer_id;
    }
}