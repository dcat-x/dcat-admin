<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Ip;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IpTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'ip', string $label = 'IP'): Ip
    {
        return new Ip($column, [$label]);
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

    public function test_options_has_ip_alias(): void
    {
        $field = $this->createField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('ip', $options['alias'] ?? null);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('server_ip', 'Server IP');

        $this->assertSame('server_ip', $field->column());
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Ip::class, 'render');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public static function ruleProvider(): array
    {
        return [
            'ip' => ['ip'],
            'nullable' => ['nullable'],
        ];
    }
}
