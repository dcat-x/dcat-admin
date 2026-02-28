# 表格

`Dcat\Admin\Widgets\Table`用于快速渲染简单的 HTML 表格，支持表头、行数据和嵌套表格。

## 基本用法

```php
<?php

use Dcat\Admin\Widgets\Table;

// 带表头
$table = Table::make(['名称', '邮箱', '状态'], [
    ['张三', 'zhangsan@example.com', '启用'],
    ['李四', 'lisi@example.com', '禁用'],
]);
```

## 只有数据

如果只传一个参数，则作为数据行使用。

```php
$table = Table::make([
    ['名称', '张三'],
    ['邮箱', 'zhangsan@example.com'],
    ['状态', '启用'],
]);
```

## 边框

```php
$table = Table::make($headers, $rows)->withBorder();
```

## 样式

```php
$table = Table::make($headers, $rows)->setStyle(['table-hover']);
```

## 嵌套表格

行数据中如果包含关联数组，会自动渲染为嵌套表格。

```php
$table = Table::make(['字段', '值'], [
    ['用户名', '张三'],
    ['角色', ['管理员', '编辑']],
    ['详情', ['邮箱' => 'test@example.com', '电话' => '13800138000']],
]);
```
