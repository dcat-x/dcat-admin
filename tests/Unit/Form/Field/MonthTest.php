<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Date;
use Dcat\Admin\Form\Field\Month;
use Dcat\Admin\Tests\TestCase;

class MonthTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createMonth(string $column = 'birth_month', string $label = 'Birth Month'): Month
    {
        return new Month($column, [$label]);
    }

    public function test_is_instance_of_date(): void
    {
        $field = $this->createMonth();

        $this->assertInstanceOf(Date::class, $field);
    }

    public function test_default_format_is_month(): void
    {
        $field = $this->createMonth();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('MM', $format);
    }

    public function test_format_can_be_changed(): void
    {
        $field = $this->createMonth();

        $result = $field->format('MMMM');

        $this->assertSame($field, $result);
        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('MMMM', $format);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createMonth();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createMonth();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('06', $method->invoke($field, '06'));
    }
}
