<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasDateTimeFormatter;

    const ADMINISTRATOR = 'administrator';

    const ADMINISTRATOR_ID = 1;

    protected $fillable = ['name', 'slug'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.roles_table'));
    }

    /**
     * A role belongs to many users.
     */
    public function administrators(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id')->withTimestamps();
    }

    public function menus(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id')->withTimestamps();
    }

    /**
     * 关联此角色的部门
     */
    public function departments(): BelongsToMany
    {
        $pivotTable = config('admin.database.department_roles_table', 'admin_department_roles');
        $relatedModel = config('admin.database.departments_model', Department::class);

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'department_id')
            ->withTimestamps();
    }

    /**
     * 关联数据规则
     */
    public function dataRules(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_data_rules_table', 'admin_role_data_rules');
        $relatedModel = config('admin.database.data_rules_model', DataRule::class);

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'data_rule_id')
            ->withTimestamps();
    }

    /**
     * Check user has permission.
     */
    public function can(?string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     */
    public function cannot(?string $permission): bool
    {
        return ! $this->can($permission);
    }

    /**
     * Get id of the permission by id.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPermissionId(array $roleIds)
    {
        if (! $roleIds) {
            return collect();
        }
        $related = config('admin.database.role_permissions_table');

        $model = new static;
        $keyName = $model->getKeyName();

        return $model->newQuery()
            ->leftJoin($related, $keyName, '=', 'role_id')
            ->whereIn($keyName, $roleIds)
            ->get(['permission_id', 'role_id'])
            ->groupBy('role_id')
            ->map(function ($v) {
                $v = $v instanceof Arrayable ? $v->toArray() : $v;

                return array_column($v, 'permission_id');
            });
    }

    /**
     * @return bool
     */
    public static function isAdministrator(?string $slug)
    {
        return $slug === static::ADMINISTRATOR;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->administrators()->detach();

            $model->permissions()->detach();

            if (config('admin.department.enable', false)) {
                $model->departments()->detach();
            }

            if (config('admin.data_permission.enable', false)) {
                $model->dataRules()->detach();
            }
        });
    }
}
