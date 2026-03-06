<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Form\Field\Url;
use Dcat\Admin\Tests\TestCase;

class UrlTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createField(string $column = 'url', string $label = 'URL'): Url
    {
        return new Url($column, [$label]);
    }

    public function test_it_is_instance_of_text(): void
    {
        $field = $this->createField();

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_rules_contain_url(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertContains('url', $rules);
    }

    public function test_rules_contain_nullable(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertContains('nullable', $rules);
    }

    public function test_rules_has_exactly_two_entries(): void
    {
        $field = $this->createField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertCount(2, $rules);
    }

    public function test_can_be_constructed_with_custom_column(): void
    {
        $field = $this->createField('website', 'Website');

        $this->assertSame('website', $field->column());
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Url::class, 'render');

        $this->assertSame(0, $method->getNumberOfParameters());
    }
}
