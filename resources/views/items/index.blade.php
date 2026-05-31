<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            物品市场
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 顶部筛选栏 - 优化布局和交互 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6 transition-all duration-300">
                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <x-input-label for="keyword" value="关键词" />
                        <x-text-input 
                            id="keyword" 
                            name="keyword" 
                            type="text" 
                            class="mt-1 block w-full" 
                            placeholder="搜索物品名称/描述"
                            autocomplete="off"
                        />
                    </div>
                    
                    <div>
                        <x-input-label for="category" value="分类" />
                        <select id="category" name="category" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">全部分类</option>
                            <option value="textbook">教材书籍</option>
                            <option value="electronics">电子产品</option>
                            <option value="daily">生活用品</option>
                            <option value="clothing">衣物服饰</option>
                            <option value="beauty">美妆个护</option>
                            <option value="food">食品饮料</option>
                            <option value="other">其他</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="trade_type" value="交易模式" />
                        <select id="trade_type" name="trade_type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">全部模式</option>
                            <option value="sell">现金出售</option>
                            <option value="exchange">以物换物</option>
                            <option value="free">免费赠送</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-input-label for="price_range" value="价格范围" />
                        <div class="flex gap-2 mt-1">
                            <x-text-input 
                                id="min_price" 
                                name="min_price" 
                                type="number" 
                                min="0" 
                                step="0.01"
                                class="block w-full" 
                                placeholder="最低"
                                oninput="validatePriceRange()"
                            />
                            <span class="self-center text-gray-500">-</span>
                            <x-text-input 
                                id="max_price" 
                                name="max_price" 
                                type="number" 
                                min="0" 
                                step="0.01"
                                class="block w-full" 
                                placeholder="最高"
                                oninput="validatePriceRange()"
                            />
                            <!-- 价格验证提示 -->
                            <div id="priceError" class="hidden absolute text-xs text-red-500 mt-6 -ml-20">
                                最高价格不能低于最低价格
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <x-primary-button type="submit" class="flex-1 transition-all hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            搜索
                        </x-primary-button>
                        <x-secondary-button type="button" id="resetBtn" class="flex-1 transition-all hover:scale-105">重置</x-secondary-button>
                    </div>
                </form>
            </div>

            <!-- DHTMLX Grid 容器 - 优化样式和加载体验 -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden transition-all duration-300">
                <div id="grid_container" style="height: 650px; width: 100%;"></div>
                <!-- 加载状态 - 优化动画和样式 -->
                <div id="grid_loading" class="hidden absolute inset-0 bg-white/90 dark:bg-gray-800/90 flex items-center justify-center z-10 backdrop-blur-sm">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-12 w-12 text-indigo-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300 text-lg">正在加载数据...</span>
                    </div>
                </div>
                
                <!-- 空数据提示增强 -->
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

    <!-- 引入 DHTMLX Grid CDN 资源（稳定版） -->
    <script src="https://cdn.dhtmlx.com/suite/7.4.13/suite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/suite/7.4.13/suite.min.css">

    <script>
        // 全局变量
        let grid;
        const loadingEl = document.getElementById('grid_loading');
        const emptyStateEl = document.getElementById('empty_state');
        const dataStatsEl = document.getElementById('data_stats');
        let debounceTimer = null;
        
        // 分类和交易模式映射（完整映射）
        const categoryMap = {
            'textbook': '教材书籍',
            'electronics': '电子产品',
            'daily': '生活用品',
            'clothing': '衣物服饰',
            'beauty': '美妆个护',
            'food': '食品饮料',
            'other': '其他',
            'books': '书籍',
            'clothes': '衣物',
            'others': '其他'
        };
        
        const tradeTypeMap = {
            'sell': '现金出售',
            'exchange': '以物换物',
            'free': '免费赠送'
        };

        // 页面加载完成初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化Grid
            initGrid();
            
            // 绑定筛选事件
            bindFilterEvents();
            
            // 监听深色模式切换
            watchDarkMode();
            
            // 恢复上次筛选条件
            restoreFilterState();
            
            // 绑定刷新按钮事件
            document.getElementById('refreshBtn').addEventListener('click', function() {
                loadGridData(true);
            });
        });

        /**
         * 初始化 DHTMLX Grid - 全面优化配置
         */
        function initGrid() {
            grid = new dhx.Grid("grid_container", {
                // 列定义 - 优化宽度和交互
                columns: [
                    { 
                        id: "thumbnail", 
                        header: [{ text: "缩略图" }], 
                        width: 120,
                        sortable: false,
                        htmlEnable: true,
                        template: function(item) {
                            const imgSrc = item.photos && item.photos.length > 0 
                                ? `/storage/${item.photos[0]}` 
                                : '/images/default-item.png';
                            // 图片懒加载 + 错误处理
                            return `<div class="flex items-center justify-center h-20 bg-gray-50 dark:bg-gray-700/50 rounded-md overflow-hidden">
                                <img src="${imgSrc}" 
                                     alt="${escapeHtml(item.title || '物品图片')}" 
                                     class="max-w-full max-h-full object-cover hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     onError="this.src='/images/default-item.png'">
                            </div>`;
                        }
                    },
                    { 
                        id: "title", 
                        header: [{ text: "物品名称" }], 
                        width: 300,
                        htmlEnable: true,
                        sortable: true,
                        template: function(item) {
                            return `<a href="/items/${item.id}" 
                                     class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium transition-colors">
                                ${escapeHtml(item.title || '未命名物品')}
                            </a>`;
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
                                    return `<span class="font-semibold text-green-600 dark:text-green-400">¥${parseFloat(item.price).toFixed(2)}</span>`;
                                case 'exchange':
                                    return `<span class="text-blue-600 dark:text-blue-400">以物换物</span>`;
                                case 'free':
                                    return `<span class="text-red-600 dark:text-red-400 font-medium">免费</span>`;
                                default:
                                    return '-';
                            }
                        }
                    },
                    { 
                        id: "trade_type", 
                        header: [{ text: "交易模式" }], 
                        width: 110,
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
                        sort: "desc", // 默认按发布时间降序
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
                            return `
                                <a href="/items/${item.id}" 
                                   class="inline-block px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors text-sm">
                                    查看详情
                                </a>
                            `;
                        }
                    }
                ],
                
                // 分页配置 - 优化用户体验
                pagination: {
                    limit: 20,
                    limits: [10, 20, 50, 100],
                    page: 1,
                    showInfo: true,
                    infoTemplate: "共 {total} 条，第 {page} / {totalPages} 页",
                    size: "small"
                },
                
                // 基础配置 - 增强交互
                sortable: true,
                autoWidth: true,
                autoHeight: false,
                headerRowHeight: 50,
                rowHeight: 85,
                resize: true,
                selection: "row",
                multiselection: false,
                navigation: true, // 启用键盘导航
                editable: false,
                readonly: true,
                
                // 主题配置 - 精准匹配深色模式
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                
                // 空数据提示（备用）
                emptyText: "",
            });

            // 行点击事件 - 优化交互
            grid.events.on("rowClick", function(id, e) {
                if (!e.target.closest('a') && !e.target.closest('button')) {
                    const item = grid.data.getItem(id);
                    if (item && item.id) {
                        // 平滑跳转
                        window.location.href = `/items/${item.id}`;
                    }
                }
            });

            // 排序事件 - 防抖处理
            grid.events.on("sort", function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    loadGridData();
                }, 300);
            });

            // 分页事件 - 优化加载
            grid.events.on("pageChange", function() {
                loadGridData();
                // 滚动到顶部
                document.getElementById('grid_container').scrollIntoView({ behavior: 'smooth' });
            });

            // 数据加载完成事件
            grid.events.on("dataParse", function() {
                updateEmptyState();
                updateDataStats();
            });

            // 初始加载数据
            loadGridData();
        }

        /**
         * 绑定筛选表单事件 - 增强交互体验
         */
        function bindFilterEvents() {
            const form = document.getElementById('filterForm');
            
            // 表单提交 - 防抖 + 验证
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // 价格范围验证
                if (!validatePriceRange()) {
                    showToast('价格范围输入错误，请检查', 'error');
                    return;
                }
                
                grid.pagination.setPage(1);
                // 保存筛选条件
                saveFilterState();
                // 加载数据
                loadGridData();
            });

            // 重置按钮
            document.getElementById('resetBtn').addEventListener('click', function() {
                form.reset();
                grid.pagination.setPage(1);
                // 清除筛选条件
                localStorage.removeItem('item_filter_state');
                // 隐藏价格错误提示
                document.getElementById('priceError').classList.add('hidden');
                // 加载数据
                loadGridData();
            });

            // 关键词输入防抖
            document.getElementById('keyword').addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (this.value.length >= 2 || this.value.length === 0) {
                        grid.pagination.setPage(1);
                        saveFilterState();
                        loadGridData();
                    }
                }, 500);
            });

            // 键盘快捷键：ESC重置，Enter提交
            document.addEventListener('keydown', function(e) {
                // 只在筛选区域生效
                const isFilterFocused = document.activeElement.closest('#filterForm');
                if (!isFilterFocused) return;
                
                if (e.key === 'Escape') {
                    form.reset();
                } else if (e.key === 'Enter' && !e.target.closest('button')) {
                    form.dispatchEvent(new Event('submit'));
                }
            });
        }

        /**
         * 加载表格数据 - 优化错误处理和性能
         * @param {boolean} forceRefresh - 是否强制刷新（忽略缓存）
         */
        function loadGridData(forceRefresh = false) {
            showLoading();
            
            // 获取当前分页和排序信息
            const pagination = grid.pagination.getState();
            const sort = grid.getSortingState();
            
            // 构建请求参数
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            
            // 添加分页参数
            params.append('page', pagination.page);
            params.append('per_page', pagination.limit);
            
            // 添加排序参数
            if (sort.direction) {
                params.append('sort_by', sort.by);
                params.append('sort_dir', sort.direction);
            }
            
            // 添加缓存控制
            if (forceRefresh) {
                params.append('_t', Date.now());
            }

            // 使用fetch加载数据 - 优化错误处理
            fetch(`/api/items?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP错误：${res.status}`);
                    }
                    return res.json();
                })
                .then(res => {
                    // 解析数据
                    grid.data.parse(res.data || []);
                    // 更新分页总数
                    if (res.meta) {
                        grid.pagination.setTotal(res.meta.total || 0);
                    }
                    hideLoading();
                })
                .catch((error) => {
                    hideLoading();
                    console.error('数据加载失败:', error);
                    showToast('数据加载失败，点击重试', 'error', true);
                    // 显示空状态
                    updateEmptyState();
                });
        }

        /**
         * 监听深色模式切换 - 优化兼容性
         */
        function watchDarkMode() {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        // 安全切换主题
                        try {
                            grid.setTheme(isDark ? 'dark' : 'light');
                            // 重新渲染解决样式问题
                            grid.refresh();
                        } catch (e) {
                            console.warn('切换主题失败:', e);
                        }
                    }
                });
            });

            observer.observe(document.documentElement, { attributes: true });
            
            // 窗口大小变化时刷新grid
            window.addEventListener('resize', function() {
                setTimeout(() => grid.refresh(), 100);
            });
        }

        /**
         * 价格范围验证
         * @returns {boolean} 验证结果
         */
        function validatePriceRange() {
            const minPrice = parseFloat(document.getElementById('min_price').value) || 0;
            const maxPrice = parseFloat(document.getElementById('max_price').value) || Infinity;
            const errorEl = document.getElementById('priceError');
            
            if (minPrice > maxPrice && maxPrice !== Infinity) {
                errorEl.classList.remove('hidden');
                return false;
            } else {
                errorEl.classList.add('hidden');
                return true;
            }
        }

        /**
         * 保存筛选条件到本地存储
         */
        function saveFilterState() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const state = {};
            
            for (let [key, value] of formData.entries()) {
                state[key] = value;
            }
            
            localStorage.setItem('item_filter_state', JSON.stringify(state));
        }

        /**
         * 从本地存储恢复筛选条件
         */
        function restoreFilterState() {
            try {
                const state = JSON.parse(localStorage.getItem('item_filter_state'));
                if (!state) return;
                
                const form = document.getElementById('filterForm');
                Object.keys(state).forEach(key => {
                    const element = form.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = state[key];
                    }
                });
            } catch (e) {
                console.warn('恢复筛选条件失败:', e);
            }
        }

        /**
         * 更新空数据状态显示
         */
        function updateEmptyState() {
            const hasData = grid.data.getLength() > 0;
            emptyStateEl.classList.toggle('hidden', hasData);
            // 调整z-index确保显示正确
            if (!hasData) {
                emptyStateEl.style.zIndex = 5;
            } else {
                emptyStateEl.style.zIndex = -1;
            }
        }

        /**
         * 更新数据统计信息
         */
        function updateDataStats() {
            const total = grid.pagination.getState().total || 0;
            dataStatsEl.textContent = `共 ${total} 条物品数据`;
        }

        /**
         * 显示加载状态
         */
        function showLoading() {
            loadingEl.classList.remove('hidden');
            // 禁用筛选表单
            document.querySelectorAll('#filterForm input, #filterForm select, #filterForm button').forEach(el => {
                el.disabled = true;
                el.classList.add('opacity-70');
            });
        }

        /**
         * 隐藏加载状态
         */
        function hideLoading() {
            loadingEl.classList.add('hidden');
            // 启用筛选表单
            document.querySelectorAll('#filterForm input, #filterForm select, #filterForm button').forEach(el => {
                el.disabled = false;
                el.classList.remove('opacity-70');
            });
        }

        /**
         * 格式化日期时间 - 优化显示
         */
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            
            try {
                const date = new Date(dateString);
                // 相对时间显示
                const now = new Date();
                const diff = now - date;
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (days === 0) {
                    return `今天 ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                } else if (days === 1) {
                    return `昨天 ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                } else if (days < 7) {
                    return `${days}天前`;
                } else {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day} ${hours}:${minutes}`;
                }
            } catch (e) {
                return dateString.substring(0, 16).replace('T', ' ');
            }
        }

        /**
         * HTML转义（防止XSS）- 增强安全性
         */
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }

        /**
         * 显示提示消息 - 优化样式和交互
         */
        function showToast(message, type = 'info', allowRetry = false) {
            // 移除现有toast
            document.querySelectorAll('.custom-toast').forEach(el => el.remove());
            
            const toast = document.createElement('div');
            toast.className = `custom-toast fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-0 ${
                type === 'error' 
                    ? 'bg-red-500 text-white' 
                    : 'bg-gray-800 text-white'
            }`;
            
            let toastContent = `<div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'error' 
                            ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' 
                            : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span>${message}</span>
            </div>`;
            
            // 重试按钮
            if (allowRetry) {
                toastContent += `<button class="mt-2 px-3 py-1 bg-white/20 rounded text-sm hover:bg-white/30 transition-colors w-full mt-2">
                    点击重试
                </button>`;
            }
            
            toast.innerHTML = toastContent;
            document.body.appendChild(toast);
            
            // 重试按钮事件
            if (allowRetry) {
                toast.querySelector('button').addEventListener('click', function() {
                    loadGridData(true);
                    toast.remove();
                });
            }
            
            // 自动关闭
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, allowRetry ? 8000 : 3000);
        }
    </script>

    <!-- 修复DHTMLX与Tailwind的样式冲突 - 全面优化 -->
    <style>
        /* Grid容器基础样式 */
        #grid_container {
            position: relative;
            font-family: inherit !important;
        }
        
        /* 深色模式样式修复 */
        [data-dhx-theme="dark"] {
            --dhx-color-primary: #4f46e5 !important;
            --dhx-color-border: #374151 !important;
            --dhx-color-bg: #1f2937 !important;
            --dhx-color-list-item-hover: #374151 !important;
        }
        
        /* Grid内容区域样式修复 */
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
        
        /* 表头样式优化 */
        .dhx_grid-header-cell {
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0 12px !important;
        }
        
        /* 单元格样式优化 */
        .dhx_grid-cell {
            padding: 10px 12px !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }
        
        /* 行悬停效果优化 */
        .dhx_grid-row {
            transition: background-color 0.2s ease !important;
        }
        
        .dhx_grid-row:hover .dhx_grid-cell {
            background-color: rgba(99, 102, 241, 0.05) !important;
        }
        
        [data-dhx-theme="dark"] .dhx_grid-row:hover .dhx_grid-cell {
            background-color: rgba(99, 102, 241, 0.1) !important;
        }
        
        /* 分页控件样式修复 */
        [data-dhx-theme="dark"] .dhx_pagination {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        
        [data-dhx-theme="dark"] .dhx_pagination-button {
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }
        
        [data-dhx-theme="dark"] .dhx_pagination-button:hover {
            background-color: #374151 !important;
        }
        
        [data-dhx-theme="dark"] .dhx_pagination-select {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }
        
        /* 修复分页信息文字颜色 */
        .dhx_pagination-info {
            color: inherit !important;
        }
        
        /* 空状态容器高度 */
        #empty_state {
            height: 650px;
        }
        
        /* 价格错误提示定位 */
        #priceError {
            position: absolute;
        }
    </style>
</x-app-layout>