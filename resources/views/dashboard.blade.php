<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            仪表盘
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <p class="text-base text-black font-medium mb-6">您已成功登录系统！</p>

                <div id="dashboard_grid" style="width: 100%; min-height: 180px;"></div>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <div id="card_market">
            <a href="{{ route('items.index') }}" class="dashboard-card-link block h-full p-5 bg-white border border-gray-200 rounded-lg transition hover:shadow-md">
                <h3 class="font-bold text-lg text-black mb-2 flex items-center">
                    <span class="mdi mdi-store-search-outline mr-2 text-indigo-600 text-xl"></span>
                    浏览物品市场
                </h3>
                <p class="text-sm text-black leading-relaxed">查看所有可交易的校园/社区闲置物品，寻找您心仪的宝贝。</p>
            </a>
        </div>

        <div id="card_create">
            <a href="{{ route('items.create') }}" class="dashboard-card-link block h-full p-5 bg-white border border-gray-200 rounded-lg transition hover:shadow-md">
                <h3 class="font-bold text-lg text-black mb-2 flex items-center">
                    <span class="mdi mdi-plus-circle-outline mr-2 text-green-600 text-xl"></span>
                    发布新物品
                </h3>
                <p class="text-sm text-black leading-relaxed">快速发布你的闲置物品，支持以物换物、出售或免费赠送。</p>
            </a>
        </div>

        <div id="card_manage">
            <a href="{{ route('my.items') }}" class="dashboard-card-link block h-full p-5 bg-white border border-gray-200 rounded-lg transition hover:shadow-md">
                <h3 class="font-bold text-lg text-black mb-2 flex items-center">
                    <span class="mdi mdi-folder-manage-outline mr-2 text-orange-500 text-xl"></span>
                    管理我的物品
                </h3>
                <p class="text-sm text-black leading-relaxed">查看、编辑或上下架你已发布的物品，实时掌握交易动态。</p>
            </a>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof dhx === 'undefined') {
        console.error("DHTMLX 未载入");
        return;
    }

    // 使用 DHTMLX Layout 构建 3 列的网格布局
    const layout = new dhx.Layout("dashboard_grid", {
        cols: [
            { id: "col_market", padding: "0 12px 0 0" },
            { id: "col_create", padding: "0 12px" },
            { id: "col_manage", padding: "0 0 0 12px" }
        ]
    });

    // 将预埋的卡片 HTML 渲染进对应的 DHTMLX 布局单元格中
    layout.getCell("col_market").attachHTML(document.getElementById("card_market").innerHTML);
    layout.getCell("col_create").attachHTML(document.getElementById("card_create").innerHTML);
    layout.getCell("col_manage").attachHTML(document.getElementById("card_manage").innerHTML);
});
</script>

<style>
    /* 彻底杜绝暗黑模式干扰，强制覆盖样式 */
    .dashboard-card-link {
        background-color: #ffffff !important;
        border-color: #e5e7eb !important;
        text-decoration: none !important;
    }
    
    /* 悬停时的高亮效果 */
    .dashboard-card-link:hover {
        border-color: #4f46e5 !important;
        background-color: #f9fafb !important;
    }

    /* 保证卡片内的所有文字都是纯黑色 */
    .dashboard-card-link h3,
    .dashboard-card-link p {
        color: #000000 !important;
    }

    /* 如果系统没有载入 mdi 图标，这段样式可作为备用间距调整 */
    .mdi {
        display: inline-block;
        vertical-align: middle;
    }
</style>
</x-app-layout>