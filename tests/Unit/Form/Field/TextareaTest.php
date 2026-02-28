<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Textarea;
use Dcat\Admin\Tests\TestCase;

class TextareaTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createTextarea(string $column = 'content', string $label = 'Content'): Textarea
    {
        return new Textarea($column, [$label]);
    }

    // -------------------------------------------------------
    // rows()
    // -------------------------------------------------------

    public function test_rows_default_value(): void
    {
        $field = $this->createTextarea();

        $rows = $this->getProtectedProperty($field, 'rows');

        $this->assertSame(5, $rows);
    }

    public function test_rows_sets_value(): void
    {
        $field = $this->createTextarea();

        $result = $field->rows(10);

        $this->assertSame($field, $result);
        $rows = $this->getProtectedProperty($field, 'rows');
        $this->assertSame(10, $rows);
    }

    public function test_rows_with_custom_value(): void
    {
        $field = $this->createTextarea();

        $field->rows(3);

        $rows = $this->getProtectedProperty($field, 'rows');
        $this->assertSame(3, $rows);
    }

    // -------------------------------------------------------
    // value handling
    // -------------------------------------------------------

    public function test_fill_with_string_value(): void
    {
        $field = $this->createTextarea();

        $field->fill(['content' => 'Hello World']);

        $this->assertSame('Hello World', $field->value());
    }

    public function test_fill_with_array_value(): void
    {
        $field = $this->createTextarea();

        $field->fill(['content' => ['key' => 'value']]);

        $value = $this->getProtectedProperty($field, 'value');
        $this->assertIsArray($value);
    }
}
