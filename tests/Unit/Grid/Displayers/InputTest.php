<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Grid\Displayers\Input;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class InputTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value, $original = null): Input
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('title');
        $column->shouldReceive('getOriginal')->andReturn($original ?? $value);

        $row = ['id' => 1, 'title' => $value];

        return new Input($value, $grid, $column, $row);
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

    public function test_type_is_input(): void
    {
        $displayer = $this->makeDisplayer('test');

        $type = $this->getProtectedProperty($displayer, 'type');

        $this->assertSame('input', $type);
    }

    public function test_view_is_input_editinline_view(): void
    {
        $displayer = $this->makeDisplayer('test');

        $view = $this->getProtectedProperty($displayer, 'view');

        $this->assertSame('admin::grid.displayer.editinline.input', $view);
    }

    // -------------------------------------------------------
    // variables()
    // -------------------------------------------------------

    #[DataProvider('variablesProvider')]
    public function test_variables_contain_expected_values($value, $original, string $key, mixed $expected): void
    {
        $displayer = $this->makeDisplayer($value, $original);

        $vars = $displayer->variables();

        $this->assertSame($expected, $vars[$key] ?? null);
    }

    // -------------------------------------------------------
    // Options
    // -------------------------------------------------------

    public function test_default_options_contain_refresh_false(): void
    {
        $displayer = $this->makeDisplayer('test');

        $options = $this->getProtectedProperty($displayer, 'options');

        $this->assertFalse($options['refresh'] ?? null);
    }

    // -------------------------------------------------------
    // display() - renders view output
    // -------------------------------------------------------

    public function test_display_renders_inline_edit_html(): void
    {
        $displayer = $this->makeDisplayer('My Title');

        $result = $displayer->display();

        $this->assertIsString($result);
        $this->assertStringContainsString('ie-wrap', $result);
        $this->assertStringContainsString('grid-editable-input', $result);
        $this->assertStringContainsString('My Title', $result);
    }

    public function test_display_output_contains_data_attributes(): void
    {
        $displayer = $this->makeDisplayer('Hello', 'Hello');

        $result = $displayer->display();

        $this->assertStringContainsString('data-name="title"', $result);
        $this->assertStringContainsString('data-key="1"', $result);
        $this->assertStringContainsString('data-value="Hello"', $result);
        $this->assertStringContainsString('data-url="/admin/users/1"', $result);
    }

    public function test_display_output_contains_editinline_popover_trigger(): void
    {
        $displayer = $this->makeDisplayer('test');

        $result = $displayer->display();

        $this->assertStringContainsString('data-editinline="popover"', $result);
        $this->assertStringContainsString('data-temp="grid-editinline-input-title"', $result);
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

    public function test_display_with_boolean_false_sets_refresh_false(): void
    {
        $displayer = $this->makeDisplayer('test');

        $result = $displayer->display(false);

        $this->assertIsString($result);
        // refresh should be empty/falsy in output
        $this->assertStringContainsString('data-refresh=""', $result);
    }

    public function test_display_with_empty_value_shows_edit_icon(): void
    {
        $displayer = $this->makeDisplayer('', '');

        $result = $displayer->display();

        $this->assertStringContainsString('icon-edit-2', $result);
    }

    public static function variablesProvider(): array
    {
        return [
            ['hello', null, 'key', 1],
            ['hello', null, 'name', 'title'],
            ['hello', null, 'type', 'input'],
            ['hello', null, 'display', 'hello'],
            ['display_val', 'original_val', 'value', 'original_val'],
            ['hello', null, 'url', '/admin/users/1'],
            ['hello', null, 'class', 'grid-editable-input'],
        ];
    }
}
