<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Services\Interfaces\ItemServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ItemService implements ItemServiceInterface
{
    public function createItem(array $data, User $user): Item
    {
        // 处理图片上传
        if (isset($data['photos']) && is_array($data['photos'])) {
            $photoPaths = [];
            foreach ($data['photos'] as $photo) {
                $path = $photo->store('items', 'public');
                $photoPaths[] = $path;
            }
            $data['photos'] = $photoPaths;
        }

        // 免费模式价格设为0
        if ($data['trade_type'] === 'free') {
            $data['price'] = 0;
        }

        // 换物模式清空价格
        if ($data['trade_type'] === 'exchange') {
            $data['price'] = null;
        }

        // ✅ 新增：默认设为待审核状态（对齐你的状态机）
        $data['status'] = 'pending_approval';

        return $user->items()->create($data);
    }

    public function updateItem(Item $item, array $data): Item
    {
        // 已锁定/已完成的物品不能修改交易模式和价格
        if (!$item->canBeEdited()) {
            unset($data['trade_type'], $data['price']);
        }

        // 处理新增图片
        if (isset($data['photos']) && is_array($data['photos'])) {
            // 保留原有图片
            $existingPhotos = $item->photos ?? [];
            
            foreach ($data['photos'] as $photo) {
                if (is_file($photo)) {
                    $path = $photo->store('items', 'public');
                    $existingPhotos[] = $path;
                }
            }
            
            $data['photos'] = array_slice($existingPhotos, 0, 5); // 最多5张
        }

        // 免费模式价格设为0
        if (isset($data['trade_type']) && $data['trade_type'] === 'free') {
            $data['price'] = 0;
        }

        // 换物模式清空价格
        if (isset($data['trade_type']) && $data['trade_type'] === 'exchange') {
            $data['price'] = null;
        }

        $item->update($data);
        return $item;
    }

    public function deleteItem(Item $item): void
    {
        // 删除关联图片
        if ($item->photos) {
            foreach ($item->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $item->delete();
    }

    public function changeStatus(Item $item, string $status): Item
    {
        // 状态机验证
        $allowedTransitions = [
            'pending_approval' => ['published', 'cancelled'],
            'published' => ['locked', 'unpublished', 'completed', 'cancelled'],
            'locked' => ['completed', 'cancelled', 'published'],
            'unpublished' => ['published', 'cancelled'],
        ];

        if (!isset($allowedTransitions[$item->status]) || 
            !in_array($status, $allowedTransitions[$item->status])) {
            throw new \InvalidArgumentException("非法状态变更：{$item->status} → {$status}");
        }

        $item->update(['status' => $status]);
        return $item;
    }

    public function getPublishedItems(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Item::where('status', 'published')
            ->with('user')
            ->latest();

        // 应用筛选条件
        if (isset($filters['keyword']) && !empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['keyword']}%")
                  ->orWhere('description', 'like', "%{$filters['keyword']}%");
            });
        }

        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['trade_type']) && !empty($filters['trade_type'])) {
            $query->where('trade_type', $filters['trade_type']);
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // 应用排序
        if (isset($filters['sort_by'])) {
            $direction = $filters['sort_dir'] ?? 'desc';
            $query->orderBy($filters['sort_by'], $direction);
        }

        return $query->paginate($perPage);
    }

    public function getUserItems(User $user, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $user->items()
            ->latest();

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function incrementViews(Item $item): void
    {
        $item->increment('view_count');
    }

    /**
     * 获取所有物品（后台管理专用）
     */
    public function getAllItems(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Item::with('user')
            ->latest();

        // 按状态筛选
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

}