<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\ListField;
use Dcat\Admin\Tests\TestCase;

class ListFieldTest extends TestCase
{
    protected function createListField(string $column = 'items', string $label = 'Items'): ListField
    {
        return new ListField($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function callProtectedMethod(object $object, string $method, array $args = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($object, ...$args);
    }

    // -------------------------------------------------------
    // max()
    // -------------------------------------------------------

    public function test_max_defaults_to_null(): void
    {
        $field = $this->createListField();

        $this->assertNull($this->getProtectedProperty($field, 'max'));
    }

    public function test_max_sets_value(): void
    {
        $field = $this->createListField();

        $result = $field->max(10);

        $this->assertSame($field, $result);
        $this->assertSame(10, $this->getProtectedProperty($field, 'max'));
    }

    // -------------------------------------------------------
    // min()
    // -------------------------------------------------------

    public function test_min_defaults_to_zero(): void
    {
        $field = $this->createListField();

        $this->assertSame(0, $this->getProtectedProperty($field, 'min'));
    }

    public function test_min_sets_value(): void
    {
        $field = $this->createListField();

        $result = $field->min(3);

        $this->assertSame($field, $result);
        $this->assertSame(3, $this->getProtectedProperty($field, 'min'));
    }

    // -------------------------------------------------------
    // DEFAULT_FLAG_NAME constant
    // -------------------------------------------------------

    public function test_default_flag_name_constant(): void
    {
        $this->assertSame('_def_', ListField::DEFAULT_FLAG_NAME);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_extracts_values(): void
    {
        $field = $this->createListField();

        $input = [
            'values' => ['foo', 'bar', 'baz'],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame(['foo', 'bar', 'baz'], $result);
    }

    public function test_prepare_input_value_removes_default_flag(): void
    {
        $field = $this->createListField();

        $input = [
            'values' => [
                ListField::DEFAULT_FLAG_NAME => '1',
                0 => 'item1',
                1 => 'item2',
            ],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame(['item1', 'item2'], $result);
    }

    public function test_prepare_input_value_returns_empty_array_when_no_values(): void
    {
        $field = $this->createListField();

        $input = [
            'values' => [ListField::DEFAULT_FLAG_NAME => '1'],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame([], $result);
    }

    public function test_prepare_input_value_reindexes_values(): void
    {
        $field = $this->createListField();

        $input = [
            'values' => [3 => 'a', 7 => 'b'],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame(['a', 'b'], $result);
    }

    // -------------------------------------------------------
    // formatValidatorMessages()
    // -------------------------------------------------------

    public function test_format_validator_messages_flattens_to_column(): void
    {
        $field = $this->createListField('items');

        $originalBag = new \Illuminate\Support\MessageBag([
            'items.values.0' => ['Item 0 is invalid'],
            'items.values.1' => ['Item 1 is invalid'],
        ]);

        $result = $field->formatValidatorMessages($originalBag);

        $this->assertInstanceOf(\Illuminate\Support\MessageBag::class, $result);
        $this->assertTrue($result->has('items'));
    }
}
