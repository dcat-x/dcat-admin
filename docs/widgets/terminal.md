# 终端输出

`Dcat\Admin\Widgets\Terminal`用于显示终端风格的输出内容，可以执行 Artisan 命令并展示结果。

## 基本用法

```php
<?php

use Dcat\Admin\Widgets\Terminal;

$terminal = Terminal::make('输出内容...');
```

## 执行 Artisan 命令

```php
// 执行命令并显示输出
$terminal = Terminal::call('migrate:status');

// 带参数的命令
$terminal = Terminal::call('route:list', ['--compact' => true]);
```

## 样式

```php
// 深色背景
$terminal = Terminal::make($output)->dark();

// 透明背景
$terminal = Terminal::make($output)->transparent();
```

## 在页面中使用

```php
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Terminal;

return $content->body(Terminal::call('about')->dark());
```
