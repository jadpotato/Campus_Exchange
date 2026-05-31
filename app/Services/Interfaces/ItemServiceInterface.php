<?php

namespace App\Services\Interfaces;

use App\Models\Item;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface ItemServiceInterface
{
    public function createItem(array $data, User $user): Item;
    public function updateItem(Item $item, array $data): Item;
    public function deleteItem(Item $item): void;
    public function changeStatus(Item $item, string $status): Item;
    public function getPublishedItems(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getUserItems(User $user, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function incrementViews(Item $item): void;

    /**
     * 获取所有物品（后台管理专用）
     */
    public function getAllItems(array $filters = [], int $perPage = 20): LengthAwarePaginator;
}