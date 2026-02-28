<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\ArrayField;
use Dcat\Admin\Tests\TestCase;

class ArrayFieldTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    protected function createArrayField(string $column = 'items', ?string $label = null, ?\Closure $builder = null): ArrayField
    {
        $builder = $builder ?? function ($form) {};

        $args = $label !== null ? [$label, $builder] : [$builder];

        return new ArrayField($column, $args);
    }

    // -------------------------------------------------------
    // constructor
    // -------------------------------------------------------

    public function test_constructor_with_builder_only(): void
    {
        $builder = function ($form) {};
        $field = new ArrayField('items', [$builder]);

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('items', $column);
    }

    public function test_constructor_with_label_and_builder(): void
    {
        $builder = function ($form) {};
        $field = new ArrayField('items', ['My Items', $builder]);

        $label = $this->getProtectedProperty($field, 'label');
        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('My Items', $label);
        $this->assertSame('items', $column);
    }

    public function test_constructor_stores_builder(): void
    {
        $builder = function ($form) {
            $form->text('name');
        };
        $field = new ArrayField('items', [$builder]);

        $storedBuilder = $this->getProtectedProperty($field, 'builder');

        $this->assertSame($builder, $storedBuilder);
    }

    // -------------------------------------------------------
    // class hierarchy
    // -------------------------------------------------------

    public function test_extends_has_many(): void
    {
        $field = $this->createArrayField();

        $this->assertInstanceOf(\Dcat\Admin\Form\Field\HasMany::class, $field);
    }

    public function test_extends_field(): void
    {
        $field = $this->createArrayField();

        $this->assertInstanceOf(\Dcat\Admin\Form\Field::class, $field);
    }

    // -------------------------------------------------------
    // buildRelatedForms() returns empty when no form set
    // -------------------------------------------------------

    public function test_build_related_forms_returns_empty_when_no_form(): void
    {
        $field = $this->createArrayField();

        $reflection = new \ReflectionMethod($field, 'buildRelatedForms');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($field);

        $this->assertSame([], $result);
    }

    // -------------------------------------------------------
    // columnClass is set
    // -------------------------------------------------------

    public function test_column_class_is_set(): void
    {
        $field = $this->createArrayField('test_items');

        $columnClass = $this->getProtectedProperty($field, 'columnClass');

        $this->assertNotEmpty($columnClass);
        $this->assertIsString($columnClass);
    }
}
