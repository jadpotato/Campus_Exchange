<x-app-layout>
    <x-slot name="header">交易详情</x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto px-4">
            <div class="bg-white p-6 rounded shadow">
                <p><strong>物品：</strong>{{ $trade->item->title }}</p>
                <p><strong>类型：</strong>{{ $trade->trade_type }}</p>
                <p><strong>价格：</strong>{{ $trade->price }}</p>
                <p><strong>状态：</strong>{{ $trade->status }}</p>
                <p><strong>发起者：</strong>{{ $trade->proposer_id == auth()->id() ? '我' : '对方' }}</p>

                <!-- <div class="mt-4 flex gap-2">
                    <button onclick="updateStatus('{{ $trade->id }}', 'completed')" 
                            class="bg-green-600 text-white px-3 py-1 rounded">
                        已完成
                    </button>
                    <button onclick="updateStatus('{{ $trade->id }}', 'cancelled')" 
                            class="bg-red-600 text-white px-3 py-1 rounded">
                        取消交易
                    </button>
                </div> -->
                <div class="mt-4 flex gap-2">
                    <button onclick="updateStatus('{{ $trade->id }}', 'completed')" 
                            class="bg-green-600 text-white px-3 py-1 rounded">
                        已完成
                    </button>
                    <button onclick="updateStatus('{{ $trade->id }}', 'cancelled')" 
                            class="bg-red-600 text-white px-3 py-1 rounded">
                        取消交易
                    </button>
                    
                    <!-- 评价按钮 -->
                    @if($trade->status == 'completed' && $trade->updated_at->diffInDays() <= 7)
                        @php
                            $hasReviewed = \App\Models\Review::where('trade_id', $trade->id)
                                ->where('reviewer_id', Auth::id())
                                ->exists();
                        @endphp
                        
                        @if(!$hasReviewed)
                            <a href="{{ route('reviews.create', $trade) }}" class="bg-indigo-600 text-white px-3 py-1 rounded">
                                评价对方
                            </a>
                        @else
                            <span class="text-gray-500 px-3 py-1">已评价</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(tradeId, status) {
            fetch(`/api/trades/${tradeId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            }).then(() => location.reload());
        }
    </script>
</x-app-layout>