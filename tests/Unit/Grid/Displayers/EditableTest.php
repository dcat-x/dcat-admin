<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EditableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value = 'Active', $original = 'active'): Editable
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');
        $column->shouldReceive('getOriginal')->andReturn($original);

        return new class($value, $grid, $column, ['id' => 9, 'status' => $original]) extends Editable
        {
            protected $type = 'input';

            protected $view = 'admin::grid.displayer.editinline.input';

            protected function defaultOptions()
            {
                return ['mask' => null];
            }

            public function exposeName(): string
            {
                return $this->getName();
            }

            public function exposeValue()
            {
                return $this->getValue();
            }

            public function exposeOriginal()
            {
                return $this->getOriginal();
            }

            public function exposeSelector(): string
            {
                return $this->getSelector();
            }

            public function exposeUrl(): string
            {
                return $this->getUrl();
            }
        };
    }

    public function test_editable_is_abstract_displayer_instance(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }

    public function test_variables_contains_expected_payload(): void
    {
        $displayer = $this->makeDisplayer('Enabled', 'enabled');
        $variables = $displayer->variables();

        $this->assertSame(9, $variables['key']);
        $this->assertSame('grid-editable-input', $variables['class']);
        $this->assertSame('status', $variables['name']);
        $this->assertSame('input', $variables['type']);
        $this->assertSame('Enabled', $variables['display']);
        $this->assertSame('enabled', $variables['value']);
        $this->assertSame('/admin/users/9', $variables['url']);
    }

    public function test_display_renders_editable_markup_with_default_refresh_false(): void
    {
        $displayer = $this->makeDisplayer('Enabled', 'enabled');
        $html = $displayer->display();

        $this->assertStringContainsString('data-editinline="popover"', $html);
        $this->assertStringContainsString('data-name="status"', $html);
        $this->assertStringContainsString('data-url="/admin/users/9"', $html);
        $this->assertStringContainsString('data-refresh=""', $html);
    }

    public function test_display_boolean_true_maps_to_refresh_true(): void
    {
        $displayer = $this->makeDisplayer('Enabled', 'enabled');
        $html = $displayer->display(true);

        $this->assertStringContainsString('data-refresh="1"', $html);
    }

    public function test_protected_helpers_return_expected_values(): void
    {
        $displayer = $this->makeDisplayer('Enabled', 'enabled');

        $this->assertSame('status', $displayer->exposeName());
        $this->assertSame('Enabled', $displayer->exposeValue());
        $this->assertSame('enabled', $displayer->exposeOriginal());
        $this->assertSame('grid-editable-input', $displayer->exposeSelector());
        $this->assertSame('/admin/users/9', $displayer->exposeUrl());
    }
}
