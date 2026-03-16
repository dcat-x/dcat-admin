<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\DateRange;
use Dcat\Admin\Form\Field\DatetimeRange;
use Dcat\Admin\Tests\TestCase;

class DatetimeRangeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createDatetimeRange(
        string $startColumn = 'start_datetime',
        string $endColumn = 'end_datetime',
        string $label = 'Datetime Range'
    ): DatetimeRange {
        return new DatetimeRange($startColumn, [$endColumn, $label]);
    }

    public function test_is_instance_of_field(): void
    {
        $field = $this->createDatetimeRange();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_extends_date_range(): void
    {
        $field = $this->createDatetimeRange();

        $this->assertInstanceOf(DateRange::class, $field);
    }

    public function test_format_is_datetime(): void
    {
        $field = $this->createDatetimeRange();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $format);
    }

    public function test_constructor_sets_start_and_end_columns(): void
    {
        $field = $this->createDatetimeRange('start_at', 'end_at');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertIsArray($column);
        $this->assertSame('start_at', $column['start']);
        $this->assertSame('end_at', $column['end']);
    }

    public function test_constructor_sets_format_in_options(): void
    {
        $field = $this->createDatetimeRange();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $options['format'] ?? null);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createDatetimeRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }
}
