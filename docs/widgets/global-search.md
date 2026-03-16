# 全局搜索

全局搜索组件提供一个导航栏搜索框，支持通过快捷键唤起，在多个数据源中搜索并展示分组结果。该功能默认关闭，需要注册搜索提供者后才会启用。

## 基本使用

在 `app/Admin/bootstrap.php` 或 ServiceProvider 中注册：

```php
use Dcat\Admin\Admin;
use Dcat\Admin\Support\GlobalSearch\MenuSearchProvider;

Admin::globalSearch()
    ->provider(new MenuSearchProvider)
    ->shortcut('Ctrl+K');
```

启用后：
- 导航栏左侧出现搜索框
- 按 `Ctrl+K`（或自定义快捷键）聚焦搜索框
- 输入关键词后 300ms 自动搜索，结果按提供者分组显示
- 支持键盘上下导航和 Enter 跳转

## 内置搜索提供者

### MenuSearchProvider

搜索后台菜单标题，返回匹配的菜单项及其链接。

```php
Admin::globalSearch()
    ->provider(new MenuSearchProvider);
```

## 自定义搜索提供者

### 使用 ModelSearchProvider 基类

适用于搜索 Eloquent 模型的场景：

```php
use Dcat\Admin\Support\GlobalSearch\ModelSearchProvider;

class UserSearchProvider extends ModelSearchProvider
{
    public function title(): string
    {
        return '用户';
    }

    protected function model(): string
    {
        return \App\Models\User::class;
    }

    protected function searchColumns(): array
    {
        return ['name', 'email'];
    }

    protected function titleColumn(): string
    {
        return 'name';
    }

    protected function url($model): string
    {
        return admin_url('users/' . $model->id);
    }

    // 可选：自定义图标
    protected function icon(): string
    {
        return 'feather icon-user';
    }

    // 可选：描述列
    protected function descriptionColumn(): ?string
    {
        return 'email';
    }
}
```

### 实现 SearchProviderInterface

完全自定义搜索逻辑：

```php
use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;

class OrderSearchProvider implements SearchProviderInterface
{
    public function title(): string
    {
        return '订单';
    }

    public function search(string $keyword, int $limit = 5): array
    {
        // 返回格式：
        return [
            [
                'title' => '订单 #1001',
                'url' => '/admin/orders/1001',
                'icon' => 'feather icon-shopping-cart',  // 可选
                'description' => '2024-01-01',           // 可选
            ],
        ];
    }
}
```

## 注册多个提供者

```php
Admin::globalSearch()
    ->provider(new MenuSearchProvider)
    ->provider(new UserSearchProvider)
    ->provider(new OrderSearchProvider)
    ->shortcut('Ctrl+K');
```

搜索结果按提供者分组显示，每组最多返回 5 条结果（可在提供者中自定义 `$limit`）。

## 自定义快捷键

```php
Admin::globalSearch()->shortcut('Cmd+K');   // macOS
Admin::globalSearch()->shortcut('Ctrl+K');  // 默认
```

## 交互特性

- 300ms 防抖，避免频繁请求
- 键盘上下箭头导航结果列表
- Enter 跳转到选中的结果
- ESC 或点击外部关闭结果面板
- 搜索结果中的 HTML 实体自动转义，防止 XSS
