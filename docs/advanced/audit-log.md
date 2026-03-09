# 操作审计与日志控制

系统内置了操作审计日志、权限拒绝日志、数据权限异常日志和配置健康检查日志，并提供统一的日志输出控制能力。

## 日志类型

| 日志通道 | 配置组 | 触发时机 | 日志级别 |
|---------|--------|---------|---------|
| `admin.operation.audit` | `audit` | 表单保存/更新/删除操作 | `info` |
| `admin.permission.denied` | `permission_denied` | 权限中间件拒绝访问 | `warning` |
| `admin.data_permission.rule_anomaly` | `data_permission` | 数据权限规则异常（条件不合法、字段为空等） | `warning` |
| `admin.config.health` | `config_health` | 菜单/权限管理页面保存时检测到配置问题 | `warning` |

每条日志都会自动附带 `trace_id`，便于在分布式环境下串联同一请求的所有日志。

### trace_id 解析优先级

1. 请求头 `X-Request-Id`
2. 请求头 `X-Trace-Id`
3. 请求属性 `_admin_trace_id`（自动生成 UUID）

## 日志控制配置

在 `config/admin.php` 中通过 `log_control` 统一控制各组日志的输出行为：

```php
'log_control' => [
    'audit' => [
        'sample_rate'  => 1.0,      // 采样率，0~1，1 表示全部记录
        'only_paths'   => [],       // 仅这些路径才记录，空数组表示不限制
        'except_paths' => [],       // 排除这些路径
    ],
    'permission_denied' => [
        'sample_rate'  => 1.0,
        'only_paths'   => [],
        'except_paths' => [],
    ],
    'data_permission' => [
        'sample_rate'  => 1.0,
        'only_paths'   => [],
        'except_paths' => [],
    ],
    'config_health' => [
        'sample_rate'  => 1.0,
        'only_paths'   => [],
        'except_paths' => [],
    ],
],
```

### 配置项说明

| 配置项 | 类型 | 说明 |
|--------|------|------|
| `sample_rate` | `float` | 采样率，`0` 关闭日志，`1` 全量记录，`0.1` 表示 10% 请求会记录 |
| `only_paths` | `array` | 白名单路径模式，支持 `*` 通配符，如 `['auth/*', 'users']` |
| `except_paths` | `array` | 黑名单路径模式，支持 `*` 通配符 |

### 路径匹配规则

- 使用 `Str::is()` 进行通配符匹配
- `only_paths` 和 `except_paths` 均为空时，所有路径都记录
- `only_paths` 非空时，仅匹配的路径才记录
- `except_paths` 非空时，匹配的路径被排除
- 两者同时配置时，先过 `only_paths`，再过 `except_paths`

### 采样策略

当 `sample_rate` 介于 0 到 1 之间时，系统使用基于 `trace_id` 的确定性采样：

- 同一请求（相同 `trace_id`）的同一日志组，采样结果一致
- 不同请求之间的采样相互独立
- 无 `trace_id` 时退化为随机采样

## 使用示例

### 生产环境：仅记录 10% 审计日志

```php
'log_control' => [
    'audit' => [
        'sample_rate' => 0.1,
    ],
],
```

### 仅记录特定路径的权限拒绝

```php
'log_control' => [
    'permission_denied' => [
        'sample_rate'  => 1.0,
        'only_paths'   => ['auth/*', 'api/*'],
    ],
],
```

### 排除健康检查的某些路径

```php
'log_control' => [
    'config_health' => [
        'sample_rate'  => 1.0,
        'except_paths' => ['auth/menu/test*'],
    ],
],
```

### 完全关闭某组日志

```php
'log_control' => [
    'data_permission' => [
        'sample_rate' => 0,
    ],
],
```
