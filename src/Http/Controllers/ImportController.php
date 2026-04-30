<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Grid\Importers\AbstractImporter;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
     * @return array{titles?: array, rules?: array, upsert_key?: string|null, importer?: string, repository?: string}
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
        $importer = $this->resolveImporter($request);

        return $importer->template();
    }

    public function execute(Request $request)
    {
        $request->validate(['import_file' => 'required|file']);

        $importer = $this->resolveImporter($request);

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

    protected function resolveImporter(Request $request): AbstractImporter
    {
        // Priority: request-embedded config > static registry > empty
        $config = self::decodeConfig((string) $request->input('_import_config', ''));

        if (! $config) {
            $gridName = (string) $request->input('_grid', '');
            $config = static::$importerRegistry[$gridName] ?? [];
        }

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
            $repository = $this->buildRepository((string) $config['repository']);
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
     * Instantiate the repository declared in the signed config.
     * Returns null if class isn't a Repository implementation; the importer
     * will then surface a clearer error than blindly trusting the input.
     */
    protected function buildRepository(string $class): ?Repository
    {
        if (! class_exists($class) || ! is_subclass_of($class, Repository::class)) {
            return null;
        }

        /** @var Repository $repository */
        $repository = app($class);

        return $repository;
    }
}
