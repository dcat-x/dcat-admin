<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

    public function test_resolve_importer_returns_default_when_grid_name_not_registered(): void
    {
        ImportController::registerImporter('other-grid', [
            'titles' => ['name' => 'Name'],
        ]);

        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_grid' => 'unknown-grid']);

        $importer = $this->callResolveImporter($controller, $request);

        $this->assertInstanceOf(ExcelImporter::class, $importer);
        $this->assertSame([], $importer->titles());
    }

    public function test_execute_validates_import_file_required(): void
    {
        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_grid' => 'test']);
        $request->setLaravelSession($this->app['session.store']);

        $this->expectException(ValidationException::class);
        $controller->execute($request);
    }

    public function test_api_routes_include_template_and_execute_but_not_preview(): void
    {
        Admin::registerApiRoutes();

        $routes = collect(Route::getRoutes()->getRoutes());
        $apiRouteNames = $routes->map(fn ($r) => $r->getName())->filter()->values()->toArray();

        $this->assertContains('dcat-api.import.template', $apiRouteNames);
        $this->assertContains('dcat-api.import.execute', $apiRouteNames);
        $this->assertNotContains('dcat-api.import.preview', $apiRouteNames);
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
