# 从原版 Dcat Admin 升级

本文档说明如何从原版 [Dcat Admin](https://github.com/jqhph/dcat-admin) 升级到 Dcat Admin X。

## 版本对比

### Dcat Admin (原版)

- **最后版本**: 2.x
- **Laravel 支持**: 5.5 - 9.x
- **PHP 支持**: 7.1 - 8.1
- **维护状态**: 已停止维护

### Dcat Admin X (本项目)

- **当前版本**: 1.x
- **Laravel 支持**: 12.x
- **PHP 支持**: 8.2+
- **维护状态**: 持续维护

## 主要变化

### 1. 环境要求

| 项目 | 原版 Dcat Admin | Dcat Admin X |
|------|----------------|--------------|
| PHP | 7.1 - 8.1 | 8.2+ |
| Laravel | 5.5 - 9.x | 12.x |
| Composer 包名 | `dcat/laravel-admin` | `dcat-x/laravel-admin` |

### 2. 新增功能

Dcat Admin X 在原版基础上新增了以下功能:

#### 表单字段
- `fee()` - 金额字段(分/元自动转换)
- `ossDirectUpload()` - OSS 直传上传
- `aliImage()` - 阿里云私有图片
- `aliMultipleImage()` - 阿里云多图上传
- `privateMultipleImage()` - 私有多图上传

#### Grid 显示器
- `fee()` - 金额显示
- `emptyData()` - 空数据占位符
- `rate()` - 比率显示

#### Show 字段
- `fee()` - 金额显示
- `emptyData()` - 空数据处理
- `rate()` - 比率显示

#### 辅助函数
- `money_formatter()` - 金额格式化
- `rate_formatter()` - 比率格式化
- `ali_sign_url()` - OSS 签名 URL 生成

#### 主题系统
- 新增 19 个 Tailwind CSS 主题
- Gray 深色主题
- 现代化 UI 组件库

### 3. 前端依赖更新

- AdminLTE: 2.x → 3.2.0
- Bootstrap: 3.x → 4.6.2
- 现代化的前端构建工具链

## 升级步骤

### 准备工作

1. **备份数据库**

```bash
# 备份数据库
mysqldump -u root -p database_name > backup.sql
```

2. **备份项目**

```bash
# 创建项目备份
cp -r /path/to/project /path/to/project-backup
```

3. **检查 PHP 版本**

```bash
php -v
```

确保 PHP 版本 >= 8.2。如果不满足,需要先升级 PHP。

4. **检查 Laravel 版本**

```bash
php artisan --version
```

如果 Laravel 版本 < 12.x,需要先升级 Laravel。

### 升级 Laravel (如需要)

如果当前 Laravel 版本低于 12.x,请参考 [Laravel 升级指南](https://laravel.com/docs/12.x/upgrade) 进行升级。

主要步骤:

1. 更新 `composer.json` 中的 Laravel 版本
2. 更新依赖包版本
3. 更新配置文件
4. 更新代码适配新版本 API

### 升级 Dcat Admin

#### 1. 更新 Composer 依赖

编辑 `composer.json`:

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "dcat-x/laravel-admin": "^1.0"
    }
}
```

移除原版 Dcat Admin:

```bash
composer remove dcat/laravel-admin
```

安装 Dcat Admin X:

```bash
composer require dcat-x/laravel-admin
```

#### 2. 发布资源

```bash
php artisan admin:publish --force
```

> 注意: 使用 `--force` 会覆盖现有的公共资源文件。如果你修改过这些文件,请提前备份。

#### 3. 更新配置文件

比对并更新 `config/admin.php`:

```bash
# 查看差异
diff config/admin.php vendor/dcat-x/laravel-admin/config/admin.php
```

主要检查以下配置项:

```php
// 默认主题已更改为 gray
'layout' => [
    'color' => 'gray',
],

// 新增 OSS 配置
'upload' => [
    'oss' => [
        'private_disk' => 'oss-private',
    ],
],
```

#### 4. 更新数据库

运行数据库迁移:

```bash
php artisan migrate
```

#### 5. 清理缓存

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 6. 编译前端资源(如需要)

如果你自定义了前端资源:

```bash
npm install
npm run prod
```

## 代码适配

### 1. 命名空间保持不变

好消息! Dcat Admin X 保持了与原版相同的命名空间,大部分代码无需修改:

```php
// 这些代码无需修改
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Content;
```

### 2. PHP 8.2+ 语法适配

需要注意 PHP 8.2+ 的语法变化:

#### 动态属性

PHP 8.2+ 不再允许动态属性,需要使用 `#[AllowDynamicProperties]` 或定义属性:

```php
// 原代码
class MyController
{
    public function index()
    {
        $this->dynamicProperty = 'value'; // PHP 8.2+ 会报错
    }
}

// 方案 1: 使用 attribute
#[\AllowDynamicProperties]
class MyController
{
    // ...
}

// 方案 2: 显式声明属性
class MyController
{
    protected $dynamicProperty;
    // ...
}
```

#### null 类型

PHP 8.1+ 对 null 类型检查更严格:

```php
// 原代码
public function show($id)
{
    $user = User::find($id);
    return $user->name; // $user 可能为 null
}

// 建议写法
public function show($id)
{
    $user = User::findOrFail($id);
    return $user->name;
}
```

### 3. Laravel 12 API 适配

#### 字符串辅助函数

Laravel 9+ 移除了全局字符串辅助函数,需要使用 `Str` 门面:

```php
// 原代码
$slug = str_slug($title);

// 新代码
use Illuminate\Support\Str;
$slug = Str::slug($title);
```

#### 数组辅助函数

```php
// 原代码
$result = array_get($array, 'key', 'default');

// 新代码
use Illuminate\Support\Arr;
$result = Arr::get($array, 'key', 'default');
```

### 4. 数据库查询

Laravel 12 的查询构建器更严格:

```php
// 原代码
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo $user->name;
}

// 新代码 (推荐)
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo $user->name ?? '';
}
```

## 测试升级

### 1. 基本功能测试

- [ ] 登录功能正常
- [ ] 菜单显示正常
- [ ] 表格列表正常
- [ ] 表单创建正常
- [ ] 表单编辑正常
- [ ] 数据删除正常
- [ ] 权限控制正常

### 2. 自定义功能测试

- [ ] 自定义表单字段
- [ ] 自定义 Grid 显示
- [ ] 自定义页面
- [ ] 自定义动作
- [ ] 扩展功能

### 3. 性能测试

```bash
# 使用 Laravel Telescope 监控性能
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## 常见问题

### 1. Composer 依赖冲突

**问题**: 安装时提示依赖冲突

**解决**:

```bash
# 更新所有依赖到兼容版本
composer update

# 如果仍有冲突,尝试
composer update --with-all-dependencies
```

### 2. 静态资源 404

**问题**: 前端资源加载失败

**解决**:

```bash
# 重新发布资源
php artisan admin:publish --force

# 创建软链接
php artisan storage:link
```

### 3. 主题样式异常

**问题**: 升级后界面样式错乱

**解决**:

```bash
# 清理浏览器缓存
# 或使用硬刷新: Ctrl+Shift+R (Windows/Linux) 或 Cmd+Shift+R (Mac)

# 检查主题配置
php artisan config:cache
```

### 4. 数据库迁移失败

**问题**: 迁移时提示表已存在

**解决**:

```bash
# 查看迁移状态
php artisan migrate:status

# 如果表已存在,手动标记为已迁移
php artisan migrate --pretend
```

### 5. 权限问题

**问题**: storage 或 bootstrap/cache 目录权限错误

**解决**:

```bash
# Linux/Mac
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 或者
sudo chmod -R 777 storage bootstrap/cache
```

## 回滚方案

如果升级后出现严重问题,可以回滚:

### 1. 回滚代码

```bash
# 还原项目
rm -rf /path/to/project
cp -r /path/to/project-backup /path/to/project
cd /path/to/project
```

### 2. 回滚数据库

```bash
# 还原数据库
mysql -u root -p database_name < backup.sql
```

### 3. 恢复依赖

```bash
composer install
```

## 升级后优化

### 1. 使用新功能

尝试使用 Dcat Admin X 的新功能:

```php
// 使用金额字段
$form->fee('price', '价格')->symbol('¥');
$grid->column('price')->fee('¥', 2);

// 使用新主题
// config/admin.php
'layout' => [
    'color' => 'gray', // 或其他新主题
],
```

### 2. 代码优化

利用 PHP 8.2+ 新特性优化代码:

```php
// 使用 match 表达式
$status = match($code) {
    200 => 'success',
    404 => 'not found',
    500 => 'error',
    default => 'unknown',
};

// 使用构造器属性提升
class User {
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
```

### 3. 性能优化

```php
// 启用 OPcache
// php.ini
opcache.enable=1
opcache.memory_consumption=256

// 使用 Laravel Octane (可选)
composer require laravel/octane
php artisan octane:install
```

## 获取帮助

如果遇到问题:

1. 查看 [FAQ 文档](qa.md)
2. 查看 [GitHub Issues](https://github.com/dcat-x/dcat-admin/issues)
3. 提交新的 Issue

## 总结

升级到 Dcat Admin X 的主要步骤:

1. ✅ 确保环境满足要求 (PHP 8.2+, Laravel 12+)
2. ✅ 备份数据库和代码
3. ✅ 更新 Composer 依赖
4. ✅ 发布资源和更新配置
5. ✅ 适配 PHP 8.2+ 和 Laravel 12 API
6. ✅ 测试所有功能
7. ✅ 使用新功能优化代码

升级过程中保持耐心,建议在测试环境先完成升级,确认无误后再在生产环境进行。
