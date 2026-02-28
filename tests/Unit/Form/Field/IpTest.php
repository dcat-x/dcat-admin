<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Ip;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;

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

    public function test_rules_contain_ip(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertContains('ip', $rules);
    }

    public function test_rules_contain_nullable(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertContains('nullable', $rules);
    }

    public function test_options_has_ip_alias(): void
    {
        $field = $this->createField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertArrayHasKey('alias', $options);
        $this->assertSame('ip', $options['alias']);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('server_ip', 'Server IP');

        $this->assertSame('server_ip', $field->column());
    }

    public function test_render_method_exists(): void
    {
        $field = $this->createField();

        $this->assertTrue(method_exists($field, 'render'));
    }
}
