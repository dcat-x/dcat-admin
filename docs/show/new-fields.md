# 新增详情字段

Dcat Admin X 为 Show 页面新增了实用的字段显示组件。

## 金额字段 (fee)

用于在详情页显示以"分"为单位存储的金额,自动转换为带货币符号的格式化金额。

### 基本使用

```php
$show->field('amount')->fee();
```

默认显示为美元符号 `$` 和 2 位小数。

### 自定义符号和小数位

```php
// 人民币符号
$show->field('price', '价格')->fee('¥');

// 自定义小数位数
$show->field('amount', '金额')->fee('$', 3);

// 欧元
$show->field('total', '总计')->fee('€', 2);
```

### 示例

```php
// 数据库存储: 12345(分)
// 显示结果: $123.45

$show->field('order_amount', '订单金额')->fee('¥', 2);
// 数据库: 9999
// 显示: ¥99.99

$show->field('balance', '账户余额')->fee('¥', 2);
$show->field('refund_amount', '退款金额')->fee('¥', 2);
```

## 空数据字段 (emptyData)

用于在详情页处理空值,当值为空时显示占位符。

### 基本使用

```php
$show->field('remark')->emptyData();
```

默认空值显示为 `-`。

### 自定义占位符

```php
// 自定义占位符文本
$show->field('description', '描述')->emptyData('暂无描述');

// 使用 HTML
$show->field('content', '内容')->emptyData('<span class="text-muted">未填写</span>');

// 不同场景使用不同占位符
$show->field('phone', '电话')->emptyData('未绑定');
$show->field('email', '邮箱')->emptyData('未设置');
$show->field('address', '地址')->emptyData('未填写地址');
```

### 示例

```php
$show->field('id', 'ID');
$show->field('username', '用户名');

$show->field('nickname', '昵称')->emptyData('未设置');
$show->field('bio', '个人简介')->emptyData('这个人很懒,什么都没写');
$show->field('company', '公司')->emptyData('未填写');
$show->field('position', '职位')->emptyData('未填写');
```

## 比率字段 (rate)

用于显示百分比或比率值,自动添加后缀。

### 基本使用

```php
$show->field('rate')->rate();
```

默认显示百分号 `%`,不指定小数位。

### 自定义后缀和小数位

```php
// 自定义小数位数
$show->field('rate', '比率')->rate('%', 2);

// 自定义后缀
$show->field('score', '评分')->rate('分', 1);

// 倍数显示
$show->field('multiple', '倍率')->rate('x', 2);

// 温度显示
$show->field('temperature', '温度')->rate('°C', 1);
```

### 示例

```php
$show->field('completion_rate', '完成率')->rate('%', 1);
// 数据库: 95.5
// 显示: 95.5%

$show->field('growth_multiple', '增长倍数')->rate('x', 2);
// 数据库: 3.14159
// 显示: 3.14x

$show->field('discount', '折扣')->rate('%', 0);
// 数据库: 85
// 显示: 85%
```

## 综合示例

### 订单详情页

```php
$show->field('id', '订单ID');
$show->field('order_no', '订单号');

$show->divider();

$show->field('amount', '订单金额')->fee('¥', 2);
$show->field('discount_amount', '优惠金额')->fee('¥', 2);
$show->field('actual_amount', '实付金额')->fee('¥', 2);

$show->divider();

$show->field('discount_rate', '折扣')->rate('%', 1);
$show->field('refund_rate', '退款率')->rate('%', 2);

$show->divider();

$show->field('remark', '订单备注')->emptyData('无备注');
$show->field('express_no', '快递单号')->emptyData('未发货');

$show->field('created_at', '创建时间');
$show->field('updated_at', '更新时间');
```

### 用户详情页

```php
$show->field('id', 'ID');
$show->field('username', '用户名');

$show->field('nickname', '昵称')->emptyData('未设置');
$show->field('avatar', '头像')->image();

$show->divider();

$show->field('balance', '余额')->fee('¥', 2);
$show->field('total_spent', '累计消费')->fee('¥', 2);

$show->divider();

$show->field('completion_rate', '资料完成度')->rate('%', 0);
$show->field('active_rate', '活跃度')->rate('%', 1);

$show->divider();

$show->field('phone', '手机号')->emptyData('未绑定');
$show->field('email', '邮箱')->emptyData('未设置');
$show->field('address', '地址')->emptyData('未填写');

$show->field('bio', '个人简介')
    ->emptyData('这个人很懒,什么都没写')
    ->limit(200);

$show->divider();

$show->field('status', '状态')
    ->using([1 => '正常', 0 => '禁用'])
    ->dot([1 => 'success', 0 => 'danger']);

$show->field('created_at', '注册时间');
$show->field('last_login_at', '最后登录')->emptyData('从未登录');
```

### 商品详情页

```php
$show->field('id', 'ID');
$show->field('name', '商品名称');
$show->field('cover', '封面图')->image();

$show->divider();

$show->field('price', '价格')->fee('¥', 2);
$show->field('cost', '成本')->fee('¥', 2);
$show->field('market_price', '市场价')->fee('¥', 2);

$show->divider();

$show->field('discount', '折扣')->rate('%', 0);
$show->field('profit_rate', '利润率')->rate('%', 1);

$show->divider();

$show->field('stock', '库存');
$show->field('sales', '销量');

$show->divider();

$show->field('description', '商品描述')->emptyData('暂无描述');
$show->field('seo_title', 'SEO标题')->emptyData('未设置');
$show->field('seo_keywords', 'SEO关键词')->emptyData('未设置');

$show->field('created_at', '创建时间');
$show->field('updated_at', '更新时间');
```

## 与其他功能配合

### 与表单和列表保持一致

```php
// Grid 列表
$grid->column('price')->fee('¥', 2);

// Show 详情
$show->field('price')->fee('¥', 2);

// Form 表单
$form->fee('price', '价格')->symbol('¥');
```

### 使用面板组织数据

```php
$show->panel()
    ->title('基本信息')
    ->body(function ($show) {
        $show->field('id', 'ID');
        $show->field('name', '名称');
        $show->field('status', '状态');
    });

$show->panel()
    ->title('金额信息')
    ->body(function ($show) {
        $show->field('amount')->fee('¥', 2);
        $show->field('balance')->fee('¥', 2);
    });

$show->panel()
    ->title('统计信息')
    ->body(function ($show) {
        $show->field('rate')->rate('%', 1);
        $show->field('growth')->rate('%', 2);
    });
```

## 注意事项

1. **Fee 字段**:
   - 数据库中必须以"分"为单位存储
   - 显示时自动除以 100
   - 使用 `money_formatter()` 辅助函数处理

2. **EmptyData 字段**:
   - 判断为空的值: `null`、`''`
   - 可以使用 HTML,不会被转义
   - 建议使用语义化的占位符文本

3. **Rate 字段**:
   - 空值显示为 `0` + 后缀
   - 可以自定义任意后缀符号
   - 小数位为 `null` 时保留原始精度

4. **链式调用**:
   ```php
   $show->field('amount')
       ->fee('¥', 2)
       ->help('单位:元');
   ```

5. **自定义显示**:
   ```php
   // 可以与 display 方法配合使用
   $show->field('amount')->display(function ($value) {
       if ($value > 100000) {
           return '<span class="text-danger">'.
                  money_formatter($value).'</span>';
       }
       return money_formatter($value);
   });
   ```
