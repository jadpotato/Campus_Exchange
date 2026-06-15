<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            发布物品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <!-- DHTMLX 表单主体（稳定版配置，无兼容风险属性） -->
                <div id="item-form"></div>

                <!-- 原生图片上传（样式与表单对齐） -->
                <div style="margin-top: 16px;">
                    <label class="dhx_form-label" style="display:block;margin-bottom:8px;font-size:14px;color:#333;">
                        物品图片 (最多5张)
                    </label>
                    <input 
                        id="photos" 
                        name="photos[]" 
                        type="file" 
                        multiple 
                        accept="image/jpeg,image/png"
                        style="width:100%;font-size:14px;color:#666;"
                    >
                    <p style="font-size:12px;color:#999;margin-top:4px;">
                        支持JPG、PNG格式，单张最大2MB
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function startInit() {
                if (typeof dhx === 'undefined' || !window.appReady) {
                    setTimeout(startInit, 50);
                    return;
                }
                try {
                    var itemForm = new dhx.Form("item-form", {
                        rows: [
                            {
                                type: "input",
                                name: "title",
                                label: "物品名称",
                                labelPosition: "top"
                            },
                            {
                                type: "textarea",
                                name: "description",
                                label: "物品描述",
                                labelPosition: "top"
                            },
                            {
                                type: "combo",
                                name: "category",
                                label: "物品分类",
                                labelPosition: "top",
                                value: "textbook",
                                options: [
                                    {value: "textbook", text: "教材书籍"},
                                    {value: "electronics", text: "电子产品"},
                                    {value: "daily", text: "生活用品"},
                                    {value: "clothing", text: "衣物服饰"},
                                    {value: "beauty", text: "美妆个护"},
                                    {value: "food", text: "食品饮料"},
                                    {value: "other", text: "其他"}
                                ]
                            },
                            {
                                type: "combo",
                                name: "trade_type",
                                label: "交易模式",
                                labelPosition: "top",
                                value: "sell",
                                options: [
                                    {value: "sell", text: "现金出售"},
                                    {value: "exchange", text: "以物换物"},
                                    {value: "free", text: "免费赠送"}
                                ]
                            },
                            {
                                type: "input",
                                name: "price",
                                label: "价格 (元)",
                                labelPosition: "top",
                                id: "price_field"
                            },
                            {
                                type: "input",
                                name: "expected_item",
                                label: "期望交换物品",
                                labelPosition: "top",
                                id: "expected_item_field",
                                hidden: true
                            },
                            {
                                type: "button",
                                text: "发布物品",
                                view: "primary",
                                submit: true,
                                offsetTop: 20
                            }
                        ]
                    });

                    // 交易模式切换联动
                    itemForm.getItem("trade_type").events.on("change", function(value) {
                        if (value === "sell") {
                            itemForm.showItem("price_field");
                            itemForm.hideItem("expected_item_field");
                        } else if (value === "exchange") {
                            itemForm.hideItem("price_field");
                            itemForm.showItem("expected_item_field");
                        } else {
                            itemForm.hideItem("price_field");
                            itemForm.hideItem("expected_item_field");
                        }
                    });

                    // 表单提交
                    itemForm.events.on("submit", function() {
                        var formValues = itemForm.getValue();

                        // 基础校验
                        if (!formValues.title || !formValues.description) {
                            alert("请填写物品名称和描述");
                            return;
                        }
                        if (formValues.trade_type === "sell" && !formValues.price) {
                            alert("请输入物品价格");
                            return;
                        }

                        // 构造 FormData，完全兼容原后端接口
                        var formData = new FormData();
                        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
                        formData.append("title", formValues.title);
                        formData.append("description", formValues.description);
                        formData.append("category", formValues.category);
                        formData.append("trade_type", formValues.trade_type);

                        if (formValues.trade_type === "sell") {
                            formData.append("price", formValues.price);
                        }
                        if (formValues.trade_type === "exchange") {
                            formData.append("expected_item", formValues.expected_item || "");
                        }

                        // 添加图片文件
                        var photoInput = document.getElementById("photos");
                        if (photoInput && photoInput.files.length > 0) {
                            for (var i = 0; i < photoInput.files.length; i++) {
                                formData.append("photos[]", photoInput.files[i]);
                            }
                        }

                        // 提交到后端
                        fetch("{{ route('items.store') }}", {
                            method: "POST",
                            body: formData,
                            credentials: "same-origin"
                        })
                        .then(function(res) {
                            if (res.redirected) {
                                window.location.href = res.url;
                                return;
                            }
                            if (!res.ok) throw new Error("发布失败，请检查填写内容");
                            return res.json();
                        })
                        .then(function() {
                            alert("发布成功！");
                            window.location.href = "{{ route('items.index') }}";
                        })
                        .catch(function(error) {
                            console.error("发布失败:", error);
                            alert(error.message || "发布失败，请重试");
                        });
                    });

                    console.log("✅ 表单初始化成功");
                } catch (e) {
                    console.error("❌ 表单初始化失败:", e);
                }
            }
            startInit();
        });
    </script>

    <style>
        /* 去除表单默认黑色外框、阴影，融入白色卡片 */
        .dhx_form {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-family: inherit !important;
        }
    </style>
</x-app-layout>