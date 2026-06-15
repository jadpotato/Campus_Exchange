<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ $user->name }} 的个人主页
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white shadow sm:rounded-lg p-6 border border-gray-100">
                <div class="flex items-center space-x-6">
                    <img class="h-20 w-20 rounded-full object-cover border border-gray-200 shadow-sm" 
                         src="{{ $user->avatar_url ?? ($user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png')) }}" 
                         alt="{{ $user->name }}">
                    
                    <div class="space-y-1">
                        <h1 class="text-xl font-bold text-black leading-tight">{{ $user->name }}</h1>
                        
                        <div class="flex items-center text-sm">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $user->rating_avg ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="ml-1.5 text-black font-bold">{{ $user->rating_avg }} 分</span>
                            </div>
                            <span class="mx-3 text-gray-300">|</span>
                            <span class="text-black font-medium">完成交易 {{ $user->total_trades }} 次</span>
                        </div>
                        <p class="text-black text-xs">加入时间：{{ $user->created_at->format('Y年m月') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div id="user_tabbar" style="width: 100%; min-height: 450px;"></div>
            </div>

        </div>
    </div>

    <div style="display: none;">
        <div id="tab_items_src" class="dhx-tab-padding">
            @if($user->items->isEmpty())
                <p class="text-black text-sm font-medium py-4">暂无发布的物品</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($user->items as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <img class="h-40 w-full object-cover" 
                                 src="{{ $item->photos ? asset('storage/'.json_decode($item->photos)[0]) : asset('images/default-item.png') }}" 
                                 alt="{{ $item->title }}">
                            <div class="p-4">
                                <h4 class="font-bold text-black text-base mb-1">{{ $item->title }}</h4>
                                <p class="text-sm font-bold text-red-600">
                                    @if($item->trade_type === 'sell')
                                        ¥{{ $item->price }}
                                    @elseif($item->trade_type === 'exchange')
                                        以物换物
                                    @else
                                        免费赠送
                                    @endif
                                </p>
                                <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold mt-3 inline-block">查看详情 →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="tab_reviews_src" class="dhx-tab-padding">
            @if($user->receivedReviews->isEmpty())
                <p class="text-black text-sm font-medium py-4">暂无评价</p>
            @else
                <div class="space-y-4">
                    @foreach($user->receivedReviews as $review)
                        <div class="border-b pb-4 border-gray-100 last:border-none">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover border border-gray-200" 
                                         src="{{ $review->reviewer->avatar_url ?? ($review->reviewer->avatar ? asset('storage/'.$review->reviewer->avatar) : asset('images/default-avatar.png')) }}" 
                                         alt="{{ $review->reviewer->name }}">
                                    <span class="ml-2 text-sm font-bold text-black">{{ $review->reviewer->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-black text-sm font-medium mt-2 pl-10">{{ $review->comment }}</p>
                            <p class="text-xs text-gray-500 mt-1 pl-10">{{ $review->created_at->format('Y年m月d日') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof dhx === 'undefined') {
        console.error("DHTMLX 未载入");
        return;
    }

    // 初始化 DHTMLX Tabbar 组件
    const tabbar = new dhx.Tabbar("user_tabbar", {
        css: "dhx_widget--bg-white",
        views: [
            { id: "items", tab: "发布的物品" },
            { id: "reviews", tab: "收到的评价 ({{ $user->receivedReviews->count() }})" }
        ],
        activeView: "items"
    });

    // 装载 HTML 模版
    tabbar.getCell("items").attachHTML(document.getElementById("tab_items_src").innerHTML);
    tabbar.getCell("reviews").attachHTML(document.getElementById("tab_reviews_src").innerHTML);
});
</script>

<style>
    .dhx-tab-padding {
        padding: 24px 8px;
    }
    
    .dhx_tabbar-tab {
        color: #000000 !important;
        font-weight: 600 !important;
        font-size: 14px !important; /* 同步微调了标签栏字体大小 */
    }
    .dhx_tabbar-tab--active {
        color: #4f46e5 !important;
    }
</style>
</x-app-layout>