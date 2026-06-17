<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/suite/edge/suite.css">
    <script src="https://cdn.dhtmlx.com/suite/edge/suite.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc; /* 管理端专属：冷灰色背景 */
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-h: 100vh;
            height: 100vh;
        }

        /* 管理员高级卡片容器 */
        .admin-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 45px 35px 40px 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* 管理端专属：顶部的身份区分装饰条 */
        .admin-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1e293b, #334155);
        }

        /* 🚀 Logo 上方的英文管理后台字样 */
        .admin-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 20px;
            margin-bottom: 20px;
            letter-spacing: 1.5px;
            border: 1px solid #e2e8f0;
        }

        /* 放大版的 Logo 外层包围框 */
        .logo-container {
            display: inline-block;
            margin-bottom: 32px;
            cursor: pointer;
            transition: transform 0.2s;
            user-select: none;
        }
        .logo-container:hover { transform: scale(1.05); }
        .logo-container svg, .logo-container img {
            width: 80px;
            height: 80px;
            color: #1e293b;
            fill: currentColor;
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
            margin-bottom: 20px;
        }
        .error-box ul { padding-left: 20px; }

        /* DHTMLX 统一样式微调 */
        .dhx_form-element { font-family: inherit !important; }
        
        /* DHTMLX 管理端按钮配色（深雅色调） */
        .dhx_button--view-primary {
            background-color: #1e293b !important;
            border-color: #1e293b !important;
        }
        .dhx_button--view-primary:hover {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
        }
    </style>
</head>
<body>

    <div class="admin-card">
        
        <div>
            <span class="admin-badge">Administrator Control Panel</span>
        </div>
        
        <div class="logo-container">
            <x-application-logo />
        </div>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="laravel_admin_form" method="POST" action="{{ route('admin.login') }}" style="display: none;">
            @csrf
            <input type="hidden" name="email" id="real_email">
            <input type="hidden" name="password" id="real_password">
        </form>

        <div id="dhtmlx_form_container"></div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ========= DHTMLX 管理员登录表单构建 =========
            if (typeof dhx !== 'undefined') {
                const adminForm = new dhx.Form("dhtmlx_form_container", {
                    width: "100%",
                    padding: 0,
                    rows: [
                        {
                            type: "input",
                            label: "Email Address",
                            name: "email",
                            placeholder: "", /* 保持干净，无任何格式示例限制 */
                            required: true,
                            labelPosition: "top",
                            value: "{{ old('email') }}",
                            errorMessage: "Email is required"
                        },
                        {
                            type: "input",
                            inputType: "password",
                            label: "Password",
                            name: "password",
                            placeholder: "••••••••",
                            required: true,
                            labelPosition: "top",
                            errorMessage: "Password is required"
                        },
                        {
                            type: "spacer",
                            height: 16
                        },
                        {
                            type: "button",
                            name: "loginBtn",
                            text: "Log In",
                            view: "primary",
                            size: "medium",
                            fullWidth: true
                        }
                    ]
                });

                // 精确按钮点击事件监听
                adminForm.getItem("loginBtn").events.on("click", function() {
                    if (!adminForm.validate()) return;

                    const values = adminForm.getValue();
                    
                    document.getElementById('real_email').value = values.email || '';
                    document.getElementById('real_password').value = values.password || '';

                    document.getElementById('laravel_admin_form').submit();
                });
            }
        });
    </script>
</body>
</html>