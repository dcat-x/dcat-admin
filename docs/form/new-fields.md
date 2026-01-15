# 新增表单字段

Dcat Admin X 在原有基础上新增了以下实用表单字段组件。

## 金额字段 (Fee)

用于处理以"分"为单位存储的金额，自动在输入时转换为"元"，提交时转换回"分"。

### 基本使用

```php
$form->fee('amount', '金额');
```

### 字段说明

- **存储单位**: 数据库中以"分"为单位存储(整数)
- **显示单位**: 表单中以"元"为单位显示(小数)
- **自动转换**:
  - 编辑时:将数据库的分值除以 100 显示
  - 提交时:将输入的元值乘以 100 存储

### 示例

```php
// 数据库存储 10000(分)
// 表单显示 100.00(元)
// 用户输入 200.50(元)
// 提交存储 20050(分)

$form->fee('price', '商品价格')
    ->symbol('¥')  // 设置货币符号
    ->help('请输入商品价格,单位:元');
```

### 配合使用

通常与 Grid 的 `fee()` 显示器和 Show 的 `fee()` 字段配合使用,确保数据在整个流程中的一致性。

## OSS 直传上传 (OssDirectUpload)

支持前端直连阿里云 OSS 上传大文件,使用 STS 临时授权,支持分片上传、断点续传。

### 基本使用

```php
$form->ossDirectUpload('file', '文件上传');
```

### 配置方法

#### 设置最大文件大小

```php
$form->ossDirectUpload('video', '视频上传')
    ->maxSize(1000); // 最大 1000MB
```

#### 设置分片大小

```php
$form->ossDirectUpload('file', '文件上传')
    ->chunkSize(10); // 每片 10MB
```

#### 设置文件类型限制

```php
// 限制图片类型
$form->ossDirectUpload('image', '图片')
    ->accept('jpg,jpeg,png,gif', 'image/*');

// 限制视频类型
$form->ossDirectUpload('video', '视频')
    ->accept('mp4,avi,mov', 'video/*');
```

#### 设置上传目录

```php
$form->ossDirectUpload('file', '文件')
    ->directory('uploads/documents');
```

#### 自定义 STS Token 地址

```php
$form->ossDirectUpload('file', '文件')
    ->stsTokenUrl('/api/oss/token');
```

### 完整示例

```php
$form->ossDirectUpload('video', '视频文件')
    ->maxSize(2000)                    // 最大 2GB
    ->chunkSize(20)                    // 每片 20MB
    ->accept('mp4,mov', 'video/*')     // 限制视频格式
    ->directory('videos')              // 上传到 videos 目录
    ->help('支持 MP4、MOV 格式,最大 2GB');
```

### 后端配置

需要配置 STS Token 获取接口,参考阿里云 OSS 文档配置临时访问凭证。

## 阿里云图片字段 (AliImage)

用于处理存储在阿里云 OSS 私有 Bucket 中的图片,自动生成带签名的临时访问 URL。

### 基本使用

```php
$form->aliImage('avatar', '头像');
```

### 功能说明

- 自动调用 `ali_sign_url()` 辅助函数生成签名 URL
- 支持私有 Bucket 的图片访问
- 自动处理图片预览

### 示例

```php
$form->aliImage('cover', '封面图')
    ->move('images/covers')
    ->uniqueName()
    ->help('上传后图片将存储到私有 OSS Bucket');
```

### 多图上传

```php
$form->aliMultipleImage('gallery', '图片集')
    ->move('images/gallery')
    ->removable();
```

## 私有图片字段 (PrivateMultipleImage)

用于上传多张私有图片。

### 基本使用

```php
$form->privateMultipleImage('images', '图片集');
```

### 示例

```php
$form->privateMultipleImage('product_images', '产品图片')
    ->move('products')
    ->limit(9)
    ->help('最多上传 9 张图片');
```

## 配置说明

### OSS 配置

在 `config/admin.php` 中配置 OSS 相关参数:

```php
'upload' => [
    'oss' => [
        'private_disk' => 'oss-private',  // 私有 OSS Disk 名称
    ],
],
```

在 `config/filesystems.php` 中配置 OSS Disk:

```php
'disks' => [
    'oss-private' => [
        'driver' => 'oss',
        'access_id' => env('OSS_ACCESS_KEY_ID'),
        'access_key' => env('OSS_ACCESS_KEY_SECRET'),
        'bucket' => env('OSS_PRIVATE_BUCKET'),
        'endpoint' => env('OSS_ENDPOINT'),
        // ... 其他配置
    ],
],
```

### 辅助函数

系统提供了 `ali_sign_url()` 辅助函数用于生成阿里云 OSS 签名 URL:

```php
// 生成 60 分钟有效期的签名 URL
$url = ali_sign_url($path);

// 自定义有效期(分钟)
$url = ali_sign_url($path, 120);

// 指定 Disk
$url = ali_sign_url($path, 60, 'oss-private');
```

## 注意事项

1. **Fee 字段**:
   - 数据库字段类型应为整数类型(如 `bigint` 或 `int`)
   - 确保精度足够,避免金额溢出

2. **OSS 直传**:
   - 需要配置阿里云 OSS STS 服务
   - 注意设置合理的权限和有效期
   - 大文件上传建议设置合理的分片大小

3. **私有图片**:
   - 签名 URL 有时效性,过期后需要重新生成
   - 前端显示时会自动生成签名 URL
