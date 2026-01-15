<?php

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Admin;
use Dcat\Admin\Models\DataRule;
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

        // 移除 /create, /edit, /{id} 等路径部分
        $path = preg_replace('#/(create|edit|\d+)$#', '', ltrim($path, '/'));

        // 查找匹配的菜单
        $menuModel = config('admin.database.menu_model');

        if (! class_exists($menuModel)) {
            return null;
        }

        $menu = $menuModel::where('uri', $path)->first();

        return $menu ? $menu->id : null;
    }

    /**
     * 应用表单字段数据权限
     */
    public function applyFormFieldPermissions(): self
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
        $rules = $dataPermission->getFormRules($menuId);

        foreach ($rules as $rule) {
            $this->applyFieldRule($rule);
        }

        return $this;
    }

    /**
     * 应用单个字段规则
     */
    protected function applyFieldRule(DataRule $rule): void
    {
        $fieldName = $rule->field;

        // 查找字段
        $field = $this->findFieldByColumn($fieldName);

        if (! $field) {
            return;
        }

        switch ($rule->condition) {
            case 'hide':
                $field->hide();
                break;

            case 'disabled':
                $field->disable();
                break;

            case 'readonly':
                $field->readOnly();
                break;
        }
    }

    /**
     * 根据列名查找字段
     */
    protected function findFieldByColumn(string $column)
    {
        foreach ($this->fields() as $field) {
            if ($field->column() === $column) {
                return $field;
            }
        }

        return null;
    }

    /**
     * 启用数据权限
     */
    public function withDataPermission(?int $menuId = null): self
    {
        if ($menuId !== null) {
            $this->setDataPermissionMenuId($menuId);
        }

        // 在表单构建后应用权限
        $this->built(function () {
            $this->applyFormFieldPermissions();
        });

        return $this;
    }
}
