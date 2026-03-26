<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

class ImportControllerTest extends TestCase
{
    public function test_resolve_importer_returns_default_without_registry(): void
    {
        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST');

        $importer = $this->callResolveImporter($controller, $request);

        $this->assertInstanceOf(ExcelImporter::class, $importer);
    }

    public function test_resolve_importer_uses_registered_config(): void
    {
        ImportController::registerImporter('test-grid', [
            'titles' => ['name' => 'Name', 'email' => 'Email'],
            'rules' => ['name' => 'required'],
            'upsert_key' => 'email',
        ]);

        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_grid' => 'test-grid']);

        $importer = $this->callResolveImporter($controller, $request);

        $this->assertInstanceOf(ExcelImporter::class, $importer);
        $this->assertSame(['name' => 'Name', 'email' => 'Email'], $importer->titles());
        $this->assertSame(['name' => 'required'], $importer->rules());
        $this->assertSame('email', $importer->upsertKey());
    }

    protected function tearDown(): void
    {
        ImportController::flushImporterRegistry();
        parent::tearDown();
    }

    protected function callResolveImporter(ImportController $controller, Request $request): ExcelImporter
    {
        $method = new \ReflectionMethod($controller, 'resolveImporter');

        return $method->invoke($controller, $request);
    }
}
