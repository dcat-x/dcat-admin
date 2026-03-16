<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\KeyValue;
use Dcat\Admin\Tests\TestCase;

class KeyValueTest extends TestCase
{
    protected function createKeyValue(string $column = 'meta', string $label = 'Meta'): KeyValue
    {
        return new KeyValue($column, [$label]);
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
    // setKeyLabel() / getKeyLabel()
    // -------------------------------------------------------

    public function test_key_label_defaults_to_translation(): void
    {
        $field = $this->createKeyValue();

        $this->assertSame(__('Key'), $field->getKeyLabel());
    }

    public function test_set_key_label(): void
    {
        $field = $this->createKeyValue();

        $result = $field->setKeyLabel('Property Name');

        $this->assertSame($field, $result);
        $this->assertSame('Property Name', $field->getKeyLabel());
    }

    public function test_set_key_label_to_null_uses_default(): void
    {
        $field = $this->createKeyValue();

        $field->setKeyLabel(null);

        $this->assertSame(__('Key'), $field->getKeyLabel());
    }

    // -------------------------------------------------------
    // setValueLabel() / getValueLabel()
    // -------------------------------------------------------

    public function test_value_label_defaults_to_translation(): void
    {
        $field = $this->createKeyValue();

        $this->assertSame(__('Value'), $field->getValueLabel());
    }

    public function test_set_value_label(): void
    {
        $field = $this->createKeyValue();

        $result = $field->setValueLabel('Property Value');

        $this->assertSame($field, $result);
        $this->assertSame('Property Value', $field->getValueLabel());
    }

    public function test_set_value_label_to_null_uses_default(): void
    {
        $field = $this->createKeyValue();

        $field->setValueLabel(null);

        $this->assertSame(__('Value'), $field->getValueLabel());
    }

    // -------------------------------------------------------
    // DEFAULT_FLAG_NAME constant
    // -------------------------------------------------------

    public function test_default_flag_name_constant(): void
    {
        $this->assertSame('_def_', KeyValue::DEFAULT_FLAG_NAME);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_combines_keys_and_values(): void
    {
        $field = $this->createKeyValue();

        $input = [
            'keys' => ['name', 'color'],
            'values' => ['apple', 'red'],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame(['name' => 'apple', 'color' => 'red'], $result);
    }

    public function test_prepare_input_value_removes_default_flag(): void
    {
        $field = $this->createKeyValue();

        $input = [
            KeyValue::DEFAULT_FLAG_NAME => '1',
            'keys' => ['k1'],
            'values' => ['v1'],
        ];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame(['k1' => 'v1'], $result);
    }

    public function test_prepare_input_value_returns_empty_array_when_empty(): void
    {
        $field = $this->createKeyValue();

        $input = [KeyValue::DEFAULT_FLAG_NAME => '1'];

        $result = $this->callProtectedMethod($field, 'prepareInputValue', [$input]);

        $this->assertSame([], $result);
    }
}
