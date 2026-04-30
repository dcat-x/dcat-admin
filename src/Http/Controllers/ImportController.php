<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Grid\Importers\AbstractImporter;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Http\Auth\Permission as PermissionChecker;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;

class ImportController extends Controller implements Resettable
{
    /**
     * @var array<string, array{titles?: array, rules?: array, upsert_key?: string|null}>
     */
    protected static array $importerRegistry = [];

    public static function registerImporter(string $gridName, array $config): void
    {
        static::$importerRegistry[$gridName] = $config;
    }

    public static function flushImporterRegistry(): void
    {
        static::$importerRegistry = [];
    }

    public static function resetState(): void
    {
        static::$importerRegistry = [];
    }

    /**
     * Encode importer config for embedding in HTML/JS (signed to prevent tampering).
     *
     * Envelope: base64( json({ p: base64(payload), s: hmac }) ).
     * payload 用 base64 包一层避免和 envelope 分隔符混淆，
     * 防止 payload 中包含 | 等字符时签名校验失败。
     */
    public static function encodeConfig(array $config): string
    {
        if (! $config) {
            return '';
        }

        $json = json_encode($config, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $json, (string) config('app.key'));

        $envelope = json_encode([
            'p' => base64_encode($json),
            's' => $signature,
        ], JSON_THROW_ON_ERROR);

        return base64_encode($envelope);
    }

    /**
     * Decode and verify importer config from request.
     *
     * @return array{
     *     titles?: array,
     *     rules?: array,
     *     upsert_key?: string|null,
     *     importer?: string,
     *     repository?: string,
     *     model?: string,
     *     user_id?: int|string,
     *     source?: string,
     *     expires_at?: int,
     * }
     */
    public static function decodeConfig(string $encoded): array
    {
        if (! $encoded) {
            return [];
        }

        $envelopeJson = base64_decode($encoded, true);
        if ($envelopeJson === false) {
            return [];
        }

        $envelope = json_decode($envelopeJson, true);
        if (! is_array($envelope) || ! isset($envelope['p'], $envelope['s'])) {
            return [];
        }

        $json = base64_decode((string) $envelope['p'], true);
        if ($json === false) {
            return [];
        }

        $expected = hash_hmac('sha256', $json, (string) config('app.key'));

        if (! hash_equals($expected, (string) $envelope['s'])) {
            return [];
        }

        $config = json_decode($json, true);

        return is_array($config) ? $config : [];
    }

    public function template(Request $request)
    {
        $config = $this->loadConfig($request);
        // template 不写库，跳过过期 / 用户绑定 / 路径权限重检——
        // 让用户在签名过期后仍能下载模板调试，副作用只是模板生成。
        $importer = $this->resolveImporter($config);

        return $importer->template();
    }

    public function execute(Request $request)
    {
        $request->validate(['import_file' => 'required|file']);

        $config = $this->loadConfig($request);
        $this->guardExecute($config);

        $importer = $this->resolveImporter($config);

        $result = $importer->import($request->file('import_file'));

        $message = trans('admin.import_completed', [
            'success' => $result->success,
            'failed' => $result->failed,
        ], 'en') !== 'admin.import_completed'
            ? trans('admin.import_completed', ['success' => $result->success, 'failed' => $result->failed])
            : "Import completed: {$result->success} succeeded, {$result->failed} failed";

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'success' => $result->success,
                'failed' => $result->failed,
                'errors' => $result->errors,
            ],
        ]);
    }

    /**
     * 解析请求中的 import 配置：优先用 client 的签名 config，回退静态注册表。
     *
     * @return array<string, mixed>
     */
    protected function loadConfig(Request $request): array
    {
        $config = self::decodeConfig((string) $request->input('_import_config', ''));

        if (! $config) {
            $gridName = (string) $request->input('_grid', '');
            $config = static::$importerRegistry[$gridName] ?? [];
        }

        return $config;
    }

    /**
     * execute 写库前的三重授权检查：过期、用户绑定、来源页面权限重检。
     * 任一失败立即 abort，避免签名 config 沦为可重放的写权限。
     *
     * @param  array<string, mixed>  $config
     */
    protected function guardExecute(array $config): void
    {
        // 过期：超过 expires_at 即使签名合法也拒绝。
        if (! empty($config['expires_at']) && (int) $config['expires_at'] < time()) {
            abort(419, 'Import config expired.');
        }

        // 用户绑定：签名 config 只对生成它时的登录用户有效。
        // 即使两个管理员都登录、cookie 都没过期，也无法互相重放。
        $currentUserKey = Admin::user()?->getKey();
        if (! empty($config['user_id'])
            && (string) $config['user_id'] !== (string) $currentUserKey) {
            abort(403, 'Import config bound to a different user.');
        }

        // 来源权限重检：用 source 路由名/路径模拟一次该页面的权限检查。
        // 离职/降权后即使持有未过期的签名 config，也会因失去源页面权限而被拒。
        if (! empty($config['source'])) {
            $this->checkSourcePermission((string) $config['source']);
        }
    }

    /**
     * 用签名 config 中的 source 字段重跑一次权限检查。
     * source 可能是路由名（首选）或 URL path。
     */
    protected function checkSourcePermission(string $source): void
    {
        $route = Route::getRoutes()->getByName($source);
        if (! $route) {
            return; // 路由不存在则跳过此项；过期 / 用户绑定仍是兜底。
        }

        // 取该路由 admin.permission 中间件参数（如有），用 Checker 校验。
        // 没有显式参数时跳过——这与中间件本身行为一致（无参数=任意已登录管理员）。
        foreach ($route->gatherMiddleware() as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }
            if (str_starts_with($middleware, 'admin.permission:')) {
                $args = substr($middleware, strlen('admin.permission:'));
                $permissions = array_filter(array_map('trim', explode(',', $args)));
                if ($permissions) {
                    PermissionChecker::check($permissions);
                }
            }
        }
    }

    /**
     * 用 config 重建 importer + repository。所有授权检查必须在调用前完成。
     *
     * @param  array<string, mixed>  $config
     */
    protected function resolveImporter(array $config): AbstractImporter
    {
        $importer = $this->buildImporter($config);

        if (! empty($config['titles'])) {
            $importer->titles($config['titles']);
        }

        if (! empty($config['rules'])) {
            $importer->rules($config['rules']);
        }

        if (! empty($config['upsert_key'])) {
            $importer->upsertKey($config['upsert_key']);
        }

        if (! empty($config['repository'])) {
            $repository = $this->buildRepository(
                (string) $config['repository'],
                isset($config['model']) ? (string) $config['model'] : null
            );
            if ($repository) {
                $importer->setRepository($repository);
            }
        }

        return $importer;
    }

    /**
     * Instantiate the importer class declared in the signed config.
     * Falls back to ExcelImporter for safety; rejects classes that aren't
     * AbstractImporter subclasses.
     *
     * @throws AdminException
     */
    protected function buildImporter(array $config): AbstractImporter
    {
        $class = (string) ($config['importer'] ?? ExcelImporter::class);

        if (! class_exists($class) || ! is_subclass_of($class, AbstractImporter::class)) {
            throw new AdminException("Importer [{$class}] is not a valid AbstractImporter subclass.");
        }

        /** @var AbstractImporter $importer */
        $importer = $class::make();

        return $importer;
    }

    /**
     * 重建 repository 实例。
     *
     * - 用户自定义 repository（继承 EloquentRepository 并自带 eloquentClass）：
     *   通过 IoC 容器解析即可，构造函数会读到子类硬编码的 model。
     * - 默认 EloquentRepository（$grid = new Grid(new SomeModel) 这种用法）：
     *   IoC 解析会拿到一个空壳；需要从 config['model'] 中拿回 model 类名重建。
     *
     * 不合法的输入返回 null，让 importer 在 import() 时报 RuntimeException
     * 而不是裸指针。
     */
    protected function buildRepository(string $class, ?string $modelClass = null): ?Repository
    {
        if (! class_exists($class) || ! is_subclass_of($class, Repository::class)) {
            return null;
        }

        // 默认 EloquentRepository 必须配 model 类名才能用，否则构造时抛 TypeError。
        if ($class === EloquentRepository::class) {
            if (! $modelClass
                || ! class_exists($modelClass)
                || ! is_subclass_of($modelClass, EloquentModel::class)) {
                return null;
            }

            return new EloquentRepository($modelClass);
        }

        /** @var Repository $repository */
        $repository = app($class);

        return $repository;
    }
}
