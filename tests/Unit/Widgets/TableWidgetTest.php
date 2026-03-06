<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Table;

class TableWidgetTest extends TestCase
{
    public function test_constructor_with_headers_and_rows(): void
    {
        $table = new Table(['Name', 'Age'], [['John', 25], ['Jane', 30]]);
        $this->assertInstanceOf(Table::class, $table);
    }

    public function test_constructor_rows_only(): void
    {
        $table = new Table([['John', 25], ['Jane', 30]]);
        $ref = new \ReflectionProperty($table, 'headers');
        $ref->setAccessible(true);
        $this->assertEmpty($ref->getValue($table));
    }

    public function test_set_headers(): void
    {
        $table = new Table;
        $result = $table->setHeaders(['Col1', 'Col2']);
        $this->assertSame($table, $result);
        $ref = new \ReflectionProperty($table, 'headers');
        $ref->setAccessible(true);
        $this->assertSame(['Col1', 'Col2'], $ref->getValue($table));
    }

    public function test_set_rows_indexed_array(): void
    {
        $table = new Table;
        $result = $table->setRows([['A', 'B'], ['C', 'D']]);
        $this->assertSame($table, $result);
        $ref = new \ReflectionProperty($table, 'rows');
        $ref->setAccessible(true);
        $rows = $ref->getValue($table);
        $this->assertCount(2, $rows);
    }

    public function test_set_rows_associative(): void
    {
        $table = new Table;
        $table->setRows(['name' => 'John', 'age' => 25]);
        $ref = new \ReflectionProperty($table, 'rows');
        $ref->setAccessible(true);
        $rows = $ref->getValue($table);
        $this->assertCount(2, $rows);
        $this->assertSame('name', $rows[0][0]);
        $this->assertSame('John', $rows[0][1]);
    }

    public function test_set_style(): void
    {
        $table = new Table;
        $result = $table->setStyle(['table-striped']);
        $this->assertSame($table, $result);
    }

    public function test_depth(): void
    {
        $table = new Table;
        $result = $table->depth(2);
        $this->assertSame($table, $result);
        $ref = new \ReflectionProperty($table, 'depth');
        $ref->setAccessible(true);
        $this->assertSame(2, $ref->getValue($table));
    }

    public function test_with_border(): void
    {
        $table = new Table;
        $result = $table->withBorder();
        $this->assertSame($table, $result);
    }
}
