<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            物品市场
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 顶部筛选栏：DHTMLX Form -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                <div id="filter-form"></div>
            </div>

            <!-- DHTMLX Grid 表格 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden relative">
                <div id="grid_container" style="height: 650px; width: 100%;"></div>
                
                <!-- 加载状态 -->
                <div id="grid_loading" class="hidden absolute inset-0 bg-white/90 dark:bg-gray-800/90 flex items-center justify-center z-10 backdrop-blur-sm">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-12 w-12 text-indigo-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300 text-lg">正在加载数据...</span>
                    </div>
                </div>
                
                <!-- 空数据提示 -->
                <div id="empty_state" class="hidden absolute inset-0 flex flex-col items-center justify-center h-650px bg-gray-50 dark:bg-gray-800/50">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl text-gray-500 dark:text-gray-400 mb-2">暂无符合条件的物品</h3>
                    <p class="text-gray-400 dark:text-gray-500 mb-4">尝试调整筛选条件或发布新的物品</p>
                    <a href="/items/create" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                        发布新物品
                    </a>
                </div>
            </div>
            
            <!-- 数据统计信息 -->
            <div class="mt-4 text-gray-600 dark:text-gray-400 text-sm flex justify-between items-center">
                <span id="data_stats">共 0 条物品数据</span>
                <span>
                    <button id="refreshBtn" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        刷新数据
                    </button>
                </span>
            </div>
        </div>
    </div>

    <script>
        var grid = null;
        var filterForm = null;
        const loadingEl = document.getElementById('grid_loading');
        const emptyStateEl = document.getElementById('empty_state');
        const dataStatsEl = document.getElementById('data_stats');
        let debounceTimer = null;

        // 手动维护分页状态
        var pageState = {
            page: 1,
            pageSize: 20,
            total: 0
        };
        
        // 分类和交易模式映射
        const categoryMap = {
            'textbook': '教材书籍',
            'electronics': '电子产品',
            'daily': '生活用品',
            'clothing': '衣物服饰',
            'beauty': '美妆个护',
            'food': '食品饮料',
            'other': '其他'
        };
        
        const tradeTypeMap = {
            'sell': '现金出售',
            'exchange': '以物换物',
            'free': '免费赠送'
        };

        document.addEventListener('DOMContentLoaded', function() {
            // 等待全局DHTMLX就绪
            function startInit() {
                if (typeof dhx === 'undefined' || !window.appReady) {
                    setTimeout(startInit, 50);
                    return;
                }
                try {
                    initFilterForm();
                    initGrid();
                    document.getElementById('refreshBtn').addEventListener('click', function() {
                        loadGridData(true);
                    });
                    watchDarkMode();
                    restoreFilterState();
                } catch (e) {
                    console.error('初始化失败:', e);
                    hideLoading();
                }
            }
            startInit();
        });

        // ========== 筛选表单初始化（修复下拉框字段映射） ==========
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
                                placeholder: "搜索物品名称/描述"
                            },
                            {
                                type: "combo",
                                label: "分类",
                                name: "category",
                                labelPosition: "top",
                                value: "",
                                // 关键修复：手动指定值字段和显示字段
                                valueField: "id",
                                textField: "text",
                                options: [
                                    { id: "", text: "全部分类" },
                                    { id: "textbook", text: "教材书籍" },
                                    { id: "electronics", text: "电子产品" },
                                    { id: "daily", text: "生活用品" },
                                    { id: "clothing", text: "衣物服饰" },
                                    { id: "beauty", text: "美妆个护" },
                                    { id: "food", text: "食品饮料" },
                                    { id: "other", text: "其他" }
                                ]
                            },
                            {
                                type: "combo",
                                label: "交易模式",
                                name: "trade_type",
                                labelPosition: "top",
                                value: "",
                                valueField: "id",
                                textField: "text",
                                options: [
                                    { id: "", text: "全部模式" },
                                    { id: "sell", text: "现金出售" },
                                    { id: "exchange", text: "以物换物" },
                                    { id: "free", text: "免费赠送" }
                                ]
                            },
                            {
                                type: "multiselection",
                                columns: 2,
                                gap: 8,
                                rows: [
                                    {
                                        type: "input",
                                        label: "最低价格",
                                        name: "min_price",
                                        labelPosition: "top",
                                        inputType: "number",
                                        min: 0,
                                        step: 0.01,
                                        placeholder: "¥0.00"
                                    },
                                    {
                                        type: "input",
                                        label: "最高价格",
                                        name: "max_price",
                                        labelPosition: "top",
                                        inputType: "number",
                                        min: 0,
                                        step: 0.01,
                                        placeholder: "不限"
                                    }
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
                                    {
                                        type: "button",
                                        text: "搜索",
                                        view: "primary",
                                        submit: true,
                                        fullWidth: true,
                                        size: "medium"
                                    },
                                    {
                                        type: "button",
                                        text: "重置",
                                        view: "secondary",
                                        name: "resetBtn",
                                        fullWidth: true,
                                        size: "medium"
                                    }
                                ]
                            }
                        ]
                    }
                ]
            });

            // 搜索提交
            filterForm.events.on("submit", function() {
                if (!validatePriceRange()) {
                    showToast('价格范围输入错误，请检查', 'error');
                    return;
                }
                pageState.page = 1;
                saveFilterState();
                loadGridData();
            });

            // 重置按钮
            filterForm.getItem("resetBtn").events.on("click", function() {
                filterForm.clear();
                pageState.page = 1;
                localStorage.removeItem('item_filter_state');
                loadGridData();
            });

            // 关键词输入防抖
            filterForm.getItem("keyword").events.on("input", function(value) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (value.length >= 2 || value.length === 0) {
                        pageState.page = 1;
                        saveFilterState();
                        loadGridData();
                    }
                }, 500);
            });

            // 价格实时验证
            filterForm.getItem("min_price").events.on("change", validatePriceRange);
            filterForm.getItem("max_price").events.on("change", validatePriceRange);
        }

        // ========== 表格初始化（移除无效refresh调用） ==========
        function initGrid() {
            grid = new dhx.Grid("grid_container", {
                columns: [
                    { 
                        id: "thumbnail", 
                        header: [{ text: "缩略图" }], 
                        width: 120,
                        sortable: false,
                        htmlEnable: true,
                        template: function(item) {
                            const imgSrc = item.photos && item.photos.length > 0 
                                ? '/storage/' + item.photos[0]
                                : '/images/default-item.png';
                            return '<div class="flex items-center justify-center h-20 bg-gray-50 dark:bg-gray-700/50 rounded-md overflow-hidden">' +
                                '<img src="' + imgSrc + '" alt="' + escapeHtml(item.title || '物品图片') +
                                '" class="max-w-full max-h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.src=\'/images/default-item.png\'">' +
                                '</div>';
                        }
                    },
                    { 
                        id: "title", 
                        header: [{ text: "物品名称" }], 
                        width: 300,
                        htmlEnable: true,
                        sortable: true,
                        template: function(item) {
                            return '<a href="/items/' + item.id + '" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium transition-colors">' +
                                escapeHtml(item.title || '未命名物品') + '</a>';
                        }
                    },
                    { 
                        id: "category", 
                        header: [{ text: "分类" }], 
                        width: 110,
                        sortable: true,
                        template: function(item) {
                            return categoryMap[item.category] || item.category || '未分类';
                        }
                    },
                    { 
                        id: "price", 
                        header: [{ text: "价格" }], 
                        width: 120,
                        sortable: true,
                        align: "right",
                        template: function(item) {
                            switch(item.trade_type) {
                                case 'sell':
                                    return '<span class="font-semibold text-green-600 dark:text-green-400">¥' + parseFloat(item.price).toFixed(2) + '</span>';
                                case 'exchange':
                                    return '<span class="text-blue-600 dark:text-blue-400">以物换物</span>';
                                case 'free':
                                    return '<span class="text-red-600 dark:text-red-400 font-medium">免费</span>';
                                default:
                                    return '-';
                            }
                        }
                    },
                    { 
                        id: "trade_type", 
                        header: [{ text: "交易模式" }], 
                        width: 120,
                        sortable: true,
                        template: function(item) {
                            return tradeTypeMap[item.trade_type] || item.trade_type || '-';
                        }
                    },
                    { 
                        id: "view_count", 
                        header: [{ text: "浏览" }], 
                        width: 80,
                        sortable: true,
                        align: "center",
                        template: function(item) {
                            return item.view_count || 0;
                        }
                    },
                    { 
                        id: "created_at", 
                        header: [{ text: "发布时间" }], 
                        width: 180,
                        sortable: true,
                        sort: "desc",
                        template: function(item) {
                            return formatDateTime(item.created_at);
                        }
                    },
                    { 
                        id: "actions", 
                        header: [{ text: "操作" }], 
                        width: 120,
                        sortable: false,
                        htmlEnable: true,
                        align: "center",
                        template: function(item) {
                            return '<a href="/items/' + item.id + '" class="inline-block px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors text-sm">查看详情</a>';
                        }
                    }
                ],
                sortable: true,
                autoWidth: true,
                autoHeight: false,
                headerRowHeight: 50,
                rowHeight: 85,
                resize: true,
                selection: "row",
                multiselection: false,
                navigation: true,
                editable: false,
                readonly: true,
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                emptyText: ""
            });

            // 行点击跳转
            grid.events.on("rowClick", function(id, e) {
                if (!e.target.closest('a') && !e.target.closest('button')) {
                    const item = grid.data.getItem(id);
                    if (item && item.id) {
                        window.location.href = '/items/' + item.id;
                    }
                }
            });

            // 排序防抖
            grid.events.on("sort", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    loadGridData();
                }, 300);
            });

            // 数据加载完成回调
            grid.events.on("dataParse", function() {
                updateEmptyState();
                updateDataStats();
            });

            // 暴露全局实例，供侧边栏筛选调用
            window.gridInstance = grid;

            // 初始加载
            loadGridData();
        }

        // ========== 数据加载 ==========
        function loadGridData(forceRefresh) {
            if (typeof forceRefresh === 'undefined') forceRefresh = false;
            if (!grid || !filterForm) return;
            
            showLoading();
            
            const sort = grid.getSortingState ? grid.getSortingState() : {};
            const formValues = filterForm.getValue();
            const params = new URLSearchParams(formValues);
            
            // 手动拼接分页参数
            params.append('page', pageState.page);
            params.append('per_page', pageState.pageSize);
            
            // 排序参数
            if (sort && sort.direction) {
                params.append('sort_by', sort.by || 'created_at');
                params.append('sort_dir', sort.direction);
            }
            
            if (forceRefresh) {
                params.append('_t', Date.now());
            }

            fetch('/api/items?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function(res) {
                if (!res.ok) throw new Error('HTTP错误：' + res.status);
                return res.json();
            })
            .then(function(res) {
                grid.data.parse(res.data || []);
                // 更新总数
                if (res.meta && res.meta.total) {
                    pageState.total = res.meta.total;
                }
                hideLoading();
            })
            .catch(function(error) {
                hideLoading();
                console.error('数据加载失败:', error);
                showToast('数据加载失败，点击重试', 'error', true);
                updateEmptyState();
            });
        }

        // ========== 工具函数 ==========
        function watchDarkMode() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        try {
                            grid.setTheme(isDark ? 'dark' : 'light');
                        } catch (e) {
                            console.warn('切换主题失败:', e);
                        }
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
            
            // 窗口缩放时重绘（使用兼容方法）
            window.addEventListener('resize', function() {
                setTimeout(function() {
                    if (grid && grid.paint) grid.paint();
                }, 100);
            });
        }

        function validatePriceRange() {
            const values = filterForm.getValue();
            const minPrice = parseFloat(values.min_price) || 0;
            const maxPrice = parseFloat(values.max_price) || Infinity;
            return !(minPrice > maxPrice && maxPrice !== Infinity);
        }

        function saveFilterState() {
            const state = filterForm.getValue();
            localStorage.setItem('item_filter_state', JSON.stringify(state));
        }

        function restoreFilterState() {
            try {
                const state = JSON.parse(localStorage.getItem('item_filter_state'));
                if (!state) return;
                filterForm.setValue(state);
            } catch (e) {
                console.warn('恢复筛选条件失败:', e);
            }
        }

        function updateEmptyState() {
            const hasData = grid.data.getLength() > 0;
            emptyStateEl.classList.toggle('hidden', hasData);
            if (!hasData) {
                emptyStateEl.style.zIndex = '5';
            } else {
                emptyStateEl.style.zIndex = '-1';
            }
        }

        function updateDataStats() {
            dataStatsEl.textContent = '共 ' + pageState.total + ' 条物品数据';
        }

        function showLoading() {
            loadingEl.classList.remove('hidden');
            if (filterForm) filterForm.disable();
        }

        function hideLoading() {
            loadingEl.classList.add('hidden');
            if (filterForm) filterForm.enable();
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            
            try {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (days === 0) {
                    return '今天 ' + String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                } else if (days === 1) {
                    return '昨天 ' + String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                } else if (days < 7) {
                    return days + '天前';
                } else {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                }
            } catch (e) {
                return dateString.substring(0, 16).replace('T', ' ');
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function showToast(message, type, allowRetry) {
            if (typeof type === 'undefined') type = 'info';
            if (typeof allowRetry === 'undefined') allowRetry = false;
            
            document.querySelectorAll('.custom-toast').forEach(function(el) { el.remove(); });
            
            const toast = document.createElement('div');
            toast.className = 'custom-toast fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-0 ' +
                (type === 'error' ? 'bg-red-500 text-white' : 'bg-gray-800 text-white');
            
            var iconPath = type === 'error'
                ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
                : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

            var toastContent = '<div class="flex items-center">' +
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + iconPath + '"></path>' +
                '</svg><span>' + message + '</span></div>';
            
            if (allowRetry) {
                toastContent += '<button class="mt-2 px-3 py-1 bg-white/20 rounded text-sm hover:bg-white/30 transition-colors w-full mt-2">点击重试</button>';
            }
            
            toast.innerHTML = toastContent;
            document.body.appendChild(toast);
            
            if (allowRetry) {
                toast.querySelector('button').addEventListener('click', function() {
                    loadGridData(true);
                    toast.remove();
                });
            }
            
            setTimeout(function() {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(function() { toast.remove(); }, 300);
            }, allowRetry ? 8000 : 3000);
        }
    </script>

    <style>
        #grid_container {
            position: relative;
            font-family: inherit !important;
        }
        
        [data-dhx-theme="dark"] {
            --dhx-color-primary: #4f46e5 !important;
            --dhx-color-border: #374151 !important;
            --dhx-color-bg: #1f2937 !important;
            --dhx-color-list-item-hover: #374151 !important;
        }
        
        [data-dhx-theme="dark"] .dhx_grid-content,
        [data-dhx-theme="light"] .dhx_grid-content {
            border: none !important;
        }
        
        [data-dhx-theme="dark"] .dhx_grid-header-cell,
        [data-dhx-theme="dark"] .dhx_grid-cell {
            border-color: #374151 !important;
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
        }
        
        [data-dhx-theme="light"] .dhx_grid-header-cell,
        [data-dhx-theme="light"] .dhx_grid-cell {
            border-color: #e5e7eb !important;
            background-color: #ffffff !important;
        }
        
        .dhx_grid-header-cell {
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0 12px !important;
        }
        
        .dhx_grid-cell {
            padding: 10px 12px !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }
        
        .dhx_grid-row {
            transition: background-color 0.2s ease !important;
        }
        
        .dhx_grid-row:hover .dhx_grid-cell {
            background-color: rgba(99, 102, 241, 0.05) !important;
        }
        
        [data-dhx-theme="dark"] .dhx_grid-row:hover .dhx_grid-cell {
            background-color: rgba(99, 102, 241, 0.1) !important;
        }
        
        #empty_state {
            height: 650px;
        }

        /* 表单深色模式适配 */
        .dark .dhx_form {
            --dhx-color-bg: #1f2937;
            --dhx-color-text: #e5e7eb;
            --dhx-color-border: #374151;
            --dhx-color-input-bg: #374151;
        }
    </style>
</x-app-layout>