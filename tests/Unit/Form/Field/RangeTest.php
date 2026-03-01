<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Range;
use Dcat\Admin\Tests\TestCase;

class RangeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createRange(
        string $startColumn = 'min_value',
        string $endColumn = 'max_value',
        string $label = 'Range'
    ): Range {
        return new Range($startColumn, [$endColumn, $label]);
    }

    public function test_is_instance_of_field(): void
    {
        $field = $this->createRange();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_sets_start_column(): void
    {
        $field = $this->createRange('price_min', 'price_max');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('price_min', $column['start']);
    }

    public function test_constructor_sets_end_column(): void
    {
        $field = $this->createRange('price_min', 'price_max');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('price_max', $column['end']);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('100', $method->invoke($field, '100'));
    }

    public function test_prepare_input_value_passes_zero_through(): void
    {
        $field = $this->createRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke($field, 0));
    }
}
