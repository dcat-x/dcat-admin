<?php

declare(strict_types=1);

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

    public function test_render_adds_password_type_and_eye_icon(): void
    {
        $field = $this->createField();

        $html = $field->render();

        $this->assertStringContainsString('type="password"', $html);
        $this->assertStringContainsString('icon-eye', $html);
    }

    public function test_type_inputmask_and_datalist_are_chainable(): void
    {
        $field = $this->createField();

        $result = $field
            ->type('password')
            ->inputmask(['mask' => '******'])
            ->datalist(['secret', 'strong-password']);

        $this->assertSame($field, $result);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('password', $attributes['type']);
    }

    public function test_does_not_have_default_rules(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertEmpty($rules);
    }
}
