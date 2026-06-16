<x-app-layout>
    <script>
        window.customSidebarConfig = {
            data: [
                { id: "dash_home", value: "控制台概览", icon: "dxi dxi-chart-bar" },
                { id: "dash_items", value: "我发布的物品", icon: "dxi dxi-package" },
                { id: "dash_trades", value: "我的交易订单", icon: "dxi dxi-sync" },
                { id: "dash_msg", value: "消息与会话", icon: "dxi dxi-email" },
                { id: "dash_profile", value: "修改个人资料", icon: "dxi dxi-account-circle" }
            ],
            // 劫持侧边栏的点击事件，使其成为控制台中心的页面级路由导航
            onItemClick: function (id) {
                var centerRoutes = {
                    dash_home: "{{ route('dashboard') }}",
                    dash_items: "{{ route('my.items') }}",
                    dash_trades: "{{ route('trades.my') }}",
                    dash_msg: "{{ route('messages.index') }}",
                    dash_profile: "{{ route('profile.edit') }}"
                };
                if (centerRoutes[id]) {
                    window.location.href = centerRoutes[id];
                }
            },
            // 初始化完成后，高亮选中当前的“控制台概览”项
            afterInit: function(sidebarInstance) {
                sidebarInstance.select("dash_home");
            }
        };
    </script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            个人工作台
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <p class="text-base text-black font-medium mb-6">欢迎回来，{{ Auth::user()->name }}！您已成功登录系统。</p>

                <div id="dashboard_dataview" style="width: 100%;"></div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof dhx === 'undefined') {
        return;
    }

    const dataset = [
        {
            title: "浏览物品市场",
            desc: "查看所有可交易的校园/社区闲置物品，寻找您心仪的宝贝。",
            link: "{{ route('items.index') }}",
            icon: "mdi-store-search-outline",
            customCss: "dhx-card-blue"
        },
        {
            title: "发布新物品",
            desc: "快速发布你的闲置物品，支持以物换物、出售或免费赠送。",
            link: "{{ route('items.create') }}",
            icon: "mdi-plus-circle-outline",
            customCss: "dhx-card-green"
        },
        {
            title: "管理我的物品",
            desc: "查看、编辑或上下架你已发布的物品，实时掌握交易动态。",
            link: "{{ route('my.items') }}",
            icon: "mdi-folder-manage-outline",
            customCss: "dhx-card-purple"
        }
    ];

    const dataview = new dhx.DataView("dashboard_dataview", {
        itemsInRow: 3,
        gap: 20,
        height: "170px",
        template: function (item) {
            return `
                <a href="${item.link}" class="dhx-custom-card ${item.customCss}">
                    <div class="dhx-card-title">
                        <span class="mdi ${item.icon}"></span>
                        ${item.title}
                    </div>
                    <div class="dhx-card-desc">${item.desc}</div>
                </a>
            `;
        },
        data: dataset
    });
});
</script>

<style>
    .dhx_dataview-item {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
    }

    .dhx-custom-card {
        display: block;
        height: 100%;
        padding: 20px;
        border-radius: 8px;
        text-decoration: none !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .dhx-custom-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .dhx-card-blue {
        background-color: #f0f7ff !important;
        border: 1px solid #bae6fd !important;
    }
    .dhx-card-blue:hover { background-color: #e0f2fe !important; }

    .dhx-card-green {
        background-color: #f0fdf4 !important;
        border: 1px solid #bbf7d0 !important;
    }
    .dhx-card-green:hover { background-color: #dcfce7 !important; }

    .dhx-card-purple {
        background-color: #faf5ff !important;
        border: 1px solid #e9d5ff !important;
    }
    .dhx-card-purple:hover { background-color: #f3e8ff !important; }

    .dhx-card-title {
        font-size: 18px;
        font-weight: 700;
        color: #000000 !important;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
    }
    .dhx-card-title .mdi {
        margin-right: 8px;
        font-size: 20px;
    }
    
    .dhx-card-desc {
        font-size: 14px;
        color: #000000 !important;
        line-height: 1.6;
    }
</style>
</x-app-layout>