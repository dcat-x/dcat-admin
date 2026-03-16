<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Pagination\AbstractPaginator;

class Administrator extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = config('admin.database.users_model');

        parent::__construct($relations);
    }

    public function get(Grid\Model $model)
    {
        $results = parent::get($model);

        $isPaginator = $results instanceof AbstractPaginator;

        $items = $isPaginator ? $results->getCollection() : $results;
        $items = is_array($items) ? collect($items) : $items;

        if ($items->isEmpty()) {
            return $results;
        }

        $roleModel = config('admin.database.roles_model');

        $roleKeyName = (new $roleModel)->getKeyName();

        $roleIds = $this->collectRoleIds($items, $roleKeyName);

        $permissions = $roleModel::getPermissionId($roleIds);

        if (! $permissions->isEmpty()) {
            $items = $items->map(function ($v) use ($roleKeyName, $permissions) {
                $v['permissions'] = $this->collectPermissionsForRoles($v['roles'] ?? [], $roleKeyName, $permissions);

                return $v;
            });
        }

        if ($isPaginator) {
            $results->setCollection($items);

            return $results;
        }

        return $items;
    }

    /**
     * @param  iterable<mixed>  $items
     * @return array<int, int|string>
     */
    protected function collectRoleIds(iterable $items, string $roleKeyName): array
    {
        $roleIds = [];

        foreach ($items as $item) {
            $roles = $item['roles'] ?? [];

            foreach ($roles as $role) {
                $roleId = $this->extractRoleId($role, $roleKeyName);
                if ($roleId === null) {
                    continue;
                }

                $roleIds[$roleId] = true;
            }
        }

        return array_keys($roleIds);
    }

    /**
     * @param  iterable<mixed>  $roles
     * @param  mixed  $permissions
     * @return array<int, int|string>
     */
    protected function collectPermissionsForRoles(iterable $roles, string $roleKeyName, $permissions): array
    {
        $permissionSet = [];

        foreach ($roles as $role) {
            $roleId = $this->extractRoleId($role, $roleKeyName);
            if ($roleId === null) {
                continue;
            }

            foreach ((array) $permissions->get($roleId, []) as $permissionId) {
                $permissionSet[$permissionId] = true;
            }
        }

        return array_keys($permissionSet);
    }

    /**
     * @param  mixed  $role
     * @return int|string|null
     */
    protected function extractRoleId($role, string $roleKeyName)
    {
        if (is_array($role)) {
            return $role[$roleKeyName] ?? null;
        }

        return $role->{$roleKeyName} ?? null;
    }
}
