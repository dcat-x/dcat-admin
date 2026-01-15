<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\DataPermission;

trait HasDataPermission
{
    /**
     * 当前菜单ID（用于数据权限）
     */
    protected $dataPermissionMenuId;

    /**
     * 设置数据权限菜单ID
     */
    public function setDataPermissionMenuId(?int $menuId): self
    {
        $this->dataPermissionMenuId = $menuId;

        return $this;
    }

    /**
     * 获取数据权限菜单ID
     */
    public function getDataPermissionMenuId(): ?int
    {
        if ($this->dataPermissionMenuId !== null) {
            return $this->dataPermissionMenuId;
        }

        // 尝试自动检测
        return $this->detectMenuIdFromRequest();
    }

    /**
     * 从请求中检测菜单ID
     */
    protected function detectMenuIdFromRequest(): ?int
    {
        $path = request()->path();
        $prefix = config('admin.route.prefix', 'admin');

        // 移除前缀
        if ($prefix && strpos($path, $prefix) === 0) {
            $path = substr($path, strlen($prefix));
        }

        $path = ltrim($path, '/');

        // 查找匹配的菜单
        $menuModel = config('admin.database.menu_model');

        if (! class_exists($menuModel)) {
            return null;
        }

        $menu = $menuModel::where('uri', $path)->first();

        return $menu ? $menu->id : null;
    }

    /**
     * 应用列级数据权限（隐藏受限列）
     */
    public function applyColumnPermissions(): self
    {
        if (! config('admin.data_permission.enable', true)) {
            return $this;
        }

        $user = Admin::user();

        if (! $user || $user->isAdministrator()) {
            return $this;
        }

        $menuId = $this->getDataPermissionMenuId();

        if (! $menuId) {
            return $this;
        }

        $dataPermission = DataPermission::make($user);
        $hiddenColumns = $dataPermission->getHiddenColumns($menuId);

        foreach ($hiddenColumns as $columnName) {
            if (isset($this->allColumns[$columnName])) {
                $this->allColumns[$columnName]->hide();
            }
        }

        return $this;
    }

    /**
     * 启用数据权限
     */
    public function withDataPermission(?int $menuId = null): self
    {
        if ($menuId !== null) {
            $this->setDataPermissionMenuId($menuId);
        }

        $this->applyColumnPermissions();

        return $this;
    }
}
