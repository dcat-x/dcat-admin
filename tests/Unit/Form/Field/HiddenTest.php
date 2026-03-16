<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Hidden;
use Dcat\Admin\Tests\TestCase;

class HiddenTest extends TestCase
{
    protected function createHidden(string $column = 'secret', string $label = 'Secret'): Hidden
    {
        return new Hidden($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // constructor
    // -------------------------------------------------------

    public function test_constructor_sets_column(): void
    {
        $field = $this->createHidden('user_id');

        $this->assertSame('user_id', $this->getProtectedProperty($field, 'column'));
    }

    public function test_constructor_sets_label(): void
    {
        $field = $this->createHidden('user_id', 'User ID');

        $this->assertSame('User ID', $this->getProtectedProperty($field, 'label'));
    }

    // -------------------------------------------------------
    // extends Field
    // -------------------------------------------------------

    public function test_extends_field(): void
    {
        $field = $this->createHidden();

        $this->assertInstanceOf(Field::class, $field);
    }

    // -------------------------------------------------------
    // default value
    // -------------------------------------------------------

    public function test_default_value_can_be_set(): void
    {
        $field = $this->createHidden();

        $result = $field->default('default_value');

        $this->assertSame($field, $result);
        $this->assertSame('default_value', $this->getProtectedProperty($field, 'default'));
    }

    // -------------------------------------------------------
    // value
    // -------------------------------------------------------

    public function test_value_can_be_set(): void
    {
        $field = $this->createHidden();

        $result = $field->value('my_value');

        $this->assertSame($field, $result);
        $this->assertSame('my_value', $field->value());
    }

    // -------------------------------------------------------
    // attribute
    // -------------------------------------------------------

    public function test_attribute_can_be_set(): void
    {
        $field = $this->createHidden();

        $result = $field->attribute('data-id', '123');

        $this->assertSame($field, $result);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('123', $attributes['data-id']);
    }

    // -------------------------------------------------------
    // rules
    // -------------------------------------------------------

    public function test_rules_can_be_set(): void
    {
        $field = $this->createHidden();

        $result = $field->rules('required|integer');

        $this->assertSame($field, $result);

        $rules = $this->getProtectedProperty($field, 'rules');
        $this->assertNotEmpty($rules);
    }
}
