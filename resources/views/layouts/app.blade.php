<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Campus Exchange') }}</title>

    <!-- 字体 -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- DHTMLX 本地资源（全局唯一引入，子页面不再重复加载） -->
    <link rel="stylesheet" href="/dhtmlx/suite.min.css">

    <!-- Vite 资源 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            font-family: 'Figtree', system-ui, sans-serif;
        }
        .app-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
        }
        .app-toolbar {
            height: 56px;
            flex-shrink: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .app-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .app-sidebar {
            width: 220px;
            flex-shrink: 0;
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
            background: #fff;
        }
        .app-content {
            flex: 1;
            overflow-y: auto;
            background-color: #f3f4f6;
        }
        .page-header {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .page-header-inner {
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }
        .page-body {
            padding: 1.5rem 1rem;
            max-width: 80rem;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- 顶部导航栏 -->
        <div id="dhx-toolbar" class="app-toolbar"></div>

        <div class="app-main">
            <!-- 左侧分类侧边栏 -->
            <div id="dhx-sidebar" class="app-sidebar"></div>

            <!-- 右侧主内容区 -->
            <div class="app-content">
                @isset($header)
                <header class="page-header">
                    <div class="page-header-inner">
                        {{ $header }}
                    </div>
                </header>
                @endisset
                <div class="page-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    <!-- DHTMLX 脚本（全局唯一引入） -->
    <script src="/dhtmlx/suite.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. 顶部导航栏
            window.toolbar = new dhx.Toolbar("dhx-toolbar", {
                data: [
                    { id: "logo", value: "Campus Exchange", type: "title" },
                    { type: "spacer" },
                    { id: "items", value: "物品大厅", icon: "dxi dxi-view-grid" },
                    { id: "publish", value: "发布物品", icon: "dxi dxi-plus-circle" },
                    { id: "my-items", value: "我的物品", icon: "dxi dxi-package" },
                    { id: "messages", value: "消息", icon: "dxi dxi-email", badge: 0 },
                    { type: "separator" },
                    {
                        id: "user",
                        type: "menuItem",
                        value: "{{ Auth::user()->name }}",
                        icon: "dxi dxi-account-circle",
                        items: [
                            { id: "profile", value: "个人资料" },
                            { id: "logout", value: "退出登录" }
                        ]
                    }
                ]
            });

            // 导航跳转
            toolbar.events.on("click", function (id) {
                var routes = {
                    items: "{{ route('items.index') }}",
                    publish: "{{ route('items.create') }}",
                    "my-items": "{{ route('my.items') }}",
                    messages: "{{ route('messages.index') }}",
                    profile: "{{ route('profile.edit') }}"
                };
                if (routes[id]) window.location.href = routes[id];
                if (id === "logout") {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('logout') }}";
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });

            // 2. 左侧分类侧边栏（分类ID与后端接口完全对齐）
            window.sidebar = new dhx.Sidebar("dhx-sidebar", {
                data: [
                    { id: "all", value: "全部物品", icon: "dxi dxi-view-grid" },
                    { id: "textbook", value: "书籍教材", icon: "dxi dxi-book" },
                    { id: "electronics", value: "数码电子", icon: "dxi dxi-laptop" },
                    { id: "daily", value: "生活用品", icon: "dxi dxi-home-variant" },
                    { id: "clothing", value: "服饰鞋包", icon: "dxi dxi-tshirt-crew" },
                    { id: "free", value: "免费赠送", icon: "dxi dxi-gift" }
                ]
            });

            // 分类点击筛选（与物品页表格联动）
            sidebar.events.on("itemClick", function (id) {
                if (window.gridInstance && window.gridInstance.data) {
                    var url = id === "all" ? "/api/items" : "/api/items?category=" + id;
                    window.gridInstance.data.load(url);
                }
            });

            // 3. 未读消息轮询
            setInterval(function () {
                fetch('/api/messages/unread')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var count = data.count || 0;
                    toolbar.data.update("messages", { badge: count > 0 ? count : null });
                });
            }, 5000);

            // 标记全局就绪
            window.appReady = true;
        });
    </script>

    @stack('scripts')
</body>
</html>