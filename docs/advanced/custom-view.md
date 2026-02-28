# 自定义页面视图

`AdminController` 默认使用 `Grid`、`Form`、`Show` 组件来构建列表、新建/编辑、详情页面。如果你需要完全自定义某个页面的 HTML，可以通过实现 `custom*` 方法来替代默认行为，无需重写整个控制器方法。

## 基本用法

在子类控制器中，实现以下任意 `custom*` 方法即可接管对应页面，未实现的方法仍走原有的 `grid()`、`form()`、`detail()` 逻辑。

### 可用方法

| 方法 | 作用 | 替代原来的 |
|------|------|-----------|
| `customIndex()` | 自定义列表页内容 | `grid()` |
| `customShow($id)` | 自定义详情页内容 | `detail($id)` |
| `customCreate()` | 自定义新建页内容 | `form()` |
| `customEdit($id)` | 自定义编辑页内容 | `form()->edit($id)` |
| `customStore()` | 自定义新建提交处理 | `form()->store()` |
| `customUpdate($id)` | 自定义更新提交处理 | `form()->update($id)` |
| `customDestroy($id)` | 自定义删除处理 | `form()->destroy($id)` |

### 返回值约定

**展示方法** (`customIndex`、`customShow`、`customCreate`、`customEdit`)：

返回值会传给 `Content::body()`，支持以下类型：
- `string` — 直接输出 HTML 字符串
- `Renderable` — 如 `view()` 返回的视图、自定义 Widget
- `Closure` — 回调接收 `Row` 对象，用于 Row/Column 栅格布局
- `Htmlable` — Laravel 的 `Htmlable` 接口实现

**提交方法** (`customStore`、`customUpdate`、`customDestroy`)：

返回 `JsonResponse` 对象以对接前端 AJAX 响应机制，在控制器中使用时需要加上 `send` 方法：

```php
use Dcat\Admin\Http\JsonResponse;

return JsonResponse::make()->success('操作成功')->redirect('orders')->send();
```

更多 `JsonResponse` 用法请参考[动作以及表单响应](response.md)。

## 完整示例

### 所有页面自定义

```php
<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\JsonResponse;

class OrderController extends AdminController
{
    protected $title = '订单管理';

    // 自定义列表页
    protected function customIndex()
    {
        return view('admin.order.index', [
            'orders' => Order::paginate(20),
        ]);
    }

    // 自定义详情页
    protected function customShow($id)
    {
        return view('admin.order.show', [
            'order' => Order::findOrFail($id),
        ]);
    }

    // 自定义新建页
    protected function customCreate()
    {
        return view('admin.order.create');
    }

    // 自定义编辑页
    protected function customEdit($id)
    {
        return view('admin.order.edit', [
            'order' => Order::findOrFail($id),
        ]);
    }

    // 自定义新建提交
    protected function customStore()
    {
        $data = request()->validate([
            'title'  => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $order = Order::create($data);

        return JsonResponse::make()
            ->success('创建成功')
            ->redirect('orders/' . $order->id)
            ->send();
    }

    // 自定义更新提交
    protected function customUpdate($id)
    {
        $data = request()->validate([
            'title'  => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Order::findOrFail($id)->update($data);

        return JsonResponse::make()
            ->success('更新成功')
            ->redirect('orders')
            ->send();
    }

    // 自定义删除
    protected function customDestroy($id)
    {
        Order::destroy(explode(',', $id));

        return JsonResponse::make()
            ->success('删除成功')
            ->send();
    }
}
```

### 混合模式

可以只对部分页面使用自定义视图，其余继续使用默认组件：

```php
class ProductController extends AdminController
{
    // 列表页继续使用 Grid
    protected function grid()
    {
        return Grid::make(new Product(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('price');
        });
    }

    // 详情页继续使用 Show
    protected function detail($id)
    {
        return Show::make($id, new Product(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('price');
        });
    }

    // 只有新建和编辑页使用自定义视图
    protected function customCreate()
    {
        return view('admin.product.create');
    }

    protected function customEdit($id)
    {
        return view('admin.product.edit', [
            'product' => Product::findOrFail($id),
        ]);
    }

    protected function customStore()
    {
        // 自定义新建逻辑...
    }

    protected function customUpdate($id)
    {
        // 自定义更新逻辑...
    }

    // form() 仍然需要定义，因为 destroy 默认使用它
    protected function form()
    {
        return Form::make(new Product(), function (Form $form) {
            $form->text('name');
            $form->currency('price');
        });
    }
}
```

## 自定义样式和JS

在 `custom*` 方法中，可以通过 `Admin::style()`、`Admin::css()`、`Admin::js()`、`Admin::script()` 注入页面资源：

```php
use Dcat\Admin\Admin;

protected function customEdit($id)
{
    // 注入 CSS 文件
    Admin::css('vendor/order/edit.css');

    // 注入内联样式
    Admin::style('.order-form .amount { font-weight: bold; color: #e74c3c; }');

    // 注入 JS 文件
    Admin::js('vendor/order/edit.js');

    // 注入 JS 代码
    Admin::script(
        <<<JS
$('.order-form').on('submit', function(e) {
    // ...
});
JS
    );

    return view('admin.order.edit', [
        'order' => Order::findOrFail($id),
    ]);
}
```

也可以在 Blade 视图中使用 `admin_view` 来组织 HTML、CSS、JS，参考[视图与自定义页面](../getting-started/custom-page.md)。

## 使用 Row/Column 布局

展示方法支持传入闭包来使用栅格布局：

```php
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;

protected function customShow($id)
{
    $order = Order::findOrFail($id);

    return function (Row $row) use ($order) {
        $row->column(8, view('admin.order.detail', compact('order')));
        $row->column(4, view('admin.order.sidebar', compact('order')));
    };
}
```
