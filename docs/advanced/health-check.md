# 配置健康检查

系统提供了后台菜单和权限配置的健康检查功能，可通过 Artisan 命令或代码调用，帮助发现常见的配置问题。

## Artisan 命令

```bash
php artisan admin:health-check
```

### 命令选项

| 选项 | 说明 | 默认值 |
|------|------|--------|
| `--json` | 以 JSON 格式输出 | 否 |
| `--scope=all` | 检查范围：`all`、`menu`、`permission` | `all` |
| `--fail-on=warning` | 非零退出条件：`never`、`warning`、`error` | `warning` |
| `--refresh` | 绕过缓存强制刷新 | 否 |
| `--quiet` | 不输出详细内容 | 否 |

### 使用示例

```bash
# 仅检查菜单配置
php artisan admin:health-check --scope=menu

# JSON 输出，适合 CI/CD 集成
php artisan admin:health-check --json

# 仅在检测到 error 级别问题时返回非零退出码
php artisan admin:health-check --fail-on=error

# 绕过缓存
php artisan admin:health-check --refresh

# CI/CD：仅关注 error，静默输出
php artisan admin:health-check --fail-on=error --quiet
```

### 退出码

| 退出码 | 含义 |
|--------|------|
| `0` | 无问题或未达到 `--fail-on` 阈值 |
| `1` | 检测到问题且达到 `--fail-on` 阈值 |
| `2` | 参数错误 |

## 检查项

### 菜单检查 (`menu`)

| 检查类型 | severity | 说明 |
|---------|----------|------|
| `menu.uri.whitespace` | warning | 菜单 URI 包含空白字符 |
| `menu.uri.duplicate` | warning | 存在重复的菜单 URI |

### 权限检查 (`permission`)

| 检查类型 | severity | 说明 |
|---------|----------|------|
| `permission.path.whitespace` | warning | 权限路径包含空白字符 |
| `permission.path.invalid_method` | error | 权限路径包含无效的 HTTP 方法前缀 |
| `permission.slug.duplicate` | error | 存在重复的权限标识 (slug) |

## 缓存配置

通过 `config/admin.php` 的 `health_check` 配置缓存 TTL：

```php
'health_check' => [
    'cache_ttl' => env('ADMIN_HEALTH_CHECK_CACHE_TTL', 0),
],
```

- `0`（默认）：不缓存，每次都实时检查
- 正整数：缓存秒数，减少数据库查询开销
- `--refresh` 选项可强制绕过缓存

## CI/CD 集成

在部署流水线中添加健康检查：

```yaml
# GitHub Actions 示例
- name: Admin health check
  run: php artisan admin:health-check --fail-on=error --quiet
```

```bash
# 部署脚本示例
php artisan admin:health-check --json --fail-on=error > /tmp/health.json
if [ $? -ne 0 ]; then
    echo "Admin config issues detected!"
    cat /tmp/health.json
    exit 1
fi
```

## 代码调用

```php
use Dcat\Admin\Support\ConfigHealthInspector;

$inspector = app(ConfigHealthInspector::class);

// 检查所有
$issues = $inspector->inspect();

// 按范围检查
$issues = $inspector->inspectByScope('menu');
$issues = $inspector->inspectByScope('permission');

// 强制刷新（忽略缓存）
$issues = $inspector->inspectByScope('all', true);
```

每个 issue 的结构：

```php
[
    'type'     => 'permission.slug.duplicate',
    'severity' => 'error',
    'message'  => 'Duplicate permission slug detected: manage-users',
    'ids'      => [3, 7],
    'value'    => 'manage-users',
]
```
