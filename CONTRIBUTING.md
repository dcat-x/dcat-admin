# Contributing

感谢你考虑为 Dcat Admin X 做出贡献！

## 行为准则

请友善对待每一位参与者，保持尊重和包容的态度。

## 提交 Bug 报告

在提交 Bug 报告之前，请确保：

1. 使用最新版本的 Dcat Admin X
2. 搜索现有的 [Issues](https://github.com/dcat-x/dcat-admin/issues) 确认问题未被报告过
3. 提供详细的复现步骤

Bug 报告应包含：

- PHP 版本
- Laravel 版本
- Dcat Admin X 版本
- 详细的错误信息
- 复现步骤

## 提交功能请求

在提交功能请求之前，请先在 [Issues](https://github.com/dcat-x/dcat-admin/issues) 中讨论。

## 开发流程

### 1. Fork 并克隆仓库

```bash
git clone https://github.com/your-username/dcat-admin.git
cd dcat-admin
```

### 2. 安装依赖

```bash
composer install
```

### 3. 创建分支

```bash
git checkout -b feature/your-feature
# 或
git checkout -b fix/your-fix
```

### 4. 编写代码

请遵循以下规范：

- 代码风格遵循 [Laravel Pint](https://laravel.com/docs/pint) 规范
- 为新功能编写测试
- 保持向后兼容

### 5. 运行测试

```bash
# 代码格式检查
composer pint

# 静态分析
composer phpstan

# 运行测试
composer test

# 或运行完整 CI 检查
composer ci
```

### 6. 提交代码

提交信息格式：

```
类型: 简短描述

详细描述（可选）
```

类型包括：

- `feat`: 新功能
- `fix`: Bug 修复
- `docs`: 文档更新
- `style`: 代码格式调整
- `refactor`: 代码重构
- `test`: 测试相关
- `chore`: 构建/工具相关

### 7. 创建 Pull Request

- 确保所有测试通过
- 更新相关文档
- 在 PR 描述中说明改动内容

## 代码审查

所有提交都需要经过代码审查。请耐心等待维护者的反馈。

## 许可

提交代码即表示你同意将代码以 [MIT 许可证](LICENSE) 发布。
