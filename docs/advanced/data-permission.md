# 数据权限控制

数据权限提供了细粒度的数据访问控制能力，支持三个层级：

- **行级权限**：控制用户能看到哪些数据行
- **列级权限**：控制用户能看到哪些字段/列
- **表单字段权限**：控制表单中字段的可见性和可编辑性

## 启用数据权限

在配置文件 `config/admin.php` 中启用：

```php
'data_permission' => [
    'enable' => true,
],
```

## 数据规则

### 数据规则表 (admin_data_rules)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar | 规则名称 |
| menu_id | bigint | 关联菜单ID |
| scope | varchar | 作用域：row/column/form |
| field | varchar | 字段名 |
| condition | varchar | 条件：=/!=/>/<等 |
| value | text | 条件值 |
| value_type | varchar | 值类型：fixed/variable |
| action | varchar | 表单操作：hide/disable/readonly |
| status | tinyint | 状态：1启用，0禁用 |

### 规则作用域

| 作用域 | 常量 | 说明 |
|--------|------|------|
| 行级 | `DataRule::SCOPE_ROW` | 过滤数据行 |
| 列级 | `DataRule::SCOPE_COLUMN` | 隐藏表格列 |
| 表单 | `DataRule::SCOPE_FORM` | 控制表单字段 |

### 条件操作符

| 操作符 | 说明 |
|--------|------|
| `=` | 等于 |
| `!=` | 不等于 |
| `>` | 大于 |
| `<` | 小于 |
| `>=` | 大于等于 |
| `<=` | 小于等于 |
| `in` | 在列表中 |
| `not_in` | 不在列表中 |
| `like` | 模糊匹配 |
| `is_null` | 为空 |
| `is_not_null` | 不为空 |

### 值类型

| 类型 | 常量 | 说明 |
|------|------|------|
| 固定值 | `DataRule::VALUE_TYPE_FIXED` | 使用固定的值 |
| 变量 | `DataRule::VALUE_TYPE_VARIABLE` | 使用系统变量 |

### 系统变量

数据规则支持以下系统变量，在运行时自动替换为当前用户的值：

| 变量 | 说明 |
|------|------|
| `{user_id}` | 当前用户ID |
| `{department_id}` | 当前用户主部门ID |
| `{department_path}` | 当前用户主部门路径 |
| `{department_ids}` | 当前用户所有部门ID（逗号分隔） |

## 创建数据规则

### 通过管理界面

访问 `/admin/auth/data-rules` 创建数据规则：

1. 填写规则名称
2. 选择关联的菜单
3. 选择作用域（行级/列级/表单）
4. 配置字段、条件和值

### 通过代码创建

```php
use Dcat\Admin\Models\DataRule;

// 创建行级规则：只能查看自己创建的数据
DataRule::create([
    'name' => '只看自己的数据',
    'menu_id' => 5, // 菜单ID
    'scope' => DataRule::SCOPE_ROW,
    'field' => 'created_by',
    'condition' => '=',
    'value' => '{user_id}',
    'value_type' => DataRule::VALUE_TYPE_VARIABLE,
    'status' => 1,
]);

// 创建行级规则：只能查看本部门及下级部门的数据
DataRule::create([
    'name' => '本部门及下级数据',
    'menu_id' => 5,
    'scope' => DataRule::SCOPE_ROW,
    'field' => 'department_path',
    'condition' => 'like',
    'value' => '{department_path}%',
    'value_type' => DataRule::VALUE_TYPE_VARIABLE,
    'status' => 1,
]);

// 创建列级规则：隐藏薪资列
DataRule::create([
    'name' => '隐藏薪资',
    'menu_id' => 5,
    'scope' => DataRule::SCOPE_COLUMN,
    'field' => 'salary',
    'status' => 1,
]);

// 创建表单规则：禁止编辑状态字段
DataRule::create([
    'name' => '禁止编辑状态',
    'menu_id' => 5,
    'scope' => DataRule::SCOPE_FORM,
    'field' => 'status',
    'action' => 'disable',
    'status' => 1,
]);
```

## 分配规则给角色

数据规则通过角色分配给用户：

```php
use Dcat\Admin\Models\Role;
use Dcat\Admin\Models\DataRule;

$role = Role::find(2);
$rule = DataRule::find(1);

// 为角色分配数据规则
$role->dataRules()->attach($rule->id);

// 批量分配
$role->dataRules()->sync([1, 2, 3]);
```

## 在 Grid 中使用

### 自动应用数据权限

```php
use Dcat\Admin\Grid;

protected function grid()
{
    return Grid::make(new User(), function (Grid $grid) {
        $grid->column('id');
        $grid->column('name');
        $grid->column('email');
        $grid->column('salary'); // 如果用户没有权限，此列会自动隐藏

        // 启用数据权限，传入当前菜单ID
        $grid->withDataPermission(5);
    });
}
```

### 分别控制行和列权限

```php
protected function grid()
{
    return Grid::make(new User(), function (Grid $grid) {
        // ... 列定义

        // 只应用列级权限
        $grid->applyColumnPermissions();

        // 在模型中应用行级权限
        $grid->model()->whereExists(function ($query) {
            // 自定义行级过滤
        });
    });
}
```

### 手动获取隐藏列

```php
use Dcat\Admin\Support\DataPermission;

// 获取当前用户在某个菜单下需要隐藏的列
$hiddenColumns = DataPermission::make()->getHiddenColumns($menuId);

// 在 Grid 中使用
foreach ($hiddenColumns as $column) {
    $grid->column($column)->hide();
}
```

## 在 Form 中使用

### 自动应用表单权限

```php
use Dcat\Admin\Form;

protected function form()
{
    return Form::make(new User(), function (Form $form) {
        $form->text('name');
        $form->text('email');
        $form->text('salary'); // 根据权限可能被隐藏、禁用或只读

        // 启用数据权限
        $form->withDataPermission(5);
    });
}
```

### 手动控制字段

```php
use Dcat\Admin\Support\DataPermission;

protected function form()
{
    return Form::make(new User(), function (Form $form) {
        $dataPermission = DataPermission::make();
        $menuId = 5;

        $form->text('name');

        // 检查是否可以访问某个字段
        if ($dataPermission->canAccessFormField($menuId, 'salary')) {
            $form->text('salary');
        }

        // 获取需要隐藏的字段
        $hiddenFields = $dataPermission->getHiddenFormFields($menuId);
    });
}
```

## DataPermission 类

`DataPermission` 类提供了数据权限的核心功能：

```php
use Dcat\Admin\Support\DataPermission;

// 创建实例（使用当前登录用户）
$dataPermission = DataPermission::make();

// 或指定用户
$dataPermission = new DataPermission($user);

// 获取某个菜单的所有规则
$rules = $dataPermission->getRulesForMenu($menuId);

// 获取行级规则
$rowRules = $dataPermission->getRowRules($menuId);

// 获取列级规则
$columnRules = $dataPermission->getColumnRules($menuId);

// 获取表单规则
$formRules = $dataPermission->getFormRules($menuId);

// 应用行级规则到查询
$query = $dataPermission->applyRowRules($query, $menuId);

// 获取需要隐藏的列
$hiddenColumns = $dataPermission->getHiddenColumns($menuId);

// 获取需要隐藏的表单字段
$hiddenFields = $dataPermission->getHiddenFormFields($menuId);

// 检查是否可以访问某列
$canAccess = $dataPermission->canAccessColumn($menuId, 'field_name');

// 检查是否可以访问表单字段
$canAccess = $dataPermission->canAccessFormField($menuId, 'field_name');

// 清除缓存
DataPermission::clearCache();
```

## 使用 Trait

### 在模型中使用

```php
use Dcat\Admin\Traits\HasDataPermission;

class Order extends Model
{
    use HasDataPermission;

    // 获取应用了数据权限的查询
    public function scopeWithDataPermission($query, $menuId)
    {
        return $this->applyDataPermissionScope($query, $menuId);
    }
}

// 使用
$orders = Order::withDataPermission(5)->get();
```

## 典型使用场景

### 场景一：只能查看自己创建的数据

创建规则：
- 作用域：行级
- 字段：`created_by`
- 条件：`=`
- 值：`{user_id}`
- 值类型：变量

### 场景二：只能查看本部门的数据

创建规则：
- 作用域：行级
- 字段：`department_id`
- 条件：`=`
- 值：`{department_id}`
- 值类型：变量

### 场景三：查看本部门及下级部门的数据

创建规则：
- 作用域：行级
- 字段：`department_path`
- 条件：`like`
- 值：`{department_path}%`
- 值类型：变量

### 场景四：隐藏敏感字段

创建规则：
- 作用域：列级
- 字段：`salary`（或 `phone`、`id_card` 等）

### 场景五：禁止普通用户编辑某字段

创建规则：
- 作用域：表单
- 字段：`status`
- 操作：`disable` 或 `readonly`

## 配置选项

```php
// config/admin.php

'database' => [
    // 数据规则表
    'data_rules_table' => 'admin_data_rules',
    // 数据规则模型
    'data_rules_model' => Dcat\Admin\Models\DataRule::class,
    // 角色-数据规则关联表
    'role_data_rules_table' => 'admin_role_data_rules',
],

'data_permission' => [
    // 是否启用数据权限
    'enable' => true,
],
```

## 注意事项

1. **性能考虑**：数据规则会在查询时应用条件，复杂的规则可能影响查询性能
2. **缓存**：规则会被缓存，修改规则后需要清除缓存才能生效
3. **优先级**：多个规则同时生效时，采用 AND 逻辑组合
4. **超级管理员**：超级管理员默认不受数据权限限制
