<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\SwitchField;
use Dcat\Admin\Tests\TestCase;

class SwitchFieldTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createSwitch(string $column = 'is_active', string $label = 'Active'): SwitchField
    {
        return new SwitchField($column, [$label]);
    }

    // -------------------------------------------------------
    // color()
    // -------------------------------------------------------

    public function test_color_sets_data_color_attribute(): void
    {
        $field = $this->createSwitch();

        $result = $field->color('#ff0000');

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('#ff0000', $attributes['data-color']);
    }

    // -------------------------------------------------------
    // secondary()
    // -------------------------------------------------------

    public function test_secondary_sets_data_secondary_color_attribute(): void
    {
        $field = $this->createSwitch();

        $result = $field->secondary('#cccccc');

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('#cccccc', $attributes['data-secondary-color']);
    }

    // -------------------------------------------------------
    // small()
    // -------------------------------------------------------

    public function test_small_sets_data_size(): void
    {
        $field = $this->createSwitch();

        $result = $field->small();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('small', $attributes['data-size']);
    }

    // -------------------------------------------------------
    // large()
    // -------------------------------------------------------

    public function test_large_sets_data_size(): void
    {
        $field = $this->createSwitch();

        $result = $field->large();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('large', $attributes['data-size']);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_truthy_returns_one(): void
    {
        $field = $this->createSwitch();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(1, $method->invoke($field, true));
        $this->assertSame(1, $method->invoke($field, 1));
        $this->assertSame(1, $method->invoke($field, 'yes'));
    }

    public function test_prepare_input_value_falsy_returns_zero(): void
    {
        $field = $this->createSwitch();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke($field, false));
        $this->assertSame(0, $method->invoke($field, 0));
        $this->assertSame(0, $method->invoke($field, null));
        $this->assertSame(0, $method->invoke($field, ''));
    }

    // -------------------------------------------------------
    // color convenience methods
    // -------------------------------------------------------

    public function test_primary_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->primary();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
        $this->assertNotEmpty($attributes['data-color']);
    }

    public function test_green_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->green();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }

    public function test_red_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->red();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }

    public function test_yellow_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->yellow();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }

    public function test_blue_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->blue();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }

    public function test_purple_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->purple();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }

    public function test_custom_sets_color(): void
    {
        $field = $this->createSwitch();

        $result = $field->custom();

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertArrayHasKey('data-color', $attributes);
    }
}
