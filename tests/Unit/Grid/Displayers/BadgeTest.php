<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Badge;
use Dcat\Admin\Grid\Displayers\Label;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class BadgeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value, $original = null): Badge
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');
        $column->shouldReceive('getOriginal')->andReturn($original ?? $value);

        $row = ['id' => 1, 'status' => $value];

        return new Badge($value, $grid, $column, $row);
    }

    public function test_badge_extends_label(): void
    {
        $this->assertTrue(is_subclass_of(Badge::class, Label::class));
    }

    public function test_display_uses_badge_class(): void
    {
        $displayer = $this->makeDisplayer('active');
        $result = $displayer->display();

        $this->assertStringContainsString("class='badge'", $result);
        $this->assertStringNotContainsString("class='label'", $result);
    }

    public function test_display_single_value(): void
    {
        $displayer = $this->makeDisplayer('pending');
        $result = $displayer->display('warning');

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('badge', $result);
        $this->assertStringContainsString('pending', $result);
    }

    public function test_display_array_value(): void
    {
        $displayer = $this->makeDisplayer(['tag1', 'tag2']);
        $result = $displayer->display('primary');

        $this->assertStringContainsString('tag1', $result);
        $this->assertStringContainsString('tag2', $result);
        $this->assertStringContainsString('badge', $result);
    }

    public function test_display_with_style_mapping(): void
    {
        $displayer = $this->makeDisplayer('active', 'active');
        $result = $displayer->display(['active' => 'success', 'default' => 'warning']);

        $this->assertStringContainsString('badge', $result);
        $this->assertStringContainsString('active', $result);
    }

    public function test_display_empty_value_returns_null(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertNull($result);
    }

    public function test_display_with_max(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b', 'c', 'd']);
        $result = $displayer->display('primary', 2);

        $this->assertStringContainsString('a', $result);
        $this->assertStringContainsString('b', $result);
        $this->assertStringContainsString('...', $result);
    }
}
