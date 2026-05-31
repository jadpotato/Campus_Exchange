<?php

namespace App\Providers;

use App\Models\Item;
use App\Policies\ItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * 模型与权限策略的映射关系
     * 所有需要权限控制的模型都在这里注册对应的策略类
     */
    protected $policies = [
        Item::class => ItemPolicy::class, // 物品模型 → 物品权限策略
    ];

    /**
     * 注册任何认证/授权服务
     */
    public function boot(): void
    {
        // 自动注册所有上面定义的权限策略
        $this->registerPolicies();
    }
}