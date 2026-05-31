<x-app-layout>
    <x-slot name="header">{{ $user->name }} 的评价</x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4">
            <!-- 信誉统计 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-bold text-yellow-500">{{ $user->averageRating() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">平均评分</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-green-500">{{ $user->goodRate() }}%</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">好评率</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">{{ $user->receivedReviews()->count() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">总评价数</div>
                    </div>
                </div>
            </div>

            <!-- 评价列表 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                @foreach($reviews as $review)
                    <div class="p-4 border-b dark:border-gray-700 {{ $review->isBad() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-400">
                                    @for($i=1; $i<=5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </span>
                                <span class="font-medium">{{ $review->reviewer->name }}</span>
                                @if($review->isBad())
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">差评</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $review->created_at->diffForHumans() }}
                            </div>
                        </div>
                        
                        <!-- ✅ 改 $review->comment -->
                        @if($review->comment)
                            <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $review->comment }}</p>
                        @endif
                        
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            交易物品：<a href="{{ route('items.show', $review->trade->item) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $review->trade->item->title }}
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="p-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>