<x-app-layout>
    <x-slot name="header">我的消息</x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
                @foreach($trades as $trade)
                    <a href="{{ route('messages.show', $trade) }}" 
                       class="block p-3 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex justify-between items-center">
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                交易：{{ $trade->item->title }}
                            </div>
                            @php
                                $lastMsg = $trade->messages->first();
                            @endphp
                            @if($lastMsg && !$lastMsg->is_read && $lastMsg->sender_id != Auth::id())
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">未读</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                            {{ $lastMsg?->content ?? '暂无消息' }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>