<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Date;
use Dcat\Admin\Form\Field\Year;
use Dcat\Admin\Tests\TestCase;

class YearTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createYear(string $column = 'birth_year', string $label = 'Birth Year'): Year
    {
        return new Year($column, [$label]);
    }

    public function test_is_instance_of_date(): void
    {
        $field = $this->createYear();

        $this->assertInstanceOf(Date::class, $field);
    }

    public function test_default_format_is_year(): void
    {
        $field = $this->createYear();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('YYYY', $format);
    }

    public function test_format_can_be_changed(): void
    {
        $field = $this->createYear();

        $result = $field->format('YY');

        $this->assertSame($field, $result);
        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('YY', $format);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createYear();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createYear();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('2024', $method->invoke($field, '2024'));
    }
}
