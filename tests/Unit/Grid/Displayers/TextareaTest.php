<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Grid\Displayers\Textarea;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TextareaTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value, $original = null): Textarea
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/posts');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('description');
        $column->shouldReceive('getOriginal')->andReturn($original ?? $value);

        $row = ['id' => 1, 'description' => $value];

        return new Textarea($value, $grid, $column, $row);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // Inheritance
    // -------------------------------------------------------

    public function test_extends_editable(): void
    {
        $displayer = $this->makeDisplayer('test');

        $this->assertInstanceOf(Editable::class, $displayer);
    }

    // -------------------------------------------------------
    // Construction & type/view defaults
    // -------------------------------------------------------

    public function test_type_is_textarea(): void
    {
        $displayer = $this->makeDisplayer('test');

        $type = $this->getProtectedProperty($displayer, 'type');

        $this->assertSame('textarea', $type);
    }

    public function test_view_is_textarea_editinline_view(): void
    {
        $displayer = $this->makeDisplayer('test');

        $view = $this->getProtectedProperty($displayer, 'view');

        $this->assertSame('admin::grid.displayer.editinline.textarea', $view);
    }

    // -------------------------------------------------------
    // defaultOptions()
    // -------------------------------------------------------

    public function test_default_options_returns_rows_five(): void
    {
        $displayer = $this->makeDisplayer('test');

        $defaultOptions = $displayer->defaultOptions();

        $this->assertArrayHasKey('rows', $defaultOptions);
        $this->assertSame(5, $defaultOptions['rows']);
    }

    public function test_default_options_returns_array(): void
    {
        $displayer = $this->makeDisplayer('test');

        $defaultOptions = $displayer->defaultOptions();

        $this->assertIsArray($defaultOptions);
        $this->assertCount(1, $defaultOptions);
    }

    // -------------------------------------------------------
    // variables()
    // -------------------------------------------------------

    public function test_variables_contain_key(): void
    {
        $displayer = $this->makeDisplayer('hello');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('key', $vars);
        $this->assertSame(1, $vars['key']);
    }

    public function test_variables_contain_name(): void
    {
        $displayer = $this->makeDisplayer('hello');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('name', $vars);
        $this->assertSame('description', $vars['name']);
    }

    public function test_variables_contain_type(): void
    {
        $displayer = $this->makeDisplayer('hello');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('type', $vars);
        $this->assertSame('textarea', $vars['type']);
    }

    public function test_variables_contain_display_value(): void
    {
        $displayer = $this->makeDisplayer('some long text');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('display', $vars);
        $this->assertSame('some long text', $vars['display']);
    }

    public function test_variables_contain_original_value(): void
    {
        $displayer = $this->makeDisplayer('display_text', 'original_text');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('value', $vars);
        $this->assertSame('original_text', $vars['value']);
    }

    public function test_variables_contain_url(): void
    {
        $displayer = $this->makeDisplayer('hello');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('url', $vars);
        $this->assertSame('/admin/posts/1', $vars['url']);
    }

    public function test_variables_contain_class_selector(): void
    {
        $displayer = $this->makeDisplayer('hello');

        $vars = $displayer->variables();

        $this->assertArrayHasKey('class', $vars);
        $this->assertSame('grid-editable-textarea', $vars['class']);
    }

    // -------------------------------------------------------
    // Options
    // -------------------------------------------------------

    public function test_base_options_contain_refresh_false(): void
    {
        $displayer = $this->makeDisplayer('test');

        $options = $this->getProtectedProperty($displayer, 'options');

        $this->assertArrayHasKey('refresh', $options);
        $this->assertFalse($options['refresh']);
    }

    // -------------------------------------------------------
    // display() - renders view output
    // -------------------------------------------------------

    public function test_display_renders_inline_edit_html(): void
    {
        $displayer = $this->makeDisplayer('My Description');

        $result = $displayer->display();

        $this->assertIsString($result);
        $this->assertStringContainsString('ie-wrap', $result);
        $this->assertStringContainsString('grid-editable-textarea', $result);
        $this->assertStringContainsString('My Description', $result);
    }

    public function test_display_output_contains_data_attributes(): void
    {
        $displayer = $this->makeDisplayer('Hello World', 'Hello World');

        $result = $displayer->display();

        $this->assertStringContainsString('data-name="description"', $result);
        $this->assertStringContainsString('data-key="1"', $result);
        $this->assertStringContainsString('data-value="Hello World"', $result);
        $this->assertStringContainsString('data-url="/admin/posts/1"', $result);
    }

    public function test_display_output_contains_editinline_popover_trigger(): void
    {
        $displayer = $this->makeDisplayer('test');

        $result = $displayer->display();

        $this->assertStringContainsString('data-editinline="popover"', $result);
        $this->assertStringContainsString('data-temp="grid-editinline-textarea-description"', $result);
    }

    public function test_display_output_contains_ie_display_span(): void
    {
        $displayer = $this->makeDisplayer('My content here');

        $result = $displayer->display();

        $this->assertStringContainsString('ie-display', $result);
        $this->assertStringContainsString('My content here', $result);
    }

    public function test_display_with_refresh_true_option(): void
    {
        $displayer = $this->makeDisplayer('test');

        $result = $displayer->display(['refresh' => true]);

        $this->assertIsString($result);
        $this->assertStringContainsString('data-refresh="1"', $result);
    }

    public function test_display_with_boolean_true_converts_to_refresh_option(): void
    {
        $displayer = $this->makeDisplayer('test');

        // Editable::display() converts bool to ['refresh' => $bool]
        $result = $displayer->display(true);

        $this->assertIsString($result);
        $this->assertStringContainsString('data-refresh="1"', $result);
    }

    public function test_display_with_empty_value_shows_edit_icon(): void
    {
        $displayer = $this->makeDisplayer('', '');

        $result = $displayer->display();

        $this->assertStringContainsString('icon-edit-2', $result);
    }
}
