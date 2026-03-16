<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\DateRange;
use Dcat\Admin\Form\Field\TimeRange;
use Dcat\Admin\Tests\TestCase;

class TimeRangeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createTimeRange(
        string $startColumn = 'start_time',
        string $endColumn = 'end_time',
        string $label = 'Time Range'
    ): TimeRange {
        return new TimeRange($startColumn, [$endColumn, $label]);
    }

    public function test_is_instance_of_field(): void
    {
        $field = $this->createTimeRange();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_extends_date_range(): void
    {
        $field = $this->createTimeRange();

        $this->assertInstanceOf(DateRange::class, $field);
    }

    public function test_format_is_time(): void
    {
        $field = $this->createTimeRange();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('HH:mm:ss', $format);
    }

    public function test_constructor_sets_start_and_end_columns(): void
    {
        $field = $this->createTimeRange('open_at', 'close_at');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertIsArray($column);
        $this->assertSame('open_at', $column['start']);
        $this->assertSame('close_at', $column['end']);
    }

    public function test_constructor_sets_format_in_options(): void
    {
        $field = $this->createTimeRange();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('HH:mm:ss', $options['format'] ?? null);
    }

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createTimeRange();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }
}
