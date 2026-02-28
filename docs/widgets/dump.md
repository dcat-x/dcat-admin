# 调试输出

`Dcat\Admin\Widgets\Dump`用于在页面中格式化输出调试信息，支持数组、对象、JSON 等数据类型。

## 基本用法

```php
<?php

use Dcat\Admin\Widgets\Dump;

// 输出字符串
$dump = Dump::make('调试信息...');

// 输出数组
$dump = Dump::make(['name' => '张三', 'email' => 'test@example.com']);

// 输出 JSON 字符串（自动解析为数组格式化展示）
$dump = Dump::make('{"name":"张三","age":18}');
```

## 设置样式

```php
// 设置内边距
$dump = Dump::make($data)->padding('20px');

// 设置最大宽度
$dump = Dump::make($data)->maxWidth('600px');
```

## 在页面中使用

```php
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Dump;

return $content->body(
    Dump::make(config('admin'))
);
```
