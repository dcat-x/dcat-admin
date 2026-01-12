# Dcat Admin

[![Tests](https://github.com/jqhph/dcat-admin/actions/workflows/tests.yml/badge.svg)](https://github.com/jqhph/dcat-admin/actions)
[![Latest Stable Version](https://poser.pugx.org/dcat-x/laravel-admin/v/stable)](https://packagist.org/packages/dcat-x/laravel-admin)
[![Total Downloads](https://img.shields.io/packagist/dt/dcat-x/laravel-admin.svg)](https://packagist.org/packages/dcat-x/laravel-admin)
[![PHP Version](https://img.shields.io/badge/php-8.2+-59a9f8.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-12+-59a9f8.svg)](https://laravel.com/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Dcat Admin 是一个基于 [Laravel Admin](https://www.laravel-admin.org/) 二次开发而成的后台系统构建工具，只需很少的代码即可快速构建出一个功能完善的高颜值后台系统。

## 特性

- 简洁优雅、灵活可扩展的 API
- 用户管理与 RBAC 权限管理
- 菜单管理与多主题切换
- 使用 PJAX 构建无刷新页面，支持按需加载静态资源
- 松耦合的页面构建与数据操作设计
- 插件功能与可视化代码生成器
- 数据表格、表单、详情页构建工具
- 树状表格与无限层级树状页面
- 丰富的常用页面组件（图表、数据卡片、下拉菜单等）
- 异步文件上传，支持分块多线程上传

## 环境要求

- PHP >= 8.2
- Laravel >= 12.0

## 安装

```bash
composer require dcat-x/laravel-admin
```

发布资源：

```bash
php artisan admin:publish
```

运行安装命令：

```bash
php artisan admin:install
```

启动服务后，访问 `http://localhost/admin`，使用用户名 `admin` 和密码 `admin` 登录。

## 文档

- [中文文档](https://learnku.com/docs/dcat-admin)

## 开发

```bash
# 安装依赖
composer install

# 代码格式化
composer pint

# 静态分析
composer phpstan

# 运行测试
composer test
```

## 鸣谢

Dcat Admin 基于以下开源项目：

- [Laravel](https://laravel.com/)
- [Laravel Admin](https://www.laravel-admin.org/)
- [AdminLTE](https://github.com/ColorlibHQ/AdminLTE)
- [Bootstrap](https://getbootstrap.com/)
- [jQuery](https://jquery.com/)

## License

[MIT License](LICENSE)
