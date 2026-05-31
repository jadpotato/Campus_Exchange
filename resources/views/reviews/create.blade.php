<x-app-layout>
    <x-slot name="header">提交评价</x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">评价交易：{{ $trade->item->title }}</h3>
                
                <form method="POST" action="{{ route('reviews.store', $trade) }}">
                    @csrf
                    <input type="hidden" name="reviewee_id" value="{{ $revieweeId }}">

                    <div class="mb-6">
                        <label class="block mb-2">评分</label>
                        <div class="flex gap-2">
                            @for($i=1; $i<=5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                                    <span class="text-3xl peer-checked:text-yellow-400 text-gray-300">★</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block mb-2">评价内容（可选）</label>
                        <!-- ✅ 改 name="comment" -->
                        <textarea name="comment" rows="4" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="分享您的交易体验..."></textarea>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                            提交评价
                        </button>
                        <a href="{{ route('trades.show', $trade) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            返回
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>