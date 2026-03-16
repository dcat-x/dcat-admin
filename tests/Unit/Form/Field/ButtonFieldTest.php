<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Button;
use Dcat\Admin\Tests\TestCase;

class ButtonFieldTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // instanceof & construction
    // -------------------------------------------------------

    public function test_is_instance_of_field(): void
    {
        $field = new Button('Click Me');

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_sets_label(): void
    {
        $field = new Button('Submit');

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertSame('Submit', $label);
    }

    public function test_constructor_generates_random_column(): void
    {
        $field = new Button('Test');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertNotEmpty($column);
        $this->assertIsString($column);
    }

    public function test_constructor_sets_default_button_class(): void
    {
        $field = new Button('Test');

        $variables = $this->getProtectedProperty($field, 'variables');

        $this->assertSame('btn-primary', $variables['buttonClass'] ?? null);
    }

    // -------------------------------------------------------
    // class()
    // -------------------------------------------------------

    public function test_class_sets_button_class(): void
    {
        $field = new Button('Test');

        $result = $field->class('btn-danger');

        $this->assertSame($field, $result);

        $variables = $this->getProtectedProperty($field, 'variables');
        $this->assertSame('btn-danger', $variables['buttonClass']);
    }

    public function test_class_overrides_default_class(): void
    {
        $field = new Button('Test');

        $field->class('btn-success');

        $variables = $this->getProtectedProperty($field, 'variables');
        $this->assertSame('btn-success', $variables['buttonClass']);
    }

    // -------------------------------------------------------
    // on()
    // -------------------------------------------------------

    public function test_on_sets_event_script(): void
    {
        $field = new Button('Test');

        $result = $field->on('click', 'alert("clicked")');

        $this->assertSame($field, $result);

        $script = $this->getProtectedProperty($field, 'script');
        $this->assertStringContainsString('click', $script);
        $this->assertStringContainsString('alert("clicked")', $script);
    }

    public function test_on_generates_jquery_event_binding(): void
    {
        $field = new Button('Test');

        $field->on('click', 'console.log("test")');

        $script = $this->getProtectedProperty($field, 'script');
        $this->assertStringContainsString(".on('click'", $script);
        $this->assertStringContainsString('console.log("test")', $script);
    }
}
