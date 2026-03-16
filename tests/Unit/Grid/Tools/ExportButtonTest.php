<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Grid\Tools\ExportButton;
use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExportButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $args = [])
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($object, $args);
    }

    protected function createMockGrid(array $exportOptions = [], bool $rowSelector = true): Grid
    {
        $defaultExportOptions = [
            'show_export_all' => true,
            'show_export_current_page' => true,
            'show_export_selected_rows' => true,
        ];
        $exportOptions = array_merge($defaultExportOptions, $exportOptions);

        $exporter = Mockery::mock(AbstractExporter::class);
        $exporter->shouldReceive('option')->andReturnUsing(function ($key) use ($exportOptions) {
            return $exportOptions[$key] ?? null;
        });

        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getCurrentPage')->andReturn(1);

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('exporter')->andReturn($exporter);
        $grid->shouldReceive('model')->andReturn($model);
        $grid->shouldReceive('exportUrl')->andReturnUsing(function ($scope, $extra = null) {
            return "/admin/test/export?_export_={$scope}".($extra ? "&extra={$extra}" : '');
        });
        $grid->shouldReceive('getExportSelectedName')->andReturn('export-selected-test');
        $grid->shouldReceive('getName')->andReturn('test-grid');
        $grid->shouldReceive('option')->with('row_selector')->andReturn($rowSelector);

        $tools = Mockery::mock(Tools::class);
        $tools->shouldReceive('format')->andReturnUsing(function ($html) {
            return $html;
        });
        $grid->shouldReceive('tools')->andReturn($tools);

        return $grid;
    }

    protected function bindAdminAsset(): void
    {
        $this->app->instance('admin.asset', new Asset);
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function test_constructor_stores_grid(): void
    {
        $grid = $this->createMockGrid();
        $button = new ExportButton($grid);

        $this->assertSame($grid, $this->getProtectedProperty($button, 'grid'));
    }

    public function test_implements_renderable(): void
    {
        $grid = $this->createMockGrid();
        $button = new ExportButton($grid);

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Renderable::class, $button);
    }

    // -------------------------------------------------------------------------
    // renderExportAll (protected)
    // -------------------------------------------------------------------------

    public function test_render_export_all_returns_html_when_enabled(): void
    {
        $grid = $this->createMockGrid(['show_export_all' => true]);
        $button = new ExportButton($grid);

        $html = $this->invokeProtectedMethod($button, 'renderExportAll');

        $this->assertStringContainsString('dropdown-item', $html);
        $this->assertStringContainsString('_export_=all', $html);
        $this->assertStringContainsString('target="_blank"', $html);
    }

    public function test_render_export_all_returns_null_when_disabled(): void
    {
        $grid = $this->createMockGrid(['show_export_all' => false]);
        $button = new ExportButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderExportAll');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // renderExportCurrentPage (protected)
    // -------------------------------------------------------------------------

    public function test_render_export_current_page_returns_html_when_enabled(): void
    {
        $grid = $this->createMockGrid(['show_export_current_page' => true]);
        $button = new ExportButton($grid);

        $html = $this->invokeProtectedMethod($button, 'renderExportCurrentPage');

        $this->assertStringContainsString('dropdown-item', $html);
        $this->assertStringContainsString('_export_=page', $html);
    }

    public function test_render_export_current_page_returns_null_when_disabled(): void
    {
        $grid = $this->createMockGrid(['show_export_current_page' => false]);
        $button = new ExportButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderExportCurrentPage');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // renderExportSelectedRows (protected)
    // -------------------------------------------------------------------------

    public function test_render_export_selected_rows_returns_html_when_enabled(): void
    {
        $grid = $this->createMockGrid(['show_export_selected_rows' => true]);
        $button = new ExportButton($grid);

        $html = $this->invokeProtectedMethod($button, 'renderExportSelectedRows');

        $this->assertStringContainsString('dropdown-item', $html);
        $this->assertStringContainsString('export-selected-test', $html);
    }

    public function test_render_export_selected_rows_returns_null_when_disabled(): void
    {
        $grid = $this->createMockGrid(['show_export_selected_rows' => false]);
        $button = new ExportButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderExportSelectedRows');

        $this->assertNull($result);
    }

    public function test_render_export_selected_rows_returns_null_when_no_row_selector(): void
    {
        $grid = $this->createMockGrid(
            ['show_export_selected_rows' => true],
            false // row_selector disabled
        );
        $button = new ExportButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderExportSelectedRows');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // render()
    // -------------------------------------------------------------------------

    public function test_render_returns_html_with_dropdown(): void
    {
        $this->bindAdminAsset();
        $grid = $this->createMockGrid();
        $button = new ExportButton($grid);

        $html = $button->render();

        $this->assertStringContainsString('dropdown', $html);
        $this->assertStringContainsString('icon-download', $html);
        $this->assertStringContainsString('dropdown-menu', $html);
    }

    public function test_render_contains_btn_primary_class(): void
    {
        $this->bindAdminAsset();
        $grid = $this->createMockGrid();
        $button = new ExportButton($grid);

        $html = $button->render();

        $this->assertStringContainsString('btn-primary', $html);
    }
}
