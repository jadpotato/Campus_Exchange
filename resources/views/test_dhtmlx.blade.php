<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHTMLX 最终测试</title>

    <!-- 纯本地文件，无任何 CDN -->
    <link rel="stylesheet" href="/dhtmlx/suite.min.css">
</head>
<body>
    <h1>DHTMLX Grid 测试</h1>
    <div id="grid" style="width: 800px; height: 400px; border: 1px solid #ccc;"></div>

    <h1 style="margin-top: 50px;">DHTMLX Chart 测试</h1>
    <div id="chart" style="width: 800px; height: 400px; border: 1px solid #ccc;"></div>

    <!-- ✅ 把脚本放在 body 最底部，确保 DOM 和脚本都加载完成 -->
    <script src="/dhtmlx/suite.min.js"></script>
    <script>
        // 等待 window 完全加载，确保 dhx 对象存在
        window.onload = function() {
            console.log("DHTMLX 版本：", dhx.version);

            // Grid 测试
            const grid = new dhx.Grid("grid", {
                columns: [
                    { id: "id", header: "ID", width: 80 },
                    { id: "name", header: "名称", width: 200 },
                    { id: "price", header: "价格", width: 100 }
                ],
                data: [
                    { id: 1, name: "测试物品1", price: 100 },
                    { id: 2, name: "测试物品2", price: 200 },
                    { id: 3, name: "测试物品3", price: 300 }
                ]
            });

            // Chart 测试（最简单的柱状图，确保能显示）
            const chart = new dhx.Chart("chart", {
                type: "bar",
                data: [
                    { month: "Jan", sales: 40 },
                    { month: "Feb", sales: 20 },
                    { month: "Mar", sales: 35 },
                    { month: "Apr", sales: 50 },
                    { month: "May", sales: 25 }
                ],
                series: [
                    { value: "sales", color: "#4f46e5" }
                ],
                scales: {
                    x: { text: "month" },
                    y: { max: 60 }
                }
            });

            console.log("✅ 所有组件正常运行！");
        };
    </script>
</body>
</html>