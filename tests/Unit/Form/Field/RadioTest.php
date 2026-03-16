<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Radio;
use Dcat\Admin\Tests\TestCase;

class RadioTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createRadio(string $column = 'gender', string $label = 'Gender'): Radio
    {
        return new Radio($column, [$label]);
    }

    // -------------------------------------------------------
    // options()
    // -------------------------------------------------------

    public function test_options_with_array(): void
    {
        $field = $this->createRadio();
        $options = ['male' => 'Male', 'female' => 'Female'];

        $result = $field->options($options);

        $this->assertSame($field, $result);
        $this->assertSame($options, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_closure(): void
    {
        $field = $this->createRadio();
        $closure = function () {
            return ['a' => 'A'];
        };

        $result = $field->options($closure);

        $this->assertSame($field, $result);
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_empty_array(): void
    {
        $field = $this->createRadio();

        $field->options([]);

        $this->assertSame([], $this->getProtectedProperty($field, 'options'));
    }

    // -------------------------------------------------------
    // style()
    // -------------------------------------------------------

    public function test_style_default_value(): void
    {
        $field = $this->createRadio();

        $style = $this->getProtectedProperty($field, 'style');

        $this->assertSame('primary', $style);
    }

    public function test_style_sets_value(): void
    {
        $field = $this->createRadio();

        $result = $field->style('danger');

        $this->assertSame($field, $result);
        $style = $this->getProtectedProperty($field, 'style');
        $this->assertSame('danger', $style);
    }

    public function test_style_with_info(): void
    {
        $field = $this->createRadio();

        $field->style('info');

        $style = $this->getProtectedProperty($field, 'style');
        $this->assertSame('info', $style);
    }

    public function test_style_with_success(): void
    {
        $field = $this->createRadio();

        $field->style('success');

        $style = $this->getProtectedProperty($field, 'style');
        $this->assertSame('success', $style);
    }

    // -------------------------------------------------------
    // inline()
    // -------------------------------------------------------

    public function test_inline_default_value(): void
    {
        $field = $this->createRadio();

        $inline = $this->getProtectedProperty($field, 'inline');

        $this->assertTrue($inline);
    }

    public function test_inline_set_false(): void
    {
        $field = $this->createRadio();

        $result = $field->inline(false);

        $this->assertSame($field, $result);
        $inline = $this->getProtectedProperty($field, 'inline');
        $this->assertFalse($inline);
    }

    public function test_inline_set_true(): void
    {
        $field = $this->createRadio();

        $field->inline(false);
        $field->inline(true);

        $inline = $this->getProtectedProperty($field, 'inline');
        $this->assertTrue($inline);
    }

    // -------------------------------------------------------
    // cascadeEvent default
    // -------------------------------------------------------

    public function test_cascade_event_defaults_to_change(): void
    {
        $field = $this->createRadio();

        $cascadeEvent = $this->getProtectedProperty($field, 'cascadeEvent');

        $this->assertSame('change', $cascadeEvent);
    }
}
