<?php
namespace App\Http\Controllers;
use App\Models\Trade;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    // ==========================================
    // 交易创建页（/trades/create?item_id={id}）
    // ==========================================
    public function create(Request $request) {
        $item = Item::findOrFail($request->item_id);
        // 权限：不能买自己的 + 物品必须发布中
        if ($item->user_id == Auth::id()) abort(403,'不能交易自己的物品');
        if ($item->status !== 'published') abort(403,'物品不可交易');
        // 业务规则：物品只能有一个活跃交易
        $exists = Trade::where('item_id',$item->id)->whereIn('status',['negotiating','pending_confirm','waiting_pickup'])->exists();
        if ($exists) abort(403,'该物品已有进行中交易');

        $myItems = Item::where('user_id',Auth::id())->where('status','published')->get();
        return view('trades.create',compact('item','myItems'));
    }

    // 提交创建（新增交易说明）
    public function store(Request $request) {
        $item = Item::findOrFail($request->item_id);
        $validated = $request->validate([
            'trade_type'=>'required|in:sell,exchange,free',
            'offer_item_id'=>'nullable|exists:items,id',
            'price'=>'nullable|numeric|min:0',
            'description'=>'nullable|string|max:500'
        ]);

        $trade = Trade::create([
            'item_id'=>$item->id,
            'proposer_id'=>Auth::id(),
            'responder_id'=>$item->user_id,
            'offer_item_id'=>$validated['offer_item_id']??null,
            'trade_type'=>$validated['trade_type'],
            'price'=>$validated['price']??$item->price,
            'status'=>'negotiating',
            'description'=>$validated['description']
        ]);

        return redirect()->route('trades.show',$trade); // 跳转→交易详情
    }

    // ==========================================
    // 我的交易看板
    // ==========================================
    public function myTrades() {
        return view('trades.my');
    }

    // ==========================================
    // 交易详情页（状态时间线 + 操作按钮）
    // ==========================================
    public function show(Trade $trade) {
        if(!$trade->isParticipant(Auth::id())) abort(403);
        return view('trades.show',compact('trade'));
    }

    // ==========================================
    // 预约确认页（预设学校地点）
    // ==========================================
    public function appointment(Trade $trade) {
        if(!$trade->isParticipant(Auth::id())) abort(403);
        if(!in_array($trade->status,['negotiating','pending_confirm'])) abort(403);
        // 预设学校安全交易点
        $locations = ['图书馆门口','教学楼大厅','食堂门口','校门卫室'];
        return view('trades.appointment',compact('trade','locations'));
    }

    // 保存预约
    public function saveAppointment(Request $request,Trade $trade) {
        $validated = $request->validate([
            'appoint_time'=>'required|date',
            'appoint_location'=>'required|string'
        ]);
        $trade->update($validated);
        $trade->update(['status'=>'waiting_pickup']);
        return redirect()->route('trades.show',$trade);
    }

    // ==========================================
    // 状态更新（严格遵守状态机 + 业务规则）
    // ==========================================
    public function updateStatus(Request $request,Trade $trade) {
        if(!$trade->isParticipant(Auth::id())) abort(403);
        $newStatus = $request->status;

        // 核心：禁止非法状态跳转
        if(!$trade->canChangeTo($newStatus)) {
            return response()->json(['error'=>'非法状态操作'],403);
        }

        // 业务规则：待取货状态禁止随意取消
        if($trade->status === 'waiting_pickup' && $newStatus === 'cancelled') {
            return response()->json(['error'=>'待取货需双方协商取消'],403);
        }

        $trade->update([
            'status'=>$newStatus,
            'cancelled_by'=>$newStatus==='cancelled'?Auth::id():null
        ]);

        return response()->json(['ok'=>true]);
    }

    // ==========================================
    // API：Kanban数据
    // ==========================================
    public function apiIndex() {
        $uid = Auth::id();
        $trades = Trade::where(function($q)use($uid){
            $q->where('proposer_id',$uid)->orWhere('responder_id',$uid);
        })->with('item')->get();
        return response()->json($trades);
    }
}