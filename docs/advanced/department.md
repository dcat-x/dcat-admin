# 组织机构管理

组织机构功能提供了完善的部门层级管理能力，支持：

- 树形部门结构
- 用户多部门归属
- 部门与角色关联
- 基于部门的权限继承

## 启用组织机构

在配置文件 `config/admin.php` 中启用：

```php
'department' => [
    'enable' => true,
],
```

## 数据库表结构

### 部门表 (admin_departments)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| parent_id | bigint | 父部门ID，0表示顶级部门 |
| path | varchar | 层级路径，格式如 `-1-2-3-` |
| name | varchar | 部门名称 |
| code | varchar | 部门编码（可选） |
| order | int | 排序序号 |
| status | tinyint | 状态：1启用，0禁用 |

### 用户-部门关联表 (admin_department_users)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| department_id | bigint | 部门ID |
| user_id | bigint | 用户ID |
| is_primary | tinyint | 是否主部门：1是，0否 |

### 部门-角色关联表 (admin_department_roles)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| department_id | bigint | 部门ID |
| role_id | bigint | 角色ID |

## 部门模型

### 基本用法

```php
use Dcat\Admin\Models\Department;

// 获取所有启用的部门
$departments = Department::where('status', 1)->get();

// 获取部门树形结构
$tree = Department::selectOptions();

// 获取某个部门的所有子部门ID
$department = Department::find(1);
$descendantIds = $department->getDescendantIds();

// 获取部门及其所有子部门
$descendants = $department->getDescendantsWithSelf();
```

### 部门关系

```php
// 获取部门下的用户
$users = $department->users;

// 获取部门关联的角色
$roles = $department->roles;

// 获取父部门
$parent = $department->parent;
```

### 检查部门状态

```php
// 检查部门是否启用
if ($department->isEnabled()) {
    // 部门已启用
}
```

## 用户与部门

### 获取用户的部门

```php
use Dcat\Admin\Admin;

// 获取用户的所有部门
$departments = Admin::user()->departments;

// 获取用户的主部门
$primaryDepartment = Admin::user()->primaryDepartment();

// 获取用户主部门的ID
$departmentId = Admin::user()->primary_department_id;
```

### 获取部门继承的角色

用户可以通过部门继承角色权限：

```php
// 获取用户通过部门继承的所有角色
$departmentRoles = Admin::user()->getDepartmentRoles();

// 获取用户的所有角色（包括直接分配的和部门继承的）
$allRoles = Admin::user()->allRoles();
```

## 部门管理界面

系统已内置部门管理控制器，访问路径为 `/admin/auth/departments`。

### 自定义部门控制器

如需自定义，可以继承 `DepartmentController`：

```php
use Dcat\Admin\Http\Controllers\DepartmentController as BaseDepartmentController;

class DepartmentController extends BaseDepartmentController
{
    protected function grid()
    {
        return Grid::make(new Department(), function (Grid $grid) {
            // 自定义表格
            $grid->column('id');
            $grid->column('name');
            $grid->column('code');
            $grid->column('status')->switch();

            // 使用树形展示
            $grid->model()->toTree();
        });
    }
}
```

## 部门树形选择

在表单中使用部门树形选择：

```php
$form->select('department_id', '部门')
    ->options(Department::selectOptions())
    ->required();

// 多选部门
$form->multipleSelect('departments', '所属部门')
    ->options(Department::selectOptions());
```

## 权限继承

部门支持与角色关联，用户可以通过所属部门继承角色的权限：

1. **创建部门**：在部门管理中创建部门层级结构
2. **关联角色**：为部门分配角色
3. **分配用户**：将用户加入部门
4. **权限继承**：用户自动获得部门关联角色的所有权限

```php
// 检查用户是否有某个权限（包括部门继承的）
if (Admin::user()->can('edit-post')) {
    // 有权限
}
```

## 配置选项

在 `config/admin.php` 中可配置：

```php
'database' => [
    // 部门表名
    'departments_table' => 'admin_departments',
    // 部门模型
    'departments_model' => Dcat\Admin\Models\Department::class,
    // 用户-部门关联表
    'department_users_table' => 'admin_department_users',
    // 部门-角色关联表
    'department_roles_table' => 'admin_department_roles',
],

'department' => [
    // 是否启用部门功能
    'enable' => true,
],
```

## 辅助方法

```php
use Dcat\Admin\Admin;

// 获取当前用户的主部门ID
$departmentId = Admin::user()->primary_department_id;

// 获取用户所有部门的ID数组
$departmentIds = Admin::user()->departments->pluck('id')->toArray();

// 获取用户主部门及其所有子部门的ID
$department = Admin::user()->primaryDepartment();
if ($department) {
    $allDepartmentIds = $department->getDescendantIds();
    $allDepartmentIds[] = $department->id;
}
```
