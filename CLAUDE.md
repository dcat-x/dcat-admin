# dcat-admin

## 常用命令

```bash
composer test          # PHPUnit 测试
composer pint:test     # Pint 代码风格检查
composer phpstan       # PHPStan 静态分析
composer ci            # 以上三项依次执行（pint → phpstan → test）
composer pint          # 自动修复代码风格
npx mix --production   # 编译前端资源
php scripts/release.php <version>  # 发布新版本
```

## 验证

完成任务前必须运行对应验证：

- 修改 `src/` 下 PHP 文件 → `composer ci`
- 仅修改测试 → `composer test`
- 修改前端源码 → `npx mix --production`
