<x-app-layout>
    <x-slot name="header">发起交易</x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <form method="POST" action="{{ route('trades.store') }}">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">

                <div class="mb-4">
                    <label>交易类型</label>
                    <select name="trade_type" class="w-full border p-2 rounded" required>
                        <option value="sell" {{ $item->trade_type == 'sell' ? 'selected' : '' }}>现金购买</option>
                        <option value="exchange" {{ $item->trade_type == 'exchange' ? 'selected' : '' }}>以物换物</option>
                        <option value="free" {{ $item->trade_type == 'free' ? 'selected' : '' }}>免费领取</option>
                    </select>
                </div>

                @if($item->trade_type == 'sell')
                <div class="mb-4">
                    <label>价格</label>
                    <input type="number" step="0.01" name="price" 
                           value="{{ $item->price }}" class="w-full border p-2 rounded">
                </div>
                @endif

                @if($item->trade_type == 'exchange')
                <div class="mb-4">
                    <label>选择你要交换的物品</label>
                    <select name="offer_item_id" class="w-full border p-2 rounded">
                        @foreach($myItems as $myItem)
                        <option value="{{ $myItem->id }}">{{ $myItem->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button class="bg-indigo-600 text-white px-4 py-2 rounded w-full">
                    发起交易
                </button>
            </form>
        </div>
    </div>
</x-app-layout>