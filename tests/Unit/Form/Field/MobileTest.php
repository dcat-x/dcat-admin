<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Mobile;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MobileTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'mobile', string $label = 'Mobile'): Mobile
    {
        return new Mobile($column, [$label]);
    }

    public function test_it_is_instance_of_text(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Text::class, $field);
    }

    #[DataProvider('optionProvider')]
    public function test_options_contain_expected_values(string $key, mixed $expected): void
    {
        $field = $this->createField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame($expected, $options[$key] ?? null);
    }

    public function test_options_mask_is_eleven_digits(): void
    {
        $field = $this->createField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame(11, strlen($options['mask']));
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('phone', 'Phone');

        $this->assertSame('phone', $field->column());
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Mobile::class, 'render');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_does_not_have_default_rules(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertEmpty($rules);
    }

    public static function optionProvider(): array
    {
        return [
            'mask' => ['mask', '99999999999'],
        ];
    }
}
