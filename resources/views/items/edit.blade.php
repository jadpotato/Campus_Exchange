<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            编辑物品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- 标准Laravel原生表单，文件上传、提交、CSRF全部标配 --}}
                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data" id="editForm">
                        @csrf
                        @method('PUT')

                        {{-- 1.物品名称 --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">物品名称 <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- 2.物品分类 --}}
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">物品分类 <span class="text-red-500">*</span></label>
                            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">请选择分类</option>
                                <option value="textbook" {{ old('category', $item->category) == 'textbook' ? 'selected' : '' }}>教材书籍</option>
                                <option value="electronics" {{ old('category', $item->category) == 'electronics' ? 'selected' : '' }}>电子产品</option>
                                <option value="daily" {{ old('category', $item->category) == 'daily' ? 'selected' : '' }}>生活用品</option>
                                <option value="clothing" {{ old('category', $item->category) == 'clothing' ? 'selected' : '' }}>衣物服饰</option>
                                <option value="beauty" {{ old('category', $item->category) == 'beauty' ? 'selected' : '' }}>美妆个护</option>
                                <option value="food" {{ old('category', $item->category) == 'food' ? 'selected' : '' }}>食品饮料</option>
                                <option value="other" {{ old('category', $item->category) == 'other' ? 'selected' : '' }}>其他</option>
                            </select>
                        </div>

                        {{-- 3.交易模式 --}}
                        <div class="mb-4">
                            <label for="trade_type" class="block text-sm font-medium text-gray-700">交易模式 <span class="text-red-500">*</span></label>
                            <select id="trade_type" name="trade_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">请选择交易模式</option>
                                <option value="sell" {{ old('trade_type', $item->trade_type) == 'sell' ? 'selected' : '' }}>现金出售</option>
                                <option value="exchange" {{ old('trade_type', $item->trade_type) == 'exchange' ? 'selected' : '' }}>以物换物</option>
                                <option value="free" {{ old('trade_type', $item->trade_type) == 'free' ? 'selected' : '' }}>免费赠送</option>
                            </select>
                        </div>

                        {{-- 4.价格（仅现金出售显示） --}}
                        <div class="mb-4" id="price_box">
                            <label for="price" class="block text-sm font-medium text-gray-700">价格（元）</label>
                            <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $item->price) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- 5.期望交换物品（仅以物换物显示） --}}
                        <div class="mb-4" id="desired_box">
                            <label for="desired_item" class="block text-sm font-medium text-gray-700">期望交换物品</label>
                            <input type="text" id="desired_item" name="desired_item" value="{{ old('desired_item', $item->desired_item) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- 6.物品描述 --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">物品描述 <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $item->description) }}</textarea>
                        </div>

                        {{-- 7.图片上传按钮【重点】 --}}
                        <div class="mb-4">
                            <label for="photos" class="block text-sm font-medium text-gray-700">上传图片</label>
                            <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="mt-1 block w-full">
                            <p class="text-xs text-gray-500 mt-1">可多选图片，新图片会追加</p>
                        </div>

                        {{-- 提交按钮 --}}
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                更新物品
                            </button>
                        </div>
                    </form>

                    {{-- 已有图片预览 --}}
                    @if($item->photos && count($item->photos) > 0)
                    <div class="mt-8">
                        <label class="block text-sm font-medium mb-2">已上传图片</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($item->photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}" alt="图片" class="h-20 object-cover rounded">
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 原生JS：联动显隐 + 前端校验（和登录注册逻辑一致） --}}
    <script>
        const tradeType = document.getElementById('trade_type');
        const priceBox = document.getElementById('price_box');
        const desiredBox = document.getElementById('desired_box');
        const form = document.getElementById('editForm');

        // 联动显隐规则
        function toggleField() {
            let val = tradeType.value;
            // 现金出售：显示价格，隐藏交换物品
            if (val === 'sell') {
                priceBox.style.display = 'block';
                desiredBox.style.display = 'none';
            }
            // 以物换物：隐藏价格，显示交换物品
            else if (val === 'exchange') {
                priceBox.style.display = 'none';
                desiredBox.style.display = 'block';
            }
            // 免费赠送：全部隐藏
            else {
                priceBox.style.display = 'none';
                desiredBox.style.display = 'none';
            }
        }

        // 页面加载 + 切换下拉 触发显隐
        window.onload = toggleField;
        tradeType.addEventListener('change', toggleField);

        // 前端提交校验
        form.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const category = document.getElementById('category').value;
            const desc = document.getElementById('description').value.trim();

            if (!title) {
                alert('请填写物品名称');
                e.preventDefault();
                return;
            }
            if (!category) {
                alert('请选择物品分类');
                e.preventDefault();
                return;
            }
            if (!tradeType.value) {
                alert('请选择交易模式');
                e.preventDefault();
                return;
            }
            if (!desc) {
                alert('请填写物品描述');
                e.preventDefault();
                return;
            }
        });
    </script>
</x-app-layout>