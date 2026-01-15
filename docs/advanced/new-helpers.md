# 新增辅助函数

Dcat Admin X 新增了一些实用的辅助函数,方便开发者处理常见业务场景。

## 金额格式化 (money_formatter)

将以"分"为单位的金额转换为"元"并格式化。

### 函数签名

```php
function money_formatter($fee, int $decimals = 2): string
```

### 参数说明

- `$fee`: 金额(分),整数类型
- `$decimals`: 小数位数,默认 2 位

### 返回值

返回格式化后的金额字符串(元)。

### 基本使用

```php
// 基本用法
money_formatter(10000);  // "100.00"
money_formatter(12345);  // "123.45"

// 自定义小数位数
money_formatter(10000, 3);  // "100.000"
money_formatter(10050, 2);  // "100.50"

// 处理空值
money_formatter(null);  // "0.00"
money_formatter('');    // "0.00"
```

### 实际应用

#### 在视图中使用

```php
// Blade 模板
<div class="price">
    ¥{{ money_formatter($order->amount) }}
</div>

// 显示带货币符号的价格
<span>售价: ¥{{ money_formatter($product->price, 2) }}</span>
```

#### 在控制器中使用

```php
public function show($id)
{
    $order = Order::find($id);

    return [
        'order_no' => $order->order_no,
        'amount' => money_formatter($order->amount),  // "123.45"
        'total' => '¥' . money_formatter($order->total_amount),  // "¥500.00"
    ];
}
```

#### 在 Grid 中使用

```php
$grid->column('amount')->display(function ($value) {
    return '¥' . money_formatter($value);
});

// 或使用内置的 fee 显示器(推荐)
$grid->column('amount')->fee('¥', 2);
```

### 精度说明

函数使用 BCMath 扩展的 `bcdiv()` 进行高精度除法运算:

```php
// 避免浮点数精度问题
money_formatter(10001);  // "100.01" (准确)
// 而不是 "100.00999..." (浮点数问题)
```

## 比率格式化 (rate_formatter)

将比率值转换并格式化。

### 函数签名

```php
function rate_formatter($rate, int $decimals = 3): string
```

### 参数说明

- `$rate`: 比率值
- `$decimals`: 小数位数,默认 3 位

### 返回值

返回格式化后的比率字符串。

### 基本使用

```php
// 基本用法
rate_formatter(9550, 2);   // "95.50"
rate_formatter(8500, 1);   // "85.0"

// 自定义小数位
rate_formatter(3141, 2);   // "31.41"
rate_formatter(3141, 4);   // "31.4100"

// 处理空值
rate_formatter(null);      // "0.000"
rate_formatter('');        // "0.000"
```

### 实际应用

#### 在视图中使用

```php
// Blade 模板
<div class="rate">
    完成率: {{ rate_formatter($task->completion_rate, 1) }}%
</div>

<div class="growth">
    增长: {{ rate_formatter($data->growth_rate, 2) }}%
</div>
```

#### 在控制器中使用

```php
public function statistics()
{
    $data = Statistics::first();

    return [
        'completion_rate' => rate_formatter($data->completion_rate, 1) . '%',
        'growth_rate' => rate_formatter($data->growth_rate, 2) . '%',
        'conversion_rate' => rate_formatter($data->conversion_rate, 2) . '%',
    ];
}
```

#### 在 Grid 中使用

```php
$grid->column('rate')->display(function ($value) {
    return rate_formatter($value, 1) . '%';
});

// 或使用内置的 rate 显示器(推荐)
$grid->column('rate')->rate('%', 1);
```

## 阿里云 OSS 签名 URL (ali_sign_url)

为阿里云 OSS 私有 Bucket 中的文件生成带签名的临时访问 URL。

### 函数签名

```php
function ali_sign_url(?string $path, int $expireMinutes = 60, ?string $disk = null): string
```

### 参数说明

- `$path`: OSS 文件路径
- `expireMinutes`: URL 有效期(分钟),默认 60 分钟
- `$disk`: 磁盘名称,默认使用配置的私有磁盘

### 返回值

返回带签名的临时访问 URL,空路径返回空字符串。

### 基本使用

```php
// 基本用法 - 60 分钟有效期
$url = ali_sign_url('images/avatar.jpg');

// 自定义有效期 - 120 分钟
$url = ali_sign_url('videos/demo.mp4', 120);

// 指定磁盘
$url = ali_sign_url('files/document.pdf', 60, 'oss-private');

// 处理空值
ali_sign_url(null);  // ""
ali_sign_url('');    // ""
```

### 实际应用

#### 在视图中显示私有图片

```php
// Blade 模板
<img src="{{ ali_sign_url($user->avatar) }}" alt="头像">

// 视频播放
<video src="{{ ali_sign_url($video->path, 120) }}" controls></video>

// 文件下载链接
<a href="{{ ali_sign_url($file->path, 30) }}" download>下载文件</a>
```

#### 在控制器中生成 URL

```php
public function show($id)
{
    $product = Product::find($id);

    return [
        'name' => $product->name,
        'cover' => ali_sign_url($product->cover),  // 自动生成签名 URL
        'images' => collect($product->images)->map(function ($image) {
            return ali_sign_url($image, 90);  // 90 分钟有效期
        }),
    ];
}
```

#### 在 API 响应中使用

```php
class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cover' => ali_sign_url($this->cover),
            'detail_images' => $this->detail_images ? array_map(function ($img) {
                return ali_sign_url($img, 120);
            }, $this->detail_images) : [],
        ];
    }
}
```

#### 在 Grid 中使用

```php
$grid->column('avatar', '头像')->image('', 50)->display(function ($value) {
    return ali_sign_url($value);
});

// 使用 AliImage 字段(自动处理签名)
$form->aliImage('avatar', '头像');
```

### 配置说明

在 `config/admin.php` 中配置:

```php
'upload' => [
    'oss' => [
        'private_disk' => 'oss-private',  // 私有 OSS Disk 名称
    ],
],
```

在 `config/filesystems.php` 中配置 OSS:

```php
'disks' => [
    'oss-private' => [
        'driver' => 'oss',
        'access_id' => env('OSS_ACCESS_KEY_ID'),
        'access_key' => env('OSS_ACCESS_KEY_SECRET'),
        'bucket' => env('OSS_PRIVATE_BUCKET'),
        'endpoint' => env('OSS_ENDPOINT'),
        'is_cname' => false,
        'use_ssl' => true,
        'prefix' => env('OSS_PREFIX', ''),
    ],
],
```

### 注意事项

1. **签名有效期**: 根据业务场景设置合理的有效期
   - 图片预览: 60-120 分钟
   - 视频播放: 120-240 分钟
   - 文件下载: 15-30 分钟

2. **性能优化**: 对于频繁访问的资源,考虑使用缓存

3. **错误处理**: 函数内部已处理异常,失败时返回原路径

## 综合示例

### 订单管理

```php
class OrderController extends Controller
{
    public function show($id)
    {
        $order = Order::find($id);

        return view('order.show', [
            'order' => $order,
            'amount_formatted' => '¥' . money_formatter($order->amount),
            'discount_formatted' => rate_formatter($order->discount_rate, 1) . '%',
            'invoice_url' => $order->invoice ? ali_sign_url($order->invoice, 30) : null,
        ]);
    }

    public function api($id)
    {
        $order = Order::find($id);

        return response()->json([
            'order_no' => $order->order_no,
            'amount' => money_formatter($order->amount, 2),
            'discount' => rate_formatter($order->discount_rate, 1),
            'attachments' => collect($order->attachments)->map(function ($file) {
                return [
                    'name' => $file['name'],
                    'url' => ali_sign_url($file['path'], 60),
                ];
            }),
        ]);
    }
}
```

### 商品展示

```php
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(20);

        $products->transform(function ($product) {
            $product->price_formatted = '¥' . money_formatter($product->price);
            $product->cover_url = ali_sign_url($product->cover);
            $product->discount_formatted = rate_formatter($product->discount, 0) . '% OFF';
            return $product;
        });

        return view('products.index', compact('products'));
    }
}
```

### 用户中心

```php
class UserController extends Controller
{
    public function profile()
    {
        $user = auth()->user();

        return view('user.profile', [
            'user' => $user,
            'balance' => '¥' . money_formatter($user->balance),
            'avatar_url' => ali_sign_url($user->avatar),
            'completion_rate' => rate_formatter($user->profile_completion, 0) . '%',
        ]);
    }
}
```

## 注意事项

### 通用注意事项

1. **命名空间**: 这些函数是全局函数,无需导入命名空间
2. **类型安全**: 参数类型不匹配时可能返回意外结果
3. **性能**: 频繁调用时注意性能影响

### money_formatter

1. **存储单位**: 数据库必须以"分"为单位存储整数
2. **精度**: 使用 BCMath 确保精度,需要启用 BCMath 扩展
3. **显示**: 返回值是字符串,不是数字

### rate_formatter

1. **存储方式**: 通常以百分比的 100 倍存储(如 95.5% 存储为 9550)
2. **小数位**: 根据业务需求选择合适的小数位数
3. **计算**: 返回值是字符串,用于显示不用于计算

### ali_sign_url

1. **依赖**: 需要安装和配置阿里云 OSS 相关包
2. **权限**: 确保配置的 AccessKey 有足够的权限
3. **有效期**: 签名 URL 有时效性,过期后需要重新生成
4. **安全**: 不要将签名 URL 长期存储或公开分享
