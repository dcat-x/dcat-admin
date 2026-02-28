<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Display;
use Dcat\Admin\Tests\TestCase;

class DisplayTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createDisplay(string $column = 'name', string $label = 'Name'): Display
    {
        return new Display($column, [$label]);
    }

    public function test_is_instance_of_field(): void
    {
        $field = $this->createDisplay();

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_callback_is_null_by_default(): void
    {
        $field = $this->createDisplay();

        $callback = $this->getProtectedProperty($field, 'callback');

        $this->assertNull($callback);
    }

    public function test_with_sets_callback(): void
    {
        $field = $this->createDisplay();

        $closure = function ($value) {
            return strtoupper($value);
        };

        $field->with($closure);

        $callback = $this->getProtectedProperty($field, 'callback');

        $this->assertSame($closure, $callback);
    }

    public function test_with_accepts_closure(): void
    {
        $field = $this->createDisplay();

        $field->with(function ($value) {
            return $value.' modified';
        });

        $callback = $this->getProtectedProperty($field, 'callback');

        $this->assertInstanceOf(\Closure::class, $callback);
    }

    public function test_column_is_set(): void
    {
        $field = $this->createDisplay('title', 'Title');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('title', $column);
    }
}
