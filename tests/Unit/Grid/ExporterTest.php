<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Exporter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExporterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($name) {
            return $name;
        });

        return $grid;
    }

    public function test_scope_constants_are_defined_correctly(): void
    {
        $this->assertSame('all', Exporter::SCOPE_ALL);
        $this->assertSame('page', Exporter::SCOPE_CURRENT_PAGE);
        $this->assertSame('selected', Exporter::SCOPE_SELECTED_ROWS);
    }

    public function test_default_options_are_correct(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $this->assertTrue($exporter->option('show_export_all'));
        $this->assertTrue($exporter->option('show_export_current_page'));
        $this->assertTrue($exporter->option('show_export_selected_rows'));
        $this->assertSame(5000, $exporter->option('chunk_size'));
    }

    public function test_option_getter_returns_value(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $this->assertTrue($exporter->option('show_export_all'));
        $this->assertSame(5000, $exporter->option('chunk_size'));
    }

    public function test_option_getter_returns_null_for_missing_key(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $this->assertNull($exporter->option('nonexistent_key'));
    }

    public function test_option_setter_sets_value_and_returns_this(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->option('chunk_size', 1000);

        $this->assertSame($exporter, $result);
        $this->assertSame(1000, $exporter->option('chunk_size'));
    }

    public function test_disable_export_all_sets_option_correctly(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->disableExportAll();

        $this->assertSame($exporter, $result);
        $this->assertFalse($exporter->option('show_export_all'));
    }

    public function test_disable_export_current_page_sets_option_correctly(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->disableExportCurrentPage();

        $this->assertSame($exporter, $result);
        $this->assertFalse($exporter->option('show_export_current_page'));
    }

    public function test_disable_export_selected_row_sets_option_correctly(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->disableExportSelectedRow();

        $this->assertSame($exporter, $result);
        $this->assertFalse($exporter->option('show_export_selected_rows'));
    }

    public function test_chunk_size_sets_option(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->chunkSize(2000);

        $this->assertSame($exporter, $result);
        $this->assertSame(2000, $exporter->option('chunk_size'));
    }

    public function test_get_query_name_calls_grid_make_name(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')
            ->once()
            ->with('_export_')
            ->andReturn('prefix__export_');

        $exporter = new Exporter($grid);

        $this->assertSame('prefix__export_', $exporter->getQueryName());
    }

    public function test_format_export_query_with_scope_all(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->formatExportQuery(Exporter::SCOPE_ALL);

        $this->assertSame(['_export_' => 'all'], $result);
    }

    public function test_format_export_query_with_scope_current_page(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->formatExportQuery(Exporter::SCOPE_CURRENT_PAGE, 3);

        $this->assertSame(['_export_' => 'page:3'], $result);
    }

    public function test_format_export_query_with_scope_selected_rows(): void
    {
        $exporter = new Exporter($this->createMockGrid());

        $result = $exporter->formatExportQuery(Exporter::SCOPE_SELECTED_ROWS, '1,2,3');

        $this->assertSame(['_export_' => 'selected:1,2,3'], $result);
    }

    public function test_extend_registers_driver(): void
    {
        Exporter::extend('csv', 'CsvExporterClass');

        $reflection = new \ReflectionClass(Exporter::class);
        $property = $reflection->getProperty('drivers');
        $property->setAccessible(true);
        $drivers = $property->getValue();

        $this->assertSame('CsvExporterClass', $drivers['csv'] ?? null);

        // Clean up static state
        $property->setValue(null, []);
    }
}
