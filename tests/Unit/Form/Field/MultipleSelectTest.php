<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\MultipleSelect;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MultipleSelectTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMultipleSelect(string $column = 'tags', string $label = 'Tags'): MultipleSelect
    {
        return new MultipleSelect($column, [$label]);
    }

    // -------------------------------------------------------
    // Inheritance
    // -------------------------------------------------------

    public function test_extends_select(): void
    {
        $field = $this->createMultipleSelect();

        $this->assertInstanceOf(Select::class, $field);
    }

    // -------------------------------------------------------
    // formatFieldData()
    // -------------------------------------------------------

    protected function invokeFormatFieldData(MultipleSelect $field, array $data)
    {
        $reflection = new \ReflectionMethod($field, 'formatFieldData');
        $reflection->setAccessible(true);

        return $reflection->invoke($field, $data);
    }

    public function test_format_field_data_returns_array_from_array_value(): void
    {
        $field = $this->createMultipleSelect();

        $result = $this->invokeFormatFieldData($field, ['tags' => [1, 2, 3]]);

        $this->assertSame([1, 2, 3], $result);
    }

    public function test_format_field_data_returns_array_from_comma_string(): void
    {
        $field = $this->createMultipleSelect();

        $result = $this->invokeFormatFieldData($field, ['tags' => '1,2,3']);

        $this->assertSame(['1', '2', '3'], $result);
    }

    public function test_format_field_data_returns_empty_array_for_null(): void
    {
        $field = $this->createMultipleSelect();

        $result = $this->invokeFormatFieldData($field, ['tags' => null]);

        $this->assertSame([], $result);
    }

    public function test_format_field_data_returns_empty_array_for_empty_string(): void
    {
        $field = $this->createMultipleSelect();

        $result = $this->invokeFormatFieldData($field, ['tags' => '']);

        $this->assertSame([], $result);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_returns_array(): void
    {
        $field = $this->createMultipleSelect();

        $reflection = new \ReflectionMethod($field, 'prepareInputValue');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($field, [1, 2, 3]);

        $this->assertIsArray($result);
        $this->assertSame([1, 2, 3], $result);
    }

    public function test_prepare_input_value_converts_string_to_array(): void
    {
        $field = $this->createMultipleSelect();

        $reflection = new \ReflectionMethod($field, 'prepareInputValue');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($field, '1,2,3');

        $this->assertIsArray($result);
        $this->assertSame(['1', '2', '3'], $result);
    }

    public function test_prepare_input_value_returns_empty_array_for_null(): void
    {
        $field = $this->createMultipleSelect();

        $reflection = new \ReflectionMethod($field, 'prepareInputValue');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($field, null);

        $this->assertSame([], $result);
    }

    public function test_prepare_input_value_filters_empty_values(): void
    {
        $field = $this->createMultipleSelect();

        $reflection = new \ReflectionMethod($field, 'prepareInputValue');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($field, ['1', '', null, '2']);

        $this->assertIsArray($result);
        // Helper::array with filter=true removes empty values
        $this->assertNotContains('', $result);
        $this->assertNotContains(null, $result);
    }
}
