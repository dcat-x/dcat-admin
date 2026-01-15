# 新增列显示

Dcat Admin X 新增了以下实用的列显示器。

## 金额显示 (fee)

用于在 Grid 列表中显示以"分"为单位存储的金额,自动转换为带货币符号的格式化金额。

### 基本使用

```php
$grid->column('amount')->fee();
```

默认显示为美元符号 `$` 和 2 位小数。

### 自定义符号和小数位

```php
// 人民币符号
$grid->column('price', '价格')->fee('¥');

// 自定义小数位数
$grid->column('amount', '金额')->fee('$', 3);

// 欧元
$grid->column('total', '总计')->fee('€', 2);
```

### 示例

```php
// 数据库存储: 12345(分)
// 显示结果: $123.45

$grid->column('order_amount', '订单金额')->fee('¥', 2);
// 数据库: 9999
// 显示: ¥99.99
```

### 配合使用

通常与表单的 `fee()` 字段配合使用,确保数据的一致性:

```php
// Grid 显示
$grid->column('price')->fee('¥');

// Form 编辑
$form->fee('price', '价格')->symbol('¥');

// Show 详情
$show->field('price')->fee('¥');
```

## 空数据显示 (emptyData)

用于在 Grid 列表中处理空值,当值为空时显示占位符。

### 基本使用

```php
$grid->column('remark')->emptyData();
```

默认空值显示为 `-`。

### 自定义占位符

```php
// 自定义占位符文本
$grid->column('description', '描述')->emptyData('暂无描述');

// 使用 HTML
$grid->column('content', '内容')->emptyData('<span class="text-muted">未填写</span>');

// 不同场景使用不同占位符
$grid->column('phone', '电话')->emptyData('未绑定');
$grid->column('email', '邮箱')->emptyData('未设置');
```

### 示例

```php
// 值为 null、''、0、false 时显示占位符
$grid->column('nickname', '昵称')->emptyData('匿名用户');

// 配合其他显示器使用
$grid->column('status', '状态')
    ->using([1 => '启用', 0 => '禁用'])
    ->dot([1 => 'success', 0 => 'danger'])
    ->emptyData('未知');
```

## 比率显示 (rate)

用于显示百分比或比率值,自动添加后缀。

### 基本使用

```php
$grid->column('rate')->rate();
```

默认显示百分号 `%`,不指定小数位。

### 自定义后缀和小数位

```php
// 自定义小数位数
$grid->column('rate', '比率')->rate('%', 2);

// 自定义后缀
$grid->column('score', '评分')->rate('分', 1);

// 倍数显示
$grid->column('multiple', '倍率')->rate('x', 2);
```

### 示例

```php
// 数据库存储: 95.5
// 显示结果: 95.5%
$grid->column('completion_rate', '完成率')->rate('%', 1);

// 数据库存储: 3.14159
// 显示结果: 3.14x
$grid->column('growth_multiple', '增长倍数')->rate('x', 2);

// 显示温度
$grid->column('temperature', '温度')->rate('°C', 1);
```

### 处理空值

```php
// 空值显示为 0%
$grid->column('rate')->rate();

// 配合 emptyData 处理空值
$grid->column('rate', '比率')
    ->display(function ($value) {
        return $value ?? null;
    })
    ->emptyData('N/A');
```

## 综合示例

### 订单管理表格

```php
$grid->column('id', 'ID')->sortable();

$grid->column('order_no', '订单号')->copyable();

$grid->column('amount', '订单金额')
    ->fee('¥', 2)
    ->sortable();

$grid->column('discount_rate', '折扣')
    ->rate('%', 1);

$grid->column('remark', '备注')
    ->emptyData('无备注')
    ->limit(50);

$grid->column('status', '状态')
    ->using([
        1 => '待支付',
        2 => '已支付',
        3 => '已取消',
    ])
    ->dot([
        1 => 'warning',
        2 => 'success',
        3 => 'danger',
    ]);
```

### 用户统计表格

```php
$grid->column('username', '用户名');

$grid->column('balance', '余额')
    ->fee('¥', 2);

$grid->column('total_spent', '累计消费')
    ->fee('¥', 2);

$grid->column('completion_rate', '完成率')
    ->rate('%', 1);

$grid->column('growth_rate', '增长率')
    ->rate('%', 2)
    ->display(function ($value) {
        $color = $value > 0 ? 'success' : 'danger';
        return "<span class='text-{$color}'>{$value}%</span>";
    });

$grid->column('phone', '手机号')
    ->emptyData('未绑定');

$grid->column('email', '邮箱')
    ->emptyData('未设置');
```

## 辅助函数

在自定义显示时,可以使用以下辅助函数:

### money_formatter

格式化金额(分转元):

```php
// 基本用法
money_formatter(10000);  // "100.00"
money_formatter(12345, 2);  // "123.45"

// 自定义小数位
money_formatter(10000, 3);  // "100.000"

// 在 display 回调中使用
$grid->column('amount')->display(function ($value) {
    return '¥' . money_formatter($value);
});
```

### rate_formatter

格式化比率:

```php
// 基本用法
rate_formatter(9550, 2);  // "95.50"

// 在 display 回调中使用
$grid->column('rate')->display(function ($value) {
    return rate_formatter($value, 1) . '%';
});
```

## 注意事项

1. **Fee 显示器**:
   - 数据库中必须以"分"为单位存储(整数)
   - 显示时自动除以 100 转换为"元"
   - 使用 BCMath 扩展确保精度

2. **EmptyData 显示器**:
   - 判断为空的值: `null`、`''`、`0`、`false`
   - 可以与其他显示器链式调用
   - HTML 内容不会被转义

3. **Rate 显示器**:
   - 空值会显示为 `0` + 后缀
   - 小数位参数为 `null` 时保留原始精度
   - 可以自定义任意后缀

4. **链式调用**:
   ```php
   // 多个显示器可以链式调用
   $grid->column('amount')
       ->fee('¥', 2)
       ->sortable()
       ->help('单位:元');
   ```
