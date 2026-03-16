<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Label;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class LabelTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value, $original = null): Label
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');
        $column->shouldReceive('getOriginal')->andReturn($original ?? $value);

        $row = ['id' => 1, 'status' => $value];

        return new Label($value, $grid, $column, $row);
    }

    public function test_display_single_value_with_default_style(): void
    {
        $displayer = $this->makeDisplayer('active');
        $result = $displayer->display();

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('label', $result);
        $this->assertStringContainsString('active', $result);
        $this->assertStringContainsString('style=', $result);
    }

    public function test_display_with_primary_style(): void
    {
        $displayer = $this->makeDisplayer('active');
        $result = $displayer->display('primary');

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString("class='label'", $result);
        $this->assertStringContainsString('active', $result);
    }

    public function test_display_with_default_style_string(): void
    {
        $displayer = $this->makeDisplayer('active');
        $result = $displayer->display('default');

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('#d2d6de', $result);
    }

    public function test_display_with_style_array_mapping(): void
    {
        $displayer = $this->makeDisplayer('active', 'active');
        $result = $displayer->display(['active' => 'success', 'inactive' => 'danger', 'default' => 'warning']);

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('active', $result);
    }

    public function test_display_with_style_array_default_fallback(): void
    {
        $displayer = $this->makeDisplayer('unknown', 'unknown');
        $result = $displayer->display(['active' => 'success', 'default' => 'warning']);

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('unknown', $result);
    }

    public function test_display_array_value(): void
    {
        $displayer = $this->makeDisplayer(['tag1', 'tag2']);
        $result = $displayer->display('primary');

        $this->assertStringContainsString('tag1', $result);
        $this->assertStringContainsString('tag2', $result);
        // Two spans joined by space
        $this->assertStringContainsString('</span> <span', $result);
    }

    public function test_display_with_max_limit(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b', 'c', 'd', 'e']);
        $result = $displayer->display('primary', 3);

        $this->assertStringContainsString('a', $result);
        $this->assertStringContainsString('b', $result);
        $this->assertStringContainsString('c', $result);
        $this->assertStringContainsString('...', $result);
        $this->assertStringNotContainsString('>d<', $result);
        $this->assertStringNotContainsString('>e<', $result);
    }

    public function test_display_with_max_not_exceeded(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b']);
        $result = $displayer->display('primary', 5);

        $this->assertStringContainsString('a', $result);
        $this->assertStringContainsString('b', $result);
        $this->assertStringNotContainsString('...', $result);
    }

    public function test_display_empty_value_returns_null(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertNull($result);
    }

    public function test_display_null_value_returns_null(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        $this->assertNull($result);
    }

    public function test_base_class_is_label(): void
    {
        $displayer = $this->makeDisplayer('test');
        $result = $displayer->display();

        $this->assertStringContainsString("class='label'", $result);
    }
}
