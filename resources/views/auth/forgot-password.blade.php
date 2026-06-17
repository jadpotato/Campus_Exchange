<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物品市场 - 找回密码</title>
    
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/suite/edge/suite.css">
    <script src="https://cdn.dhtmlx.com/suite/edge/suite.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6; /* 全局浅灰色背景 */
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-h: 100vh;
            height: 100vh;
        }

        /* 卡片容器 */
        .reset-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        /* 放大版的 Logo 外层包围框 */
        .logo-container {
            display: inline-block;
            margin-bottom: 24px;
            cursor: pointer;
            transition: transform 0.2s;
            user-select: none;
        }
        .logo-container:hover { transform: scale(1.05); }
        .logo-container svg, .logo-container img {
            width: 80px;
            height: 80px;
            color: #4f46e5;
            fill: currentColor;
        }

        /* 中文提示文字样式 */
        .info-text {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 24px;
        }

        /* 底部返回链接 */
        .bottom-links {
            margin-top: 24px;
            font-size: 14px;
        }
        .bottom-links a {
            color: #4f46e5;
            text-decoration: underline;
        }
        .bottom-links a:hover { color: #4338ca; }

        /* 后端 Laravel 验证成功状态框（绿字） */
        .status-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 12px;
            border-radius: 4px;
            font-size: 13px;
            text-align: left;
            margin-bottom: 16px;
        }

        /* 后端 Laravel 验证错误提示框（红字） */
        .error-box {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 4px;
            font-size: 13px;
            text-align: left;
            margin-bottom: 16px;
        }
        .error-box ul { padding-left: 20px; }

        /* DHTMLX 表单字体微调 */
        .dhx_form-element { font-family: inherit !important; }
    </style>
</head>
<body>

    <div class="reset-card">
        
        <div id="login-logo" class="logo-container" title="连续点击5次切换管理员后台">
            <x-application-logo />
        </div>

        <div class="info-text">
            忘记密码了吗？没关系。只需将您的电子邮箱地址告诉我们，我们将向您的邮箱发送一个密码重置链接，您可以点击该链接设置新密码。
        </div>

        @if (session('status'))
            <div class="status-box">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="laravel_reset_form" method="POST" action="{{ route('password.email') }}" style="display: none;">
            @csrf
            <input type="hidden" name="email" id="real_email">
        </form>

        <div id="dhtmlx_form_container"></div>

        <div class="bottom-links">
            <a href="{{ route('login') }}">返回登录</a>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========= 彩蛋：连续点击 Logo 5次进入管理员登录 =========
            let clickCount = 0;
            const logo = document.getElementById('login-logo');
            if (logo) {
                logo.addEventListener('click', function() {
                    clickCount++;
                    if (clickCount === 5) {
                        window.location.href = "{{ url('admin/login') }}";
                    }
                    setTimeout(() => { clickCount = 0; }, 2500);
                });
            }

            // ========= DHTMLX 找回密码表单构建 =========
            if (typeof dhx !== 'undefined') {
                const resetForm = new dhx.Form("dhtmlx_form_container", {
                    width: "100%",
                    padding: 0,
                    rows: [
                        {
                            type: "input",
                            label: "电子邮箱 (Email)",
                            name: "email",
                            placeholder: "your-id@stu.ecnu.edu.cn", /* ECNU 专属示例 */
                            required: true,
                            labelPosition: "top",
                            value: "{{ old('email') }}",
                            errorMessage: "请输入电子邮箱地址"
                        },
                        {
                            type: "spacer",
                            height: 12
                        },
                        {
                            type: "button",
                            name: "submitBtn",
                            text: "发送密码重置链接", /* 中文按钮 */
                            view: "primary",
                            size: "medium",
                            fullWidth: true
                        }
                    ]
                });

                // 精确按钮点击事件监听
                resetForm.getItem("submitBtn").events.on("click", function() {
                    if (!resetForm.validate()) return;

                    const values = resetForm.getValue();
                    
                    document.getElementById('real_email').value = values.email || '';

                    document.getElementById('laravel_reset_form').submit();
                });
            }
        });
    </script>
</body>
</html>