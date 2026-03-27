# Octane 兼容

dcat-admin 提供了对 Laravel Octane（Swoole/RoadRunner）的基础兼容支持。核心机制是 `Resettable` 接口和 `FlushAdminState` 监听器。

## 配置

在 `config/octane.php` 的 `listeners` 中注册监听器：

```php
use Dcat\Admin\Octane\Listeners\FlushAdminState;
use Laravel\Octane\Events\RequestReceived;

'listeners' => [
    RequestReceived::class => [
        ...Octane::prepareApplicationForNextOperation(),
        ...Octane::prepareApplicationForNextRequest(),
        FlushAdminState::class,
    ],
],
```

## FlushAdminState 做了什么

每次新请求到达时：

1. **清理静态状态** — 调用所有已注册 `Resettable` 类的 `resetState()` 方法
2. **重置容器服务** — 重新注册 admin 相关的容器服务实例
3. **重新启动扩展** — 重新注册和启动扩展

## 扩展作者：实现 Resettable

如果你的扩展使用了静态属性存储请求级数据，应该实现 `Resettable` 接口：

```php
use Dcat\Admin\Contracts\Resettable;

class MyExtensionService implements Resettable
{
    protected static array $cache = [];

    public static function resetState(): void
    {
        static::$cache = [];
    }
}
```

然后在 `FlushAdminState` 中注册（通过 PR 或在你的 ServiceProvider 中手动调用）。

## 哪些不需要清理

- **Boot 时注册表**（如 `Form::$availableFields`、`Column::$displayers`）— 每次 boot 内容相同，不会跨请求泄漏
- **Request hash 自重置缓存**（如 `Permission` 中间件的菜单缓存、`DataPermission` 的规则缓存）— 内置了请求 ID 检测机制，自动在新请求时重置
- **静态常量**（如 `$css`/`$js` 资产声明）— 硬编码值，不随请求变化

## 当前已纳管的类

| 类 | 清理内容 |
|---|---|
| `ImportController` | importer 注册表 |
| `JavaScript` | 内联脚本缓存 |
| `Form` | 已收集的资产 |
| `Grid\Column` | 原始 Grid 数据 |
| `Admin` | 全局搜索单例 |
| `Helper` | 控制器名称缓存 |
