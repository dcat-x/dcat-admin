<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
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
     * 部门角色缓存
     */
    protected ?Collection $departmentRolesCache = null;

    /**
     * 所有角色缓存（直接角色 + 部门角色）
     */
    protected ?Collection $allRolesCache = null;

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
        if (! config('admin.department.enable', false)) {
            return null;
        }

        $primary = $this->primaryDepartment()->first();

        return $primary ? $primary->id : null;
    }

    /**
     * 获取用户所有部门的 ID（逗号分隔）
     */
    public function getDepartmentIdsAttribute(): string
    {
        if (! config('admin.department.enable', false)) {
            return '';
        }

        return implode(',', $this->resolveDepartmentIds());
    }

    /**
     * 获取通过部门继承的角色
     */
    public function getDepartmentRoles()
    {
        if ($this->departmentRolesCache !== null) {
            return $this->departmentRolesCache;
        }

        // 部门功能未启用或不继承部门角色时，返回空集合
        if (! config('admin.department.enable', false) || ! config('admin.department.inherit_department_roles', true)) {
            return $this->departmentRolesCache = collect();
        }

        $departmentIds = $this->resolveDepartmentIds();

        if (empty($departmentIds)) {
            return $this->departmentRolesCache = collect();
        }

        return $this->departmentRolesCache = $this->queryDepartmentRolesByIds($departmentIds);
    }

    /**
     * 获取所有角色（直接角色 + 部门继承角色）
     */
    public function allRoles()
    {
        if ($this->allRolesCache !== null) {
            return $this->allRolesCache;
        }

        $directRoles = $this->roles;
        $departmentRoles = $this->getDepartmentRoles();

        return $this->allRolesCache = $directRoles->merge($departmentRoles)->unique('id');
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

    protected function resolveDepartmentIds(): array
    {
        return $this->departments()->pluck('id')->toArray();
    }

    protected function queryDepartmentRolesByIds(array $departmentIds): Collection
    {
        $roleModel = config('admin.database.roles_model');
        $pivotTable = config('admin.database.department_roles_table', 'admin_department_roles');

        return $roleModel::query()
            ->join($pivotTable, 'role_id', '=', 'id')
            ->whereIn('department_id', $departmentIds)
            ->get();
    }
}
