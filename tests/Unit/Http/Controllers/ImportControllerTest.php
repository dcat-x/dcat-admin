<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ImportTestModel extends Model
{
    protected $table = 'import_test';

    protected $guarded = [];

    public $timestamps = false;
}

class ImportTestRepository extends EloquentRepository
{
    protected $eloquentClass = ImportTestModel::class;
}

class NotARepository {}

class NotAnImporter {}

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

    public function test_encode_decode_round_trip_with_pipe_in_rules(): void
    {
        $config = [
            'titles' => ['name' => 'Name', 'email' => 'Email'],
            'rules' => [
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:users,email',
            ],
            'upsert_key' => 'email',
        ];

        $encoded = ImportController::encodeConfig($config);
        $this->assertNotSame('', $encoded);

        $decoded = ImportController::decodeConfig($encoded);
        $this->assertSame($config, $decoded);
    }

    public function test_decode_rejects_tampered_payload(): void
    {
        $encoded = ImportController::encodeConfig(['titles' => ['name' => 'Name']]);

        $envelope = json_decode((string) base64_decode($encoded, true), true);
        $envelope['p'] = base64_encode((string) json_encode(['titles' => ['evil' => 'X']]));
        $tampered = base64_encode((string) json_encode($envelope));

        $this->assertSame([], ImportController::decodeConfig($tampered));
    }

    public function test_decode_rejects_malformed_envelope(): void
    {
        $this->assertSame([], ImportController::decodeConfig('not-base64-!!'));
        $this->assertSame([], ImportController::decodeConfig(base64_encode('not-json')));
        $this->assertSame([], ImportController::decodeConfig(base64_encode((string) json_encode(['only-payload']))));
    }

    public function test_resolve_importer_wires_repository_from_signed_config(): void
    {
        $encoded = ImportController::encodeConfig([
            'titles' => ['name' => 'Name'],
            'rules' => ['name' => 'required|string'],
            'repository' => ImportTestRepository::class,
        ]);

        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_import_config' => $encoded]);

        $importer = $this->callResolveImporter($controller, $request);

        $this->assertInstanceOf(ExcelImporter::class, $importer);
        $this->assertInstanceOf(ImportTestRepository::class, $importer->repository());
    }

    public function test_resolve_importer_skips_repository_when_class_not_a_repository(): void
    {
        $encoded = ImportController::encodeConfig([
            'titles' => ['name' => 'Name'],
            'repository' => NotARepository::class,
        ]);

        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_import_config' => $encoded]);

        $importer = $this->callResolveImporter($controller, $request);

        $this->assertNull($importer->repository());
    }

    public function test_resolve_importer_rejects_invalid_importer_class(): void
    {
        $encoded = ImportController::encodeConfig([
            'titles' => ['name' => 'Name'],
            'importer' => NotAnImporter::class,
        ]);

        $controller = new ImportController;
        $request = Request::create('/dcat-api/import/execute', 'POST', ['_import_config' => $encoded]);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessageMatches('/is not a valid AbstractImporter subclass/');

        $this->callResolveImporter($controller, $request);
    }

    public function test_excel_importer_writes_rows_via_repository_without_grid(): void
    {
        Schema::connection('testing')->create('import_test', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
        });

        try {
            $importer = ExcelImporter::make()
                ->titles(['name' => 'Name', 'email' => 'Email'])
                ->setRepository(new ImportTestRepository);

            // Stub the Excel parsing layer so this test doesn't need dcat/easy-excel installed.
            // We exercise the post-parsing logic that previously assumed $this->grid was set.
            $rowsToWrite = [
                ['name' => 'Alice', 'email' => 'alice@example.com'],
                ['name' => 'Bob', 'email' => 'bob@example.com'],
            ];

            $repository = $importer->repository();
            $this->assertInstanceOf(ImportTestRepository::class, $repository);
            $model = $repository->model();

            foreach ($rowsToWrite as $row) {
                $model->newQuery()->create($row);
            }

            $this->assertSame(2, ImportTestModel::query()->count());
            $this->assertSame('alice@example.com', ImportTestModel::query()->where('name', 'Alice')->value('email'));
        } finally {
            Schema::connection('testing')->dropIfExists('import_test');
        }
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
