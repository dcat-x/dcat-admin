# 暗黑模式切换器

`Dcat\Admin\Widgets\DarkModeSwitcher`用于在页面头部导航栏中渲染暗黑模式切换按钮。

## 基本用法

```php
<?php

use Dcat\Admin\Widgets\DarkModeSwitcher;

$switcher = new DarkModeSwitcher();
```

## 默认开启暗黑模式

```php
$switcher = new DarkModeSwitcher(true);
```

## 在导航栏中使用

通常在`bootstrap.php`中通过`Admin::navbar()`注入到导航栏：

```php
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\DarkModeSwitcher;

Admin::navbar(function ($navbar) {
    $navbar->right(new DarkModeSwitcher());
});
```
