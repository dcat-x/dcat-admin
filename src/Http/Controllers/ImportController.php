<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Contracts\Resettable;
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
     */
    public static function encodeConfig(array $config): string
    {
        if (! $config) {
            return '';
        }

        $json = json_encode($config, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $json, (string) config('app.key'));

        return base64_encode($json.'|'.$signature);
    }

    /**
     * Decode and verify importer config from request.
     *
     * @return array{titles?: array, rules?: array, upsert_key?: string|null}
     */
    public static function decodeConfig(string $encoded): array
    {
        if (! $encoded) {
            return [];
        }

        $decoded = base64_decode($encoded, true);
        if ($decoded === false) {
            return [];
        }

        $parts = explode('|', $decoded, 2);
        if (count($parts) !== 2) {
            return [];
        }

        [$json, $signature] = $parts;
        $expected = hash_hmac('sha256', $json, (string) config('app.key'));

        if (! hash_equals($expected, $signature)) {
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

    protected function resolveImporter(Request $request): ExcelImporter
    {
        // Priority: request-embedded config > static registry > empty
        $config = self::decodeConfig((string) $request->input('_import_config', ''));

        if (! $config) {
            $gridName = (string) $request->input('_grid', '');
            $config = static::$importerRegistry[$gridName] ?? [];
        }

        $importer = ExcelImporter::make();

        if (! empty($config['titles'])) {
            $importer->titles($config['titles']);
        }

        if (! empty($config['rules'])) {
            $importer->rules($config['rules']);
        }

        if (! empty($config['upsert_key'])) {
            $importer->upsertKey($config['upsert_key']);
        }

        return $importer;
    }
}
