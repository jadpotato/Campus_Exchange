<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }} 的个人主页
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 用户基本信息 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center space-x-6">
                        <img class="h-24 w-24 rounded-full object-cover" 
                             src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}" 
                             alt="{{ $user->name }}">
                        
                        <div>
                            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                            <div class="flex items-center mt-2">
                                <div class="flex items-center">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $user->rating_avg ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-gray-600">{{ $user->rating_avg }} 分</span>
                                </div>
                                <span class="mx-2 text-gray-400">|</span>
                                <span class="text-gray-600">完成交易 {{ $user->total_trades }} 次</span>
                            </div>
                            <p class="text-gray-500 mt-1">加入时间：{{ $user->created_at->format('Y年m月') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 发布的物品 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">发布的物品</h3>
                    
                    @if($user->items->isEmpty())
                        <p class="text-gray-500">暂无发布的物品</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($user->items as $item)
                                <div class="border rounded-lg overflow-hidden">
                                    <img class="h-40 w-full object-cover" 
                                         src="{{ $item->photos ? asset('storage/'.json_decode($item->photos)[0]) : asset('images/default-item.png') }}" 
                                         alt="{{ $item->title }}">
                                    <div class="p-4">
                                        <h4 class="font-medium">{{ $item->title }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            @if($item->trade_type === 'sell')
                                                ¥{{ $item->price }}
                                            @elseif($item->trade_type === 'exchange')
                                                以物换物
                                            @else
                                                免费赠送
                                            @endif
                                        </p>
                                        <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 text-sm mt-2 inline-block">查看详情</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 收到的评价 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">收到的评价</h3>
                    
                    @if($user->receivedReviews->isEmpty())
                        <p class="text-gray-500">暂无评价</p>
                    @else
                        <div class="space-y-4">
                            @foreach($user->receivedReviews as $review)
                                <div class="border-b pb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full object-cover" 
                                                 src="{{ $review->reviewer->avatar ? asset('storage/'.$review->reviewer->avatar) : asset('images/default-avatar.png') }}" 
                                                 alt="{{ $review->reviewer->name }}">
                                            <span class="ml-2 font-medium">{{ $review->reviewer->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-600 mt-2">{{ $review->comment }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('Y年m月d日') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>