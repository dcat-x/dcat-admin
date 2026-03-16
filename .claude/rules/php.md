---
description: PHP/Laravel 代码规范，编辑 src/ 或 tests/ 下的 PHP 文件时适用
globs: ["src/**/*.php", "tests/**/*.php"]
---

# PHP 规范

- 全局启用 `declare(strict_types=1)`，禁止传 `null`/`mixed` 给 string 参数，必要时用 `(string)` 转换
- 代码风格: Pint preset `laravel`，修改后运行 `composer pint`
- 类型安全: Rector (`NullToStrictStringFuncCallArgRector`)，新代码不得引入新的 Rector 错误
- 静态分析: PHPStan level 5，新代码不得引入新的 PHPStan 错误
- Namespace: `Dcat\Admin\*`，tests 使用 `Dcat\Admin\Tests\*`
- 断言统一使用 `assertSame`（严格相等），不用 `assertEquals`
- 闭包回调优先重构为具名 protected 方法，提升可测试性
- 避免 `call_user_func`，直接调用方法
- Trait 方法调用前用 `method_exists` 检查兼容性
- 测试基类: `Dcat\Admin\Tests\TestCase`（extends Orchestra Testbench）
