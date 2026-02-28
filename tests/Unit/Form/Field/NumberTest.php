<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Number;
use Dcat\Admin\Tests\TestCase;

class NumberTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createNumber(string $column = 'quantity', string $label = 'Quantity'): Number
    {
        return new Number($column, [$label]);
    }

    // -------------------------------------------------------
    // min()
    // -------------------------------------------------------

    public function test_min_sets_attribute(): void
    {
        $field = $this->createNumber();

        $result = $field->min(0);

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('0', $attributes['min']);
    }

    public function test_min_with_negative_value(): void
    {
        $field = $this->createNumber();

        $field->min(-10);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('-10', $attributes['min']);
    }

    // -------------------------------------------------------
    // max()
    // -------------------------------------------------------

    public function test_max_sets_attribute(): void
    {
        $field = $this->createNumber();

        $result = $field->max(100);

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('100', $attributes['max']);
    }

    public function test_max_with_large_value(): void
    {
        $field = $this->createNumber();

        $field->max(999999);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('999999', $attributes['max']);
    }

    // -------------------------------------------------------
    // disable()
    // -------------------------------------------------------

    public function test_disable_sets_options_disabled(): void
    {
        $field = $this->createNumber();

        $result = $field->disable();

        $this->assertSame($field, $result);
        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['disabled']);
    }

    public function test_disable_with_false_resets_options(): void
    {
        $field = $this->createNumber();

        $field->disable();
        $field->disable(false);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertFalse($options['disabled']);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_converts_empty_to_zero(): void
    {
        $field = $this->createNumber();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke($field, ''));
        $this->assertSame(0, $method->invoke($field, null));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createNumber();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(42, $method->invoke($field, 42));
        $this->assertSame('15', $method->invoke($field, '15'));
    }

    // -------------------------------------------------------
    // value()
    // -------------------------------------------------------

    public function test_value_getter_returns_int(): void
    {
        $field = $this->createNumber();

        $field->fill(['quantity' => '5']);

        $this->assertSame(5, $field->value());
    }

    public function test_value_getter_returns_zero_for_null(): void
    {
        $field = $this->createNumber();

        $this->assertSame(0, $field->value());
    }

    public function test_value_setter_returns_this(): void
    {
        $field = $this->createNumber();

        $result = $field->value(10);

        $this->assertSame($field, $result);
    }

    // -------------------------------------------------------
    // default options
    // -------------------------------------------------------

    public function test_default_options(): void
    {
        $field = $this->createNumber();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('primary shadow-0', $options['upClass']);
        $this->assertSame('light shadow-0', $options['downClass']);
        $this->assertTrue($options['center']);
        $this->assertFalse($options['disabled']);
    }
}
