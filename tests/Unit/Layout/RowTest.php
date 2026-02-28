<?php

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tests\TestCase;

class RowTest extends TestCase
{
    public function test_constructor_with_no_content_creates_empty_row(): void
    {
        $row = new Row;

        $columns = (new \ReflectionProperty(Row::class, 'columns'))->getValue($row);

        $this->assertCount(0, $columns);
    }

    public function test_constructor_with_string_content_creates_full_width_column(): void
    {
        $row = new Row('hello');

        $columns = (new \ReflectionProperty(Row::class, 'columns'))->getValue($row);

        $this->assertCount(1, $columns);
        $this->assertInstanceOf(Column::class, $columns[0]);
    }

    public function test_constructor_with_column_instance_adds_it_directly(): void
    {
        $column = new Column('content', 6);

        $row = new Row($column);

        $columns = (new \ReflectionProperty(Row::class, 'columns'))->getValue($row);

        $this->assertCount(1, $columns);
        $this->assertSame($column, $columns[0]);
    }

    public function test_column_adds_column_with_width(): void
    {
        $row = new Row;

        $row->column(6, 'left side');
        $row->column(6, 'right side');

        $columns = (new \ReflectionProperty(Row::class, 'columns'))->getValue($row);

        $this->assertCount(2, $columns);
        $this->assertInstanceOf(Column::class, $columns[0]);
        $this->assertInstanceOf(Column::class, $columns[1]);
    }

    public function test_no_gutters_defaults_to_false(): void
    {
        $row = new Row;

        $noGutters = (new \ReflectionProperty(Row::class, 'noGutters'))->getValue($row);

        $this->assertFalse($noGutters);
    }

    public function test_no_gutters_can_be_enabled(): void
    {
        $row = new Row;

        $result = $row->noGutters();

        $noGutters = (new \ReflectionProperty(Row::class, 'noGutters'))->getValue($row);

        $this->assertTrue($noGutters);
        $this->assertSame($row, $result);
    }

    public function test_no_gutters_can_be_disabled(): void
    {
        $row = new Row;

        $row->noGutters(true);
        $row->noGutters(false);

        $noGutters = (new \ReflectionProperty(Row::class, 'noGutters'))->getValue($row);

        $this->assertFalse($noGutters);
    }

    public function test_render_produces_row_div_without_gutters(): void
    {
        $row = new Row;
        $row->column(12, 'content');

        $html = $row->render();

        $this->assertStringContainsString('<div class="row ">', $html);
        $this->assertStringContainsString('content', $html);
        $this->assertStringEndsWith('</div>', $html);
    }

    public function test_render_with_no_gutters_includes_class(): void
    {
        $row = new Row;
        $row->noGutters();
        $row->column(12, 'content');

        $html = $row->render();

        $this->assertStringContainsString('no-gutters', $html);
    }

    public function test_render_multiple_columns(): void
    {
        $row = new Row;
        $row->column(4, 'col1');
        $row->column(4, 'col2');
        $row->column(4, 'col3');

        $html = $row->render();

        $this->assertStringContainsString('col1', $html);
        $this->assertStringContainsString('col2', $html);
        $this->assertStringContainsString('col3', $html);
        $this->assertEquals(3, substr_count($html, 'col-md-4'));
    }

    public function test_render_empty_row(): void
    {
        $row = new Row;

        $html = $row->render();

        $this->assertEquals('<div class="row "></div>', $html);
    }

    public function test_constructor_with_empty_string_creates_no_columns(): void
    {
        $row = new Row('');

        $columns = (new \ReflectionProperty(Row::class, 'columns'))->getValue($row);

        $this->assertCount(0, $columns);
    }
}
