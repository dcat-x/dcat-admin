<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;

class Department extends Model implements Sortable
{
    use HasDateTimeFormatter,
        ModelTree {
            ModelTree::boot as treeBoot;
        }

    protected $fillable = ['parent_id', 'path', 'name', 'code', 'order', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    protected $titleColumn = 'name';

    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);
        $this->setTable(config('admin.database.departments_table', 'admin_departments'));
    }

    /**
     * 部门下的用户
     */
    public function users(): BelongsToMany
    {
        $pivotTable = config('admin.database.department_users_table', 'admin_department_users');
        $relatedModel = config('admin.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'department_id', 'user_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * 部门关联的角色
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.department_roles_table', 'admin_department_roles');
        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'department_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * 获取所有下级部门 ID
     */
    public function getDescendantIds(): array
    {
        if (empty($this->path)) {
            return [];
        }

        return static::query()
            ->where('path', 'like', $this->path.'%')
            ->where('id', '!=', $this->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * 获取所有下级部门（含自身）
     */
    public function getDescendantsWithSelf()
    {
        return static::query()
            ->where('path', 'like', $this->path.'%')
            ->get();
    }

    /**
     * 是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status === 1;
    }

    /**
     * 更新路径
     */
    public function updatePath(): void
    {
        if ($this->parent_id == 0) {
            $this->path = '/'.$this->id.'/';
        } else {
            $parent = static::find($this->parent_id);
            $this->path = $parent ? $parent->path.$this->id.'/' : '/'.$this->id.'/';
        }
        $this->saveQuietly();
    }

    /**
     * 更新所有下级部门的路径
     */
    public function updateDescendantsPath(): void
    {
        $descendants = static::query()
            ->where('parent_id', $this->id)
            ->get();

        foreach ($descendants as $descendant) {
            $descendant->updatePath();
            $descendant->updateDescendantsPath();
        }
    }

    protected static function boot()
    {
        static::treeBoot();

        static::created(function ($model) {
            $model->updatePath();
        });

        static::updated(function ($model) {
            if ($model->isDirty('parent_id')) {
                $model->updatePath();
                $model->updateDescendantsPath();
            }
        });

        static::deleting(function ($model) {
            $model->users()->detach();
            $model->roles()->detach();
        });
    }
}
