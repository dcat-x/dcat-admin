<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\SelectTable;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SelectTableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makePresenterWithoutConstructor(): SelectTable
    {
        $reflection = new \ReflectionClass(SelectTable::class);

        return $reflection->newInstanceWithoutConstructor();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_js_property_contains_select_table_asset(): void
    {
        $this->assertContains('@select-table', SelectTable::$js);
    }

    public function test_style_property_default_is_primary(): void
    {
        $reflection = new \ReflectionClass(SelectTable::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertSame('primary', $defaults['style']);
    }

    public function test_options_sets_closure_and_returns_self(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();
        $callback = static fn () => [1 => 'A'];

        $result = $presenter->options($callback);

        $this->assertSame($presenter, $result);
        $this->assertSame($callback, $this->getProtectedProperty($presenter, 'options'));
    }

    public function test_pluck_sets_visible_column_and_key(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();

        $result = $presenter->pluck('name', 'id');

        $this->assertSame($presenter, $result);
        $this->assertSame('name', $this->getProtectedProperty($presenter, 'visibleColumn'));
        $this->assertSame('id', $this->getProtectedProperty($presenter, 'key'));
    }

    public function test_model_sets_pluck_and_options_closure(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();

        $result = $presenter->model(Administrator::class, 'id', 'name');

        $this->assertSame($presenter, $result);
        $this->assertSame('name', $this->getProtectedProperty($presenter, 'visibleColumn'));
        $this->assertSame('id', $this->getProtectedProperty($presenter, 'key'));
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($presenter, 'options'));
    }

    public function test_dialog_width_delegates_to_dialog(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();
        $dialog = Mockery::mock();
        $dialog->shouldReceive('width')->once()->with('70%');
        $this->setProtectedProperty($presenter, 'dialog', $dialog);

        $result = $presenter->dialogWidth('70%');

        $this->assertSame($presenter, $result);
    }

    public function test_title_delegates_to_dialog(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();
        $dialog = Mockery::mock();
        $dialog->shouldReceive('title')->once()->with('Choose User');
        $this->setProtectedProperty($presenter, 'dialog', $dialog);

        $result = $presenter->title('Choose User');

        $this->assertSame($presenter, $result);
    }

    public function test_placeholder_setter_and_getter(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();

        $result = $presenter->placeholder('Please choose');

        $this->assertSame($presenter, $result);
        $this->assertSame('Please choose', $presenter->placeholder());
    }

    public function test_render_button_contains_style_class(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();

        $button = $this->invokeProtectedMethod($presenter, 'renderButton');

        $this->assertStringContainsString('btn-primary', $button);
        $this->assertStringContainsString('icon-arrow-up', $button);
    }

    public function test_render_footer_contains_submit_and_cancel_buttons(): void
    {
        $presenter = $this->makePresenterWithoutConstructor();

        $footer = $this->invokeProtectedMethod($presenter, 'renderFooter');

        $this->assertStringContainsString('submit-btn', $footer);
        $this->assertStringContainsString('cancel-btn', $footer);
    }
}
