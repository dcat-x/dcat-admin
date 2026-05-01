# 版本升级须知


### 说明

`Dcat Admin`的版本发行将会参考主流`web框架`的发行策略，尽量降低版本升级带来的影响，小版本和补丁**决不**包含非兼容性更改；同时我们也将会提供更新日志，详细说明新版本的改动以及可能造成的影响。




### 升级命令
升级命令
```bash
composer update dcat-x/laravel-admin
```

升级成功之后需要运行 `admin:update` 命令进行重新发布语言包、配置文件、前端静态资源等文件，然后**清理浏览器缓存**

```bash
# 发布 语言包、配置文件、前端静态资源、数据迁移文件等
php artisan admin:update
```

运行 `admin:update`，相当于运行

```
php artisan admin:publish --assets --migrations --lang --force
php artisan migrate
```

#### 发布文件命令

> 运行 `admin:update` 后一般不需要运行 `admin:publish` 命令

```bash
php artisan admin:publish --force
```

只更新语言包
```bash
php artisan admin:publish --force --lang
```

只更新配置文件
```bash
php artisan admin:publish --force --config
```


只更新前端静态资源
```bash
php artisan admin:publish --force --assets
```

只更新数据库迁徙文件(这个一般不需要更新)
```bash
php artisan admin:publish --force --migrations
```

---

### 从 1.x 升级到 2.x（支持 Laravel 13）

2.x 是包含**破坏性变更**的主版本，主要变化：

| 项目 | 1.x | 2.x |
|---|---|---|
| PHP 最低版本 | 8.2 | **8.3** |
| Laravel 支持 | 12.x | 12.x / **13.x** |
| `doctrine/dbal` 依赖 | `^4.0`（实际未使用） | **已移除** |

#### 升级步骤

1. **升级 PHP 到 8.3+**

如果当前 PHP 版本是 8.2，需要先升级到 8.3 或 8.4。

2. **更新 composer.json**

```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^12.0||^13.0",
        "dcat-x/laravel-admin": "^2.0"
    }
}
```

```bash
composer update
php artisan admin:update
```

3. **关于 `doctrine/dbal`**

2.x 不再依赖 `doctrine/dbal`。dcat-admin 源码不使用该包，Laravel 11+ 已不依赖它做 schema 操作。如果你的项目代码本身在用 `doctrine/dbal`（比如自定义 schema 操作），请显式声明：

```bash
composer require doctrine/dbal
```

4. **是否升级到 Laravel 13？**

2.x 同时支持 Laravel 12 和 13，可分两步走：

- **第一步**：先升 dcat-admin 到 2.x（仍跑 Laravel 12），跑全测试确认无影响
- **第二步**：参考 [Laravel 13 升级指南](https://laravel.com/docs/13.x/upgrade)，再升级框架本身

> Laravel 13 官方宣称"零破坏性变更"，对应用代码的影响较小；详见上游升级文档。

#### 不能升级时

如需停留在 PHP 8.2 / Laravel 12，请固定 dcat-admin 1.x：

```json
"dcat-x/laravel-admin": "^1.0"
```

1.x 仍接收安全修复。
