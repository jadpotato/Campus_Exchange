<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $item->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <!-- 图片展示 -->
                    <div>
                        <img src="{{ $item->first_photo_url }}" alt="{{ $item->title }}" class="w-full h-80 object-cover rounded-lg">
                        
                        @if($item->photos && count($item->photos) > 1)
                            <div class="grid grid-cols-4 gap-2 mt-4">
                                @foreach($item->photos as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="{{ $item->title }}" class="w-full h-20 object-cover rounded cursor-pointer">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- 物品信息 -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $item->title }}</h1>
                        
                        <div class="mt-4 flex items-center">
                            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $item->trade_type_text }}
                                @if($item->trade_type === 'sell')
                                    ¥{{ $item->price }}
                                @endif
                            </span>
                            <span class="ml-4 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-600 dark:text-gray-300">
                                {{ $item->status_text }}
                            </span>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">分类</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $item->category }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">发布时间</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $item->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">浏览次数</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $item->views }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">物品描述</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ $item->description }}</p>
                        </div>

                        @if($item->trade_type === 'exchange' && $item->expected_item)
                            <div class="mt-6">
                                <h3 class="font-medium text-gray-900 dark:text-gray-100">期望交换物品</h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">{{ $item->expected_item }}</p>
                            </div>
                        @endif

                        <!-- 卖家信息 -->
                        <div class="mt-6 border-t pt-6 dark:border-gray-700">
                            <div class="flex items-center">
                                <img src="{{ $item->user->avatar_url }}" alt="{{ $item->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                <div class="ml-4">
                                    <a href="{{ route('users.show', $item->user) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $item->user->name }}
                                    </a>
                                    <div class="flex items-center mt-1">
                                        <div class="flex">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $item->user->rating_avg ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->user->rating_avg }} 分</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作按钮 -->
                        <div class="mt-6 space-y-3">
                            @if($item->canBeTraded() && $item->user_id !== auth()->id())
                                <x-primary-button class="w-full">发起交易</x-primary-button>
                            @endif

                            @if($item->user_id === auth()->id())
                                <div class="flex gap-3">
                                    <a href="{{ route('items.edit', $item) }}" class="flex-1">
                                        <x-secondary-button class="w-full">编辑物品</x-secondary-button>
                                    </a>
                                    <form method="POST" action="{{ route('items.destroy', $item) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button class="w-full" onclick="return confirm('确定要删除这个物品吗？')">删除物品</x-danger-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>