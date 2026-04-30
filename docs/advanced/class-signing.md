# Action/Form 类名签名

## 背景

dcat-admin 的 Action 和 Widget Form 通过 AJAX 将 PHP 类名传递给服务端进行实例化。为防止攻击者伪造类名实例化任意类，所有类名在传递前会附加 HMAC-SHA256 签名。

## 工作原理

```
渲染阶段（服务端 → 前端）：
  PHP 类名 → ClassSigner::sign() → "类名|签名" → 写入 JS/hidden field

请求阶段（前端 → 服务端）：
  "类名|签名" → ClassSigner::verify() → 校验通过 → app($class)
                                       → 校验失败 → 抛出异常
```

签名使用 `APP_KEY` 作为密钥，通过 `hash_hmac('sha256', $class, $key)` 生成。相同类名在相同应用实例中始终产生相同签名。

## 对前端的影响

**无影响。** 签名拼接在类名后面透传：

- Action：`calledClass` 字段包含签名，JS 原样传回 `_action` 参数
- Form：hidden field `_form_` 的值包含签名，表单提交时原样传回

## 对外部系统的影响

如果你有外部系统直接构造 POST 请求调用 `/dcat-api/action` 或 `/dcat-api/form`，需要：

1. 在服务端使用 `ClassSigner::sign()` 生成签名值
2. 将签名值作为 `_action`（Action）或 `_form_`（Form）参数传递

```php
use Dcat\Admin\Support\ClassSigner;

// Action
$signed = ClassSigner::sign(\App\Actions\MyAction::class);
$actionParam = str_replace('\\', '_', $signed);
// POST /dcat-api/action  { _action: $actionParam, ... }

// Form
$signed = ClassSigner::sign(\App\Widgets\MyForm::class);
// POST /dcat-api/form  { _form_: $signed, ... }
```

## ClassSigner API

```php
// 签名
ClassSigner::sign(string $class): string
// 返回 "Namespace\Class|64位十六进制签名"

// 验证
ClassSigner::verify(string $signed): string
// 返回类名，签名无效或缺失时抛出 AdminException
```

## 升级兼容（未签名兜底开关）

> ⚠️ 安全提示：未签名兜底**默认关闭**。开启后任何登录管理员都能通过未签名请求派发任意已存在的 Action/Form 类，绕过签名校验。

老版本（< 该签名机制引入前）渲染的页面被浏览器缓存后，提交时会带未签名的类名。如果担心这部分流量被一刀切拒绝，可在升级期间临时打开兜底：

```php
// config/admin.php
'allow_unsigned_dispatch' => env('ADMIN_ALLOW_UNSIGNED_DISPATCH', true),
```

或在 `.env` 中：

```dotenv
ADMIN_ALLOW_UNSIGNED_DISPATCH=true
```

打开后未签名请求会被放行，但日志中会留下 `admin.class_signer.unsigned` 的 warning 记录，便于追踪。**待所有用户刷新页面后必须立即关闭**，否则等同于回退到无签名状态，签名机制形同虚设。
