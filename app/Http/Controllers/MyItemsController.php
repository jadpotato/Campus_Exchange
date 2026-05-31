<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ItemServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyItemsController extends Controller
{
    public function __construct(
        protected ItemServiceInterface $itemService
    ) {}

    /**
     * 我的物品列表
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status']);
        
        $items = $this->itemService->getUserItems(
            $request->user(),
            $filters
        );

        return view('my.items', compact('items', 'filters'));
    }
}