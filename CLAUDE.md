# dcat-admin

Laravel 全功能后台管理面板构建器，基于 Laravel Admin 二次开发。

## 技术栈

- PHP 8.2+ / Laravel
- PHPStan level 1
- Pint (PSR-12 代码风格)
- 前端: jQuery + Layer.js + Select2, 构建工具 webpack (Laravel Mix)

## 常用命令

```bash
composer test          # PHPUnit 测试
composer pint:test     # Pint 代码风格检查
composer phpstan       # PHPStan 静态分析
composer ci            # 以上三项依次执行
composer pint          # 自动修复代码风格
```

## 项目结构

- `src/` — 核心代码（Grid, Form, Show, Tree, Widgets, Models, Http 等）
- `tests/Unit/` — 单元测试，目录结构与 `src/` 对应
- `tests/Feature/` — 功能测试
- `resources/views/` — Blade 模板
- `resources/dist/` — 前端编译产物，由构建生成，不要手动修改
- `docs/` — 用户文档

## 验证

完成任务前必须运行对应验证：

- 修改 `src/` 下 PHP 文件 → `composer ci`
- 仅修改测试 → `composer test`
- 修改前端源码 → 需重新编译 dist（`npx mix --production`）
- 提交前确认 `composer pint:test` 和 `composer phpstan` 通过

## 规范

- 弹窗遮罩层统一使用 `shade: [0.3, '#000']`
- Namespace 模式: `Dcat\Admin\*`
- dist 文件由构建生成，勿手动还原或直接编辑
