<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Button;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Button
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('action');

        $row = ['id' => 1, 'action' => $value];

        return new Button($value, $grid, $column, $row);
    }

    public function test_display_with_default_primary_style(): void
    {
        $displayer = $this->makeDisplayer('Click');
        $result = $displayer->display();

        $this->assertStringContainsString('<span', $result);
        $this->assertStringContainsString('btn', $result);
        $this->assertStringContainsString('btn-sm', $result);
        $this->assertStringContainsString('btn-primary', $result);
        $this->assertStringContainsString('Click', $result);
    }

    public function test_display_with_custom_style(): void
    {
        $displayer = $this->makeDisplayer('Submit');
        $result = $displayer->display('success');

        $this->assertStringContainsString('btn-success', $result);
        $this->assertStringContainsString('Submit', $result);
    }

    public function test_display_with_multiple_styles(): void
    {
        $displayer = $this->makeDisplayer('Action');
        $result = $displayer->display(['outline', 'danger']);

        $this->assertStringContainsString('btn-outline', $result);
        $this->assertStringContainsString('btn-danger', $result);
        $this->assertStringContainsString('Action', $result);
    }

    public function test_display_wraps_value_in_span(): void
    {
        $displayer = $this->makeDisplayer('Test');
        $result = $displayer->display('warning');

        $this->assertStringStartsWith("<span class='btn btn-sm btn-warning'>", $result);
        $this->assertStringEndsWith('</span>', $result);
    }

    public function test_display_always_has_btn_sm_class(): void
    {
        $displayer = $this->makeDisplayer('Text');
        $result = $displayer->display('info');

        $this->assertStringContainsString('btn-sm', $result);
        $this->assertStringContainsString('btn-info', $result);
    }
}
