<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物品市场 - 注册</title>
    
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

        /* 注册卡片容器 */
        .register-card {
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
            margin-bottom: 32px;
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

        /* 底部返回登录链接排版 */
        .bottom-links {
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
        }
        .bottom-links a {
            color: #4f46e5;
            text-decoration: underline;
        }
        .bottom-links a:hover { color: #4338ca; }

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

        /* DHTMLX 表单字体继承微调 */
        .dhx_form-element { font-family: inherit !important; }
    </style>
</head>
<body>

    <div class="register-card">
        
        <div id="login-logo" class="logo-container" title="连续点击5次切换管理员后台">
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

        <form id="laravel_register_form" method="POST" action="{{ route('register') }}" style="display: none;">
            @csrf
            <input type="hidden" name="name" id="real_name">
            <input type="hidden" name="email" id="real_email">
            <input type="hidden" name="student_id" id="real_student_id">
            <input type="hidden" name="password" id="real_password">
            <input type="hidden" name="password_confirmation" id="real_password_confirmation">
        </form>

        <div id="dhtmlx_form_container"></div>

        <div class="bottom-links">
            <a href="{{ route('login') }}">Already registered? Log in</a>
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

            // ========= DHTMLX 注册表单构建 =========
            if (typeof dhx !== 'undefined') {
                const registerForm = new dhx.Form("dhtmlx_form_container", {
                    width: "100%",
                    padding: 0,
                    rows: [
                        {
                            type: "input",
                            label: "Name",
                            name: "name",
                            placeholder: "Your Name",
                            required: true,
                            labelPosition: "top",
                            value: "{{ old('name') }}",
                            errorMessage: "Name is required"
                        },
                        {
                            type: "input",
                            label: "Email Address",
                            name: "email",
                            placeholder: "your-id@stu.ecnu.edu.cn",
                            required: true,
                            labelPosition: "top",
                            value: "{{ old('email') }}",
                            errorMessage: "Email is required"
                        },
                        {
                            type: "input",
                            label: "学号 (Student ID)",
                            name: "student_id",
                            placeholder: "", /* 👈 移除了学号框内的示例内容 */
                            required: true,
                            labelPosition: "top",
                            value: "{{ old('student_id') }}",
                            errorMessage: "Student ID is required"
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
                            type: "input",
                            inputType: "password",
                            label: "Confirm Password",
                            name: "password_confirmation",
                            placeholder: "••••••••",
                            required: true,
                            labelPosition: "top",
                            errorMessage: "Please confirm your password"
                        },
                        {
                            type: "spacer",
                            height: 12
                        },
                        {
                            type: "button",
                            name: "registerBtn",
                            text: "Register",
                            view: "primary",
                            size: "medium",
                            fullWidth: true
                        }
                    ]
                });

                // 精确按钮点击事件监听
                registerForm.getItem("registerBtn").events.on("click", function() {
                    if (!registerForm.validate()) return;

                    const values = registerForm.getValue();
                    
                    document.getElementById('real_name').value = values.name || '';
                    document.getElementById('real_email').value = values.email || '';
                    document.getElementById('real_student_id').value = values.student_id || '';
                    document.getElementById('real_password').value = values.password || '';
                    document.getElementById('real_password_confirmation').value = values.password_confirmation || '';

                    document.getElementById('laravel_register_form').submit();
                });
            }
        });
    </script>
</body>
</html>