<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Date;
use Dcat\Admin\Form\Field\Datetime;
use Dcat\Admin\Tests\TestCase;

class DatetimeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createDatetime(string $column = 'created_at', string $label = 'Created At'): Datetime
    {
        return new Datetime($column, [$label]);
    }

    public function test_is_instance_of_date(): void
    {
        $field = $this->createDatetime();

        $this->assertInstanceOf(Date::class, $field);
    }

    public function test_default_format_is_datetime(): void
    {
        $field = $this->createDatetime();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $format);
    }

    public function test_format_can_be_changed(): void
    {
        $field = $this->createDatetime();

        $result = $field->format('DD/MM/YYYY HH:mm');

        $this->assertSame($field, $result);
        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('DD/MM/YYYY HH:mm', $format);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createDatetime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createDatetime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('2024-01-15 10:30:00', $method->invoke($field, '2024-01-15 10:30:00'));
    }

    public function test_prepare_input_value_passes_null_through(): void
    {
        $field = $this->createDatetime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, null));
    }
}
