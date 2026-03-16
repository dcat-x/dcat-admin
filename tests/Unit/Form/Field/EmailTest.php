<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Email;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EmailTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'email', string $label = 'Email'): Email
    {
        return new Email($column, [$label]);
    }

    public function test_it_is_instance_of_text(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Text::class, $field);
    }

    #[DataProvider('ruleProvider')]
    public function test_rules_contain_expected_values(string $rule): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertContains($rule, $rules);
    }

    public function test_rules_has_exactly_two_entries(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertCount(2, $rules);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('user_email', 'User Email');

        $this->assertSame('user_email', $field->column());
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Email::class, 'render');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public static function ruleProvider(): array
    {
        return [
            'email' => ['email'],
            'nullable' => ['nullable'],
        ];
    }
}
