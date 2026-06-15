<?php
use Illuminate\Support\Facades\Auth; // 👈 1. 完美移到最顶部，解决 Blade 编译语法报错
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $item->title }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div id="detail_box" style="width: 100%; height: 650px;"></div>
            </div>
        </div>
    </div>

<?php
/** @var \App\Models\Item $item */ // 👈 核心：加在 <?php 的正下方
$photosData = [];
if (!empty($item->photos)) {
    foreach ($item->photos as $p) {
        $photosData[] = asset('storage/' . $p);
    }
}

$dataArr = [
    'photos'      => $photosData,
    'title'       => e($item->title),
    'desc'        => e($item->description),
    'tradeType'   => $item->trade_type,
    'tradeText'   => e($item->trade_type_text),
    'price'       => $item->price ?: '0',
    'statusText'  => e($item->status_text),
    'category'    => e($item->category_text), // 👈 【已修复】这里由 category 改为 category_text
    'createTime'  => $item->created_at->format('Y-m-d'),
    'views'       => $item->view_count,
    'expectItem'  => e($item->desired_item ?? ''),
    'isOwner'     => $item->user_id === Auth::id(),
    'canTrade'    => $item->canBeTraded() && $item->user_id !== Auth::id(),
    'editUrl'     => route('items.edit', $item),
    'delUrl'      => route('items.destroy', $item),
    'userName'    => e($item->user->name),
    'userAvatar'  => $item->user->avatar_url,
    'userScore'   => (int)($item->user->rating_avg ?? 0),
    'csrfToken'   => csrf_token() 
];
$jsonStr = json_encode($dataArr);
?>

<script>
    const DATA = <?php echo $jsonStr; ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========== 标准 DHTMLX Layout 初始化 ==========
    const layout = new dhx.Layout("detail_box", {
        cols: [
            { width: "50%", id: "left_panel" },
            { width: "50%", id: "right_panel" }
        ]
    });

    // 左侧面板 - 图片区域
    const leftPanel = layout.getCell("left_panel");
    let mainImg = DATA.photos.length > 0 ? DATA.photos[0] : "";

    let imgHtml = `
        <div style="padding: 15px;">
            <div style="width:100%; height:380px; background:#f5f5f5; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <img id="main_img" src="${mainImg}" style="max-width:100%; max-height:100%;">
            </div>
    `;
    if (DATA.photos.length > 1) {
        imgHtml += `<div style="display:flex; gap:8px; margin-top:15px; flex-wrap:wrap;">`;
        DATA.photos.forEach(src => {
            imgHtml += `<img class="thumb" src="${src}" style="width:65px;height:65px;object-fit:cover;border-radius:4px;cursor:pointer;border:2px solid transparent;">`;
        });
        imgHtml += `</div>`;
    }
    imgHtml += `</div>`;
    leftPanel.attachHTML(imgHtml);

    // 缩略图切换
    const thumbs = document.querySelectorAll(".thumb");
    const mainImgDom = document.getElementById("main_img");
    thumbs.forEach(thumb => {
        thumb.onclick = function () {
            mainImgDom.src = this.src;
            thumbs.forEach(t => t.style.border = "2px solid transparent");
            this.style.border = "2px solid #2563eb";
        };
    });

    // 右侧面板 - 信息区域
    const rightPanel = layout.getCell("right_panel");
    let priceShow = DATA.tradeType === "sell" ? "¥" + DATA.price : "";

    let starHtml = "";
    for (let i = 1; i <= 5; i++) {
        let color = i <= DATA.userScore ? "#facc15" : "#d1d5db";
        starHtml += `<svg width="16" height="16" fill="${color}" viewBox="0 0 20 20" style="display:inline-block;">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
        </svg>`;
    }

    let expectBlock = "";
    if (DATA.tradeType === "exchange" && DATA.expectItem !== "") {
        expectBlock = `
            <div style="margin-top:15px; padding-top:15px; border-top:1px solid #eee;">
                <h4 style="margin:0 0 8px 0; color:#333;">期望交换物品</h4>
                <p style="margin:0; color:#555;">${DATA.expectItem}</p>
            </div>
        `;
    }

    let btnBlock = `<div style="display:flex; gap:10px; margin-top:25px;">`;
    if (DATA.canTrade) {
        btnBlock += `<button style="flex:1;background:#4f46e5;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer;">发起交易</button>`;
    }
    if (DATA.isOwner) {
        btnBlock += `<a href="${DATA.editUrl}" style="flex:1;text-align:center;background:#f1f1f1;padding:10px;border-radius:6px;text-decoration:none;color:#333;">编辑物品</a>`;
        btnBlock += `<button id="del_btn" style="flex:1;background:#dc2626;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer;">删除物品</button>`;
    }
    btnBlock += `</div>`;

    let infoHtml = `
        <div style="padding:15px;">
            <h2 style="font-size:24px;margin:0 0 10px 0;color:#111;">${DATA.title}</h2>
            <div style="display:flex;align-items:center;gap:15px;margin-bottom:20px;">
                <span style="font-size:26px;font-weight:bold;color:#111111;">${DATA.tradeText} ${priceShow}</span>
                <span style="background:#16a34a;color:#fff;padding:4px 10px;border-radius:4px;font-size:14px;">${DATA.statusText}</span>
            </div>
            <div style="border-top:1px solid #eee;padding-top:15px;">
                <div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:#666;">分类</span><span>${DATA.category}</span></div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:#666;">发布时间</span><span>${DATA.createTime}</span></div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;"><span style="color:#666;">浏览次数</span><span>${DATA.views}</span></div>
            </div>
            <div style="border-top:1px solid #eee;padding-top:15px;margin-top:15px;">
                <h4 style="margin:0 0 8px 0;color:#333;">物品描述</h4>
                <p style="margin:0;color:#555;line-height:1.6;">${DATA.desc}</p>
            </div>
            ${expectBlock}
            <div style="border-top:1px solid #eee;padding-top:15px;margin-top:15px;">
                <div style="display:flex;align-items:center;">
                    <img src="${DATA.userAvatar}" style="width:44px;height:44px;border-radius:50%;">
                    <div style="margin-left:12px;">
                        <div style="font-weight:500;">${DATA.userName}</div>
                        <div style="margin-top:4px;">${starHtml}<span style="margin-left:4px;font-size:14px;color:#666;">${DATA.userScore} 分</span></div>
                    </div>
                </div>
            </div>
            ${btnBlock}
        </div>
    `;
    rightPanel.attachHTML(infoHtml);

    // 【优化修复】删除按钮事件
    const delBtn = document.getElementById("del_btn");
    if (delBtn && DATA.isOwner) { // 前端多加一层权限卡点
        delBtn.onclick = function () {
            if (confirm("确定删除该物品？")) {
                const form = document.createElement("form");
                form.action = DATA.delUrl;
                form.method = "POST";
                
                // 采用标准 HTML 节点构建，用 DATA 传进来的具体值，替换掉失效的 Blade 指令
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${DATA.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                
                document.body.appendChild(form);
                form.submit();
            }
        };
    }
});
</script>
</x-app-layout>