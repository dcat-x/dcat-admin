<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;
use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Support\Concerns\ControlsLogEmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class DataPermission
{
    use ControlsLogEmission;

    /**
     * 可用条件列表.
     */
    protected const VALID_CONDITIONS = [
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

    /**
     * scope 规则缓存（实例级）
     *
     * @var array<string, Collection>
     */
    protected array $scopedRulesCache = [];

    /**
     * 用户角色ID缓存（实例级）
     */
    protected ?array $roleIdsCache = null;

    /**
     * 隐藏列缓存（实例级）.
     *
     * @var array<int, array<int, string>>
     */
    protected array $hiddenColumnsCache = [];

    /**
     * 隐藏表单字段缓存（实例级）.
     *
     * @var array<int, array<int, string>>
     */
    protected array $hiddenFormFieldsCache = [];

    /**
     * 隐藏列快速查找缓存（实例级）.
     *
     * @var array<int, array<string, bool>>
     */
    protected array $hiddenColumnsLookup = [];

    /**
     * 隐藏表单字段快速查找缓存（实例级）.
     *
     * @var array<int, array<string, bool>>
     */
    protected array $hiddenFormFieldsLookup = [];

    /**
     * 规则异常日志去重缓存（请求级）
     *
     * @var array<string, bool>
     */
    protected static array $ruleAnomalyReported = [];

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

        $roleIds = $this->resolveRoleIds();

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
        return $this->getScopedRules($menuId, 'row', function ($rule) {
            return $rule->isRowScope();
        });
    }

    /**
     * 获取列级权限规则
     */
    public function getColumnRules(int $menuId): Collection
    {
        return $this->getScopedRules($menuId, 'column', function ($rule) {
            return $rule->isColumnScope();
        });
    }

    /**
     * 获取表单字段权限规则
     */
    public function getFormRules(int $menuId): Collection
    {
        return $this->getScopedRules($menuId, 'form', function ($rule) {
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
            $this->reportRuleAnomaly('invalid_field', $rule, ['field' => $field]);

            return;
        }

        if (! in_array($condition, self::VALID_CONDITIONS, true)) {
            $this->reportRuleAnomaly('invalid_condition', $rule, ['condition' => $condition]);

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
                $values = is_array($value) ? $value : explode(',', (string) $value);
                $query->whereIn($field, $values);
                break;

            case DataRule::CONDITION_NOT_IN:
                $values = is_array($value) ? $value : explode(',', (string) $value);
                $query->whereNotIn($field, $values);
                break;

            case DataRule::CONDITION_BETWEEN:
                $values = is_array($value) ? $value : explode(',', (string) $value);
                if (count($values) >= 2) {
                    $query->whereBetween($field, [$values[0], $values[1]]);
                } else {
                    $this->reportRuleAnomaly('invalid_between_value', $rule, ['value' => $value]);
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
            $value = str_replace($var, (string) $replacement, $value);
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

        $departments = $this->user->departments()->get();

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
        if (array_key_exists($menuId, $this->hiddenColumnsCache)) {
            return $this->hiddenColumnsCache[$menuId];
        }

        $rules = $this->getColumnRules($menuId);

        return $this->hiddenColumnsCache[$menuId] = $rules->pluck('field')->toArray();
    }

    /**
     * 获取需要隐藏的表单字段
     */
    public function getHiddenFormFields(int $menuId): array
    {
        if (array_key_exists($menuId, $this->hiddenFormFieldsCache)) {
            return $this->hiddenFormFieldsCache[$menuId];
        }

        $rules = $this->getFormRules($menuId);

        return $this->hiddenFormFieldsCache[$menuId] = $rules->pluck('field')->toArray();
    }

    /**
     * 检查字段是否有访问权限
     */
    public function canAccessColumn(int $menuId, string $field): bool
    {
        if (! array_key_exists($menuId, $this->hiddenColumnsLookup)) {
            $this->hiddenColumnsLookup[$menuId] = array_fill_keys($this->getHiddenColumns($menuId), true);
        }

        return ! isset($this->hiddenColumnsLookup[$menuId][$field]);
    }

    /**
     * 检查表单字段是否有访问权限
     */
    public function canAccessFormField(int $menuId, string $field): bool
    {
        if (! array_key_exists($menuId, $this->hiddenFormFieldsLookup)) {
            $this->hiddenFormFieldsLookup[$menuId] = array_fill_keys($this->getHiddenFormFields($menuId), true);
        }

        return ! isset($this->hiddenFormFieldsLookup[$menuId][$field]);
    }

    /**
     * 清除缓存
     */
    public static function clearCache(): void
    {
        static::$rulesCache = [];
        static::$ruleAnomalyReported = [];
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
            static::$ruleAnomalyReported = [];
            static::$cacheRequestHash = $requestHash;
        }
    }

    protected function reportRuleAnomaly(string $type, DataRule $rule, array $extra = []): void
    {
        $cacheKey = $type.'_'.$rule->id;

        if (isset(static::$ruleAnomalyReported[$cacheKey])) {
            return;
        }

        static::$ruleAnomalyReported[$cacheKey] = true;

        $path = App::bound('request') ? '/'.ltrim((string) request()->path(), '/') : null;
        if (! $this->shouldEmitLog('data_permission', $path)) {
            return;
        }

        Log::warning('admin.data_permission.rule_anomaly', array_merge([
            'type' => $type,
            'trace_id' => $this->resolveTraceId(),
            'rule_id' => $rule->id,
            'menu_id' => $rule->menu_id,
            'field' => $rule->field,
            'condition' => $rule->condition,
            'user_id' => $this->user->id ?? null,
        ], $extra));
    }

    protected function resolveRoleIds(): array
    {
        if ($this->roleIdsCache !== null) {
            return $this->roleIdsCache;
        }

        $roles = method_exists($this->user, 'allRoles')
            ? $this->user->allRoles()
            : $this->user->roles;

        return $this->roleIdsCache = $roles->pluck('id')->unique()->values()->all();
    }

    protected function getScopedRules(int $menuId, string $scope, callable $resolver): Collection
    {
        $key = $menuId.'_'.$scope;

        if (isset($this->scopedRulesCache[$key])) {
            return $this->scopedRulesCache[$key];
        }

        return $this->scopedRulesCache[$key] = $this->getRulesForMenu($menuId)
            ->filter($resolver)
            ->values();
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
