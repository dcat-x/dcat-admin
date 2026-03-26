<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Grid\Importers\ExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ImportController extends Controller
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

    public function template(Request $request)
    {
        $importer = $this->resolveImporter($request);

        return $importer->template();
    }

    public function preview(Request $request)
    {
        $request->validate(['import_file' => 'required|file']);

        $importer = $this->resolveImporter($request);

        return response()->json(
            $importer->preview($request->file('import_file'))
        );
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
        $gridName = (string) $request->input('_grid', '');
        $config = static::$importerRegistry[$gridName] ?? [];

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
