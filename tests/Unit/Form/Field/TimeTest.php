<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Date;
use Dcat\Admin\Form\Field\Time;
use Dcat\Admin\Tests\TestCase;

class TimeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createTime(string $column = 'start_time', string $label = 'Start Time'): Time
    {
        return new Time($column, [$label]);
    }

    public function test_is_instance_of_date(): void
    {
        $field = $this->createTime();

        $this->assertInstanceOf(Date::class, $field);
    }

    public function test_default_format_is_time(): void
    {
        $field = $this->createTime();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('HH:mm:ss', $format);
    }

    public function test_format_can_be_changed(): void
    {
        $field = $this->createTime();

        $result = $field->format('HH:mm');

        $this->assertSame($field, $result);
        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('HH:mm', $format);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createTime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createTime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('14:30:00', $method->invoke($field, '14:30:00'));
    }

    public function test_prepare_input_value_passes_null_through(): void
    {
        $field = $this->createTime();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, null));
    }
}
