<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract, Authorizable
{
    use Authenticatable,
        HasDateTimeFormatter,
        HasPermissions;

    const DEFAULT_ID = 1;

    protected $fillable = ['username', 'password', 'name', 'avatar'];

    /**
     * Create a new Eloquent model instance.
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

        $this->setTable(config('admin.database.users_table'));
    }

    /**
     * Get avatar attribute.
     *
     * @return mixed|string
     */
    public function getAvatar()
    {
        $avatar = $this->avatar;

        if ($avatar) {
            if (! URL::isValidUrl($avatar)) {
                $avatar = Storage::disk(config('admin.upload.disk'))->url($avatar);
            }

            return $avatar;
        }

        return admin_asset(config('admin.default_avatar') ?: '@admin/images/default-avatar.jpg');
    }

    /**
     * A user has and belongs to many roles.
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 用户所属的部门（多对多）
     */
    public function departments(): BelongsToMany
    {
        $pivotTable = config('admin.database.department_users_table', 'admin_department_users');
        $relatedModel = config('admin.database.departments_model', Department::class);

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'department_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * 用户的主部门
     */
    public function primaryDepartment()
    {
        return $this->departments()->wherePivot('is_primary', 1);
    }

    /**
     * 获取用户的主部门 ID
     */
    public function getPrimaryDepartmentIdAttribute(): ?int
    {
        $primary = $this->primaryDepartment()->first();

        return $primary ? $primary->id : null;
    }

    /**
     * 获取用户所有部门的 ID（逗号分隔）
     */
    public function getDepartmentIdsAttribute(): string
    {
        return $this->departments()->pluck('id')->implode(',');
    }

    /**
     * 获取通过部门继承的角色
     */
    public function getDepartmentRoles()
    {
        if (! config('admin.department.inherit_department_roles', true)) {
            return collect();
        }

        $departmentIds = $this->departments()->pluck('id')->toArray();

        if (empty($departmentIds)) {
            return collect();
        }

        $roleModel = config('admin.database.roles_model');
        $pivotTable = config('admin.database.department_roles_table', 'admin_department_roles');

        return $roleModel::query()
            ->join($pivotTable, 'role_id', '=', 'id')
            ->whereIn('department_id', $departmentIds)
            ->get();
    }

    /**
     * 获取所有角色（直接角色 + 部门继承角色）
     */
    public function allRoles()
    {
        $directRoles = $this->roles;
        $departmentRoles = $this->getDepartmentRoles();

        return $directRoles->merge($departmentRoles)->unique('id');
    }

    /**
     * 判断是否允许查看菜单.
     *
     * @param  array|Menu  $menu
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        return true;
    }
}
