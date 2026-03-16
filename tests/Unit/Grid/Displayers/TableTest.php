<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Table;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Table
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('items');
        $column->shouldReceive('getOriginal')->andReturn($value);

        $row = ['id' => 1, 'items' => $value];

        return new Table($value, $grid, $column, $row);
    }

    // -------------------------------------------------------
    // display() - empty / null values
    // -------------------------------------------------------

    public function test_display_with_empty_array_returns_empty_string(): void
    {
        $displayer = $this->makeDisplayer([]);

        $result = $displayer->display();

        $this->assertSame('', $result);
    }

    public function test_display_with_null_value_returns_empty_string(): void
    {
        $displayer = $this->makeDisplayer(null);

        $result = $displayer->display();

        $this->assertSame('', $result);
    }

    // -------------------------------------------------------
    // display() - auto-detected titles from first row keys
    // -------------------------------------------------------

    public function test_display_renders_html_table_tag(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 25],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
    }

    public function test_display_renders_thead_and_tbody(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 25],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('<thead>', $result);
        $this->assertStringContainsString('</thead>', $result);
        $this->assertStringContainsString('<tbody>', $result);
        $this->assertStringContainsString('</tbody>', $result);
    }

    public function test_display_auto_detects_column_titles_from_first_row(): void
    {
        $data = [
            ['product' => 'Widget', 'price' => 9.99],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('product', $result);
        $this->assertStringContainsString('price', $result);
        $this->assertStringContainsString('Widget', $result);
        $this->assertStringContainsString('9.99', $result);
    }

    public function test_display_renders_cell_values_in_td_tags(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 25],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('<td>', $result);
        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('25', $result);
    }

    // -------------------------------------------------------
    // display() - explicit sequential titles
    // -------------------------------------------------------

    public function test_display_with_sequential_titles_array(): void
    {
        $data = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display(['name', 'email']);

        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('email', $result);
        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('alice@example.com', $result);
    }

    // -------------------------------------------------------
    // display() - associative titles (key => label)
    // -------------------------------------------------------

    public function test_display_with_associative_titles_uses_labels(): void
    {
        $data = [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display(['name' => 'Full Name', 'email' => 'Email Address']);

        $this->assertStringContainsString('Full Name', $result);
        $this->assertStringContainsString('Email Address', $result);
        $this->assertStringContainsString('Alice', $result);
    }

    // -------------------------------------------------------
    // display() - column ordering via titles
    // -------------------------------------------------------

    public function test_display_orders_columns_by_titles_key_order(): void
    {
        $data = [
            ['b' => 'B1', 'a' => 'A1', 'c' => 'C1'],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display(['a' => 'Alpha', 'c' => 'Charlie', 'b' => 'Bravo']);

        // All labels should be present
        $this->assertStringContainsString('Alpha', $result);
        $this->assertStringContainsString('Charlie', $result);
        $this->assertStringContainsString('Bravo', $result);

        // Verify order: Alpha before Charlie before Bravo
        $posAlpha = strpos($result, 'Alpha');
        $posCharlie = strpos($result, 'Charlie');
        $posBravo = strpos($result, 'Bravo');
        $this->assertLessThan($posCharlie, $posAlpha);
        $this->assertLessThan($posBravo, $posCharlie);
    }

    // -------------------------------------------------------
    // display() - partial columns (subset)
    // -------------------------------------------------------

    public function test_display_shows_only_specified_columns(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 25, 'email' => 'alice@test.com'],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display(['name' => 'Name']);

        $this->assertStringContainsString('Alice', $result);
        // "email" should not appear as a header (th)
        $this->assertStringNotContainsString('email', $result);
    }

    // -------------------------------------------------------
    // display() - multiple rows
    // -------------------------------------------------------

    public function test_display_with_multiple_rows(): void
    {
        $data = [
            ['item' => 'A', 'qty' => 1],
            ['item' => 'B', 'qty' => 2],
            ['item' => 'C', 'qty' => 3],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('A', $result);
        $this->assertStringContainsString('B', $result);
        $this->assertStringContainsString('C', $result);
        $this->assertStringContainsString('1', $result);
        $this->assertStringContainsString('3', $result);
    }

    // -------------------------------------------------------
    // display() - table CSS class
    // -------------------------------------------------------

    public function test_display_table_has_css_classes(): void
    {
        $data = [
            ['name' => 'Alice'],
        ];
        $displayer = $this->makeDisplayer($data);

        $result = $displayer->display();

        $this->assertStringContainsString('table', $result);
        $this->assertStringContainsString('table-hover', $result);
    }

    // -------------------------------------------------------
    // display() - row with missing column key
    // -------------------------------------------------------

    public function test_display_skips_missing_columns_in_data_rows(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 25],
            ['name' => 'Bob'],  // missing 'age'
        ];
        $displayer = $this->makeDisplayer($data);

        // Use explicit titles including 'age'
        $result = $displayer->display(['name' => 'Name', 'age' => 'Age']);

        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('Bob', $result);
        $this->assertStringContainsString('25', $result);
    }

    // -------------------------------------------------------
    // Inheritance
    // -------------------------------------------------------

    public function test_extends_abstract_displayer(): void
    {
        $displayer = $this->makeDisplayer([]);

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }
}
