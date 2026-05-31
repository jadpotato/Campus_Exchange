@extends('admin.layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">后台首页</h1>

    <!-- 统计卡片 -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-3xl font-bold text-indigo-600" id="today_trades">0</div>
            <div class="text-gray-500">今日交易数</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-3xl font-bold text-green-600" id="total_users">0</div>
            <div class="text-gray-500">总用户数</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-3xl font-bold text-yellow-600" id="total_items">0</div>
            <div class="text-gray-500">总物品数</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-3xl font-bold text-red-600" id="total_trades">0</div>
            <div class="text-gray-500">总交易数</div>
        </div>
        <!-- ✅ 待审核物品数 -->
        <div class="bg-white p-4 rounded shadow">
            <div class="text-3xl font-bold text-orange-600" id="pending_items">0</div>
            <div class="text-gray-500">待审核物品</div>
        </div>
    </div>

    <!-- 图表区域 -->
    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">近30天交易数趋势</h3>
            <div id="trade_trend" style="height: 300px;"></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">交易模式分布</h3>
            <div id="trade_types" style="height: 300px;"></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">物品分类分布</h3>
            <div id="item_categories" style="height: 300px;"></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">近30天用户增长</h3>
            <div id="user_trend" style="height: 300px;"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/admin/api/stats')
        .then(res => res.json())
        .then(data => {
            // 更新统计卡片
            document.getElementById('today_trades').textContent = data.stats.today_trades;
            document.getElementById('total_users').textContent = data.stats.total_users;
            document.getElementById('total_items').textContent = data.stats.total_items;
            document.getElementById('total_trades').textContent = data.stats.total_trades;
            document.getElementById('pending_items').textContent = data.stats.pending_items;
            // 交易趋势图
            new dhx.Chart("trade_trend", {
                type: "line",
                data: data.trade_trend,
                series: [
                    { value: "count", color: "#4f46e5" }
                ],
                scales: {
                    "bottom": { text: "date" }
                }
            });

            // 交易模式饼图
            new dhx.Chart("trade_types", {
                type: "pie",
                data: data.trade_types,
                series: [
                    { value: "value", color: ["#4f46e5", "#10b981", "#f59e0b"] }
                ]
            });

            // 物品分类饼图
            new dhx.Chart("item_categories", {
                type: "pie",
                data: data.item_categories,
                series: [
                    { value: "value" }
                ]
            });

            // 用户增长图
            new dhx.Chart("user_trend", {
                type: "line",
                data: data.user_trend,
                series: [
                    { value: "count", color: "#10b981" }
                ],
                scales: {
                    "bottom": { text: "date" }
                }
            });
        });
});
</script>
@endsection