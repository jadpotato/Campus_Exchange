<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            我的物品
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 筛选区域 样式优化 -->
            <div style="padding: 16px; background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 16px; display: flex; gap: 12px; align-items: center;">
                <span style="font-size: 14px; color: #4b5563;">物品状态：</span>
                <select id="statusSel" style="padding: 6px 10px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; width: 160px;">
                    <option value="">全部状态</option>
                    <option value="pending_approval">待审核</option>
                    <option value="published">发布中</option>
                    <option value="locked">已锁定</option>
                    <option value="completed">已完成</option>
                    <option value="unpublished">已下架</option>
                </select>
                <button id="searchBtn" style="padding: 6px 16px; font-size: 14px; background: #3b82f6; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
                    筛选
                </button>
            </div>

            <!-- 表格容器 -->
            <div style="background:#fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x:auto;">
                <div id="grid_container" style="height: 600px; min-width: 1180px;"></div>
            </div>

            <!-- 统计文字 -->
            <div style="margin-top: 12px; font-size: 14px; color: #6b7280;">
                <span id="totalText">共 0 条物品数据</span>
            </div>
        </div>
    </div>

    <!-- 底部悬浮新增按钮 -->
    <div id="addBtnBox" style="
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 56px;
        height: 56px;
        background: #3b82f6;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 999;
    " onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        <span style="color: #fff; font-size: 32px; font-weight: bold; line-height: 1;">+</span>
    </div>

    <script>
        let grid;
        const totalText = document.getElementById('totalText');
        const page = 1;
        const pageSize = 20;

        // 文字映射
        const statusMap = {
            'pending_approval':'待审核',
            'published':'发布中',
            'locked':'已锁定',
            'completed':'已完成',
            'unpublished':'已下架'
        };
        const categoryMap = {
            'textbook':'教材书籍','electronics':'电子产品','daily':'生活用品',
            'clothing':'衣物服饰','beauty':'美妆个护','food':'食品饮料','other':'其他'
        };
        const tradeMap = {
            'sell':'现金出售','exchange':'以物换物','free':'免费赠送'
        };

        // 时间格式化
        function formatTime(str){
            if(!str) return '-';
            const d = new Date(str);
            return d.getFullYear() + '-'
                + String(d.getMonth()+1).padStart(2,'0') + '-'
                + String(d.getDate()).padStart(2,'0') + ' '
                + String(d.getHours()).padStart(2,'0') + ':'
                + String(d.getMinutes()).padStart(2,'0');
        }

        // 初始化表格 新增操作列
        function initGrid(){
            grid = new dhx.Grid("grid_container",{
                columns: [
                    {id:"img", header:[{text:"缩略图"}], width: 100, htmlEnable:true, align:"center"},
                    {id:"id", header:[{text:"ID"}], width: 50, align:"center"},
                    {id:"title", header:[{text:"物品名称"}], width: 170},
                    {id:"category_cn", header:[{text:"分类"}], width: 90, align:"center"},
                    {id:"trade_cn", header:[{text:"交易模式"}], width: 100, align:"center"},
                    {id:"price", header:[{text:"价格"}], width: 80, align:"right"},
                    {id:"status_cn", header:[{text:"状态"}], width: 90, align:"center"},
                    {id:"view_count", header:[{text:"浏览量"}], width: 80, align:"center"},
                    {id:"create_time", header:[{text:"发布时间"}], width: 160, align:"center"},
                    // 操作列
                    {
                        id:"action",
                        header:[{text:"操作"}],
                        width: 140,
                        htmlEnable:true,
                        align:"center"
                    }
                ],
                autoWidth:false,
                headerRowHeight: 42,
                rowHeight: 80,
                css: "font-size:14px;"
            });
        }

        // 加载数据 + 修正路由地址
        function loadData(){
            const status = document.getElementById('statusSel').value;
            let url = `/api/my/items?page=${page}&per_page=${pageSize}`;
            if(status){
                url += `&status=${status}`;
            }

            fetch(url, {credentials:"same-origin"})
            .then(res => res.json())
            .then(res => {
                const raw = res.data || [];
                const render = [];
                for(let i=0;i<raw.length;i++){
                    const item = raw[i];
                    const row = {};
                    // 图片
                    row.img = `<img src="/storage/${item.photos[0]}" style="width:70px;height:70px;object-fit:cover;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.15);">`;
                    row.id = item.id;
                    row.title = item.title;
                    row.price = '¥' + parseFloat(item.price).toFixed(2);
                    row.view_count = item.view_count;
                    row.category_cn = categoryMap[item.category] || item.category;
                    row.trade_cn = tradeMap[item.trade_type] || item.trade_type;
                    row.status_cn = statusMap[item.status] || item.status;
                    row.create_time = formatTime(item.created_at);

                    // 修正路由：编辑 /items/{item}/edit
                    const editUrl = `/items/${item.id}/edit`;
                    // 删除建议走接口，这里保留链接+确认提示
                    row.action = `
                        <a href="${editUrl}" style="display:inline-block;padding:4px 8px;background:#10b981;color:#fff;border-radius:4px;text-decoration:none;margin-right:6px;font-size:13px;">编辑</a>
                        <a href="javascript:void(0)" onclick="delItem(${item.id})" style="display:inline-block;padding:4px 8px;background:#ef4444;color:#fff;border-radius:4px;text-decoration:none;font-size:13px;">删除</a>
                    `;

                    render.push(row);
                }
                grid.data.parse(render);
                totalText.innerText = `共 ${res.total} 条物品数据`;
            })
        }

        // 删除物品方法
        function delItem(id) {
            if(confirm('确定要删除该物品吗？此操作不可恢复！')){
                // 后续自行对接删除接口，这里先刷新列表
                fetch(`/items/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    loadData();
                });
            }
        }

        // 页面加载完成
        window.onload = function(){
            initGrid();
            loadData();
            document.getElementById('searchBtn').addEventListener('click', loadData);

            // 底部+号按钮：发布物品 /items/create
            document.getElementById('addBtnBox').addEventListener('click', function(){
                window.location.href = "/items/create";
            });
        }
    </script>
</x-app-layout>