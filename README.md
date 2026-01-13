<div align="center">

# Dcat Admin X

<p>
    <a href="https://github.com/dcat-x/dcat-admin/actions"><img src="https://github.com/dcat-x/dcat-admin/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
    <a href="https://packagist.org/packages/dcat-x/laravel-admin"><img src="https://poser.pugx.org/dcat-x/laravel-admin/v/stable" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/dcat-x/laravel-admin"><img src="https://img.shields.io/packagist/dt/dcat-x/laravel-admin.svg" alt="Total Downloads"></a>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/php-8.2+-59a9f8.svg" alt="PHP Version"></a>
    <a href="https://laravel.com/"><img src="https://img.shields.io/badge/laravel-12+-59a9f8.svg" alt="Laravel Version"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

**基于 [Dcat Admin](https://github.com/jqhph/dcat-admin) 二次开发的后台系统构建工具**

</div>

---

Dcat Admin 只需很少的代码即可快速构建出一个功能完善的高颜值后台系统。

## 特性

- **简洁优雅** - 灵活可扩展的 API 设计
- **权限管理** - 用户管理与 RBAC 权限系统
- **菜单管理** - 支持多主题切换
- **无刷新体验** - 使用 PJAX 构建，支持按需加载静态资源
- **松耦合设计** - 页面构建与数据操作分离
- **扩展系统** - 插件功能与可视化代码生成器
- **数据展示** - 表格、表单、详情页构建工具
- **树形结构** - 树状表格与无限层级树状页面
- **丰富组件** - 图表、数据卡片、下拉菜单等
- **文件上传** - 异步上传，支持分块多线程

## 安装

```bash
composer require dcat-x/laravel-admin
```

发布资源并运行安装：

```bash
php artisan admin:publish
php artisan admin:install
```

访问 `http://localhost/admin`，使用 `admin` / `admin` 登录。

## 文档

- [中文文档](https://learnku.com/docs/dcat-admin)

## 开发

```bash
composer install      # 安装依赖
composer pint         # 代码格式化
composer phpstan      # 静态分析
composer test         # 运行测试
```

## 鸣谢

| 项目 | 说明 |
|------|------|
| [Dcat Admin](https://github.com/jqhph/dcat-admin) | 原版项目 |
| [Laravel](https://laravel.com/) | PHP 框架 |
| [Laravel Admin](https://github.com/z-song/laravel-admin) | 基础框架 |
| [AdminLTE](https://github.com/ColorlibHQ/AdminLTE) | 后台模板 |
| [Bootstrap](https://getbootstrap.com/) | CSS 框架 |

## License

[MIT License](LICENSE)
