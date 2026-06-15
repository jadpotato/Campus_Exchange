<?php
// DHTMLX 全组件调试页 - 校园二手交易平台场景
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Exchange - DHTMLX 全组件测试</title>
    <!-- 本地 DHTMLX 资源 -->
    <link rel="stylesheet" href="/dhtmlx/suite.min.css">
    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: system-ui, sans-serif;
        }
        /* 全屏布局容器 */
        #app {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div id="app"></div>

    <script src="/dhtmlx/suite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // ========== 1. 整体布局：顶部导航 + 左侧边栏 + 主内容区 ==========
            const layout = new dhx.Layout("app", {
                type: "line",
                rows: [
                    {
                        id: "toolbar",
                        height: 56,
                        css: "dhx_layout-cell--border_bottom"
                    },
                    {
                        type: "line",
                        cols: [
                            {
                                id: "sidebar",
                                width: 220,
                                css: "dhx_layout-cell--border_right"
                            },
                            {
                                id: "content",
                                gravity: 2
                            }
                        ]
                    }
                ]
            });

            // ========== 2. 顶部导航栏 Toolbar（替换你原来的顶部bar） ==========
            const toolbar = new dhx.Toolbar(null, {
                data: [
                    { id: "logo", value: "Campus Exchange", type: "title" },
                    { type: "spacer" },
                    { id: "home", value: "首页", icon: "dxi dxi-home" },
                    { id: "publish", value: "发布物品", icon: "dxi dxi-plus-circle" },
                    { id: "my-items", value: "我的物品", icon: "dxi dxi-package" },
                    { type: "separator" },
                    { id: "user", value: "用户中心", icon: "dxi dxi-account-circle" }
                ]
            });
            layout.getCell("toolbar").attach(toolbar);

            // ========== 3. 左侧分类 Sidebar ==========
            const sidebar = new dhx.Sidebar(null, {
                data: [
                    { id: "all", value: "全部物品", icon: "dxi dxi-view-grid" },
                    { id: "books", value: "书籍教材", icon: "dxi dxi-book" },
                    { id: "digital", value: "数码电子", icon: "dxi dxi-laptop" },
                    { id: "daily", value: "生活用品", icon: "dxi dxi-home-variant" },
                    { id: "clothes", value: "服饰鞋包", icon: "dxi dxi-tshirt-crew" },
                    { id: "free", value: "免费赠送", icon: "dxi dxi-gift" }
                ]
            });
            layout.getCell("sidebar").attach(sidebar);

            // ========== 4. 主内容区：Tab切换（表格 / 发布表单） ==========
            const tabbar = new dhx.Tabbar(null, {
                views: [
                    { id: "grid", tab: "物品列表" },
                    { id: "form", tab: "发布物品" }
                ]
            });
            layout.getCell("content").attach(tabbar);

            // --- 物品列表 Grid ---
            const grid = new dhx.Grid(null, {
                columns: [
                    { id: "id", header: "ID", width: 80 },
                    { id: "title", header: "物品名称", width: 240 },
                    { id: "category", header: "分类", width: 120 },
                    { id: "price", header: "价格(元)", width: 100 },
                    { id: "trade_type", header: "交易方式", width: 120 },
                    { id: "status", header: "状态", width: 100 }
                ],
                data: [
                    { id: 1, title: "高等数学同济第七版", category: "书籍教材", price: 25, trade_type: "出售", status: "已发布" },
                    { id: 2, title: "机械键盘 青轴 98新", category: "数码电子", price: 150, trade_type: "出售", status: "已发布" },
                    { id: 3, title: "宿舍护眼LED台灯", category: "生活用品", price: 30, trade_type: "出售", status: "待审核" },
                    { id: 4, title: "雅思真题集4-18", category: "书籍教材", price: 0, trade_type: "免费赠送", status: "已发布" },
                    { id: 5, title: "iPad Air5 256G", category: "数码电子", price: 3200, trade_type: "以物换物", status: "已发布" }
                ]
            });
            tabbar.getCell("grid").attach(grid);

            // --- 发布物品 Form 表单 ---
            const form = new dhx.Form(null, {
                padding: 20,
                rows: [
                    {
                        type: "input",
                        label: "物品标题",
                        name: "title",
                        required: true,
                        placeholder: "请输入物品名称"
                    },
                    {
                        type: "select",
                        label: "物品分类",
                        name: "category",
                        required: true,
                        options: [
                            { value: "books", content: "书籍教材" },
                            { value: "digital", content: "数码电子" },
                            { value: "daily", content: "生活用品" },
                            { value: "clothes", content: "服饰鞋包" }
                        ]
                    },
                    {
                        type: "radioGroup",
                        label: "交易方式",
                        name: "trade_type",
                        value: "sell",
                        options: [
                            { value: "sell", content: "现金出售" },
                            { value: "exchange", content: "以物换物" },
                            { value: "free", content: "免费赠送" }
                        ]
                    },
                    {
                        type: "input",
                        label: "期望价格",
                        name: "price",
                        inputType: "number",
                        placeholder: "0 表示免费"
                    },
                    {
                        type: "textarea",
                        label: "物品描述",
                        name: "description",
                        height: 120,
                        placeholder: "详细描述物品成色、使用情况等"
                    },
                    {
                        type: "upload",
                        label: "物品图片",
                        name: "photos",
                        accept: "image/*",
                        multiple: true
                    },
                    {
                        type: "button",
                        text: "发布物品",
                        submit: true,
                        size: "medium",
                        view: "primary",
                        offsetTop: 10
                    }
                ]
            });
            tabbar.getCell("form").attach(form);

            // 表单提交测试
            form.events.on("submit", function (values) {
                console.log("发布表单提交数据：", values);
                dhx.message({ text: "发布成功！", icon: "dxi dxi-check" });
            });

            console.log("✅ DHTMLX 全部组件加载成功");
        });
    </script>
</body>
</html>