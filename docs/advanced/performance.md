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

## 6. 快速排障清单

菜单修改后不生效：

1. 检查 `admin.menu.cache.enable` 是否开启。
2. 若为后台页面修改，先刷新页面/清理浏览器缓存。
3. 若为 SQL 或脚本改表，执行 `php artisan admin:menu-cache`。

权限配置正确但仍提示无权访问：

1. 检查权限 URL 是否配置为正确模式（如 `auth/users*`）。
2. 检查菜单是否绑定了角色或权限（未绑定时普通用户可能不可见/不可访问）。
