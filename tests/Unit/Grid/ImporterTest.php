<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importer;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ImporterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_constructor_accepts_grid(): void
    {
        $grid = Mockery::mock(Grid::class);
        $importer = new Importer($grid);

        $this->assertInstanceOf(Importer::class, $importer);
    }

    public function test_driver_property_initially_null(): void
    {
        $grid = Mockery::mock(Grid::class);
        $importer = new Importer($grid);
        $ref = new \ReflectionProperty($importer, 'driver');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($importer));
    }

    public function test_resolve_with_abstract_importer_instance(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('importerManager')->andReturn(new Importer($grid));

        $importer = new Importer($grid);
        $driver = ExcelImporter::make();

        $resolved = $importer->resolve($driver);

        $this->assertSame($driver, $resolved);
    }

    public function test_resolve_returns_same_driver_on_second_call(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('importerManager')->andReturn(new Importer($grid));

        $importer = new Importer($grid);
        $driver = ExcelImporter::make();

        $first = $importer->resolve($driver);
        $second = $importer->resolve();

        $this->assertSame($first, $second);
    }

    public function test_make_default_driver(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('importerManager')->andReturn(new Importer($grid));

        $importer = new Importer($grid);
        $driver = $importer->makeDefaultDriver();

        $this->assertInstanceOf(ExcelImporter::class, $driver);
    }

    public function test_extend_and_resolve_custom_driver(): void
    {
        Importer::extend('test', ExcelImporter::class);

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('importerManager')->andReturn(new Importer($grid));

        $importer = new Importer($grid);
        $driver = $importer->resolve('test');

        $this->assertInstanceOf(ExcelImporter::class, $driver);
    }
}
