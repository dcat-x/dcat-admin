# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/dcat-x/dcat-admin/compare/v1.0.5...HEAD
[1.0.5]: https://github.com/dcat-x/dcat-admin/compare/v1.0.4...v1.0.5
[1.0.4]: https://github.com/dcat-x/dcat-admin/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/dcat-x/dcat-admin/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/dcat-x/dcat-admin/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/dcat-x/dcat-admin/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/dcat-x/dcat-admin/releases/tag/v1.0.0
