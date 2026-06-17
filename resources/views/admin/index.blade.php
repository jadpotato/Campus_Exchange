@extends('admin.layouts.admin')

@section('styles')
<style>
    /* 用最清晰的纯 HTML/CSS 构建统计大盘卡片与图表容器 */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    .stat-value { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.875rem; color: #6b7280; }

    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .chart-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        height: 380px;
    }
    .chart-title { font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem; }
    .chart-view { width: 100%; height: 300px; }
</style>
@endsection

@section('content')
<div class="dashboard-grid">
    <div class="stat-card"><div id="v1" class="stat-value" style="color: #4f46e5;">0</div><div class="stat-label">今日交易数</div></div>
    <div class="stat-card"><div id="v2" class="stat-value" style="color: #10b981;">0</div><div class="stat-label">总用户数</div></div>
    <div class="stat-card"><div id="v3" class="stat-value" style="color: #f59e0b;">0</div><div class="stat-label">总物品数</div></div>
    <div class="stat-card"><div id="v4" class="stat-value" style="color: #ef4444;">0</div><div class="stat-label">总交易数</div></div>
    <div class="stat-card"><div id="v5" class="stat-value" style="color: #ec4899;">0</div><div class="stat-label">待审核物品</div></div>
</div>

<div class="charts-grid">
    <div class="chart-container">
        <div class="chart-title">近30天交易数趋势</div>
        <div id="chart-trade" class="chart-view"></div>
    </div>
    <div class="chart-container">
        <div class="chart-title">交易模式分布</div>
        <div id="chart-type" class="chart-view"></div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-container">
        <div class="chart-title">物品分类分布</div>
        <div id="chart-category" class="chart-view"></div>
    </div>
    <div class="chart-container">
        <div class="chart-title">近30天用户增长</div>
        <div id="chart-user" class="chart-view"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
window.initChildView = function() {
    fetch('/admin/api/stats')
        .then(res => res.json())
        .then(data => {
            // 1. 填入卡片数据
            document.getElementById('v1').innerText = data.stats.today_trades;
            document.getElementById('v2').innerText = data.stats.total_users;
            document.getElementById('v3').innerText = data.stats.total_items;
            document.getElementById('v4').innerText = data.stats.total_trades;
            document.getElementById('v5').innerText = data.stats.pending_items;

            // 2. 渲染图表1：近30天交易趋势
            new dhx.Chart("chart-trade", { 
                type: "line", 
                data: data.trade_trend, 
                series: [{ id: "t_line", value: "count", color: "#4f46e5", strokeWidth: 3 }], 
                scales: { 
                    "bottom": { relation: "date", value: "date" },
                    "left": { maxTicks: 5 }
                } 
            });

            // 3. 渲染图表2：交易模式分布
            new dhx.Chart("chart-type", { 
                type: "pie", 
                data: data.trade_types, 
                series: [{ value: "value", color: ["#4f46e5", "#10b981", "#f59e0b"] }] 
            });

            // 4. 渲染图表3：物品分类分布
            new dhx.Chart("chart-category", { 
                type: "pie", 
                data: data.item_categories, 
                series: [{ value: "value" }] 
            });

            // 5. 渲染图表4：近30天用户增长
            new dhx.Chart("chart-user", { 
                type: "line", 
                data: data.user_trend, 
                series: [{ id: "u_line", value: "count", color: "#10b981", strokeWidth: 3 }], 
                scales: { 
                    "bottom": { relation: "date", value: "date" },
                    "left": { maxTicks: 5 }
                } 
            });
        })
        .catch(err => console.error("图表数据装载失败: ", err));
}
</script>
@endsection