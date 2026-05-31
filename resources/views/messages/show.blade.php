<x-app-layout>
    <x-slot name="header">交易对话</x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <a href="{{ route('messages.index') }}" class="mb-4 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">
                ← 返回消息列表
            </a>

            <!-- 消息容器 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 h-96 overflow-y-auto" id="msgBox">
                @foreach($messages as $msg)
                    <div class="mb-3 {{ $msg->sender_id == Auth::id() ? 'text-right' : '' }}">
                        <div class="inline-block p-2 rounded-lg max-w-[75%]
                                    {{ $msg->sender_id == Auth::id() 
                                        ? 'bg-indigo-500 text-white' 
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                            {{ $msg->content }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $msg->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 发送表单 -->
            <form method="POST" action="{{ route('messages.store') }}" class="mt-4 flex gap-2">
                @csrf
                <input type="hidden" name="trade_id" value="{{ $trade->id }}">
                <input type="text" name="content" 
                       class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="输入消息..." required>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                    发送
                </button>
            </form>
        </div>
    </div>

    <script>
        // 滚动到底部
        const box = document.getElementById('msgBox');
        box.scrollTop = box.scrollHeight;

        // 每5秒轮询一次未读消息
        setInterval(() => {
            fetch('/api/messages/unread')
                .then(res => res.json())
                .then(data => {
                    // 这里可以更新导航栏的未读角标
                    console.log('未读消息数:', data.count);
                });
        }, 5000);
    </script>
</x-app-layout>