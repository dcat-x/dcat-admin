# 代码

`Dcat\Admin\Widgets\Code`用于显示代码高亮，继承自`Markdown`组件。

## 基本用法

```php
<?php

use Dcat\Admin\Widgets\Code;

// 默认 PHP 代码
$code = Code::make('<?php echo "Hello World";');
```

## 设置语言

```php
$code = Code::make($content)->lang('javascript');

// 或使用快捷方法
$code = Code::make($content)->javascript();
$code = Code::make($content)->asHtml();
$code = Code::make($content)->java();
$code = Code::make($content)->python();
```

## 读取文件内容

可以直接传入文件路径，自动读取文件内容。

```php
// 读取整个文件
$code = Code::make('/path/to/file.php');

// 读取指定行范围
$code = Code::make('/path/to/file.php', 10, 20);
```

## 读取文件片段

```php
// 读取第 50 行前后 5 行的内容
$code = Code::make('')->section('/path/to/file.php', 50, 5);
```

## 在页面中使用

```php
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Code;

return $content->body(
    Card::make('代码预览', Code::make($phpCode)->lang('php'))
);
```
