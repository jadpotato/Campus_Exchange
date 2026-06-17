<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            物品市场
        </h2>
    </x-slot>

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div id="filter-form"></div>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden relative border border-gray-100">
                <div id="grid_container" style="height: 500px; width: 100%;"></div>
            </div>
            
            <div class="mt-4 text-gray-400 text-xs flex justify-between items-center px-2">
                <span id="data_stats">共 0 条物品数据</span>
                <button id="refreshBtn" class="text-gray-500 hover:text-indigo-600 flex items-center transition-colors text-xs border border-gray-200 hover:border-indigo-200 px-2.5 py-1 rounded bg-white shadow-sm cursor-pointer">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.253 8H18"></path></svg>
                    刷新数据
                </button>
            </div>
        </div>
    </div>

    <script>
        var grid = null;
        var filterForm = null;
        let debounceTimer = null;

        var pageState = { page: 1, pageSize: 20, total: 0 };
        
        // 核心修改：映射表对齐，直接支持后端传来的纯中文匹配
        const categoryMap = {
            '教材书籍': '教材书籍', '电子产品': '电子产品', '生活用品': '生活用品',
            '衣物服饰': '衣物服饰', '美妆个护': '美妆个护', '食品饮料': '食品饮料', '其他': '其他'
        };
        const tradeTypeMap = { '现金出售': '现金出售', '以物换物': '以物换物', '免费赠送': '免费赠送' };

        const DEFAULT_IMG = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 24 24' fill='none' stroke='%23cbcbcb' stroke-width='1.5'><rect width='18' height='18' x='3' y='3' rx='2'/><circle cx='8.5' cy='8.5' r='1.5'/><path d='M21 15l-5-5L5 21'/></svg>";

        document.addEventListener('DOMContentLoaded', function() {
            function startInit() {
                if (typeof dhx === 'undefined' || !window.appReady) {
                    setTimeout(startInit, 50);
                    return;
                }
                try {
                    initFilterForm();
                    initGrid();
                    
                    const btn = document.getElementById('refreshBtn');
                    if (btn) {
                        btn.addEventListener('click', function() {
                            loadGridData(true);
                        });
                    }
                    restoreFilterState();
                } catch (e) {
                    console.error('DHTMLX 初始化失败:', e);
                }
            }
            startInit();
        });

        // ========== 筛选表单初始化 ==========
        function initFilterForm() {
            filterForm = new dhx.Form("filter-form", {
                padding: 20,
                rows: [
                    {
                        type: "multiselection",
                        columns: 4,
                        gap: 16,
                        rows: [
                            {
                                type: "input",
                                label: "关键词",
                                name: "keyword",
                                labelPosition: "top",
                                placeholder: "搜索物品名称/描述",
                                required: true, 
                                errorMessage: "请输入关键词后再搜索",
                                validation: function(value) { return value && value.trim().length > 0; }
                            },
                            {
                                type: "combo",
                                label: "分类",
                                name: "category",
                                labelPosition: "top",
                                required: false,
                                placeholder: "选择分类...",
                                options: [] 
                            },
                            {
                                type: "combo",
                                label: "交易模式",
                                name: "trade_type",
                                labelPosition: "top",
                                required: false,
                                placeholder: "选择模式...",
                                options: [] 
                            },
                            {
                                type: "multiselection",
                                columns: 2,
                                gap: 8,
                                rows: [
                                    { type: "input", label: "最低价格", name: "min_price", labelPosition: "top", inputType: "number", min: 0, placeholder: "¥0.00" },
                                    { type: "input", label: "最高价格", name: "max_price", labelPosition: "top", inputType: "number", min: 0, placeholder: "不限" }
                                ]
                            }
                        ]
                    },
                    {
                        type: "multiselection",
                        columns: 1,
                        rows: [
                            {
                                type: "multiselection",
                                columns: 2,
                                gap: 12,
                                width: 240,
                                align: "end",
                                rows: [
                                    { type: "button", text: "搜索", view: "primary", submit: true, fullWidth: true, size: "medium" },
                                    { type: "button", text: "重置", view: "secondary", name: "resetBtn", fullWidth: true, size: "medium" }
                                ]
                            }
                        ]
                    }
                ]
            });

            // 🚀 核心修改：将 value 和 content 全部调整为之前定义的中文命名
            try {
                const categoryCombo = filterForm.getItem("category").getWidget();
                if (categoryCombo && categoryCombo.data) {
                    categoryCombo.data.parse([
                        { value: "", content: "全部分类" },
                        { value: "教材书籍", content: "教材书籍" },
                        { value: "电子产品", content: "电子产品" },
                        { value: "生活用品", content: "生活用品" },
                        { value: "衣物服饰", content: "衣物服饰" },
                        { value: "美妆个护", content: "美妆个护" },
                        { value: "食品饮料", content: "食品饮料" },
                        { value: "其他", content: "其他" }
                    ]);
                }

                const tradeCombo = filterForm.getItem("trade_type").getWidget();
                if (tradeCombo && tradeCombo.data) {
                    tradeCombo.data.parse([
                        { value: "", content: "全部模式" },
                        { value: "现金出售", content: "现金出售" },
                        { value: "以物换物", content: "以物换物" },
                        { value: "免费赠送", content: "免费赠送" }
                    ]);
                }
            } catch (comboErr) {
                console.error("下拉框选项注入失败:", comboErr);
            }

            filterForm.events.on("submit", function() {
                if (!filterForm.validate()) { return; }
                if (!validatePriceRange()) {
                    showToast('最高价不能低于最低价', 'error');
                    return;
                }
                pageState.page = 1;
                saveFilterState();
                loadGridData();
            });

            filterForm.getItem("resetBtn").events.on("click", function() {
                filterForm.clear();
                pageState.page = 1;
                localStorage.removeItem('item_filter_state');
                loadGridData();
            });

            filterForm.getItem("keyword").events.on("input", function(value) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (value.length >= 2 || value.length === 0) {
                        pageState.page = 1;
                        loadGridData();
                    }
                }, 400);
            });
        }

        // ========== 表格初始化 ==========
        function initGrid() {
            grid = new dhx.Grid("grid_container", {
                columns: [
                    { 
                        id: "thumbnail", header: [{ text: "缩略图" }], width: 100, sortable: false, htmlEnable: true,
                        template: function(item) {
                            const hasPhotos = item && item.photos && Array.isArray(item.photos) && item.photos.length > 0;
                            const imgSrc = hasPhotos ? '/storage/' + item.photos[0] : DEFAULT_IMG;
                            return '<div class="flex items-center justify-center h-12 bg-gray-50 rounded overflow-hidden"><img src="' + imgSrc + '" class="max-w-full max-h-full object-cover" onerror="this.src=\'' + DEFAULT_IMG + '\'"></div>';
                        }
                    },
                    { 
                        id: "title", header: [{ text: "物品名称" }], width: 280, htmlEnable: true, sortable: true,
                        template: function(item) {
                            return '<a href="/items/' + (item.id || '#') + '" class="text-indigo-600 hover:underline font-medium">' + escapeHtml(item.title || '未命名物品') + '</a>';
                        }
                    },
                    { id: "category", header: [{ text: "分类" }], width: 120, sortable: true, template: function(item) { return categoryMap[item.category] || item.category || '未分类'; } },
                    { 
                        id: "price", header: [{ text: "价格" }], width: 130, sortable: true, align: "right",
                        template: function(item) {
                            // 兼容中文判断
                            if (item.trade_type === '现金出售' || item.trade_type === 'sell') return '<span class="font-semibold text-green-600">¥' + parseFloat(item.price || 0).toFixed(2) + '</span>';
                            if (item.trade_type === '以物换物' || item.trade_type === 'exchange') return '<span class="text-blue-600">以物换物</span>';
                            if (item.trade_type === '免费赠送' || item.trade_type === 'free') return '<span class="text-red-500 font-medium">免费</span>';
                            return '-';
                        }
                    },
                    { id: "trade_type", header: [{ text: "交易模式" }], width: 120, sortable: true, template: function(item) { return tradeTypeMap[item.trade_type] || item.trade_type || '-'; } },
                    { id: "view_count", header: [{ text: "浏览" }], width: 90, sortable: true, align: "center", template: function(item) { return item.view_count || 0; } },
                    { id: "created_at", header: [{ text: "发布时间" }], width: 160, sortable: true, template: function(item) { return formatDateTime(item.created_at); } }
                ],
                autoWidth: true,
                headerRowHeight: 42,
                rowHeight: 64,
                selection: "row",
                editable: false,
                theme: "light",
                emptyText: "<div class='text-center py-16'><p class='text-gray-400 text-sm mb-1'>暂无符合条件的物品数据</p></div>"
            });

            grid.events.on("rowClick", function(id) {
                const item = grid.data.getItem(id);
                if (item && item.id) window.location.href = '/items/' + item.id;
            });

            loadGridData();
        }

        // ========== 数据加载 ==========
        function loadGridData(forceRefresh) {
            if (!grid || !filterForm) return;
            
            const sort = grid.getSortingState ? grid.getSortingState() : {};
            const params = new URLSearchParams(filterForm.getValue());
            
            params.append('page', pageState.page);
            params.append('per_page', pageState.pageSize);
            if (sort && sort.direction) {
                params.append('sort_by', sort.by || 'created_at');
                params.append('sort_dir', sort.direction);
            }
            if (forceRefresh) params.append('_t', Date.now());

            if (forceRefresh) showToast('正在同步服务器最新数据...', 'info');

            fetch('/api/items?' + params.toString(), {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(res => {
                let incomingData = res.data || [];
                grid.data.parse(incomingData);
                pageState.total = (res.meta && res.meta.total) ? res.meta.total : incomingData.length;
                document.getElementById('data_stats').textContent = '共 ' + pageState.total + ' 条物品数据';
                
                if (forceRefresh) showToast('数据刷新成功！', 'success');
            })
            .catch(() => {
                showToast('无法连接到后端接口，请检查网络服务', 'error');
            });
        }

        // ========== 工具函数 ==========
        function validatePriceRange() {
            const values = filterForm.getValue();
            const min = parseFloat(values.min_price) || 0;
            const max = parseFloat(values.max_price) || Infinity;
            return !(min > max && max !== Infinity);
        }

        function saveFilterState() { localStorage.setItem('item_filter_state', JSON.stringify(filterForm.getValue())); }
        function restoreFilterState() {
            try {
                const state = JSON.parse(localStorage.getItem('item_filter_state'));
                if (state) filterForm.setValue(state);
            } catch (e) {}
        }

        // 格式化时间
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
        }

        // 安全转义 HTML
        function escapeHtml(text) {
            if (!text) return '';
            return text.toString().replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m]));
        }

        // 信息提示框
        function showToast(message, type) {
            document.querySelectorAll('.custom-toast').forEach(el => el.remove());
            const toast = document.createElement('div');
            let bgColor = 'bg-gray-800';
            if (type === 'error') bgColor = 'bg-red-500';
            if (type === 'success') bgColor = 'bg-green-600';
            
            toast.className = 'custom-toast fixed bottom-4 right-4 px-4 py-2 rounded shadow-md z-50 text-xs text-white ' + bgColor;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 2500);
        }
    </script>

    <style>
        #grid_container { font-family: ui-sans-serif, system-ui, sans-serif !important; }
        .dhx_grid {
            --dhx-color-primary: #4f46e5 !important;
            --dhx-color-border: #f3f4f6 !important;
            --dhx-color-bg: #ffffff !important;
        }
        .dhx_grid-header-cell {
            background-color: #f9fafb !important;
            color: #374151 !important;
            font-weight: 600 !important;
            border-bottom: 2px solid #e5e7eb !important;
        }
        .dhx_grid-cell { color: #4b5563 !important; border-bottom: 1px solid #f3f4f6 !important; }
        .dhx_grid-row:hover .dhx_grid-cell { background-color: #f8fafc !important; }
        
        .dhx_form-element__error {
            color: #ef4444 !important;
            font-size: 0.75rem !important;
            margin-top: 4px !important;
            display: block !important;
        }
    </style>
</x-app-layout>