<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Grid\Displayers\Radio;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RadioTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Radio
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/posts');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');
        $column->shouldReceive('getOriginal')->andReturn($value);

        $row = ['id' => 1, 'status' => $value];

        return new Radio($value, $grid, $column, $row);
    }

    public function test_type_is_radio(): void
    {
        $displayer = $this->makeDisplayer(1);

        $ref = new \ReflectionProperty($displayer, 'type');
        $ref->setAccessible(true);

        $this->assertSame('radio', $ref->getValue($displayer));
    }

    public function test_view_is_radio_view(): void
    {
        $displayer = $this->makeDisplayer(1);

        $ref = new \ReflectionProperty($displayer, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::grid.displayer.editinline.radio', $ref->getValue($displayer));
    }

    public function test_get_value_returns_option_label(): void
    {
        $displayer = $this->makeDisplayer(1);

        // Manually set options to simulate display() behavior
        $ref = new \ReflectionProperty($displayer, 'options');
        $ref->setAccessible(true);
        $ref->setValue($displayer, ['options' => [1 => 'Active', 2 => 'Inactive']]);

        $method = new \ReflectionMethod($displayer, 'getValue');
        $method->setAccessible(true);

        $this->assertSame('Active', $method->invoke($displayer));
    }

    public function test_get_value_returns_null_for_unknown_key(): void
    {
        $displayer = $this->makeDisplayer(99);

        $ref = new \ReflectionProperty($displayer, 'options');
        $ref->setAccessible(true);
        $ref->setValue($displayer, ['options' => [1 => 'Active', 2 => 'Inactive']]);

        $method = new \ReflectionMethod($displayer, 'getValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($displayer));
    }

    public function test_extends_editable(): void
    {
        $displayer = $this->makeDisplayer(1);

        $this->assertInstanceOf(Editable::class, $displayer);
    }

    public function test_constructor_stores_value(): void
    {
        $displayer = $this->makeDisplayer(2);

        $ref = new \ReflectionProperty($displayer, 'value');
        $ref->setAccessible(true);

        $this->assertSame(2, $ref->getValue($displayer));
    }
}
