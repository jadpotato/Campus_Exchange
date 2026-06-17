<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Management Control Panel</title>

    <link rel="stylesheet" href="/dhtmlx/suite.min.css">

    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: system-ui, -apple-system, sans-serif;
        }
        /* 标准的 Flex 两栏布局 */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
        }
        .app-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        /* 侧边栏原生挂载点 */
        .app-sidebar {
            width: 240px;
            flex-shrink: 0;
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
            background: #fff;
        }
        /* 主内容区域 */
        .app-content {
            flex: 1;
            overflow-y: auto;
            background-color: #f3f4f6;
            padding: 1.5rem;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <div class="app-main">
            <div id="dhx-sidebar" class="app-sidebar"></div>

            <div class="app-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="/dhtmlx/suite.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 🚀 2. 标准侧边栏实例化（无任何魔改 CSS 干扰，回归原生白底黑字）
            window.sidebar = new dhx.Sidebar("dhx-sidebar", {
                data: [
                    { id: "dashboard", value: "控制台", icon: "dxi dxi-view-grid" },
                    { id: "items", value: "物品管理", icon: "dxi dxi-package" },
                    { id: "users", value: "用户管理", icon: "dxi dxi-account-multiple" },
                    { id: "trades", value: "交易管理", icon: "dxi dxi-currency-usd" },
                    { id: "reports", value: "举报管理", icon: "dxi dxi-alert-circle" }
                ]
            });

            // 路由联动自动高亮
            var currentPath = window.location.pathname;
            if (currentPath.includes('/admin/items')) window.sidebar.data.update("items", { active: true });
            else if (currentPath.includes('/admin/users')) window.sidebar.data.update("users", { active: true });
            else if (currentPath.includes('/admin/trades')) window.sidebar.data.update("trades", { active: true });
            else if (currentPath.includes('/admin/reports')) window.sidebar.data.update("reports", { active: true });
            else window.sidebar.data.update("dashboard", { active: true });

            // 菜单点击跳转
            window.sidebar.events.on("click", function (id) {
                if (id === "dashboard") window.location.href = "{{ url('/admin') }}";
                if (id === "items") window.location.href = "{{ url('/admin/items') }}";
                if (id === "users") window.location.href = "{{ url('/admin/users') }}";
                if (id === "trades") window.location.href = "{{ url('/admin/trades') }}";
                if (id === "reports") window.location.href = "{{ url('/admin/reports') }}";
            });

            // 执行子页面初始化钩子
            if (typeof window.initChildView === 'function') {
                window.initChildView();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>