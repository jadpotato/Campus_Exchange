<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            我的物品
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 状态筛选栏：DHTMLX Form -->
            <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
                <div id="filter-form"></div>
            </div>

            <!-- 物品列表：DHTMLX Grid -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden relative">
                <div id="grid_container" style="height: 650px; width: 100%;"></div>

                <!-- 加载状态 -->
                <div id="grid_loading" class="hidden absolute inset-0 bg-white/90 flex items-center justify-center z-10 backdrop-blur-sm">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-12 w-12 text-indigo-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 text-lg">正在加载数据...</span>
                    </div>
                </div>

                <!-- 空状态 -->
                <div id="empty_state" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                    <p class="text-gray-500 mb-4">你还没有发布任何物品</p>
                    <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        发布第一个物品
                    </a>
                </div>
            </div>

            <!-- 数据统计 -->
            <div class="mt-4 text-gray-600 text-sm flex justify-between items-center">
                <span id="data_stats">共 0 条物品数据</span>
                <button id="refreshBtn" class="text-indigo-600 hover:underline flex items-center">
                    刷新数据
                </button>
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

        // 状态中文映射
        const statusMap = {
            'pending_approval': '待审核',
            'published': '发布中',
            'locked': '已锁定',
            'completed': '已完成',
            'unpublished': '已下架'
        };

        // 分类映射
        const categoryMap = {
            'textbook': '教材书籍',
            'electronics': '电子产品',
            'daily': '生活用品',
            'clothing': '衣物服饰',
            'beauty': '美妆个护',
            'food': '食品饮料',
            'other': '其他'
        };

        // 交易模式映射
        const tradeTypeMap = {
            'sell': '现金出售',
            'exchange': '以物换物',
            'free': '免费赠送'
        };

        document.addEventListener('DOMContentLoaded', function() {
            // 等待全局 DHTMLX 就绪
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
                    console.log("✅ 我的物品页面初始化成功");
                } catch (e) {
                    console.error("❌ 初始化失败:", e);
                    hideLoading();
                }
            }
            startInit();
        });

        // ========== 筛选表单初始化 ==========
        function initFilterForm() {
            filterForm = new dhx.Form("filter-form", {
                rows: [
                    {
                        type: "combo",
                        label: "物品状态",
                        name: "status",
                        labelPosition: "top",
                        value: "",
                        valueField: "value",
                        textField: "text",
                        width: 240,
                        options: [
                            { value: "", text: "全部状态" },
                            { value: "pending_approval", text: "待审核" },
                            { value: "published", text: "发布中" },
                            { value: "locked", text: "已锁定" },
                            { value: "completed", text: "已完成" },
                            { value: "unpublished", text: "已下架" }
                        ]
                    },
                    {
                        type: "button",
                        text: "筛选",
                        view: "primary",
                        submit: true,
                        width: 100,
                        offsetTop: 8
                    }
                ]
            });

            // 筛选提交
            filterForm.events.on("submit", function() {
                pageState.page = 1;
                loadGridData();
            });
        }

        // ========== 表格初始化 ==========
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
                            var imgSrc = item.first_photo_url || '/images/default-item.png';
                            return '<div class="flex items-center justify-center h-16 bg-gray-50 rounded overflow-hidden">' +
                                '<img src="' + imgSrc + '" alt="' + (item.title || '') + '" class="max-w-full max-h-full object-cover">' +
                                '</div>';
                        }
                    },
                    {
                        id: "title",
                        header: [{ text: "物品名称" }],
                        width: 240,
                        htmlEnable: true,
                        sortable: true,
                        template: function(item) {
                            return '<a href="/items/' + item.id + '" class="text-indigo-600 hover:underline font-medium">' +
                                (item.title || '') + '</a>';
                        }
                    },
                    {
                        id: "category",
                        header: [{ text: "分类" }],
                        width: 100,
                        sortable: true,
                        template: function(item) {
                            return categoryMap[item.category] || item.category || '-';
                        }
                    },
                    {
                        id: "trade_type",
                        header: [{ text: "交易模式" }],
                        width: 100,
                        sortable: true,
                        template: function(item) {
                            return tradeTypeMap[item.trade_type] || item.trade_type || '-';
                        }
                    },
                    {
                        id: "price",
                        header: [{ text: "价格" }],
                        width: 100,
                        sortable: true,
                        align: "right",
                        template: function(item) {
                            if (item.trade_type === 'sell' && item.price) {
                                return '¥' + parseFloat(item.price).toFixed(2);
                            }
                            return '-';
                        }
                    },
                    {
                        id: "status",
                        header: [{ text: "状态" }],
                        width: 100,
                        sortable: true,
                        htmlEnable: true,
                        template: function(item) {
                            var statusText = statusMap[item.status] || item.status || '-';
                            var bgClass = item.status === 'published'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-gray-100 text-gray-800';
                            return '<span class="text-xs px-2 py-1 rounded ' + bgClass + '">' + statusText + '</span>';
                        }
                    },
                    {
                        id: "view_count",
                        header: [{ text: "浏览量" }],
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
                        width: 160,
                        sortable: true,
                        template: function(item) {
                            return formatDateTime(item.created_at);
                        }
                    },
                    {
                        id: "actions",
                        header: [{ text: "操作" }],
                        width: 160,
                        sortable: false,
                        htmlEnable: true,
                        align: "center",
                        template: function(item) {
                            return '<a href="/items/' + item.id + '/edit" class="text-indigo-600 hover:underline text-sm mr-3">编辑</a>' +
                                '<button class="text-red-600 hover:underline text-sm delete-btn" data-id="' + item.id + '">删除</button>';
                        }
                    }
                ],
                sortable: true,
                autoWidth: true,
                headerRowHeight: 48,
                rowHeight: 72,
                resize: true,
                selection: "row",
                multiselection: false,
                editable: false,
                emptyText: ""
            });

            // 删除按钮点击事件（事件委托）
            grid.events.on("cellClick", function(row, column, e) {
                if (column.id === 'actions' && e.target.classList.contains('delete-btn')) {
                    var itemId = e.target.getAttribute('data-id');
                    if (confirm('确定要删除这个物品吗？')) {
                        deleteItem(itemId);
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

            // 初始加载
            loadGridData();
        }

        // ========== 加载表格数据 ==========
        function loadGridData(forceRefresh) {
            if (typeof forceRefresh === 'undefined') forceRefresh = false;
            if (!grid || !filterForm) return;

            showLoading();

            var sort = grid.getSortingState ? grid.getSortingState() : {};
            var formValues = filterForm.getValue();
            var params = new URLSearchParams(formValues);

            // 分页参数
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

            fetch('/api/my/items?' + params.toString(), {
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
                if (res.meta && res.meta.total) {
                    pageState.total = res.meta.total;
                }
                hideLoading();
            })
            .catch(function(error) {
                hideLoading();
                console.error('数据加载失败:', error);
                alert('数据加载失败，请刷新重试');
                updateEmptyState();
            });
        }

        // ========== 删除物品 ==========
        function deleteItem(itemId) {
            if (!itemId) return;

            fetch('/items/' + itemId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function(res) {
                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }
                if (!res.ok) throw new Error('删除失败');
                // 删除成功，刷新列表
                loadGridData();
                alert('删除成功');
            })
            .catch(function(error) {
                console.error('删除失败:', error);
                alert('删除失败，请重试');
            });
        }

        // ========== 工具函数 ==========
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            try {
                var date = new Date(dateString);
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                var hours = String(date.getHours()).padStart(2, '0');
                var minutes = String(date.getMinutes()).padStart(2, '0');
                return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
            } catch (e) {
                return dateString.substring(0, 16).replace('T', ' ');
            }
        }

        function updateEmptyState() {
            var hasData = grid.data.getLength() > 0;
            emptyStateEl.classList.toggle('hidden', hasData);
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
    </script>

    <style>
        /* 去除表单默认外框阴影 */
        .dhx_form {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-family: inherit !important;
        }

        #grid_container {
            font-family: inherit !important;
        }

        /* 表格样式优化 */
        .dhx_grid-header-cell {
            font-weight: 600 !important;
            font-size: 14px !important;
        }

        .dhx_grid-cell {
            font-size: 14px !important;
        }
    </style>
</x-app-layout>