<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            编辑物品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- 物品名称 -->
                        <div class="mb-4">
                            <x-input-label for="title" value="物品名称" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $item->title) }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- 物品分类 -->
                        <div class="mb-4">
                            <x-input-label for="category" value="物品分类" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="textbook" {{ old('category', $item->category) === 'textbook' ? 'selected' : '' }}>教材书籍</option>
                                <option value="electronics" {{ old('category', $item->category) === 'electronics' ? 'selected' : '' }}>电子产品</option>
                                <option value="daily" {{ old('category', $item->category) === 'daily' ? 'selected' : '' }}>生活用品</option>
                                <option value="clothing" {{ old('category', $item->category) === 'clothing' ? 'selected' : '' }}>衣物服饰</option>
                                <option value="beauty" {{ old('category', $item->category) === 'beauty' ? 'selected' : '' }}>美妆个护</option>
                                <option value="food" {{ old('category', $item->category) === 'food' ? 'selected' : '' }}>食品饮料</option>
                                <option value="other" {{ old('category', $item->category) === 'other' ? 'selected' : '' }}>其他</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <!-- 交易模式 -->
                        <div class="mb-4">
                            <x-input-label for="trade_type" value="交易模式" />
                            <select id="trade_type" name="trade_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="sell" {{ old('trade_type', $item->trade_type) === 'sell' ? 'selected' : '' }}>现金出售</option>
                                <option value="exchange" {{ old('trade_type', $item->trade_type) === 'exchange' ? 'selected' : '' }}>以物换物</option>
                                <option value="free" {{ old('trade_type', $item->trade_type) === 'free' ? 'selected' : '' }}>免费赠送</option>
                            </select>
                            <x-input-error :messages="$errors->get('trade_type')" class="mt-2" />
                        </div>

                        <!-- 价格（仅现金出售显示） -->
                        <div class="mb-4" id="price_field">
                            <x-input-label for="price" value="价格（元）" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('price', $item->price) }}" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- 期望物品（仅以物换物显示） -->
                        <div class="mb-4" id="expected_item_field">
                            <x-input-label for="expected_item" value="期望交换物品" />
                            <x-text-input id="expected_item" name="expected_item" type="text" class="mt-1 block w-full" value="{{ old('expected_item', $item->desired_item) }}" />
                            <x-input-error :messages="$errors->get('expected_item')" class="mt-2" />
                        </div>

                        <!-- 物品描述 -->
                        <div class="mb-4">
                            <x-input-label for="description" value="物品描述" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" required>{{ old('description', $item->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- 物品图片 -->
                        <div class="mb-4">
                            <x-input-label for="photos" value="物品图片（最多5张）" />
                            <x-text-input id="photos" name="photos[]" type="file" multiple accept="image/*" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                            
                            @if($item->photos && count($item->photos) > 0)
                                <div class="mt-2 grid grid-cols-5 gap-2">
                                    @foreach($item->photos as $photo)
                                        <img src="{{ asset('storage/' . $photo) }}" alt="物品图片" class="w-full h-20 object-cover rounded">
                                    @endforeach
                                </div>
                                <p class="text-sm text-gray-500 mt-1">已上传 {{ count($item->photos) }} 张图片，上传新图片会追加</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ms-4">
                                更新物品
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 交易模式切换逻辑
        document.addEventListener('DOMContentLoaded', function() {
            const tradeTypeSelect = document.getElementById('trade_type');
            const priceField = document.getElementById('price_field');
            const expectedItemField = document.getElementById('expected_item_field');

            function updateFields() {
                const value = tradeTypeSelect.value;
                priceField.style.display = value === 'sell' ? 'block' : 'none';
                expectedItemField.style.display = value === 'exchange' ? 'block' : 'none';
            }

            tradeTypeSelect.addEventListener('change', updateFields);
            updateFields(); // 初始化
        });
    </script>
</x-app-layout>