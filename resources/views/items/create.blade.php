<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            发布物品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="title" value="物品名称" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="物品描述" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category" value="物品分类" />
                            <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="textbook">教材书籍</option>
                                <option value="electronics">电子产品</option>
                                <option value="daily">生活用品</option>
                                <option value="clothing">衣物服饰</option>
                                <option value="beauty">美妆个护</option>
                                <option value="food">食品饮料</option>
                                <option value="other">其他</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="trade_type" value="交易模式" />
                            <select id="trade_type" name="trade_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="sell">现金出售</option>
                                <option value="exchange">以物换物</option>
                                <option value="free">免费赠送</option>
                            </select>
                            <x-input-error :messages="$errors->get('trade_type')" class="mt-2" />
                        </div>

                        <div id="price_field">
                            <x-input-label for="price" value="价格 (元)" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div id="expected_item_field" class="hidden">
                            <x-input-label for="expected_item" value="期望交换物品" />
                            <x-text-input id="expected_item" name="expected_item" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('expected_item')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="photos" value="物品图片 (最多5张)" />
                            <input id="photos" name="photos[]" type="file" multiple accept="image/jpeg,image/png" class="mt-1 block w-full" />
                            <p class="text-xs text-gray-500 mt-1">支持JPG、PNG格式，单张最大2MB</p>
                            <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>发布物品</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // 交易模式切换逻辑
        document.getElementById('trade_type').addEventListener('change', function() {
            const tradeType = this.value;
            const priceField = document.getElementById('price_field');
            const expectedItemField = document.getElementById('expected_item_field');

            if (tradeType === 'sell') {
                priceField.classList.remove('hidden');
                expectedItemField.classList.add('hidden');
            } else if (tradeType === 'exchange') {
                priceField.classList.add('hidden');
                expectedItemField.classList.remove('hidden');
            } else { // free
                priceField.classList.add('hidden');
                expectedItemField.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>