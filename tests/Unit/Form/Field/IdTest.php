<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Id;
use Dcat\Admin\Tests\TestCase;

class IdTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'id', string $label = 'ID'): Id
    {
        return new Id($column, [$label]);
    }

    public function test_it_is_instance_of_field(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_can_be_constructed(): void
    {
        $field = $this->createField();

        $this->assertSame('id', $field->column());
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('record_id', 'Record ID');

        $this->assertSame('record_id', $field->column());
    }

    public function test_has_default_value_method(): void
    {
        $field = $this->createField();

        $result = $field->default(42);

        $this->assertSame($field, $result);
    }

    public function test_default_value_is_stored(): void
    {
        $field = $this->createField();

        $field->default(99);

        $default = $this->getProtectedProperty($field, 'default');

        $this->assertSame(99, $default);
    }

    public function test_label_can_be_retrieved(): void
    {
        $field = $this->createField('id', 'Identifier');

        $label = $field->label();

        $this->assertSame('Identifier', $label);
    }
}
