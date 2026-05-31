@extends('admin.layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">用户管理</h1>

<!-- 原生表格 -->
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">用户名</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">邮箱</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">学号</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">信誉分</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">交易数</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">好评</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">差评</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">迟到次数</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">状态</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">注册时间</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">操作</th>
            </tr>
        </thead>
        <tbody id="users-table-body" class="divide-y divide-gray-200">
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
    loadUsers();
});

// 加载用户数据
function loadUsers(page = 1) {
    currentPage = page;
    fetch(`/admin/api/users?page=${page}`)
        .then(res => res.json())
        .then(data => {
            renderUsers(data.data);
            renderPagination(data.total);
        });
}

// 渲染用户列表
function renderUsers(users) {
    const tbody = document.getElementById('users-table-body');
    tbody.innerHTML = '';

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12" class="px-4 py-8 text-center text-gray-500">暂无数据</td></tr>';
        return;
    }

    users.forEach(user => {
        const statusMap = {
            'active': '<span class="text-green-500">正常</span>',
            'banned': '<span class="text-red-500">已封禁</span>',
            'restricted': '<span class="text-yellow-500">限制发布</span>'
        };

        let actions = '';
        if (user.status === 'banned') {
            actions = `
                <button onclick="updateUserStatus(${user.id}, 'active')" class="bg-green-500 text-white px-2 py-1 rounded text-xs mr-1">解封</button>
            `;
        } else if (user.status === 'restricted') {
            actions = `
                <button onclick="updateUserStatus(${user.id}, 'active')" class="bg-green-500 text-white px-2 py-1 rounded text-xs mr-1">解除限制</button>
                <button onclick="updateUserStatus(${user.id}, 'banned')" class="bg-red-500 text-white px-2 py-1 rounded text-xs">封禁</button>
            `;
        } else {
            actions = `
                <button onclick="updateUserStatus(${user.id}, 'restricted')" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs mr-1">限制发布</button>
                <button onclick="updateUserStatus(${user.id}, 'banned')" class="bg-red-500 text-white px-2 py-1 rounded text-xs">封禁</button>
            `;
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-3 text-sm">${user.id}</td>
            <td class="px-4 py-3 text-sm">${user.name}</td>
            <td class="px-4 py-3 text-sm">${user.email}</td>
            <td class="px-4 py-3 text-sm">${user.student_id || '-'}</td>
            <td class="px-4 py-3 text-sm">${(parseFloat(user.rating_avg) || 5.0).toFixed(1)}</td>
            <td class="px-4 py-3 text-sm">${user.total_trades}</td>
            <td class="px-4 py-3 text-sm">${user.positive_reviews}</td>
            <td class="px-4 py-3 text-sm">${user.negative_reviews}</td>
            <td class="px-4 py-3 text-sm">${user.late_count}</td>
            <td class="px-4 py-3 text-sm">${statusMap[user.status] || user.status}</td>
            <td class="px-4 py-3 text-sm">${new Date(user.created_at).toLocaleString()}</td>
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
        prevBtn.onclick = () => loadUsers(currentPage - 1);
        buttonsDiv.appendChild(prevBtn);
    }

    if (currentPage < totalPages) {
        const nextBtn = document.createElement('button');
        nextBtn.className = 'px-3 py-1 rounded bg-gray-200 hover:bg-gray-300';
        nextBtn.textContent = '下一页';
        nextBtn.onclick = () => loadUsers(currentPage + 1);
        buttonsDiv.appendChild(nextBtn);
    }
}

// 更新用户状态
function updateUserStatus(id, status) {
    fetch(`/admin/api/users/${id}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    }).then(() => {
        loadUsers(currentPage);
    });
}
</script>
@endsection