<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Checkbox;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CheckboxTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value = ['1', '3'], $original = ['1', '3']): TestableCheckboxDisplayer
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');
        $column->shouldReceive('getOriginal')->andReturn($original);

        return new TestableCheckboxDisplayer($value, $grid, $column, ['id' => 9]);
    }

    public function test_type_and_view_defaults_are_expected(): void
    {
        $type = new \ReflectionProperty(Checkbox::class, 'type');
        $type->setAccessible(true);

        $view = new \ReflectionProperty(Checkbox::class, 'view');
        $view->setAccessible(true);

        $this->assertSame('checkbox', $type->getDefaultValue());
        $this->assertSame('admin::grid.displayer.editinline.checkbox', $view->getDefaultValue());
    }

    public function test_render_checkbox_builds_widget_with_options_and_css_class(): void
    {
        $displayer = $this->makeDisplayer();

        $widget = $displayer->exposeRenderCheckbox([
            '1' => 'One',
            '2' => 'Two',
        ]);

        $this->assertSame([
            '1' => 'One',
            '2' => 'Two',
        ], $widget->getOptions());
        $this->assertSame('status[]', $widget->getHtmlAttribute('name'));
        $this->assertStringContainsString('ie-input', $widget->getHtmlAttribute('class'));
    }

    public function test_get_value_formats_selected_labels(): void
    {
        $displayer = $this->makeDisplayer(['1', '3']);
        $displayer->display([
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three',
        ]);

        $this->assertSame('One; Three', $displayer->exposeGetValue());
    }

    public function test_get_original_returns_json_encoded_string_array(): void
    {
        $displayer = $this->makeDisplayer(['1', '3'], [1, 3]);

        $this->assertSame('["1","3"]', $displayer->exposeGetOriginal());
    }

    public function test_display_renders_checkbox_template_and_refresh_flag(): void
    {
        $displayer = $this->makeDisplayer(['1']);

        $html = $displayer->display([
            '1' => 'One',
            '2' => 'Two',
        ], true);

        $this->assertStringContainsString('data-editinline="popover"', $html);
        $this->assertStringContainsString('data-name="status"', $html);
        $this->assertStringContainsString('data-refresh="1"', $html);
        $this->assertStringContainsString('One', $html);
    }

    public function test_protected_method_visibilities_are_expected(): void
    {
        $renderCheckbox = new \ReflectionMethod(Checkbox::class, 'renderCheckbox');
        $getValue = new \ReflectionMethod(Checkbox::class, 'getValue');
        $getOriginal = new \ReflectionMethod(Checkbox::class, 'getOriginal');

        $this->assertTrue($renderCheckbox->isProtected());
        $this->assertTrue($getValue->isProtected());
        $this->assertTrue($getOriginal->isProtected());
    }
}

class TestableCheckboxDisplayer extends Checkbox
{
    public function exposeRenderCheckbox(array $options)
    {
        return $this->renderCheckbox($options);
    }

    public function exposeGetValue()
    {
        return $this->getValue();
    }

    public function exposeGetOriginal()
    {
        return $this->getOriginal();
    }
}
