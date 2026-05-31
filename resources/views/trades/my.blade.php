<x-app-layout>
    <x-slot name="header">我的交易</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div id="kanban" style="width: 100%; height: 750px;"></div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.dhtmlx.com/suite/7.4.13/suite.min.css">
    <script src="https://cdn.dhtmlx.com/suite/7.4.13/suite.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kanban = new dhx.Kanban("kanban", {
                columns: [
                    { id: "negotiating", label: "🔄 协商中" },
                    { id: "pending_confirm", label: "⏳ 待确认" },
                    { id: "waiting_pickup", label: "📦 待取货" },
                    { id: "completed", label: "✅ 已完成" },
                    { id: "cancelled", label: "❌ 已取消" },
                ],

                // ======================
                // 【新增】严格状态拖拽限制（你的业务必需！）
                // ======================
                isDragAllowed: (card, targetColumnId) => {
                    const allowedTransitions = {
                        negotiating: ['pending_confirm', 'cancelled'],
                        pending_confirm: ['waiting_pickup', 'cancelled'],
                        waiting_pickup: ['completed', 'cancelled'],
                        completed: [],
                        cancelled: []
                    };
                    return allowedTransitions[card.status]?.includes(targetColumnId) ?? false;
                },

                // ======================
                // 【优化】卡片模板（更完整、更美观）
                // ======================
                cardTemplate: (card) => `
                    <div class="p-3 cursor-pointer" onclick="window.location.href='/trades/${card.id}'">
                        <div class="flex items-center gap-2 mb-1">
                            <img 
                                src="/storage/${card.item?.photos?.[0] ?? ''}" 
                                class="w-10 h-10 object-cover rounded border"
                                onError="this.src='/images/default-item.png'"
                            >
                            <div class="font-bold text-sm">${card.item?.title || '无标题物品'}</div>
                        </div>
                        <div class="text-xs text-gray-600 space-y-1">
                            <div>交易模式：${card.trade_type}</div>
                            <div>预约：${card.appoint_time ? new Date(card.appoint_time).toLocaleString() : '未设置'}</div>
                        </div>
                    </div>
                `
            });

            // 加载交易数据
            fetch("/api/trades")
                .then(res => {
                    if (!res.ok) throw new Error("加载失败");
                    return res.json();
                })
                .then(data => {
                    kanban.data.parse(data);
                })
                .catch(err => console.error("交易数据加载错误：", err));

            // 拖拽修改状态
            kanban.events.on("cardDrop", (cardId, event) => {
                const card = kanban.data.getItem(cardId);
                const newStatus = event.targetColumn;

                fetch(`/api/trades/${card.id}/status`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ status: newStatus })
                }).then(res => {
                    if (!res.ok) alert("无法修改状态：该状态不允许跳转");
                });
            });
        });
    </script>
</x-app-layout>