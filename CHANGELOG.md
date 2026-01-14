# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.14] - 2026-01-14

### Changed

- 移除按钮和组件阴影，实现扁平化设计
- 禁用所有 SCSS 阴影变量（$shadow、$shadow-100 等）
- 优化 `_modern.scss` 移除与 `_custom.scss` 冲突的样式
- 减小圆角值以保持简洁风格

## [1.0.13] - 2026-01-14

### Fixed

- 修复多主题编译时 `$primary` 颜色不随主题变化的问题
- 所有主题（gray、blue、green、blue-light）现在正确使用各自的主题颜色

### Changed

- 重构 webpack.mix.js：通过 `additionalData` 注入 `$theme-primary` 变量实现动态主题颜色
- 更新 `_primary.scss`：使用 `!default` 支持外部变量覆盖

## [1.0.12] - 2026-01-14

### Fixed

- 移除筛选区域边框，保持简洁风格

## [1.0.11] - 2026-01-14 [YANKED]

### Fixed

- 添加筛选区域边框样式（已撤销）

## [1.0.10] - 2026-01-14

### Fixed

- 修复筛选按钮在 gray 主题下样式被 `.btn-primary` 覆盖的问题

## [1.0.9] - 2026-01-14

### Changed

- 默认关闭面包屑导航（`enable_default_breadcrumb` 改为 `false`）
- 优化前端打包配置：支持多主题打包（`npm run prod:all`）
- 新增 gray 主题 CSS 文件

## [1.0.8] - 2026-01-14

### Added

- 新增 Form 字段组件：Fee（金额分转元）、OssDirectUpload（阿里云 OSS 直传）、AliImage、AliMultipleImage、PrivateMultipleImage
- 新增 Grid Displayer：Fee、EmptyData、Rate
- 新增 Show Field：Fee、EmptyData、Rate
- 新增 RefreshButton 刷新按钮组件
- 新增阿里云 OSS 直传功能（OssController、AliyunStsService）
- 新增 Gray 主题（深色 header 配色）
- 新增 `_modern.scss` 现代化 UI 样式（shadcn 风格）：
  - CSS 自定义属性系统（圆角、间距、过渡、阴影）
  - 按钮变体：btn-ghost、btn-subtle
  - Badge 变体：badge-outline、badge-soft
  - 骨架屏加载动画（skeleton）
  - 状态指示器、空状态组件
  - 丰富的工具类：rounded-*、shadow-*、animate-*、gap-*、line-clamp-* 等
- 新增辅助函数：money_formatter、rate_formatter、ali_sign_url

### Changed

- 优化 Grid Actions 组件：支持自定义文本/图标、add() 方法
- 优化 bootstrap.stub 模板：添加 Grid/Form/Show 全局配置示例
- 优化基础模板风格：
  - sidebar.blade.php：移除阴影，优化 logo 显示
  - navbar-user-panel.blade.php：简化用户面板
  - login.blade.php：优化登录页样式
- 将默认主题设置为 gray
- `_custom.scss` 重构为主题无关样式

### Fixed

- 分离主题特定颜色到 `_gray.scss`，确保不影响其他主题

## [1.0.7] - 2026-01-13

### Fixed

- 修复 SweetAlert2 this.swal 引用丢失导致 fire 方法报错的问题

## [1.0.6] - 2026-01-13

### Fixed

- 修复 Bootstrap 4 Modal 关闭时的 aria-hidden 无障碍警告

## [1.0.5] - 2026-01-13

### Fixed

- 适配 SweetAlert2 v9 新 API：type → icon，confirmButtonClass → customClass.confirmButton

## [1.0.4] - 2026-01-13

### Fixed

- 降级 SweetAlert2 到 v9，修复 confirmButtonClass 等参数不兼容问题

## [1.0.3] - 2026-01-13

### Fixed

- 修复 SweetAlert2.fire 不是函数的错误，使用 npm 包替换本地混淆压缩版

## [1.0.2] - 2026-01-13

### Fixed

- 修复 NProgress.configure 不是函数的错误，将 NProgress 导入从压缩版改为未压缩版

## [1.0.1] - 2026-01-13

### Added

- 添加 CHANGELOG.md、CONTRIBUTING.md、SECURITY.md 标准文件
- composer.json 添加 support 链接和扩展 scripts 配置
- .gitattributes 添加 export-ignore 规则减小包体积

### Changed

- README.md 添加环境要求、版本兼容矩阵、迁移指南

## [1.0.0] - 2026-01-13

### Added

- Initial release based on Dcat Admin
- Support for Laravel 12.x
- Support for PHP 8.2, 8.3, 8.4
- Updated AdminLTE to 3.2.0
- Updated Bootstrap to 4.6.2
- Modern frontend dependencies

### Changed

- Minimum PHP version requirement raised to 8.2
- Minimum Laravel version requirement raised to 12.0

[Unreleased]: https://github.com/dcat-x/dcat-admin/compare/v1.0.14...HEAD
[1.0.14]: https://github.com/dcat-x/dcat-admin/compare/v1.0.13...v1.0.14
[1.0.13]: https://github.com/dcat-x/dcat-admin/compare/v1.0.12...v1.0.13
[1.0.12]: https://github.com/dcat-x/dcat-admin/compare/v1.0.11...v1.0.12
[1.0.11]: https://github.com/dcat-x/dcat-admin/compare/v1.0.10...v1.0.11
[1.0.10]: https://github.com/dcat-x/dcat-admin/compare/v1.0.9...v1.0.10
[1.0.9]: https://github.com/dcat-x/dcat-admin/compare/v1.0.8...v1.0.9
[1.0.8]: https://github.com/dcat-x/dcat-admin/compare/v1.0.7...v1.0.8
[1.0.7]: https://github.com/dcat-x/dcat-admin/compare/v1.0.6...v1.0.7
[1.0.6]: https://github.com/dcat-x/dcat-admin/compare/v1.0.5...v1.0.6
[1.0.5]: https://github.com/dcat-x/dcat-admin/compare/v1.0.4...v1.0.5
[1.0.4]: https://github.com/dcat-x/dcat-admin/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/dcat-x/dcat-admin/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/dcat-x/dcat-admin/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/dcat-x/dcat-admin/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/dcat-x/dcat-admin/releases/tag/v1.0.0
