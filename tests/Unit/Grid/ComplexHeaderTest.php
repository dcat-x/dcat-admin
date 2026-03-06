<?php

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\ComplexHeader;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class ComplexHeaderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('allColumns')->andReturn(collect([
            'col1' => 'Column 1',
            'col2' => 'Column 2',
            'col3' => 'Column 3',
        ]));
        $grid->shouldReceive('hideColumns')->andReturn(null);

        return $grid;
    }

    public function test_constructor_sets_column_name(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'test_column', ['col1', 'col2'], 'Test Label');

        $this->assertEquals('test_column', $header->getName());
    }

    public function test_constructor_sets_label_from_parameter(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'test_column', ['col1', 'col2'], 'My Custom Label');

        $this->assertEquals('My Custom Label', $header->getLabel());
    }

    public function test_get_column_names_returns_collection(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'group', ['col1', 'col2'], 'Group');

        $result = $header->getColumnNames();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(['col1', 'col2'], $result->values()->all());
    }

    public function test_get_name_returns_column(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'my_column', ['col1'], 'Label');

        $this->assertEquals('my_column', $header->getName());
    }

    public function test_get_label_returns_label(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'col', ['col1'], 'Expected Label');

        $this->assertEquals('Expected Label', $header->getLabel());
    }

    public function test_append_adds_html_and_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'group', ['col1', 'col2'], 'Group');

        $result = $header->append('<span>extra</span>');

        $this->assertSame($header, $result);
        // Verify the appended html shows up in render output
        $rendered = $header->render();
        $this->assertStringContainsString('<span>extra</span>', $rendered);
    }

    public function test_single_column_gets_rowspan_attribute(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'single', ['col1'], 'Single');

        $attributes = $header->getHtmlAttributes();

        $this->assertEquals(2, $attributes['rowspan'] ?? null);
        $this->assertArrayNotHasKey('colspan', $attributes);
    }

    public function test_multiple_columns_get_colspan_attribute(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'multi', ['col1', 'col2', 'col3'], 'Multi');

        $attributes = $header->getHtmlAttributes();

        $this->assertEquals(3, $attributes['colspan'] ?? null);
        $this->assertArrayNotHasKey('rowspan', $attributes);
    }

    public function test_render_outputs_th_with_label(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'group', ['col1', 'col2'], 'My Group');

        $rendered = $header->render();

        $this->assertStringStartsWith('<th ', $rendered);
        $this->assertStringEndsWith('</th>', $rendered);
        $this->assertStringContainsString('My Group', $rendered);
        $this->assertStringContainsString("class='grid-column-header'", $rendered);
    }

    public function test_render_includes_appended_html(): void
    {
        $grid = $this->createMockGrid();
        $header = new ComplexHeader($grid, 'group', ['col1', 'col2'], 'Group');

        $header->append('<i class="icon-help"></i>');
        $header->append('<span class="badge">3</span>');

        $rendered = $header->render();

        $this->assertStringContainsString('<i class="icon-help"></i>', $rendered);
        $this->assertStringContainsString('<span class="badge">3</span>', $rendered);
    }

    public function test_hide_calls_grid_hide_columns(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('allColumns')->andReturn(collect());
        $grid->shouldReceive('hideColumns')->once()->with('my_col');

        $header = new ComplexHeader($grid, 'my_col', ['col1', 'col2'], 'Label');
        $result = $header->hide();

        $this->assertSame($header, $result);
    }

    public function test_columns_maps_through_grid_all_columns(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('allColumns')->andReturn(collect([
            'col1' => 'Column 1',
            'col2' => 'Column 2',
            'col3' => 'Column 3',
        ]));

        $header = new ComplexHeader($grid, 'group', ['col1', 'col3'], 'Group');

        $columns = $header->columns();

        $this->assertInstanceOf(Collection::class, $columns);
        $this->assertCount(2, $columns);
        $this->assertEquals('Column 1', $columns->first());
        $this->assertEquals('Column 3', $columns->last());
    }
}
