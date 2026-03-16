<?php

declare(strict_types=1);

namespace Dcat\Admin\Traits;

use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

trait HasPermissions
{
    protected $allPermissions;

    /**
     * 权限标识缓存
     */
    protected ?array $permissionSlugs = null;

    /**
     * 权限ID缓存
     */
    protected ?array $permissionIds = null;

    /**
     * permission_key 缓存
     */
    protected ?array $permissionKeys = null;

    /**
     * 用户角色集合缓存
     *
     * @var Collection|array|null
     */
    protected $userRolesCache = null;

    /**
     * 用户角色 slug 缓存
     */
    protected ?array $userRoleSlugs = null;

    /**
     * 用户角色 id 缓存
     */
    protected ?array $userRoleIds = null;

    /**
     * 用户角色 id（字符串）缓存
     */
    protected ?array $userRoleIdStrings = null;

    /**
     * Get all permissions of user.
     */
    public function allPermissions(): Collection
    {
        if ($this->allPermissions) {
            return $this->allPermissions;
        }

        // 获取所有角色（直接 + 部门继承）
        $allRoles = method_exists($this, 'allRoles')
            ? $this->allRoles()
            : $this->roles;

        if ($allRoles instanceof EloquentCollection) {
            $allRoles->loadMissing('permissions');
        }

        $allRoles = $allRoles instanceof Collection ? $allRoles : collect($allRoles);

        return $this->allPermissions =
            $allRoles
                ->pluck('permissions')
                ->flatten()
                ->keyBy($this->getKeyName());
    }

    /**
     * Check if user has permission.
     */
    public function can($ability, $paramters = []): bool
    {
        if (! $ability) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        return in_array($ability, $this->getPermissionSlugs(), true)
            || in_array($ability, $this->getPermissionIds(), true);
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
     */
    public function isAdministrator(): bool
    {
        $roleModel = config('admin.database.roles_model');

        return $this->isRole($roleModel::ADMINISTRATOR);
    }

    /**
     * Check if user is $role.
     */
    public function isRole(string $role): bool
    {
        return in_array($role, $this->getUserRoleSlugs(), true)
            || in_array((string) $role, $this->getUserRoleIdStrings(), true);
    }

    /**
     * Check if user in $roles.
     *
     * @param  string|array|Arrayable  $roles
     */
    public function inRoles($roles = []): bool
    {
        $roles = Helper::array($roles);
        $roleStrings = array_map('strval', $roles);

        return ! empty(array_intersect($this->getUserRoleSlugs(), $roles))
            || ! empty(array_intersect($this->getUserRoleIdStrings(), $roleStrings));
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

        return in_array($key, $this->getPermissionKeys(), true);
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
            ? collect($this->allRoles())->pluck('id')->toArray()
            : collect($this->roles)->pluck('id')->toArray();

        if (empty($roleIds)) {
            return collect();
        }

        $dataRuleModel = config('admin.database.data_rules_model', DataRule::class);
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

    protected function getPermissionSlugs(): array
    {
        if ($this->permissionSlugs !== null) {
            return $this->permissionSlugs;
        }

        return $this->permissionSlugs = $this->allPermissions()->pluck('slug')->all();
    }

    protected function getPermissionIds(): array
    {
        if ($this->permissionIds !== null) {
            return $this->permissionIds;
        }

        return $this->permissionIds = $this->allPermissions()->pluck('id')->all();
    }

    /**
     * @return Collection|array
     */
    protected function getUserRoles()
    {
        if ($this->userRolesCache !== null) {
            return $this->userRolesCache;
        }

        if (method_exists($this, 'allRoles')) {
            return $this->userRolesCache = collect($this->allRoles());
        }

        return $this->userRolesCache = $this->roles;
    }

    protected function getPermissionKeys(): array
    {
        if ($this->permissionKeys !== null) {
            return $this->permissionKeys;
        }

        return $this->permissionKeys = $this->allPermissions()
            ->pluck('permission_key')
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            })
            ->all();
    }

    protected function getUserRoleSlugs(): array
    {
        if ($this->userRoleSlugs !== null) {
            return $this->userRoleSlugs;
        }

        return $this->userRoleSlugs = $this->getUserRoles()->pluck('slug')->all();
    }

    protected function getUserRoleIds(): array
    {
        if ($this->userRoleIds !== null) {
            return $this->userRoleIds;
        }

        return $this->userRoleIds = $this->getUserRoles()->pluck('id')->all();
    }

    protected function getUserRoleIdStrings(): array
    {
        if ($this->userRoleIdStrings !== null) {
            return $this->userRoleIdStrings;
        }

        return $this->userRoleIdStrings = array_map('strval', $this->getUserRoleIds());
    }
}
