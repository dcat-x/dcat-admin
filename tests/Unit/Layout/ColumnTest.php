<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tests\TestCase;

class ColumnTest extends TestCase
{
    protected function getProperty(Column $column, string $property): mixed
    {
        return (new \ReflectionProperty(Column::class, $property))->getValue($column);
    }

    public function test_constructor_sets_default_width_to_12(): void
    {
        $column = new Column('content');

        $width = $this->getProperty($column, 'width');

        $this->assertSame(['md' => 12], $width);
    }

    public function test_constructor_with_integer_width(): void
    {
        $column = new Column('content', 6);

        $width = $this->getProperty($column, 'width');

        $this->assertSame(['md' => 6], $width);
    }

    public function test_constructor_with_fractional_width_converts_to_grid_units(): void
    {
        // 0.5 * 12 = 6
        $column = new Column('content', 0.5);

        $width = $this->getProperty($column, 'width');

        $this->assertSame(['md' => 6], $width);
    }

    public function test_constructor_with_one_third_fractional_width(): void
    {
        // 1/3 * 12 = 4
        $column = new Column('content', 1 / 3);

        $width = $this->getProperty($column, 'width');

        $this->assertSame(['md' => 4], $width);
    }

    public function test_constructor_with_string_content(): void
    {
        $column = new Column('hello world');

        $contents = $this->getProperty($column, 'contents');

        $this->assertCount(1, $contents);
        $this->assertSame('hello world', $contents[0]);
    }

    public function test_constructor_with_closure_calls_closure(): void
    {
        $called = false;
        $receivedColumn = null;

        $column = new Column(function ($col) use (&$called, &$receivedColumn) {
            $called = true;
            $receivedColumn = $col;
            $col->append('from closure');
        });

        $this->assertTrue($called);
        $this->assertSame($column, $receivedColumn);

        $contents = $this->getProperty($column, 'contents');
        $this->assertCount(1, $contents);
        $this->assertSame('from closure', $contents[0]);
    }

    public function test_append_adds_content(): void
    {
        $column = new Column('');

        $column->append('first');
        $column->append('second');

        $contents = $this->getProperty($column, 'contents');

        // '' from constructor + 'first' + 'second'
        $this->assertCount(3, $contents);
        $this->assertSame('first', $contents[1]);
        $this->assertSame('second', $contents[2]);
    }

    public function test_append_returns_self_for_chaining(): void
    {
        $column = new Column('');

        $result = $column->append('test');

        $this->assertSame($column, $result);
    }

    public function test_row_adds_row_instance_to_contents(): void
    {
        $column = new Column('');

        $column->row('row content');

        $contents = $this->getProperty($column, 'contents');

        // '' from constructor + Row instance
        $this->assertCount(2, $contents);
        $this->assertInstanceOf(Row::class, $contents[1]);
    }

    public function test_row_with_closure_creates_row_and_calls_closure(): void
    {
        $column = new Column('');
        $receivedRow = null;

        $column->row(function ($row) use (&$receivedRow) {
            $receivedRow = $row;
        });

        $this->assertInstanceOf(Row::class, $receivedRow);

        $contents = (new \ReflectionProperty(Column::class, 'contents'))->getValue($column);
        $this->assertCount(2, $contents);
        $this->assertInstanceOf(Row::class, $contents[1]);
    }

    public function test_row_returns_self_for_chaining(): void
    {
        $column = new Column('');

        $result = $column->row('test');

        $this->assertSame($column, $result);
    }

    public function test_render_wraps_content_in_column_div(): void
    {
        $column = new Column('test content', 6);

        $html = $column->render();

        $this->assertStringContainsString('col-md-6', $html);
        $this->assertStringContainsString('test content', $html);
        $this->assertStringStartsWith('<div class="', $html);
        $this->assertStringEndsWith('</div>', $html);
    }

    public function test_render_with_default_width(): void
    {
        $column = new Column('hello');

        $html = $column->render();

        $this->assertStringContainsString('col-md-12', $html);
        $this->assertStringContainsString('hello', $html);
    }

    public function test_render_multiple_appended_contents(): void
    {
        $column = new Column('first', 4);
        $column->append('second');

        $html = $column->render();

        $this->assertStringContainsString('first', $html);
        $this->assertStringContainsString('second', $html);
        $this->assertStringContainsString('col-md-4', $html);
    }

    public function test_render_with_empty_content(): void
    {
        $column = new Column('', 3);

        $html = $column->render();

        $this->assertStringContainsString('col-md-3', $html);
        $this->assertSame('<div class="col-md-3"></div>', $html);
    }
}
