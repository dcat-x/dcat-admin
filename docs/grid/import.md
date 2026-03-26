# 数据导入

Grid 提供了与数据导出对称的数据导入功能，支持通过 Excel/CSV 文件批量导入数据。该功能默认关闭，需要显式调用启用。

## 基本使用

```php
// 启用默认的 Excel 导入
$grid->import();
```

启用后，工具栏会出现「导入」按钮。点击后弹窗显示：
1. **下载模板** — 基于 Grid 列定义自动生成 Excel 模板
2. **选择文件** — 上传 `.xlsx`、`.xls` 或 `.csv` 文件
3. 确认后执行导入，返回成功/失败统计

## 自定义导入驱动

```php
use Dcat\Admin\Grid\Importers\ExcelImporter;

$grid->import(
    ExcelImporter::make()
        ->rules(['name' => 'required', 'email' => 'required|email'])
        ->upsertKey('email')  // 按 email 去重，存在则更新
);
```

## 自定义导入类

创建自定义导入类继承 `AbstractImporter`：

```php
use Dcat\Admin\Grid\Importers\AbstractImporter;
use Dcat\Admin\Grid\Importers\ImportResult;
use Illuminate\Http\UploadedFile;

class UserImporter extends AbstractImporter
{
    public function import(UploadedFile $file): ImportResult
    {
        $result = new ImportResult;

        // 自定义导入逻辑

        return $result;
    }
}
```

然后在 Grid 中使用：

```php
$grid->import(new UserImporter);
```

## API 参考

### AbstractImporter 方法

| 方法 | 说明 |
|------|------|
| `rules(array $rules)` | 设置验证规则 |
| `upsertKey(string $key)` | 设置 upsert 唯一键（默认 null，仅 insert） |
| `titles(array $titles)` | 自定义列标题映射 |
| `template()` | 生成下载模板 |

### ImportResult 属性

| 属性 | 类型 | 说明 |
|------|------|------|
| `$success` | `int` | 成功导入的行数 |
| `$failed` | `int` | 失败的行数 |
| `$errors` | `array` | 错误详情，格式 `[行号 => [字段 => 错误信息]]` |

## 扩展导入驱动

注册自定义驱动：

```php
use Dcat\Admin\Grid\Importer;

Importer::extend('custom', CustomImporter::class);
```

然后通过驱动名使用：

```php
$grid->import('custom');
```

## 注意事项

- 依赖 `dcat/easy-excel` 包（已包含在项目依赖中）
- 导入路由自动注册，无需手动配置
- 弹窗使用 Layer.js，遮罩层配置为 `shade: [0.3, '#000']`
