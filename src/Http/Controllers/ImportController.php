<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Grid\Importers\ExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ImportController extends Controller
{
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
        return ExcelImporter::make();
    }
}
