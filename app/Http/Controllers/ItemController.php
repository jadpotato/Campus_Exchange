<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\Interfaces\ItemServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ItemServiceInterface $itemService
    ) {}

    /**
     * 物品列表页
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'keyword', 'category', 'trade_type',
            'min_price', 'max_price', 'sort_by', 'sort_dir'
        ]);

        $items = $this->itemService->getPublishedItems($filters);

        return view('items.index', compact('items', 'filters'));
    }

    /**
     * 物品发布页
     */
    public function create(): View
    {
        return view('items.create');
    }

    /**
     * 保存新物品
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'trade_type' => ['required', 'in:sell,exchange,free'],
            'category' => ['required', 'string', 'max:50'],
            'desired_item' => ['nullable', 'string', 'max:200'],
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $item = $this->itemService->createItem(
            $request->all(),
            $request->user()
        );

        return redirect()->route('items.show', $item)
            ->with('status', '物品发布成功，等待审核！');
    }

    /**
     * 物品详情页
     */
    public function show(Item $item): View
    {
        if ($item->status === 'published') {
            $this->itemService->incrementViews($item);
        }

        return view('items.show', compact('item'));
    }

    /**
     * 物品编辑页
     */
    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    /**
     * 更新物品
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'trade_type' => ['required', 'in:sell,exchange,free'],
            'category' => ['required', 'string', 'max:50'],
            'desired_item' => ['nullable', 'string', 'max:200'],
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $this->itemService->updateItem($item, $request->all());

        return redirect()->route('items.show', $item)
            ->with('status', '物品更新成功！');
    }

    /**
     * 删除物品
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        $this->itemService->deleteItem($item);

        return redirect()->route('my.items')
            ->with('status', '物品删除成功！');
    }

    /**
     * DHTMLX Grid 专用 API 接口
     */
    public function apiIndex(Request $request)
    {
        $filters = $request->only([
            'keyword', 'category', 'trade_type',
            'min_price', 'max_price'
        ]);

        // DHTMLX 自动传递的分页参数
        $perPage = $request->input('count', 20);
        $page = $request->input('start', 0) / $perPage + 1;

        // DHTMLX 自动传递的排序参数
        if ($request->has('sort_by')) {
            $filters['sort_by'] = $request->input('sort_by');
            $filters['sort_dir'] = $request->input('sort_dir', 'desc');
        }

        $items = $this->itemService->getPublishedItems($filters, $perPage);

        // 转换为 DHTMLX 要求的格式
        return response()->json([
            'data' => $items->items(),
            'total' => $items->total()
        ]);
    }
}