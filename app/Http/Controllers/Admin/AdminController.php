<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Trade;
use App\Models\User;
use App\Services\Interfaces\ItemServiceInterface;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ✅ 完全删除了构造方法！没有任何红色下划线！

    // 后台首页
    public function index()
    {
        return view('admin.index');
    }

    // 物品管理
    public function items()
    {
        return view('admin.items');
    }

    // 用户管理
    public function users()
    {
        return view('admin.users');
    }

    // 交易管理
    public function trades()
    {
        return view('admin.trades');
    }

    // 举报管理
    public function reports()
    {
        return view('admin.reports');
    }

    // ====================== API接口 ======================
    // ✅ 修复：待审核状态对齐你的 `pending_approval`
    public function apiStats()
    {
        // 基础统计
        $stats = [
            'today_trades' => Trade::whereDate('created_at', today())->count(),
            'total_users' => User::count(),
            'total_items' => Item::count(),
            'total_trades' => Trade::count(),
            'pending_items' => Item::where('status', 'pending_approval')->count(),
        ];

        // 近30天交易数趋势
        $tradeTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $tradeTrend[] = [
                'date' => $date->format('m-d'),
                'count' => Trade::whereDate('created_at', $date)->count()
            ];
        }

        // 交易模式分布
        $tradeTypes = Trade::select('trade_type', DB::raw('count(*) as value'))
            ->groupBy('trade_type')
            ->get()
            ->map(function ($item) {
                $labels = ['sell' => '现金出售', 'exchange' => '以物换物', 'free' => '免费赠送'];
                return [
                    'value' => $item->value,
                    'label' => $labels[$item->trade_type] ?? $item->trade_type
                ];
            });

        // 物品分类分布
        $itemCategories = Item::select('category', DB::raw('count(*) as value'))
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => $item->category
                ];
            });

        // 近30天用户增长趋势
        $userTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $userTrend[] = [
                'date' => $date->format('m-d'),
                'count' => User::whereDate('created_at', $date)->count()
            ];
        }

        return response()->json([
            'stats' => $stats,
            'trade_trend' => $tradeTrend,
            'trade_types' => $tradeTypes,
            'item_categories' => $itemCategories,
            'user_trend' => $userTrend
        ]);
    }

    // ✅ 修复：使用方法注入获取 ItemService，没有构造方法！
    public function apiItems(Request $request, ItemServiceInterface $itemService)
    {
        $filters = $request->only(['status']);
        $items = $itemService->getAllItems($filters, 20);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total()
        ]);
    }

    // ✅ 用户列表API（保持不变）
    public function apiUsers(Request $request)
    {
        $users = User::when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $users->items(),
            'total' => $users->total()
        ]);
    }

    // ✅ 交易列表API（保持不变）
    public function apiTrades(Request $request)
    {
        $trades = Trade::with(['item', 'proposer', 'responder'])
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $trades->items(),
            'total' => $trades->total()
        ]);
    }

    // ✅ 修复：使用方法注入获取 ItemService
    public function updateItemStatus(Request $request, Item $item, ItemServiceInterface $itemService)
    {
        $validated = $request->validate([
            'status' => 'required|string'
        ]);

        try {
            // 调用你的服务层方法，自动经过状态机验证
            $itemService->changeStatus($item, $validated['status']);
            return response()->json(['ok' => true]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ 更新用户状态（保持不变）
    public function updateUserStatus(Request $request, User $user)
    {
        $user->update(['status' => $request->status]);
        return response()->json(['ok' => true]);
    }
}