<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

trait HasPermissions
{
    protected $allPermissions;

    /**
     * Get all permissions of user.
     *
     * @return mixed
     */
    public function allPermissions(): Collection
    {
        if ($this->allPermissions) {
            return $this->allPermissions;
        }

        // 获取所有角色（直接 + 部门继承）
        $allRoles = method_exists($this, 'allRoles') ? $this->allRoles() : $this->roles;

        return $this->allPermissions =
            $allRoles
                ->pluck('permissions')
                ->flatten()
                ->keyBy($this->getKeyName());
    }

    /**
     * Check if user has permission.
     *
     * @param  array|mixed  $arguments
     */
    public function can($ability, $paramters = []): bool
    {
        if (! $ability) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        $permissions = $this->allPermissions();

        return $permissions->pluck('slug')->contains($ability) ?:
            $permissions
                ->pluck('id')
                ->contains($ability);
    }

    /**
     * Check if user has no permission.
     */
    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator(): bool
    {
        $roleModel = config('admin.database.roles_model');

        return $this->isRole($roleModel::ADMINISTRATOR);
    }

    /**
     * Check if user is $role.
     *
     * @return mixed
     */
    public function isRole(string $role): bool
    {
        /* @var Collection $roles */
        $roles = $this->roles;

        return $roles->pluck('slug')->contains($role) ?:
            $roles->pluck('id')->contains($role);
    }

    /**
     * Check if user in $roles.
     *
     * @param  string|array|Arrayable  $roles
     * @return mixed
     */
    public function inRoles($roles = []): bool
    {
        /* @var Collection $all */
        $all = $this->roles;

        $roles = Helper::array($roles);

        return $all->pluck('slug')->intersect($roles)->isNotEmpty() ?:
            $all->pluck('id')->intersect($roles)->isNotEmpty();
    }

    /**
     * If visible for roles.
     */
    public function visible($roles = []): bool
    {
        if (empty($roles)) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        return $this->inRoles($roles);
    }

    /**
     * 根据权限标识检查权限
     */
    public function canPermissionKey(string $key): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        $permissions = $this->allPermissions();

        return $permissions->contains(function ($permission) use ($key) {
            return $permission->permission_key === $key;
        });
    }

    /**
     * 获取指定菜单的数据规则
     */
    public function getDataRules(int $menuId)
    {
        if (! config('admin.data_permission.enable', false)) {
            return collect();
        }

        $roleIds = method_exists($this, 'allRoles')
            ? $this->allRoles()->pluck('id')->toArray()
            : $this->roles->pluck('id')->toArray();

        if (empty($roleIds)) {
            return collect();
        }

        $dataRuleModel = config('admin.database.data_rules_model', \Dcat\Admin\Models\DataRule::class);
        $pivotTable = config('admin.database.role_data_rules_table', 'admin_role_data_rules');

        return $dataRuleModel::query()
            ->join($pivotTable, 'data_rule_id', '=', 'id')
            ->whereIn('role_id', $roleIds)
            ->where('menu_id', $menuId)
            ->where('status', 1)
            ->orderBy('order')
            ->get();
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }
}
