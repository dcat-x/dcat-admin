<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\DateRange;
use Dcat\Admin\Tests\TestCase;

class DateRangeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createDateRange(
        string $startColumn = 'start_date',
        string $endColumn = 'end_date',
        string $label = 'Date Range'
    ): DateRange {
        return new DateRange($startColumn, [$endColumn, $label]);
    }

    public function test_is_instance_of_field(): void
    {
        $field = $this->createDateRange();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_sets_start_and_end_columns(): void
    {
        $field = $this->createDateRange('begin', 'finish');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertIsArray($column);
        $this->assertSame('begin', $column['start']);
        $this->assertSame('finish', $column['end']);
    }

    public function test_default_format_is_date(): void
    {
        $field = $this->createDateRange();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('YYYY-MM-DD', $format);
    }

    public function test_constructor_sets_format_in_options(): void
    {
        $field = $this->createDateRange();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('YYYY-MM-DD', $options['format'] ?? null);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createDateRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createDateRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('2024-01-15', $method->invoke($field, '2024-01-15'));
    }
}
