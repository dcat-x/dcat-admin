<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Tel;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;

class TelTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'tel', string $label = 'Telephone'): Tel
    {
        return new Tel($column, [$label]);
    }

    public function test_it_is_instance_of_text(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('phone_number', 'Phone Number');

        $this->assertSame('phone_number', $field->column());
    }

    public function test_render_adds_tel_type_and_phone_icon(): void
    {
        $field = $this->createField();

        $html = $field->render();

        $this->assertStringContainsString('type="tel"', $html);
        $this->assertStringContainsString('fa-phone', $html);
    }

    public function test_type_inputmask_min_max_length_are_chainable(): void
    {
        $field = $this->createField();

        $result = $field
            ->type('tel')
            ->inputmask(['mask' => '999-9999'])
            ->minLength(7)
            ->maxLength(20);

        $this->assertSame($field, $result);

        $attributes = $this->getProtectedProperty($field, 'attributes');
        $this->assertSame('tel', $attributes['type']);
        $this->assertSame(7, $attributes['data-minlength']);
        $this->assertSame(20, $attributes['data-maxlength']);
    }

    public function test_does_not_have_default_rules(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertEmpty($rules);
    }
}
