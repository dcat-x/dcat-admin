<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Form\Field\CanCascadeFields;
use Dcat\Admin\Form\Field\Checkbox;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanCascadeFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    public function test_conditions_and_cascade_groups_default_to_empty_arrays(): void
    {
        $field = new FakeCascadeField;

        $this->assertSame([], $this->getProtectedProperty($field, 'conditions'));
        $this->assertSame([], $this->getProtectedProperty($field, 'cascadeGroups'));
    }

    public function test_when_with_two_arguments_uses_default_operator_and_registers_group(): void
    {
        $field = new FakeCascadeField;

        $result = $field->when('active', static function (): void {});

        $this->assertSame($field, $result);

        $conditions = $this->getProtectedProperty($field, 'conditions');
        $this->assertSame('=', $conditions[0]['operator']);
        $this->assertSame('active', $conditions[0]['value']);

        $this->assertSame('status', $field->form->groups[0]['column']);
        $this->assertSame(0, $field->form->groups[0]['index']);
        $this->assertStringContainsString('cascade-status-field-active-0', $field->form->groups[0]['class']);
    }

    public function test_get_default_operator_returns_in_for_checkbox_field(): void
    {
        $checkbox = new Checkbox('roles', ['Roles']);

        $operator = $this->invokeProtectedMethod($checkbox, 'getDefaultOperator');

        $this->assertSame('in', $operator);
    }

    public function test_format_values_wraps_in_operator_and_stringifies_values(): void
    {
        $field = new FakeCascadeField;

        $value = 100;
        $this->invokeProtectedMethod($field, 'formatValues', ['in', &$value]);

        $this->assertSame(['100'], $value);

        $value = 200;
        $this->invokeProtectedMethod($field, 'formatValues', ['=', &$value]);

        $this->assertSame('200', $value);
    }

    public function test_get_cascade_script_returns_null_when_no_conditions(): void
    {
        $field = new FakeCascadeField;

        $script = $this->invokeProtectedMethod($field, 'getCascadeScript');

        $this->assertNull($script);
    }

    public function test_get_cascade_script_contains_runtime_sections_after_when_called(): void
    {
        $field = new Select('status', ['Status']);
        $form = new FakeCascadeForm;
        $this->setProtectedProperty($field, 'form', $form);
        $this->setProtectedProperty($field, 'parent', null);

        $field->when('=', 1, static function (): void {});

        $script = $this->invokeProtectedMethod($field, 'getCascadeScript');

        $this->assertIsString($script);
        $this->assertStringContainsString('operator_table', $script);
        $this->assertStringContainsString('cascade_groups', $script);
        $this->assertStringContainsString("event = 'change'", $script);
    }

    public function test_get_form_front_value_for_select_contains_value_reader(): void
    {
        $select = new Select('status', ['Status']);

        $code = $this->invokeProtectedMethod($select, 'getFormFrontValue');

        $this->assertStringContainsString('$(this).val()', $code);
    }

    public function test_get_form_front_value_throws_for_invalid_field_type(): void
    {
        $field = new FakeCascadeField;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid form field type');

        $this->invokeProtectedMethod($field, 'getFormFrontValue');
    }
}

class FakeCascadeField
{
    use CanCascadeFields;

    public string $cascadeEvent = 'change';

    public FakeCascadeForm $form;

    public $parent = null;

    public function __construct()
    {
        $this->form = new FakeCascadeForm;
    }

    public function column(): string
    {
        return 'status';
    }

    protected function getElementClassString(): string
    {
        return 'status field';
    }

    protected function getElementClassSelector(): string
    {
        return '.status-field';
    }
}

class FakeCascadeForm
{
    public array $groups = [];

    public function cascadeGroup(\Closure $closure, array $group): void
    {
        $this->groups[] = $group;
    }
}
