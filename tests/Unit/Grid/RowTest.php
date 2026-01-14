<?php

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Row;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class RowTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(string $keyName = 'id'): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn($keyName);
        $grid->shouldReceive('columns')->andReturn(collect());

        return $grid;
    }

    public function test_row_with_array_data(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $this->assertInstanceOf(Row::class, $row);
        $this->assertInstanceOf(Fluent::class, $row->model());
    }

    public function test_row_get_key(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 123, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $this->assertEquals(123, $row->getKey());
    }

    public function test_row_get_key_custom_key_name(): void
    {
        $grid = $this->createMockGrid('uuid');
        $data = ['uuid' => 'abc-123', 'name' => 'Test'];
        $row = new Row($grid, $data);

        $this->assertEquals('abc-123', $row->getKey());
    }

    public function test_row_magic_get(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test Name', 'email' => 'test@example.com'];
        $row = new Row($grid, $data);

        $this->assertEquals('Test Name', $row->name);
        $this->assertEquals('test@example.com', $row->email);
    }

    public function test_row_magic_set(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->name = 'New Name';
        $this->assertEquals('New Name', $row->name);
    }

    public function test_row_column_get_value(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $this->assertEquals('Test', $row->column('name'));
    }

    public function test_row_column_set_value(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->column('name', 'New Value');
        $this->assertEquals('New Value', $row->column('name'));
    }

    public function test_row_column_with_closure(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->column('name', function ($value) {
            return strtoupper($value);
        });
        $this->assertEquals('TEST', $row->column('name'));
    }

    public function test_row_to_array(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test', 'email' => 'test@example.com'];
        $row = new Row($grid, $data);

        $array = $row->toArray();
        $this->assertIsArray($array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('Test', $array['name']);
        $this->assertEquals('test@example.com', $array['email']);
    }

    public function test_row_set_attributes(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->setAttributes(['class' => 'highlight', 'data-id' => '1']);
        $attributes = $row->rowAttributes();

        $this->assertStringContainsString('class="highlight"', $attributes);
        $this->assertStringContainsString('data-id="1"', $attributes);
    }

    public function test_row_style_with_string(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->style('background-color: red;');
        $attributes = $row->rowAttributes();

        $this->assertStringContainsString('style="background-color: red;"', $attributes);
    }

    public function test_row_style_with_array(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $row->style(['background-color' => 'red', 'color' => 'white']);
        $attributes = $row->rowAttributes();

        $this->assertStringContainsString('background-color:red', $attributes);
        $this->assertStringContainsString('color:white', $attributes);
    }

    public function test_row_model(): void
    {
        $grid = $this->createMockGrid();
        $data = ['id' => 1, 'name' => 'Test'];
        $row = new Row($grid, $data);

        $model = $row->model();
        $this->assertInstanceOf(Fluent::class, $model);
        $this->assertEquals(1, $model->id);
        $this->assertEquals('Test', $model->name);
    }
}
