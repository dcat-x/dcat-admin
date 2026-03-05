<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;
use Dcat\Admin\Models\DataRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class DataPermission
{
    /**
     * 当前用户
     */
    protected ?object $user;

    /**
     * 用户所有部门ID缓存（实例级）
     */
    protected ?array $departmentIdsCache = null;

    /**
     * 主部门缓存（实例级）
     */
    protected $primaryDepartmentCache;

    /**
     * 主部门是否已解析
     */
    protected bool $primaryDepartmentResolved = false;

    /**
     * 数据规则缓存（请求级别）
     */
    protected static $rulesCache = [];

    /**
     * 缓存所属的请求哈希，用于检测跨请求
     */
    protected static $cacheRequestHash;

    public function __construct($user = null)
    {
        $this->user = $user ?: Admin::user();
    }

    /**
     * 获取用户对指定菜单的数据规则
     */
    public function getRulesForMenu(int $menuId): Collection
    {
        if (! $this->user) {
            return collect();
        }

        $this->initializeRequestCache();

        $cacheKey = $this->user->id.'_'.$menuId;

        if (isset(static::$rulesCache[$cacheKey])) {
            return static::$rulesCache[$cacheKey];
        }

        // 获取用户所有角色（包括从部门继承的角色）
        $roles = method_exists($this->user, 'allRoles')
            ? $this->user->allRoles()
            : $this->user->roles;

        $roleIds = $roles->pluck('id')->toArray();

        if (empty($roleIds)) {
            return static::$rulesCache[$cacheKey] = collect();
        }

        // 获取这些角色关联的数据规则
        $rules = DataRule::where('menu_id', $menuId)
            ->where('status', 1)
            ->whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds);
            })
            ->orderBy('order')
            ->get();

        return static::$rulesCache[$cacheKey] = $rules;
    }

    /**
     * 获取行级权限规则
     */
    public function getRowRules(int $menuId): Collection
    {
        return $this->getRulesForMenu($menuId)->filter(function ($rule) {
            return $rule->isRowScope();
        });
    }

    /**
     * 获取列级权限规则
     */
    public function getColumnRules(int $menuId): Collection
    {
        return $this->getRulesForMenu($menuId)->filter(function ($rule) {
            return $rule->isColumnScope();
        });
    }

    /**
     * 获取表单字段权限规则
     */
    public function getFormRules(int $menuId): Collection
    {
        return $this->getRulesForMenu($menuId)->filter(function ($rule) {
            return $rule->isFormScope();
        });
    }

    /**
     * 应用行级权限到查询
     */
    public function applyRowRules(Builder $query, int $menuId): Builder
    {
        $rules = $this->getRowRules($menuId);

        foreach ($rules as $rule) {
            $this->applyCondition($query, $rule);
        }

        return $query;
    }

    /**
     * 应用单个条件到查询
     */
    protected function applyCondition(Builder $query, DataRule $rule): void
    {
        $field = $rule->field;
        $condition = $rule->condition;
        $value = $this->resolveValue($rule);

        // 验证字段名仅包含合法的列名字符
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $field)) {
            return;
        }

        $validConditions = [
            DataRule::CONDITION_EQUAL,
            DataRule::CONDITION_NOT_EQUAL,
            DataRule::CONDITION_GREATER,
            DataRule::CONDITION_GREATER_EQUAL,
            DataRule::CONDITION_LESS,
            DataRule::CONDITION_LESS_EQUAL,
            DataRule::CONDITION_LIKE,
            DataRule::CONDITION_IN,
            DataRule::CONDITION_NOT_IN,
            DataRule::CONDITION_BETWEEN,
        ];

        if (! in_array($condition, $validConditions, true)) {
            return;
        }

        switch ($condition) {
            case DataRule::CONDITION_EQUAL:
                $query->where($field, '=', $value);
                break;

            case DataRule::CONDITION_NOT_EQUAL:
                $query->where($field, '!=', $value);
                break;

            case DataRule::CONDITION_GREATER:
                $query->where($field, '>', $value);
                break;

            case DataRule::CONDITION_GREATER_EQUAL:
                $query->where($field, '>=', $value);
                break;

            case DataRule::CONDITION_LESS:
                $query->where($field, '<', $value);
                break;

            case DataRule::CONDITION_LESS_EQUAL:
                $query->where($field, '<=', $value);
                break;

            case DataRule::CONDITION_LIKE:
                $query->where($field, 'like', '%'.$value.'%');
                break;

            case DataRule::CONDITION_IN:
                $values = is_array($value) ? $value : explode(',', $value);
                $query->whereIn($field, $values);
                break;

            case DataRule::CONDITION_NOT_IN:
                $values = is_array($value) ? $value : explode(',', $value);
                $query->whereNotIn($field, $values);
                break;

            case DataRule::CONDITION_BETWEEN:
                $values = is_array($value) ? $value : explode(',', $value);
                if (count($values) >= 2) {
                    $query->whereBetween($field, [$values[0], $values[1]]);
                }
                break;
        }
    }

    /**
     * 解析规则值（处理系统变量）
     */
    public function resolveValue(DataRule $rule)
    {
        $value = $rule->value;

        if (! $rule->isVariableValue()) {
            return $value;
        }

        // 替换系统变量
        return $this->replaceVariables($value);
    }

    /**
     * 替换系统变量
     */
    protected function replaceVariables($value)
    {
        if (! $this->user) {
            return $value;
        }

        $variables = [
            '{user_id}' => $this->user->id,
            '{username}' => $this->user->username,
            '{department_id}' => $this->getPrimaryDepartmentId(),
            '{department_ids}' => $this->getDepartmentIds(),
            '{department_path}' => $this->getPrimaryDepartmentPath(),
        ];

        foreach ($variables as $var => $replacement) {
            if (is_array($replacement)) {
                // 对于数组类型的变量（如 department_ids），返回数组
                if ($value === $var) {
                    return $replacement;
                }
                // 否则用逗号连接
                $replacement = implode(',', $replacement);
            }
            $value = str_replace($var, $replacement, $value);
        }

        return $value;
    }

    /**
     * 获取用户主部门ID
     */
    protected function getPrimaryDepartmentId()
    {
        if (! $this->canUsePrimaryDepartment()) {
            return null;
        }

        $department = $this->getPrimaryDepartment();

        return $department ? $department->id : null;
    }

    /**
     * 获取用户所有部门ID
     */
    protected function getDepartmentIds(): array
    {
        if (! $this->canUseDepartmentList()) {
            return [];
        }

        if ($this->departmentIdsCache !== null) {
            return $this->departmentIdsCache;
        }

        $departments = call_user_func([$this->user, 'departments'])->get();

        return $this->departmentIdsCache = $departments->pluck('id')->toArray();
    }

    /**
     * 获取用户主部门路径
     */
    protected function getPrimaryDepartmentPath()
    {
        if (! $this->canUsePrimaryDepartment()) {
            return null;
        }

        $department = $this->getPrimaryDepartment();

        return $department ? $department->path : null;
    }

    /**
     * 获取需要隐藏的列
     */
    public function getHiddenColumns(int $menuId): array
    {
        $rules = $this->getColumnRules($menuId);

        return $rules->pluck('field')->toArray();
    }

    /**
     * 获取需要隐藏的表单字段
     */
    public function getHiddenFormFields(int $menuId): array
    {
        $rules = $this->getFormRules($menuId);

        return $rules->pluck('field')->toArray();
    }

    /**
     * 检查字段是否有访问权限
     */
    public function canAccessColumn(int $menuId, string $field): bool
    {
        return ! in_array($field, $this->getHiddenColumns($menuId), true);
    }

    /**
     * 检查表单字段是否有访问权限
     */
    public function canAccessFormField(int $menuId, string $field): bool
    {
        return ! in_array($field, $this->getHiddenFormFields($menuId), true);
    }

    /**
     * 清除缓存
     */
    public static function clearCache(): void
    {
        static::$rulesCache = [];
    }

    /**
     * 创建新实例
     */
    public static function make($user = null): self
    {
        return new static($user);
    }

    /**
     * 获取主部门（带实例级缓存）
     */
    protected function getPrimaryDepartment()
    {
        if ($this->primaryDepartmentResolved) {
            return $this->primaryDepartmentCache;
        }

        $this->primaryDepartmentResolved = true;
        $this->primaryDepartmentCache = $this->user->primaryDepartment()->first();

        return $this->primaryDepartmentCache;
    }

    protected function initializeRequestCache(): void
    {
        $requestHash = spl_object_id(App::make('request'));
        if (static::$cacheRequestHash !== $requestHash) {
            static::$rulesCache = [];
            static::$cacheRequestHash = $requestHash;
        }
    }

    protected function canUsePrimaryDepartment(): bool
    {
        return config('admin.department.enable', false)
            && $this->user
            && method_exists($this->user, 'primaryDepartment');
    }

    protected function canUseDepartmentList(): bool
    {
        return config('admin.department.enable', false)
            && $this->user
            && method_exists($this->user, 'departments');
    }
}
