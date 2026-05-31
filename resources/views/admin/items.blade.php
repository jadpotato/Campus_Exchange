@extends('admin.layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">物品管理</h1>

<!-- 筛选按钮 -->
<div class="mb-4 flex gap-2">
    <button onclick="filterItems('all')" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">全部</button>
    <button onclick="filterItems('pending_approval')" class="px-3 py-1 rounded bg-orange-200 hover:bg-orange-300">待审核</button>
    <button onclick="filterItems('published')" class="px-3 py-1 rounded bg-green-200 hover:bg-green-300">已发布</button>
    <button onclick="filterItems('unpublished')" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">已下架</button>
    <button onclick="filterItems('cancelled')" class="px-3 py-1 rounded bg-red-200 hover:bg-red-300">已拒绝</button>
</div>

<!-- 原生表格 -->
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">物品名称</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">分类</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">价格</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">状态</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">发布者</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">发布时间</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">操作</th>
            </tr>
        </thead>
        <tbody id="items-table-body" class="divide-y divide-gray-200">
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
let currentStatus = 'all';
const perPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    loadItems();
});

// 加载物品数据
function loadItems(page = 1) {
    currentPage = page;
    let url = `/admin/api/items?page=${page}`;
    if (currentStatus !== 'all') {
        url += `&status=${currentStatus}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            renderItems(data.data);
            renderPagination(data.total);
        });
}

// 渲染物品列表
function renderItems(items) {
    const tbody = document.getElementById('items-table-body');
    tbody.innerHTML = '';

    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">暂无数据</td></tr>';
        return;
    }

    items.forEach(item => {
        const statusMap = {
            'pending_approval': '<span class="text-orange-500">待审核</span>',
            'published': '<span class="text-green-500">已发布</span>',
            'locked': '<span class="text-blue-500">交易中</span>',
            'unpublished': '<span class="text-gray-500">已下架</span>',
            'completed': '<span class="text-purple-500">已完成</span>',
            'cancelled': '<span class="text-red-500">已拒绝</span>'
        };

        let actions = '';
        if (item.status === 'pending_approval') {
            actions = `
                <button onclick="updateItemStatus(${item.id}, 'published')" class="bg-green-500 text-white px-2 py-1 rounded text-xs mr-1">通过</button>
                <button onclick="updateItemStatus(${item.id}, 'cancelled')" class="bg-red-500 text-white px-2 py-1 rounded text-xs">拒绝</button>
            `;
        } else if (item.status === 'published') {
            actions = `
                <button onclick="updateItemStatus(${item.id}, 'unpublished')" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">下架</button>
            `;
        } else if (item.status === 'unpublished' || item.status === 'cancelled') {
            actions = `
                <button onclick="updateItemStatus(${item.id}, 'published')" class="bg-green-500 text-white px-2 py-1 rounded text-xs">恢复发布</button>
            `;
        } else {
            actions = '<span class="text-gray-400">不可操作</span>';
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-3 text-sm">${item.id}</td>
            <td class="px-4 py-3 text-sm">${item.title}</td>
            <td class="px-4 py-3 text-sm">${item.category}</td>
            <td class="px-4 py-3 text-sm">${item.price ? '¥' + item.price : '免费'}</td>
            <td class="px-4 py-3 text-sm">${statusMap[item.status] || item.status}</td>
            <td class="px-4 py-3 text-sm">${item.user?.name || '未知'}</td>
            <td class="px-4 py-3 text-sm">${new Date(item.created_at).toLocaleString()}</td>
            <td class="px-4 py-3 text-sm">${actions}</td>
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
        prevBtn.onclick = () => loadItems(currentPage - 1);
        buttonsDiv.appendChild(prevBtn);
    }

    if (currentPage < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
        nextBtn.textContent = '下一页';
        nextBtn.onclick = () => loadItems(currentPage + 1);
        buttonsDiv.appendChild(nextBtn);
    }
}

// 筛选物品
function filterItems(status) {
    currentStatus = status;
    loadItems(1);
}

// 更新物品状态
function updateItemStatus(id, status) {
    fetch(`/admin/api/items/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    }).then(() => {
        loadItems(currentPage);
    });
}
</script>
@endsection