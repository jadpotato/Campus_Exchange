<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}

                    {{-- 新增快捷入口卡片 --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('items.index') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <h3 class="font-medium text-lg mb-2">浏览物品市场</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">查看所有可交易的闲置物品</p>
                        </a>

                        <a href="{{ route('items.create') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <h3 class="font-medium text-lg mb-2">发布新物品</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">发布你的闲置物品</p>
                        </a>

                        <a href="{{ route('my.items') }}" class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <h3 class="font-medium text-lg mb-2">管理我的物品</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">查看/编辑/删除你发布的物品</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>