<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div id="profile_layout" style="width: 100%; min-height: 700px;"></div>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <div id="form_info_src" class="profile-content-block">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div id="form_freetime_src" class="profile-content-block">
            <h3 class="text-lg font-bold text-black mb-2">空闲时间设置</h3>
            <p class="text-sm text-black mb-6">设置您的空闲时间，系统会自动推荐双方都方便的交易时间</p>
            
            <form method="POST" action="{{ route('profile.free-time.update') }}">
                @csrf
                @method('PATCH')

                @php
                    $days = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
                    // 👈 已在最前方加入 9:00-10:30
                    $times = ['9:00-10:30', '12:00-13:30', '17:00-19:00', '20:00-22:00'];
                    $userFreeTime = $user->free_time ?? [];
                @endphp

                <div class="space-y-6">
                    @foreach($days as $day)
                        <div class="border-b pb-4 border-gray-200">
                            <h4 class="font-bold text-black mb-3">{{ $day }}</h4>
                            <div class="flex flex-wrap gap-4">
                                @foreach($times as $time)
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="free_time[{{ $day }}][]" 
                                               value="{{ $time }}"
                                               {{ in_array($time, $userFreeTime[$day] ?? []) ? 'checked' : '' }}
                                               class="rounded border-gray-400 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-black font-medium">{{ $time }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    <x-primary-button>保存空闲时间</x-primary-button>
                </div>
            </form>
        </div>

        <div id="form_password_src" class="profile-content-block">
            @include('profile.partials.update-password-form')
        </div>

        <div id="form_delete_src" class="profile-content-block">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof dhx === 'undefined') {
        console.error("DHTMLX 未载入，请检查全局依赖文件");
        return;
    }

    // ========== 1. 用 DHTMLX Layout 构建左边菜单栏 + 右边内容区 ==========
    const layout = new dhx.Layout("profile_layout", {
        cols: [
            { width: "220px", id: "sidebar_cell", css: "dhx_layout-sidebar-border" },
            { id: "content_cell" }
        ]
    });

    // ========== 2. 用 DHTMLX Sidebar 建立个人中心导航 ==========
    const sidebarCell = layout.getCell("sidebar_cell");
    const sidebar = new dhx.Sidebar(null, {
        css: "dhx_widget--bg-white"
    });

    // 填充菜单项
    sidebar.data.parse([
        { id: "info", value: "基本信息", icon: "mdi mdi-account" },
        { id: "freetime", value: "空闲时间设置", icon: "mdi mdi-clock-outline" },
        { id: "password", value: "安全修改密码", icon: "mdi mdi-lock-reset" },
        { id: "delete", value: "注销个人账号", icon: "mdi mdi-account-remove" }
    ]);

    sidebarCell.attach(sidebar);

    // ========== 3. 内容切换控制枢纽 ==========
    const contentCell = layout.getCell("content_cell");

    function switchTab(tabId) {
        if (tabId === "info") {
            contentCell.attachHTML(document.getElementById("form_info_src").innerHTML);
        } else if (tabId === "freetime") {
            contentCell.attachHTML(document.getElementById("form_freetime_src").innerHTML);
        } else if (tabId === "password") {
            contentCell.attachHTML(document.getElementById("form_password_src").innerHTML);
        } else if (tabId === "delete") {
            contentCell.attachHTML(document.getElementById("form_delete_src").innerHTML);
        }
    }

    // 监听左侧导航点击事件
    sidebar.events.on("click", function (id) {
        switchTab(id);
    });

    // 默认点亮并打开第一个标签“基本信息”
    sidebar.select("info");
    switchTab("info");
});
</script>

<style>
    /* 微调 DHTMLX sidebar 边框 */
    .dhx_layout-sidebar-border {
        border-right: 1px solid #e5e7eb !important;
    }
    .dhx_widget--bg-white {
        background-color: #ffffff !important;
    }
    
    .profile-content-block, 
    [id="profile_layout"] .dhx_layout-cell-content {
        padding: 20px !important;
    }

    /* 强制将右侧区域内的所有基础文字、标签颜色置为纯黑 */
    [id="profile_layout"] p,
    [id="profile_layout"] h3,
    [id="profile_layout"] h4,
    [id="profile_layout"] label,
    [id="profile_layout"] span,
    [id="profile_layout"] section header h2,
    [id="profile_layout"] section header p {
        color: #000000 !important;
    }

    /* 修复输入框：彻底抹除黑色框背景，强制改为：白底、黑字、灰色边框 */
    [id="profile_layout"] input[type="text"],
    [id="profile_layout"] input[type="email"],
    [id="profile_layout"] input[type="password"],
    [id="profile_layout"] select,
    [id="profile_layout"] textarea {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #bcbcbc !important;
    }

    /* 当输入框聚焦时的额外控制 */
    [id="profile_layout"] input:focus {
        border-color: #4f46e5 !important;
        --tw-ring-color: #4f46e5 !important;
    }
</style>
</x-app-layout>