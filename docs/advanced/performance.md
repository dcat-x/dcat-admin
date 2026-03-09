# 性能与缓存

本文汇总后台常见性能优化项，重点覆盖菜单权限、数据权限和缓存刷新策略。

## 1. 菜单缓存（强烈建议生产开启）

配置位置：`config/admin.php`

```php
'menu' => [
    'cache' => [
        'enable' => true,
        'store' => 'file', // 可切换为 redis 等驱动
    ],
],
```

建议：

- 本地开发：`enable = false`，方便实时调试菜单变更。
- 生产环境：`enable = true`，减少菜单构建与关联查询开销。

## 2. 菜单缓存刷新策略

后台菜单管理页面保存/删除菜单时，系统会自动清理并重建菜单缓存。

如果你是通过 SQL 或脚本直接改表（如 `admin_menu`、`admin_role_menu`、`admin_permission_menu`），请手动刷新：

```bash
php artisan admin:menu-cache
```

多应用（`multi_app`）场景建议在部署后统一执行一次上述命令。

## 3. 权限中间件匹配策略

当前权限中间件在菜单匹配上做了以下优化：

- 请求级缓存，避免同请求重复查询菜单。
- 前缀匹配按路径首段分组加载候选，降低大菜单场景扫描成本。
- 前缀匹配采用“路径段边界判断”，避免 `order` 误匹配 `orders`。

## 4. 数据权限查询索引

建议确保已执行最新迁移，以获得以下索引优化（对应迁移文件会自动创建）：

- `admin_menu.uri`
- `admin_data_rules(menu_id, status, order)`
- `admin_data_rules(menu_id, status, scope)`
- `admin_role_data_rules(data_rule_id, role_id)`

执行：

```bash
php artisan migrate
```

## 5. 发布/升级后的建议动作

1. 执行数据库迁移：`php artisan migrate`
2. 刷新菜单缓存：`php artisan admin:menu-cache`
3. 如涉及前端资源变更，重新发布资源：`php artisan admin:publish --force`

## 6. 内部缓存与优化策略

以下优化由框架内部自动完成，无需额外配置：

### 权限中间件

- URI 精确匹配使用哈希索引（O(1) 查找），大菜单场景不再逐条扫描
- 前缀匹配按路径首段分组加载候选，降低扫描成本
- 请求级缓存，同一请求内不重复查询菜单和权限

### 数据权限

- 角色 ID 列表、作用域规则、隐藏列/隐藏字段均使用实例级缓存
- 规则异常日志自动去重，同一请求内相同异常只记录一次

### Helper 工具方法

- `buildNestedArray`：通过 O(n) 索引构建树形结构，替代原 O(n²) 递归
- `matchRequestPath`：使用 `strpos` + `array_flip` 方法匹配优化
- `deleteByValue`：标量值使用 `array_flip` 哈希查找
- `slug`：简单小写字符串快速路径，跳过正则处理

### 控制器请求级缓存

菜单、权限、角色等管理控制器使用 `HasRequestCache` trait，在同一请求内缓存查询结果（如菜单树、权限节点、角色列表），避免重复数据库查询。

## 7. 快速排障清单

菜单修改后不生效：

1. 检查 `admin.menu.cache.enable` 是否开启。
2. 若为后台页面修改，先刷新页面/清理浏览器缓存。
3. 若为 SQL 或脚本改表，执行 `php artisan admin:menu-cache`。

权限配置正确但仍提示无权访问：

1. 检查权限 URL 是否配置为正确模式（如 `auth/users*`）。
2. 检查菜单是否绑定了角色或权限（未绑定时普通用户可能不可见/不可访问）。
