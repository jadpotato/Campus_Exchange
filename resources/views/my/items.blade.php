<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            我的物品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 状态筛选栏 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6">
                <form method="GET" action="{{ route('my.items') }}" class="flex gap-4">
                    <div>
                        <x-input-label for="status" value="物品状态" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">全部状态</option>
                            <option value="pending_approval" {{ (old('status', $filters['status'] ?? '') === 'pending_approval') ? 'selected' : '' }}>待审核</option>
                            <option value="published" {{ (old('status', $filters['status'] ?? '') === 'published') ? 'selected' : '' }}>发布中</option>
                            <option value="locked" {{ (old('status', $filters['status'] ?? '') === 'locked') ? 'selected' : '' }}>已锁定</option>
                            <option value="completed" {{ (old('status', $filters['status'] ?? '') === 'completed') ? 'selected' : '' }}>已完成</option>
                            <option value="unpublished" {{ (old('status', $filters['status'] ?? '') === 'unpublished') ? 'selected' : '' }}>已下架</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <x-primary-button type="submit">筛选</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- 物品列表 -->
            @if($items->isEmpty())
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">你还没有发布任何物品</p>
                    <a href="{{ route('items.create') }}" class="inline-block mt-4">
                        <x-primary-button>发布第一个物品</x-primary-button>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($items as $item)
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                            <a href="{{ route('items.show', $item) }}">
                                <img src="{{ $item->first_photo_url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                            </a>
                            <div class="p-4">
                                <a href="{{ route('items.show', $item) }}" class="font-medium text-lg text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $item->title }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $item->trade_type_text }}
                                    @if($item->trade_type === 'sell')
                                        · ¥{{ $item->price }}
                                    @endif
                                </p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs px-2 py-1 rounded {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->status_text }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $item->view_count }} 浏览</span>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <a href="{{ route('items.edit', $item) }}" class="flex-1">
                                        <x-secondary-button class="w-full text-sm">编辑</x-secondary-button>
                                    </a>
                                    <form method="POST" action="{{ route('items.destroy', $item) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button class="w-full text-sm" onclick="return confirm('确定要删除这个物品吗？')">删除</x-danger-button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 分页 -->
                <div class="mt-6">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>