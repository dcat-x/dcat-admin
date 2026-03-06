<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;

class TextTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createText(string $column = 'name', string $label = 'Name'): Text
    {
        return new Text($column, [$label]);
    }

    // -------------------------------------------------------
    // type()
    // -------------------------------------------------------

    public function test_type_sets_attribute(): void
    {
        $field = $this->createText();

        $result = $field->type('email');

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('email', $attributes['type']);
    }

    public function test_type_can_be_set_to_password(): void
    {
        $field = $this->createText();

        $field->type('password');

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('password', $attributes['type']);
    }

    public function test_type_can_be_set_to_url(): void
    {
        $field = $this->createText();

        $field->type('url');

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('url', $attributes['type']);
    }

    // -------------------------------------------------------
    // minLength()
    // -------------------------------------------------------

    public function test_min_length_sets_data_attribute(): void
    {
        $field = $this->createText();

        $result = $field->minLength(5);

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame(5, $attributes['data-minlength']);
    }

    public function test_min_length_sets_error_message(): void
    {
        $field = $this->createText();

        $field->minLength(3);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertNotEmpty($attributes['data-minlength-error'] ?? null);
    }

    // -------------------------------------------------------
    // maxLength()
    // -------------------------------------------------------

    public function test_max_length_sets_data_attribute(): void
    {
        $field = $this->createText();

        $result = $field->maxLength(100);

        $this->assertSame($field, $result);
        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame(100, $attributes['data-maxlength']);
    }

    public function test_max_length_sets_error_message(): void
    {
        $field = $this->createText();

        $field->maxLength(50);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertNotEmpty($attributes['data-maxlength-error'] ?? null);
    }

    // -------------------------------------------------------
    // inputmask()
    // -------------------------------------------------------

    public function test_inputmask_sets_script(): void
    {
        $field = $this->createText();

        $result = $field->inputmask(['mask' => '99/99/9999']);

        $this->assertSame($field, $result);
        $script = $this->getProtectedProperty($field, 'script');
        $this->assertNotEmpty($script);
        $this->assertStringContainsString('inputmask', $script);
    }

    // -------------------------------------------------------
    // datalist()
    // -------------------------------------------------------

    public function test_datalist_returns_this(): void
    {
        $field = $this->createText();

        $result = $field->datalist(['Option A', 'Option B']);

        $this->assertSame($field, $result);
    }

    public function test_datalist_sets_list_attribute(): void
    {
        $field = $this->createText();

        $field->datalist(['Option A', 'Option B']);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertStringStartsWith('list-', $attributes['list'] ?? '');
    }
}
