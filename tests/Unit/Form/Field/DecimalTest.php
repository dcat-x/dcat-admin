<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Decimal;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DecimalTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'price', string $label = 'Price'): Decimal
    {
        return new Decimal($column, [$label]);
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

    public function test_options_has_exactly_two_keys(): void
    {
        $field = $this->createField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertCount(2, $options);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('amount', 'Amount');

        $this->assertSame('amount', $field->column());
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Decimal::class, 'render');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public static function optionProvider(): array
    {
        return [
            'alias' => ['alias', 'decimal'],
            'rightAlign' => ['rightAlign', false],
        ];
    }
}
