<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataRule extends Model
{
    use HasDateTimeFormatter;

    // 作用域常量
    const SCOPE_ROW = 'row';       // 行级权限
    const SCOPE_COLUMN = 'column'; // 列级权限
    const SCOPE_FORM = 'form';     // 表单字段权限

    // 值类型常量
    const VALUE_TYPE_FIXED = 'fixed';       // 固定值
    const VALUE_TYPE_VARIABLE = 'variable'; // 变量值

    // 条件常量
    const CONDITION_EQUAL = '=';
    const CONDITION_NOT_EQUAL = '!=';
    const CONDITION_GREATER = '>';
    const CONDITION_GREATER_EQUAL = '>=';
    const CONDITION_LESS = '<';
    const CONDITION_LESS_EQUAL = '<=';
    const CONDITION_LIKE = 'like';
    const CONDITION_IN = 'in';
    const CONDITION_NOT_IN = 'not_in';
    const CONDITION_BETWEEN = 'between';

    protected $fillable = [
        'menu_id',
        'name',
        'field',
        'condition',
        'value',
        'value_type',
        'scope',
        'status',
        'order',
    ];

    protected $casts = [
        'menu_id' => 'integer',
        'status' => 'integer',
        'order' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.data_rules_table', 'admin_data_rules'));

        parent::__construct($attributes);
    }

    /**
     * 关联菜单
     */
    public function menu(): BelongsTo
    {
        $menuModel = config('admin.database.menu_model');

        return $this->belongsTo($menuModel, 'menu_id');
    }

    /**
     * 关联角色
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('admin.database.roles_model');
        $pivotTable = config('admin.database.role_data_rules_table', 'admin_role_data_rules');

        return $this->belongsToMany($roleModel, $pivotTable, 'data_rule_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * 检查是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status === 1;
    }

    /**
     * 检查是否为行级权限
     */
    public function isRowScope(): bool
    {
        return $this->scope === self::SCOPE_ROW;
    }

    /**
     * 检查是否为列级权限
     */
    public function isColumnScope(): bool
    {
        return $this->scope === self::SCOPE_COLUMN;
    }

    /**
     * 检查是否为表单字段权限
     */
    public function isFormScope(): bool
    {
        return $this->scope === self::SCOPE_FORM;
    }

    /**
     * 检查是否为变量值
     */
    public function isVariableValue(): bool
    {
        return $this->value_type === self::VALUE_TYPE_VARIABLE;
    }

    /**
     * 获取所有条件选项
     */
    public static function getConditionOptions(): array
    {
        return [
            self::CONDITION_EQUAL => '等于 (=)',
            self::CONDITION_NOT_EQUAL => '不等于 (!=)',
            self::CONDITION_GREATER => '大于 (>)',
            self::CONDITION_GREATER_EQUAL => '大于等于 (>=)',
            self::CONDITION_LESS => '小于 (<)',
            self::CONDITION_LESS_EQUAL => '小于等于 (<=)',
            self::CONDITION_LIKE => '包含 (LIKE)',
            self::CONDITION_IN => '在列表中 (IN)',
            self::CONDITION_NOT_IN => '不在列表中 (NOT IN)',
            self::CONDITION_BETWEEN => '区间 (BETWEEN)',
        ];
    }

    /**
     * 获取所有作用域选项
     */
    public static function getScopeOptions(): array
    {
        return [
            self::SCOPE_ROW => trans('admin.data_rule.scope_row'),
            self::SCOPE_COLUMN => trans('admin.data_rule.scope_column'),
            self::SCOPE_FORM => trans('admin.data_rule.scope_form'),
        ];
    }

    /**
     * 获取值类型选项
     */
    public static function getValueTypeOptions(): array
    {
        return [
            self::VALUE_TYPE_FIXED => trans('admin.data_rule.value_type_fixed'),
            self::VALUE_TYPE_VARIABLE => trans('admin.data_rule.value_type_variable'),
        ];
    }

    /**
     * 获取系统变量列表
     */
    public static function getSystemVariables(): array
    {
        return [
            '{user_id}' => trans('admin.data_rule.var_user_id'),
            '{username}' => trans('admin.data_rule.var_username'),
            '{department_id}' => trans('admin.data_rule.var_department_id'),
            '{department_ids}' => trans('admin.data_rule.var_department_ids'),
            '{department_path}' => trans('admin.data_rule.var_department_path'),
        ];
    }

    /**
     * 按菜单ID获取规则
     */
    public static function getByMenuId(int $menuId)
    {
        return static::where('menu_id', $menuId)
            ->where('status', 1)
            ->orderBy('order')
            ->get();
    }
}
