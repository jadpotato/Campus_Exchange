@extends('admin.layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">交易管理</h1>

<!-- 原生表格 -->
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">物品名称</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">交易模式</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">状态</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">发起者</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">响应者</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">创建时间</th>
            </tr>
        </thead>
        <tbody id="trades-table-body" class="divide-y divide-gray-200">
            <!-- 数据会通过JS动态填充 -->
        </tbody>
    </table>
</div>

<!-- 分页 -->
<div class="mt-4 flex justify-between items-center">
    <div id="pagination-info" class="text-sm text-gray-500"></div>
    <div id="pagination-buttons" class="flex gap-2"></div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
const perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadTrades();
});

// 加载交易数据
function loadTrades(page = 1) {
    currentPage = page;
    fetch(`/admin/api/trades?page=${page}`)
        .then(res => res.json())
        .then(data => {
            renderTrades(data.data);
            renderPagination(data.total);
        });
}

// 渲染交易列表
function renderTrades(trades) {
    const tbody = document.getElementById('trades-table-body');
    tbody.innerHTML = '';

    if (trades.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">暂无数据</td></tr>';
        return;
    }

    trades.forEach(trade => {
        const tradeTypeMap = {
            'sell': '现金出售',
            'exchange': '以物换物',
            'free': '免费赠送'
        };

        const statusMap = {
            'pending': '待确认',
            'confirmed': '已确认',
            'completed': '已完成',
            'cancelled': '已取消'
        };

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-3 text-sm">${trade.id}</td>
            <td class="px-4 py-3 text-sm">${trade.item?.title || '未知'}</td>
            <td class="px-4 py-3 text-sm">${tradeTypeMap[trade.trade_type] || trade.trade_type}</td>
            <td class="px-4 py-3 text-sm">${statusMap[trade.status] || trade.status}</td>
            <td class="px-4 py-3 text-sm">${trade.proposer?.name || '未知'}</td>
            <td class="px-4 py-3 text-sm">${trade.responder?.name || '未知'}</td>
            <td class="px-4 py-3 text-sm">${new Date(trade.created_at).toLocaleString()}</td>
        `;
        tbody.appendChild(tr);
    });
}

// 渲染分页
function renderPagination(total) {
    const totalPages = Math.ceil(total / perPage);
    document.getElementById('pagination-info').textContent = `共 ${total} 条记录，第 ${currentPage}/${totalPages} 页`;

    const buttonsDiv = document.getElementById('pagination-buttons');
    buttonsDiv.innerHTML = '';

    if (currentPage > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
        prevBtn.textContent = '上一页';
        prevBtn.onclick = () => loadTrades(currentPage - 1);
        buttonsDiv.appendChild(prevBtn);
    }

    if (currentPage < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
        nextBtn.textContent = '下一页';
        nextBtn.onclick = () => loadTrades(currentPage + 1);
        buttonsDiv.appendChild(nextBtn);
    }
}
</script>
@endsection