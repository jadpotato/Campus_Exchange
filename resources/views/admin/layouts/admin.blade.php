<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>管理后台 - 校园二手交易</title>
    <!-- ✅ 强制加载 DHTMLX 最新稳定版 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dhtmlx-suite@7.4.13/codebase/suite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/dhtmlx-suite@7.4.13/codebase/suite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- 侧边栏 -->
        <div class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-4 text-xl font-bold border-b border-gray-700">
                管理后台
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.index') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.index') ? 'bg-gray-700' : '' }}">
                    首页统计
                </a>
                <a href="{{ route('admin.items') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.items') ? 'bg-gray-700' : '' }}">
                    物品管理
                </a>
                <a href="{{ route('admin.users') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700' : '' }}">
                    用户管理
                </a>
                <a href="{{ route('admin.trades') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.trades') ? 'bg-gray-700' : '' }}">
                    交易管理
                </a>
                <a href="{{ route('admin.reports') }}" class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.reports') ? 'bg-gray-700' : '' }}">
                    举报管理
                </a>
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 mt-8 border-t border-gray-700">
                    返回前台
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-700">
                        退出登录
                    </button>
                </form>
            </nav>
        </div>

        <!-- 主内容区 -->
        <div class="flex-1 overflow-auto p-6">
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>