# 弹窗表格

`Dcat\Admin\Widgets\DialogTable`用于在弹窗中异步加载数据表格，需配合`LazyRenderable`使用。

## 基本用法

首先定义渲染类，继承`Dcat\Admin\Grid\LazyRenderable`

```php
<?php

namespace App\Admin\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;

class UserTable extends LazyRenderable
{
    public function grid(): Grid
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->id;
            $grid->name;
            $grid->email;
            $grid->created_at;
        });
    }
}
```

然后使用`DialogTable`

```php
<?php

use App\Admin\Renderable\UserTable;
use Dcat\Admin\Widgets\DialogTable;

$dialog = DialogTable::make(UserTable::make())
    ->title('用户列表')
    ->button('点击查看');
```

## 设置弹窗

```php
$dialog = DialogTable::make($table)
    ->title('标题')
    ->width('900px')       // 弹窗宽度
    ->maxmin(true)         // 显示最大/最小化按钮
    ->resize(true);        // 允许调整大小
```

## 自定义按钮

```php
// 传入 HTML 字符串
$dialog = DialogTable::make($table)
    ->button('<button class="btn btn-primary">查看详情</button>');

// 如果没有 HTML 标签，自动包裹为 <a> 标签
$dialog = DialogTable::make($table)->button('查看详情');
```

## 设置底部内容

```php
$dialog = DialogTable::make($table)
    ->footer('<button class="btn btn-primary">确定</button>');
```

## 事件监听

```php
$dialog = DialogTable::make($table)
    ->onShown('console.log("弹窗已打开")')
    ->onHidden('console.log("弹窗已关闭")')
    ->onLoad('console.log("表格已加载完毕")');
```

## 在页面中使用

```php
use Dcat\Admin\Layout\Content;

return $content->body(
    DialogTable::make(UserTable::make())
        ->title('用户列表')
        ->button('查看用户')
);
```
