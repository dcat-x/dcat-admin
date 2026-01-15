<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\DataPermission;
use Illuminate\Database\Eloquent\Builder;

trait HasDataPermission
{
    /**
     * 数据权限处理器
     */
    protected static $dataPermission;

    /**
     * 当前菜单ID（用于数据权限）
     */
    protected static $currentMenuId;

    /**
     * 是否启用数据权限
     */
    protected static $dataPermissionEnabled = true;

    /**
     * 启动 trait
     */
    public static function bootHasDataPermission()
    {
        // 添加全局作用域
        static::addGlobalScope('data_permission', function (Builder $builder) {
            if (! static::$dataPermissionEnabled) {
                return;
            }

            $menuId = static::getCurrentMenuId();

            if (! $menuId) {
                return;
            }

            $user = Admin::user();

            // 超级管理员跳过数据权限
            if ($user && $user->isAdministrator()) {
                return;
            }

            // 应用行级数据权限
            DataPermission::make($user)->applyRowRules($builder, $menuId);
        });
    }

    /**
     * 设置当前菜单ID
     */
    public static function setCurrentMenuId(?int $menuId): void
    {
        static::$currentMenuId = $menuId;
    }

    /**
     * 获取当前菜单ID
     */
    public static function getCurrentMenuId(): ?int
    {
        if (static::$currentMenuId !== null) {
            return static::$currentMenuId;
        }

        // 尝试从请求中获取当前菜单
        return static::detectMenuIdFromRequest();
    }

    /**
     * 从请求中检测菜单ID
     */
    protected static function detectMenuIdFromRequest(): ?int
    {
        $path = request()->path();
        $prefix = config('admin.route.prefix', 'admin');

        // 移除前缀
        if ($prefix && strpos($path, $prefix) === 0) {
            $path = substr($path, strlen($prefix));
        }

        $path = '/' . ltrim($path, '/');

        // 查找匹配的菜单
        $menuModel = config('admin.database.menu_model');

        if (! class_exists($menuModel)) {
            return null;
        }

        $menu = $menuModel::where('uri', ltrim($path, '/'))
            ->orWhere('uri', $path)
            ->first();

        return $menu ? $menu->id : null;
    }

    /**
     * 启用数据权限
     */
    public static function enableDataPermission(): void
    {
        static::$dataPermissionEnabled = true;
    }

    /**
     * 禁用数据权限
     */
    public static function disableDataPermission(): void
    {
        static::$dataPermissionEnabled = false;
    }

    /**
     * 临时禁用数据权限执行回调
     */
    public static function withoutDataPermission(callable $callback)
    {
        $previousState = static::$dataPermissionEnabled;
        static::$dataPermissionEnabled = false;

        try {
            return $callback();
        } finally {
            static::$dataPermissionEnabled = $previousState;
        }
    }

    /**
     * 使用指定菜单ID执行回调
     */
    public static function withMenuId(int $menuId, callable $callback)
    {
        $previousMenuId = static::$currentMenuId;
        static::$currentMenuId = $menuId;

        try {
            return $callback();
        } finally {
            static::$currentMenuId = $previousMenuId;
        }
    }

    /**
     * 获取需要隐藏的列
     */
    public static function getHiddenColumns(): array
    {
        $menuId = static::getCurrentMenuId();

        if (! $menuId) {
            return [];
        }

        return DataPermission::make()->getHiddenColumns($menuId);
    }

    /**
     * 获取需要隐藏的表单字段
     */
    public static function getHiddenFormFields(): array
    {
        $menuId = static::getCurrentMenuId();

        if (! $menuId) {
            return [];
        }

        return DataPermission::make()->getHiddenFormFields($menuId);
    }

    /**
     * 检查列是否可访问
     */
    public static function canAccessColumn(string $field): bool
    {
        $menuId = static::getCurrentMenuId();

        if (! $menuId) {
            return true;
        }

        return DataPermission::make()->canAccessColumn($menuId, $field);
    }

    /**
     * 检查表单字段是否可访问
     */
    public static function canAccessFormField(string $field): bool
    {
        $menuId = static::getCurrentMenuId();

        if (! $menuId) {
            return true;
        }

        return DataPermission::make()->canAccessFormField($menuId, $field);
    }
}
