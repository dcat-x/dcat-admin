<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\SwitchDisplay;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SwitchDisplayTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): SwitchDisplay
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('is_active');

        $row = ['id' => 5, 'is_active' => $value];

        return new SwitchDisplay($value, $grid, $column, $row);
    }

    public function test_url_generation(): void
    {
        $displayer = $this->makeDisplayer(1);

        // Access protected method via reflection
        $reflection = new \ReflectionMethod($displayer, 'url');
        $reflection->setAccessible(true);
        $url = $reflection->invoke($displayer);

        $this->assertSame('/admin/users/5', $url);
    }

    public function test_get_key_returns_row_key(): void
    {
        $displayer = $this->makeDisplayer(1);
        $key = $displayer->getKey();

        $this->assertSame(5, $key);
    }

    public function test_color_sets_color_property(): void
    {
        $displayer = $this->makeDisplayer(1);
        $displayer->color('primary');

        $reflection = new \ReflectionProperty($displayer, 'color');
        $reflection->setAccessible(true);
        $color = $reflection->getValue($displayer);

        // Color should be resolved via Admin::color()->get()
        $this->assertNotNull($color);
    }

    public function test_display_checked_value(): void
    {
        $displayer = $this->makeDisplayer(1);
        $result = $displayer->display();

        // Admin::view renders a Blade template; result is a string
        $this->assertIsString($result);
    }

    public function test_display_unchecked_value(): void
    {
        $displayer = $this->makeDisplayer(0);
        $result = $displayer->display();

        $this->assertIsString($result);
    }

    public function test_element_name_simple(): void
    {
        $displayer = $this->makeDisplayer(1);
        $name = $displayer->getElementName();

        $this->assertSame('is_active', $name);
    }

    public function test_element_name_with_dots(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('settings.notifications.email');

        $row = ['id' => 1];

        $displayer = new SwitchDisplay(1, $grid, $column, $row);
        $name = $displayer->getElementName();

        $this->assertSame('settings[notifications][email]', $name);
    }

    public function test_resource_returns_grid_resource(): void
    {
        $displayer = $this->makeDisplayer(1);
        $resource = $displayer->resource();

        $this->assertSame('/admin/users', $resource);
    }
}
