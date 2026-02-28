<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Password;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;

class PasswordTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'password', string $label = 'Password'): Password
    {
        return new Password($column, [$label]);
    }

    public function test_it_is_instance_of_text(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_can_be_constructed(): void
    {
        $field = $this->createField();

        $this->assertSame('password', $field->column());
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('user_password', 'User Password');

        $this->assertSame('user_password', $field->column());
    }

    public function test_render_method_exists(): void
    {
        $field = $this->createField();

        $this->assertTrue(method_exists($field, 'render'));
    }

    public function test_inherits_text_methods(): void
    {
        $field = $this->createField();

        $this->assertTrue(method_exists($field, 'type'));
        $this->assertTrue(method_exists($field, 'inputmask'));
        $this->assertTrue(method_exists($field, 'datalist'));
    }

    public function test_does_not_have_default_rules(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertEmpty($rules);
    }
}
