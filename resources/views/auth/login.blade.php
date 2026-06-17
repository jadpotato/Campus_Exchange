<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物品市场 - 登录</title>
    
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

        /* 登录卡片容器 */
        .login-card {
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

        /* 底部链接排版 */
        .bottom-links {
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .bottom-links a {
            color: #4f46e5;
            text-decoration: underline;
        }
        .bottom-links a:hover { color: #4338ca; }
        .forgot-password-link { color: #9ca3af !important; font-size: 12px; }

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

        /* 终极样式穿透：强制把 DHTMLX 的复选框整块以及内部元素全部锁死在左侧 */
        .dhx_form-element { font-family: inherit !important; }
        .dhx_layout-rows .dhx_form-group { text-align: left !important; width: 100% !important; display: block !important; }
        .dhx_checkbox { display: inline-flex !important; justify-content: flex-start !important; width: auto !important; padding-left: 2px !important; }
    </style>
</head>
<body>

    <div class="login-card">
        
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

        <form id="laravel_login_form" method="POST" action="{{ route('login') }}" style="display: none;">
            @csrf
            <input type="hidden" name="email" id="real_email">
            <input type="hidden" name="password" id="real_password">
            <input type="hidden" name="remember" id="real_remember">
        </form>

        <div id="dhtmlx_form_container"></div>

        <div class="bottom-links">
            @if (Route::has('password.request'))
                <a class="forgot-password-link" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
            <div>
                Don't have an account?
                <a href="{{ route('register') }}">Register now</a>
            </div>
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

            // ========= DHTMLX 登录表单构建 =========
            if (typeof dhx !== 'undefined') {
                const loginForm = new dhx.Form("dhtmlx_form_container", {
                    width: "100%",
                    padding: 0,
                    rows: [
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
                            inputType: "password",
                            label: "Password",
                            name: "password",
                            placeholder: "••••••••",
                            required: true,
                            labelPosition: "top",
                            errorMessage: "Password is required"
                        },
                        {
                            type: "checkbox",
                            label: "Remember me",
                            name: "remember",
                            checked: false,
                            value: "1"
                        },
                        {
                            type: "spacer",
                            height: 12
                        },
                        {
                            type: "button",
                            name: "loginBtn", /* 为按钮命名以方便单独监听 */
                            text: "Log In",
                            view: "primary",
                            size: "medium",
                            fullWidth: true
                        }
                    ]
                });

                // 🚀 修复核心：改用精确监听按钮的点击事件，绕过 DHTMLX 冲突的 submit 机制
                loginForm.getItem("loginBtn").events.on("click", function() {
                    // 1. 进行 DHTMLX 表单内置的required非空校验
                    if (!loginForm.validate()) return;

                    // 2. 校验通过，提取表单值
                    const values = loginForm.getValue();
                    
                    // 3. 把提取出来的值塞给真正要交给后端执行登录的原生 input 框
                    document.getElementById('real_email').value = values.email || '';
                    document.getElementById('real_password').value = values.password || '';
                    document.getElementById('real_remember').value = values.remember ? 'on' : '';

                    // 4. 正式激发 Laravel 表单，带着安全 @csrf 进行跳转认证
                    document.getElementById('laravel_login_form').submit();
                });
            }
        });
    </script>
</body>
</html>