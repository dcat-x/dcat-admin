# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-05-01

> v2.0.0 是包含破坏性变更的主版本，主要面向想升级到 Laravel 13 的用户。停留在 PHP 8.2 / Laravel 12 的项目请固定 `dcat-x/laravel-admin: ^1.0`，1.x 仍维护安全修复。

### Breaking Changes

- **PHP 最低版本提升至 8.3** — Laravel 13 要求 PHP 8.3+，对应 dcat-admin 升至 v2.0.0；PHP 8.2 用户请继续使用 v1.x
- **移除 `doctrine/dbal` 依赖** — Laravel 11+ 已不再依赖该包，dcat-admin 源码全程零引用；如外部项目通过本包传递依赖该库，请显式 `composer require doctrine/dbal`

### Added

- **支持 Laravel 13** — 同时兼容 Laravel 12 与 Laravel 13：
  - `laravel/framework: ^12.0||^13.0`
  - `orchestra/testbench: ^10.0||^11.0`
  - `phpunit/phpunit: ^11.0||^12.0`
  - `spatie/eloquent-sortable: ^4.0||^5.0`
- **CI 双版矩阵** — PHP 8.3/8.4 × Laravel 12/13 共 6 个 job，本地双跑 5027 tests / 8071 assertions 全绿

### Fixed

- **`ListField::formatValidatorMessages()` 数组消息处理** — 此前把 `MessageBag::toArray()` 返回的 `array<string>` 当作单条消息整体塞进新 MessageBag，行为不正确（被 Laravel 13 收紧的 `MessageBag::add()` 类型签名揭出）；修复为遍历每条字符串消息分别 add

### Docs

- README / `docs/getting-started/introduction.md` / `upgrade-from-original.md` / `update.md` 全部同步至 v2.x 兼容性矩阵
- `update.md` 新增「从 1.x 升级到 2.x」完整章节

## [1.2.2] - 2026-04-30

### Changed

- **扁平化样式收敛** — `border-radius` / `transition` / `opacity` 散落硬编码统一为 `var(--radius-*)` / `var(--transition-*)` 体系；移除滚动条 45° 斜纹与暗色 tab 渐变 active 横条
- **彻底移除 SCSS 阴影变量层** — `_variables.scss` 中 9 个值为 `none` 的 shadow 变量定义全部删除（`$shadow / $shadow-100..200 / $btn-shadow*` 等），17 个组件文件中约 30 处冗余 `box-shadow` 引用一并清除
- **移除 CSS 自定义属性 shadow 层** — `_modern.scss` `:root` 下 `--shadow-xs/sm/md/lg/xl` 5 个 `none` 定义删除；6 个 `.shadow-*` 工具类合并为单一规则（保留选择器作为外部 API）
- **保留功能性阴影** — `$menu-shadow`（菜单/侧栏轻量分层）、focus ring、`kbd` inset、固定列分隔阴影等语义性阴影不动

## [1.2.1] - 2026-04-30

### Changed

- 全面扁平化 dcat 阴影系统，主题层覆盖 AdminLTE 装饰阴影
- 统一 .alert-* 视觉风格，移除全部 box-shadow

### Fixed

- 加固 import 派发，防重放并修复默认 Grid 上下文丢失

## [1.2.0] - 2026-04-30

### Breaking Changes

- **Action/Form 类名签名校验** — `_action` 和 `_form_` 参数现在必须携带 HMAC 签名，未签名请求默认拒绝。新增 `admin.allow_unsigned_dispatch`（env: `ADMIN_ALLOW_UNSIGNED_DISPATCH`）作为升级期临时开关，默认 false。前端 JS 无需修改，直接构造请求的外部系统需适配（参见 `docs/advanced/class-signing.md`）
- **导入预览接口移除** — `POST /dcat-api/import/preview` 路由及 Importer `preview()` 方法已删除，前端从未使用
- **全局搜索最短关键词** — 从 1 字符提升到 2 字符
- **导入配置签名格式变更** — `_import_config` 由 `base64(json|sig)` 改为 `base64(json({p,s}))` envelope，避免 payload 含 `|` 时被截断；老缓存页面提交会因校验失败回退空配置，刷新页面即恢复

### Added

- **Octane 静态状态清理** — `Resettable` 接口 + `FlushAdminState` 集成，6 个核心类实现 `resetState()`（参见 `docs/advanced/octane.md`）
- **导入器签名负载** — Grid 渲染时把 importer / repository 类名一同 HMAC 签名传输，服务端反序列化时校验类型归属（`AbstractImporter` / `Repository`）
- **`AbstractImporter::setRepository()` / `repository()`** — 允许在没有 Grid 的场景显式注入 repository
- **全局搜索增强** — limit 参数限制 1~50，搜索提供者异常隔离
- **Feature 测试基础设施** — `FeatureTestCase` 基类支持 HTTP 级测试
- **`HasRequestCache::resetRequestCache()`** — 请求级缓存显式清理方法
- **Larastan 替代原生 PHPStan** — 收窄忽略规则

### Changed

- **菜单权限匹配** — 支持 UUID/ULID/slug 路径，RESTful 结构化位置匹配
- **上传控制器** — 公共逻辑抽到 `UploadsFiles` trait，新增文件校验，命名改用 `Str::random(32)`
- **路由注册** — 全部改用 `[Controller::class, 'method']` 数组语法
- **PHP 8.2+ 语法现代化** — `is_null` → `=== null`、first-class callable、消除 `call_user_func`、清理死代码

### Fixed

- **`ClassSigner::verify()` 默认拒绝未签名输入**（high）— 此前 `b8b9420` 把校验失败降级为 warn+放行，等同于回退到无签名状态；攻击者只要不带签名即可绕过 HMAC 派发任意 Action（如 `Extensions\Uninstall`）
- **`ExcelImporter::import()` 不再依赖 grid**（high）— 此前 `resolveImporter()` 创建的 importer 从未调用 `setGrid()`，`$this->grid->model()->repository()` 必空指针，generic `/dcat-api/import/execute` 真实上传时 500
- **导入配置签名 pipe 截断**（medium）— `decodeConfig` 用 `explode('|', ..., 2)` 切首个 `|`，含 `required|email` 这类规则的 JSON 必被截断，校验失败后静默落入空配置
- **导入链路** — `ImportController::resolveImporter()` 缺少 Grid 上下文导致 NPE

### Build

- **生产 sourcemap 关闭** — `webpack.mix.js` 增加 `mix.options({ sourceMaps: false })`，配合清理 57 个孤儿 `.map` 文件，仓库瘦身约 19 MB（保留被 `sourceMappingURL` 显式引用的 4 个 colorpicker map）

## [1.1.31] - 2026-03-16

### Other

- ♻️ refactor: 优化 Rector 规则配置并清理死代码
- 🎨 style: 优化代码生成模板（stubs）
- ♻️ refactor: 引入 Rector 自动检测并修复 strict_types 类型安全问题
- 🐛 fix: 修复 strict_types 下的潜在 TypeError（第五批）
- 🐛 fix: 深度扫描修复 strict_types 下的潜在 TypeError（第四批）

## [1.1.30] - 2026-03-16

### Other

- 🐛 fix: 补充修复 strict_types 下的潜在 TypeError（第三批）
- 🐛 fix: 全面修复 strict_types 下潜在的 TypeError

## [1.1.29] - 2026-03-16

### Other

- 🐛 fix: 修复 strict_types 下多处 int 传给 string 参数的 TypeError

## [1.1.28] - 2026-03-16

### Other

- 🎨 style: 升级 Pint v1.29 并修复新增代码风格规则

## [1.1.27] - 2026-03-16

### Added

- 启用 declare_strict_types 并修复兼容性问题
- 添加四项可选功能增强（表单自动保存、数据导入、全局搜索、Grid 视图模式切换）

### Other

- 📝 docs: 更新 CLAUDE.md 和 rules/php.md 保持与实际配置同步
- ⬆️ chore: 提升 PHPStan 静态分析级别至 level 5

## [1.1.26] - 2026-03-09

### Fixed

- 修复 DialogForm 弹窗关闭后遮罩层残留及 Tree 字段闭包调用错误

## [1.1.25] - 2026-03-09

### Fixed

- 修复固定列右侧行溢出问题，改用 dropdown 事件动态切换 overflow

## [1.1.24] - 2026-03-09

### Added

- 添加操作审计日志、配置健康检查与控制器查询缓存

### Changed

- 提取日志控制 trait、ErrorCode 常量与健康检查增强
- Helper 工具方法全面优化与 Context/Admin 精简
- 中间件去重优化、数据权限查找缓存与 Helper 方法精简
- 权限中间件URI索引、菜单激活迭代优化与构建清理
- 多模块优化与前端构建改进

## [1.1.23] - 2026-03-09

### Fixed

- 固定列支持折叠操作菜单并修复下拉菜单溢出

## [1.1.22] - 2026-03-09

### Changed

- 将剩余 call_user_func 对象方法调用替换为直接调用
- 将 call_user_func 闭包调用替换为具名保护方法
- 大范围 PHPStan 静态分析修复（文档注释与类型安全）
- 细化权限前缀匹配缓存粒度并优化角色/权限查询缓存
- 优化权限中间件前缀匹配策略并补全角色继承支持
- 提升 PHPStan 至 level 1 并修复静态分析问题
- 优化权限链路缓存并补充回归测试

### Fixed

- 优化 FixColumns 固定列 JS 实现

### Changed

- 优化权限与数据权限查询性能，新增菜单 URI 与数据规则相关复合索引迁移

## [1.1.21] - 2026-03-02

### Fixed

- 修复子菜单 padding 对齐问题，统一为 1.4em

## [1.1.20] - 2026-03-02

### Fixed

- 修复子菜单项 active 状态下 padding 对齐问题

## [1.1.19] - 2026-03-02

### Added

- BatchInput 新增 batchPlaceholder 方法，layer.open 显式设置 shade

## [1.1.18] - 2026-03-02

### Added

- 新增 BatchInput 批量输入过滤器，支持单条搜索与批量导入

### Fixed

- 统一 layer 弹窗遮罩配置为 shade: [0.3, '#000']

## [1.1.17] - 2026-03-02

### Changed

- 版本更新

## [1.1.16] - 2026-03-01

### Changed

- 版本更新

## [1.1.15] - 2026-02-28

### Changed

- 优化源代码并改进现有测试

### Fixed

- 修复文档内容错误
- 修复文档中 29 处无效的相对链接

## [1.1.14] - 2026-02-28

### Added

- AdminController 支持自定义页面视图

## [1.1.13] - 2026-01-22

### Fixed

- 允许访问没有对应菜单的接口

## [1.1.12] - 2026-01-22

### Fixed

- 未绑定角色的菜单只有超级管理员可见

## [1.1.11] - 2026-01-22

### Fixed

- 添加基于角色绑定菜单的权限检查

## [1.1.10] - 2026-01-22

### Fixed

- 修复菜单权限检查逻辑

## [1.1.9] - 2026-01-22

### Fixed

- 添加 HasDataPermission trait 的功能启用检查

## [1.1.8] - 2026-01-22

### Fixed

- 修复老系统权限检查失败问题

## [1.1.7] - 2026-01-22

### Fixed

- 统一部门和数据权限功能的默认值为 false

## [1.1.6] - 2026-01-22

### Fixed

- 部门功能未启用时跳过相关数据库查询

## [1.1.5] - 2026-01-17

### Changed

- 优化多处 UI 样式和布局

### Fixed

- 修复多项 Bug 和兼容性改进

## [1.1.4] - 2026-01-16

### Changed

- 版本更新

## [1.1.3] - 2026-01-16

### Fixed

- 支持 Laravel 8+ 匿名迁移类语法

## [1.1.2] - 2026-01-16

### Changed

- 移除筛选按钮边框

## [1.1.1] - 2026-01-16

### Changed

- 优化筛选按钮样式和间距

## [1.1.0] - 2026-01-15

### Added

- 新增组织机构（部门）管理功能
  - 树形部门结构管理
  - 用户多部门归属支持
  - 部门角色继承
- 新增按钮权限功能
  - permission_key 细粒度权限控制
  - Laravel Gate 集成
  - admin_can/admin_cannot 辅助函数
- 新增数据权限功能
  - 行级数据权限（过滤数据行）
  - 列级数据权限（隐藏表格列）
  - 表单字段权限（隐藏/禁用/只读）
  - 系统变量支持：{user_id}、{department_id}、{department_path}、{department_ids}
- 新增权限系统文档
  - 组织机构管理文档
  - 数据权限控制文档
  - 权限系统升级指南
- 新增权限系统单元测试（6个测试文件）

## [1.0.19] - 2026-01-14

### Fixed

- 修复 default/blue/blue-light 主题导航栏样式不生效的问题

## [1.0.18] - 2026-01-14

### Added

- 为框架默认主题创建 SCSS 样式文件 (default, blue, blue-light)

### Changed

- 将 green 主题颜色更新为 Tailwind CSS green (#16a34a)
- 简化 README.md，移除"环境要求"和"从原版迁移"章节

### Fixed

- 修复筛选按钮在 custom-data-table-header 内边框丢失的问题

## [1.0.17] - 2026-01-14

### Added

- 新增 19 个 Tailwind CSS 配色主题：slate, zinc, neutral, stone, red, orange, amber, yellow, lime, emerald, teal, cyan, sky, indigo, violet, purple, fuchsia, pink, rose
- 在 `Color.php` 中添加主题颜色定义
- 创建对应的 SCSS 主题样式文件
- 更新 `webpack.mix.js` 和 `package.json` 构建配置

## [1.0.16] - 2026-01-14

### Changed

- 将筛选按钮样式从 `btn-primary` 改为 `btn-white`，与重置按钮保持一致

## [1.0.15] - 2026-01-14

### Fixed

- 修复 `DropdownActions` 方法返回类型与父类不兼容的问题
- 修复 gray 主题按钮文字颜色丢失的问题
- 修复 gray 主题 outline 按钮样式问题
- 修复筛选按钮覆盖主题样式的问题

### Changed

- 更新页脚链接指向 dcat-x/dcat-admin 仓库

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

[Unreleased]: https://github.com/dcat-x/dcat-admin/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/dcat-x/dcat-admin/compare/v1.2.2...v2.0.0
[1.2.2]: https://github.com/dcat-x/dcat-admin/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/dcat-x/dcat-admin/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/dcat-x/dcat-admin/compare/v1.1.31...v1.2.0
[1.1.31]: https://github.com/dcat-x/dcat-admin/compare/v1.1.30...v1.1.31
[1.1.30]: https://github.com/dcat-x/dcat-admin/compare/v1.1.29...v1.1.30
[1.1.29]: https://github.com/dcat-x/dcat-admin/compare/v1.1.28...v1.1.29
[1.1.28]: https://github.com/dcat-x/dcat-admin/compare/v1.1.27...v1.1.28
[1.1.27]: https://github.com/dcat-x/dcat-admin/compare/v1.1.26...v1.1.27
[1.1.26]: https://github.com/dcat-x/dcat-admin/compare/v1.1.25...v1.1.26
[1.1.25]: https://github.com/dcat-x/dcat-admin/compare/v1.1.24...v1.1.25
[1.1.24]: https://github.com/dcat-x/dcat-admin/compare/v1.1.23...v1.1.24
[1.1.23]: https://github.com/dcat-x/dcat-admin/compare/v1.1.22...v1.1.23
[1.1.22]: https://github.com/dcat-x/dcat-admin/compare/v1.1.21...v1.1.22
[1.1.21]: https://github.com/dcat-x/dcat-admin/compare/v1.1.20...v1.1.21
[1.1.20]: https://github.com/dcat-x/dcat-admin/compare/v1.1.19...v1.1.20
[1.1.19]: https://github.com/dcat-x/dcat-admin/compare/v1.1.18...v1.1.19
[1.1.18]: https://github.com/dcat-x/dcat-admin/compare/v1.1.17...v1.1.18
[1.1.17]: https://github.com/dcat-x/dcat-admin/compare/v1.1.16...v1.1.17
[1.1.16]: https://github.com/dcat-x/dcat-admin/compare/v1.1.15...v1.1.16
[1.1.15]: https://github.com/dcat-x/dcat-admin/compare/v1.1.14...v1.1.15
[1.1.14]: https://github.com/dcat-x/dcat-admin/compare/v1.1.13...v1.1.14
[1.1.13]: https://github.com/dcat-x/dcat-admin/compare/v1.1.12...v1.1.13
[1.1.12]: https://github.com/dcat-x/dcat-admin/compare/v1.1.11...v1.1.12
[1.1.11]: https://github.com/dcat-x/dcat-admin/compare/v1.1.10...v1.1.11
[1.1.10]: https://github.com/dcat-x/dcat-admin/compare/v1.1.9...v1.1.10
[1.1.9]: https://github.com/dcat-x/dcat-admin/compare/v1.1.8...v1.1.9
[1.1.8]: https://github.com/dcat-x/dcat-admin/compare/v1.1.7...v1.1.8
[1.1.7]: https://github.com/dcat-x/dcat-admin/compare/v1.1.6...v1.1.7
[1.1.6]: https://github.com/dcat-x/dcat-admin/compare/v1.1.5...v1.1.6
[1.1.5]: https://github.com/dcat-x/dcat-admin/compare/v1.1.4...v1.1.5
[1.1.4]: https://github.com/dcat-x/dcat-admin/compare/v1.1.3...v1.1.4
[1.1.3]: https://github.com/dcat-x/dcat-admin/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/dcat-x/dcat-admin/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/dcat-x/dcat-admin/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/dcat-x/dcat-admin/compare/v1.0.19...v1.1.0
[1.0.19]: https://github.com/dcat-x/dcat-admin/compare/v1.0.18...v1.0.19
[1.0.18]: https://github.com/dcat-x/dcat-admin/compare/v1.0.17...v1.0.18
[1.0.17]: https://github.com/dcat-x/dcat-admin/compare/v1.0.16...v1.0.17
[1.0.16]: https://github.com/dcat-x/dcat-admin/compare/v1.0.15...v1.0.16
[1.0.15]: https://github.com/dcat-x/dcat-admin/compare/v1.0.14...v1.0.15
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
