# 权限系统升级指南

本文档说明如何将现有系统升级到最新的权限系统，包括组织机构、按钮权限和数据权限功能。

## 新功能概览

新版权限系统在原有 RBAC 基础上新增了以下功能：

| 功能 | 说明 |
|------|------|
| 组织机构 | 部门层级管理、用户多部门归属、部门角色继承 |
| 按钮权限 | 基于 permission_key 的细粒度操作权限控制 |
| 数据权限 | 行级、列级、表单字段级的数据访问控制 |
| Laravel Gate | 统一的权限检查机制，支持 `@can` 指令 |

## 升级步骤

### 步骤 1：运行数据库迁移

新版权限系统需要以下数据库表：

```bash
php artisan migrate
```

这将创建/更新以下表：

| 表名 | 说明 |
|------|------|
| admin_departments | 部门表 |
| admin_department_users | 用户-部门关联表 |
| admin_department_roles | 部门-角色关联表 |
| admin_permissions (更新) | 新增 type、permission_key、menu_id 字段 |
| admin_data_rules | 数据规则表 |
| admin_role_data_rules | 角色-数据规则关联表 |

#### 手动迁移（如果自动迁移失败）

如果数据库迁移失败，可以手动执行以下 SQL：

```sql
-- 1. 创建部门表
CREATE TABLE `admin_departments` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `parent_id` bigint(20) NOT NULL DEFAULT '0',
    `path` varchar(255) DEFAULT NULL,
    `name` varchar(100) NOT NULL,
    `code` varchar(50) DEFAULT NULL,
    `order` int(11) NOT NULL DEFAULT '0',
    `status` tinyint(4) NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_departments_parent_id_index` (`parent_id`),
    KEY `admin_departments_code_index` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. 创建用户-部门关联表
CREATE TABLE `admin_department_users` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `department_id` bigint(20) NOT NULL,
    `user_id` bigint(20) NOT NULL,
    `is_primary` tinyint(4) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_department_users_department_id_index` (`department_id`),
    KEY `admin_department_users_user_id_index` (`user_id`),
    UNIQUE KEY `admin_department_users_unique` (`department_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. 创建部门-角色关联表
CREATE TABLE `admin_department_roles` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `department_id` bigint(20) NOT NULL,
    `role_id` bigint(20) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_department_roles_department_id_index` (`department_id`),
    KEY `admin_department_roles_role_id_index` (`role_id`),
    UNIQUE KEY `admin_department_roles_unique` (`department_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. 更新权限表
ALTER TABLE `admin_permissions`
    ADD COLUMN `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:菜单 2:按钮 3:数据' AFTER `slug`,
    ADD COLUMN `permission_key` varchar(100) DEFAULT NULL COMMENT '权限键' AFTER `type`,
    ADD COLUMN `menu_id` bigint(20) DEFAULT NULL COMMENT '关联菜单ID' AFTER `permission_key`,
    ADD INDEX `admin_permissions_type_index` (`type`),
    ADD INDEX `admin_permissions_permission_key_index` (`permission_key`),
    ADD INDEX `admin_permissions_menu_id_index` (`menu_id`);

-- 5. 创建数据规则表
CREATE TABLE `admin_data_rules` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL COMMENT '规则名称',
    `menu_id` bigint(20) NOT NULL COMMENT '关联菜单ID',
    `scope` varchar(20) NOT NULL COMMENT '作用域: row/column/form',
    `field` varchar(100) NOT NULL COMMENT '字段名',
    `condition` varchar(20) DEFAULT NULL COMMENT '条件',
    `value` text COMMENT '值',
    `value_type` varchar(20) NOT NULL DEFAULT 'fixed' COMMENT '值类型: fixed/variable',
    `action` varchar(20) DEFAULT NULL COMMENT '表单操作: hide/disable/readonly',
    `status` tinyint(4) NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_data_rules_menu_id_index` (`menu_id`),
    KEY `admin_data_rules_scope_index` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. 创建角色-数据规则关联表
CREATE TABLE `admin_role_data_rules` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `role_id` bigint(20) NOT NULL,
    `data_rule_id` bigint(20) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `admin_role_data_rules_role_id_index` (`role_id`),
    KEY `admin_role_data_rules_data_rule_id_index` (`data_rule_id`),
    UNIQUE KEY `admin_role_data_rules_unique` (`role_id`, `data_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 步骤 2：更新配置文件

在 `config/admin.php` 中添加新的配置项：

```php
return [
    // ... 其他配置

    'database' => [
        // ... 原有配置

        // 新增：部门相关表
        'departments_table' => 'admin_departments',
        'departments_model' => Dcat\Admin\Models\Department::class,
        'department_users_table' => 'admin_department_users',
        'department_roles_table' => 'admin_department_roles',

        // 新增：数据规则相关表
        'data_rules_table' => 'admin_data_rules',
        'data_rules_model' => Dcat\Admin\Models\DataRule::class,
        'role_data_rules_table' => 'admin_role_data_rules',
    ],

    // 新增：部门功能开关
    'department' => [
        'enable' => true,
    ],

    // 新增：数据权限功能开关
    'data_permission' => [
        'enable' => true,
    ],
];
```

### 步骤 3：初始化菜单

运行以下命令初始化新的管理菜单：

```bash
php artisan db:seed --class=Dcat\\Admin\\Models\\AdminTablesSeeder
```

或者手动添加菜单：

```php
use Dcat\Admin\Models\Menu;

// 部门管理菜单
Menu::create([
    'parent_id' => 0, // Auth 菜单的 ID
    'title' => '部门管理',
    'icon' => 'fa-sitemap',
    'uri' => 'auth/departments',
    'order' => 5,
]);

// 数据规则菜单
Menu::create([
    'parent_id' => 0, // Auth 菜单的 ID
    'title' => '数据规则',
    'icon' => 'fa-database',
    'uri' => 'auth/data-rules',
    'order' => 6,
]);
```

### 步骤 4：清除缓存

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## 配置选项说明

### 启用/禁用功能

如果你不需要某些功能，可以在配置中禁用：

```php
// config/admin.php

// 禁用部门功能
'department' => [
    'enable' => false,
],

// 禁用数据权限功能
'data_permission' => [
    'enable' => false,
],
```

禁用后，相关的路由和菜单将不会注册。

## 代码迁移

### 原有权限检查代码

原有的权限检查代码无需修改，仍然有效：

```php
// 这些代码继续有效
if (Admin::user()->can('create-post')) {
    // ...
}

if (Admin::user()->cannot('delete-post')) {
    // ...
}

Permission::check('edit-post');
```

### 使用新的按钮权限

推荐使用新的 `canPermissionKey()` 方法进行按钮级权限检查：

```php
// 旧方式（仍然有效）
if (Admin::user()->can('user-create')) {
    // ...
}

// 新方式（推荐）
if (Admin::user()->canPermissionKey('user:create')) {
    // ...
}

// 或使用辅助函数
if (admin_can('user:create')) {
    // ...
}

// 或使用 Laravel Gate
if (Gate::allows('user:create')) {
    // ...
}
```

### 在 Grid 中应用数据权限

```php
protected function grid()
{
    return Grid::make(new User(), function (Grid $grid) {
        $grid->column('id');
        $grid->column('name');

        // 启用数据权限（传入菜单ID）
        $grid->withDataPermission($this->getMenuId());
    });
}

// 获取当前菜单ID的方法
protected function getMenuId()
{
    // 方式1：从路由获取
    $path = request()->path();
    $menu = Menu::where('uri', $path)->first();
    return $menu ? $menu->id : null;

    // 方式2：硬编码
    return 5; // 用户管理菜单的ID
}
```

### 在 Form 中应用数据权限

```php
protected function form()
{
    return Form::make(new User(), function (Form $form) {
        $form->text('name');
        $form->text('email');

        // 启用数据权限
        $form->withDataPermission($this->getMenuId());
    });
}
```

## 用户部门管理

### 为用户分配部门

```php
use Dcat\Admin\Models\Administrator;

$user = Administrator::find(1);

// 分配部门（支持多部门）
$user->departments()->sync([1, 2, 3]);

// 设置主部门
$user->departments()->updateExistingPivot(1, ['is_primary' => 1]);
```

### 获取用户部门信息

```php
// 获取用户所有部门
$departments = Admin::user()->departments;

// 获取用户主部门
$primaryDepartment = Admin::user()->primaryDepartment();

// 获取用户通过部门继承的角色
$departmentRoles = Admin::user()->getDepartmentRoles();

// 获取用户所有角色（直接分配 + 部门继承）
$allRoles = Admin::user()->allRoles();
```

## 创建数据规则

### 通过管理界面创建

1. 访问 `管理 -> 数据规则`
2. 点击"新建"
3. 填写规则信息：
   - 名称：规则描述
   - 关联菜单：选择该规则应用的菜单
   - 作用域：行级/列级/表单
   - 字段：要控制的字段名
   - 条件：比较条件（仅行级需要）
   - 值：条件值（支持系统变量）
   - 值类型：固定值/变量

### 系统变量说明

在数据规则的"值"字段中，可以使用以下系统变量：

| 变量 | 说明 | 示例 |
|------|------|------|
| `{user_id}` | 当前登录用户ID | `created_by = {user_id}` |
| `{department_id}` | 用户主部门ID | `department_id = {department_id}` |
| `{department_path}` | 用户主部门路径 | `department_path LIKE {department_path}%` |
| `{department_ids}` | 用户所有部门ID | `department_id IN ({department_ids})` |

### 常用规则示例

#### 只看自己的数据

```
作用域: 行级
字段: created_by
条件: =
值: {user_id}
值类型: 变量
```

#### 只看本部门数据

```
作用域: 行级
字段: department_id
条件: =
值: {department_id}
值类型: 变量
```

#### 看本部门及下级部门数据

```
作用域: 行级
字段: department_path
条件: like
值: {department_path}%
值类型: 变量
```

#### 隐藏敏感列

```
作用域: 列级
字段: salary
```

#### 禁止编辑状态字段

```
作用域: 表单
字段: status
操作: disable
```

## 验证升级

升级完成后，请检查以下功能：

- [ ] 部门管理页面可以正常访问
- [ ] 可以创建/编辑/删除部门
- [ ] 用户可以分配到部门
- [ ] 部门角色继承正常工作
- [ ] 数据规则管理页面可以正常访问
- [ ] 可以创建/编辑/删除数据规则
- [ ] 行级数据权限正常过滤数据
- [ ] 列级数据权限正常隐藏列
- [ ] 表单字段权限正常工作
- [ ] `canPermissionKey()` 方法正常工作
- [ ] Laravel Gate 集成正常工作

## 常见问题

### 1. 迁移提示表已存在

如果之前手动创建过表，可以跳过相应的迁移：

```bash
# 查看迁移状态
php artisan migrate:status

# 手动标记迁移为已执行
php artisan migrate --pretend
```

### 2. 部门/数据规则菜单不显示

检查配置文件中的功能开关：

```php
'department' => [
    'enable' => true,
],

'data_permission' => [
    'enable' => true,
],
```

### 3. 数据权限不生效

1. 确认已为角色分配数据规则
2. 确认规则状态为"启用"
3. 清除缓存：`DataPermission::clearCache()`
4. 超级管理员默认不受数据权限限制

### 4. 权限键不生效

确认权限记录的 `type` 字段设置正确：
- 菜单权限：`type = 1`
- 按钮权限：`type = 2`
- 数据权限：`type = 3`

## 回滚方案

如果升级后出现问题，可以回滚：

### 1. 禁用新功能

```php
// config/admin.php
'department' => ['enable' => false],
'data_permission' => ['enable' => false],
```

### 2. 回滚数据库（谨慎操作）

```sql
-- 删除新增的表
DROP TABLE IF EXISTS `admin_role_data_rules`;
DROP TABLE IF EXISTS `admin_data_rules`;
DROP TABLE IF EXISTS `admin_department_roles`;
DROP TABLE IF EXISTS `admin_department_users`;
DROP TABLE IF EXISTS `admin_departments`;

-- 回滚权限表字段
ALTER TABLE `admin_permissions`
    DROP INDEX `admin_permissions_menu_id_index`,
    DROP INDEX `admin_permissions_permission_key_index`,
    DROP INDEX `admin_permissions_type_index`,
    DROP COLUMN `menu_id`,
    DROP COLUMN `permission_key`,
    DROP COLUMN `type`;
```

## 相关文档

- [权限控制](permission.md) - 权限系统概述
- [组织机构管理](department.md) - 部门功能详细说明
- [数据权限控制](data-permission.md) - 数据权限详细说明
