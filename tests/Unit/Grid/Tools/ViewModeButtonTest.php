<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\ViewModeButton;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;

class ViewModeButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_implements_renderable(): void
    {
        $ref = new \ReflectionClass(ViewModeButton::class);

        $this->assertTrue($ref->implementsInterface(Renderable::class));
    }

    public function test_constructor_accepts_grid(): void
    {
        $grid = Mockery::mock(Grid::class);
        $button = new ViewModeButton($grid);

        $this->assertInstanceOf(ViewModeButton::class, $button);
    }

    public function test_icons_map_contains_three_modes(): void
    {
        $grid = Mockery::mock(Grid::class);
        $button = new ViewModeButton($grid);

        $ref = new \ReflectionProperty($button, 'icons');
        $ref->setAccessible(true);
        $icons = $ref->getValue($button);

        $this->assertArrayHasKey('table', $icons);
        $this->assertArrayHasKey('card', $icons);
        $this->assertArrayHasKey('list', $icons);
    }

    public function test_render_outputs_btn_group(): void
    {
        $tools = new class
        {
            public function format($content)
            {
                return $content;
            }
        };

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getCurrentViewMode')->andReturn('table');
        $grid->shouldReceive('getAvailableViewModes')->andReturn(['table', 'card']);
        $grid->shouldReceive('tools')->andReturn($tools);
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $button = new ViewModeButton($grid);
        $html = $button->render();

        $this->assertStringContainsString('btn-group', $html);
        $this->assertStringContainsString('btn-primary', $html);
        $this->assertStringContainsString('_view_=table', $html);
        $this->assertStringContainsString('_view_=card', $html);
    }

    public function test_render_highlights_current_mode(): void
    {
        $tools = new class
        {
            public function format($content)
            {
                return $content;
            }
        };

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getCurrentViewMode')->andReturn('card');
        $grid->shouldReceive('getAvailableViewModes')->andReturn(['table', 'card']);
        $grid->shouldReceive('tools')->andReturn($tools);
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $button = new ViewModeButton($grid);
        $html = $button->render();

        // card should be primary (active), table should be default
        $this->assertMatchesRegularExpression('/_view_=table.*btn-default/', $html);
        $this->assertMatchesRegularExpression('/_view_=card.*btn-primary/', $html);
    }
}
