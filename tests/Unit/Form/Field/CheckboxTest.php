<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Checkbox;
use Dcat\Admin\Tests\TestCase;

class CheckboxTest extends TestCase
{
    protected function createCheckbox(string $column = 'tags', string $label = 'Tags'): Checkbox
    {
        return new Checkbox($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // options()
    // -------------------------------------------------------

    public function test_options_with_array(): void
    {
        $field = $this->createCheckbox();
        $options = ['php' => 'PHP', 'js' => 'JavaScript'];

        $result = $field->options($options);

        $this->assertSame($field, $result);
        $this->assertSame($options, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_empty_array(): void
    {
        $field = $this->createCheckbox();

        $field->options([]);

        $this->assertSame([], $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_closure(): void
    {
        $field = $this->createCheckbox();
        $closure = function () {
            return ['a' => 'A'];
        };

        $result = $field->options($closure);

        $this->assertSame($field, $result);
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($field, 'options'));
    }

    // -------------------------------------------------------
    // style()
    // -------------------------------------------------------

    public function test_default_style_is_primary(): void
    {
        $field = $this->createCheckbox();

        $this->assertSame('primary', $this->getProtectedProperty($field, 'style'));
    }

    public function test_style_sets_custom_style(): void
    {
        $field = $this->createCheckbox();

        $result = $field->style('danger');

        $this->assertSame($field, $result);
        $this->assertSame('danger', $this->getProtectedProperty($field, 'style'));
    }

    public function test_style_can_be_changed_multiple_times(): void
    {
        $field = $this->createCheckbox();

        $field->style('info');
        $field->style('success');

        $this->assertSame('success', $this->getProtectedProperty($field, 'style'));
    }

    // -------------------------------------------------------
    // canCheckAll()
    // -------------------------------------------------------

    public function test_can_check_all_defaults_to_false(): void
    {
        $field = $this->createCheckbox();

        $this->assertFalse($this->getProtectedProperty($field, 'canCheckAll'));
    }

    public function test_can_check_all_enables_feature(): void
    {
        $field = $this->createCheckbox();

        $result = $field->canCheckAll();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'canCheckAll'));
    }

    // -------------------------------------------------------
    // inline()
    // -------------------------------------------------------

    public function test_inline_defaults_to_true(): void
    {
        $field = $this->createCheckbox();

        $this->assertTrue($this->getProtectedProperty($field, 'inline'));
    }

    public function test_inline_can_be_disabled(): void
    {
        $field = $this->createCheckbox();

        $result = $field->inline(false);

        $this->assertSame($field, $result);
        $this->assertFalse($this->getProtectedProperty($field, 'inline'));
    }

    public function test_inline_can_be_re_enabled(): void
    {
        $field = $this->createCheckbox();

        $field->inline(false);
        $field->inline(true);

        $this->assertTrue($this->getProtectedProperty($field, 'inline'));
    }

    // -------------------------------------------------------
    // cascadeEvent default
    // -------------------------------------------------------

    public function test_cascade_event_defaults_to_change(): void
    {
        $field = $this->createCheckbox();

        $cascadeEvent = $this->getProtectedProperty($field, 'cascadeEvent');

        $this->assertSame('change', $cascadeEvent);
    }
}
