<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\ProgressBar;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ProgressBarTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): ProgressBar
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('progress');

        $row = ['id' => 1, 'progress' => $value];

        return new ProgressBar($value, $grid, $column, $row);
    }

    public function test_display_with_default_params(): void
    {
        $displayer = $this->makeDisplayer(75);
        $result = $displayer->display();

        $this->assertStringContainsString('progress', $result);
        $this->assertStringContainsString('progress-bar', $result);
        $this->assertStringContainsString('aria-valuenow="75"', $result);
        $this->assertStringContainsString('width:75%', $result);
        $this->assertStringContainsString('aria-valuemax="100"', $result);
    }

    public function test_display_with_primary_style(): void
    {
        $displayer = $this->makeDisplayer(50);
        $result = $displayer->display('primary');

        $this->assertStringContainsString('progress-bar-primary', $result);
    }

    public function test_display_with_success_style(): void
    {
        $displayer = $this->makeDisplayer(100);
        $result = $displayer->display('success');

        $this->assertStringContainsString('progress-bar-success', $result);
        $this->assertStringContainsString('aria-valuenow="100"', $result);
    }

    public function test_display_with_multiple_styles(): void
    {
        $displayer = $this->makeDisplayer(60);
        $result = $displayer->display(['striped', 'animated']);

        $this->assertStringContainsString('progress-bar-striped', $result);
        $this->assertStringContainsString('progress-bar-animated', $result);
    }

    public function test_display_with_custom_max(): void
    {
        $displayer = $this->makeDisplayer(50);
        $result = $displayer->display('primary', 'sm', 200);

        $this->assertStringContainsString('aria-valuemax="200"', $result);
    }

    public function test_display_zero_value(): void
    {
        $displayer = $this->makeDisplayer(0);
        $result = $displayer->display();

        $this->assertStringContainsString('aria-valuenow="0"', $result);
        $this->assertStringContainsString('width:0%', $result);
    }

    public function test_display_contains_progressbar_role(): void
    {
        $displayer = $this->makeDisplayer(30);
        $result = $displayer->display();

        $this->assertStringContainsString('role="progressbar"', $result);
        $this->assertStringContainsString('aria-valuemin="0"', $result);
    }

    public function test_display_has_shadow_class(): void
    {
        $displayer = $this->makeDisplayer(50);
        $result = $displayer->display();

        $this->assertStringContainsString('shadow-100', $result);
    }
}
