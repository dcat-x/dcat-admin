<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasHtmlAttributes;

class HasHtmlAttributesTest extends TestCase
{
    protected function makeInstance(): object
    {
        return new class
        {
            use HasHtmlAttributes;
        };
    }

    public function test_set_and_get_single_attribute(): void
    {
        $obj = $this->makeInstance();

        $result = $obj->setHtmlAttribute('id', 'my-id');

        $this->assertSame($obj, $result);
        $this->assertSame('my-id', $obj->getHtmlAttribute('id'));
    }

    public function test_set_attributes_with_array(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute(['id' => 'test', 'class' => 'btn']);

        $this->assertSame('test', $obj->getHtmlAttribute('id'));
        $this->assertSame('btn', $obj->getHtmlAttribute('class'));
    }

    public function test_get_attribute_returns_default_when_not_set(): void
    {
        $obj = $this->makeInstance();

        $this->assertNull($obj->getHtmlAttribute('nonexistent'));
        $this->assertSame('fallback', $obj->getHtmlAttribute('nonexistent', 'fallback'));
    }

    public function test_default_html_attribute_does_not_overwrite(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute('class', 'original');
        $obj->defaultHtmlAttribute('class', 'overwritten');

        $this->assertSame('original', $obj->getHtmlAttribute('class'));
    }

    public function test_default_html_attribute_sets_when_missing(): void
    {
        $obj = $this->makeInstance();

        $obj->defaultHtmlAttribute('class', 'default-class');

        $this->assertSame('default-class', $obj->getHtmlAttribute('class'));
    }

    public function test_append_html_attribute_string(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute('class', 'btn');
        $obj->appendHtmlAttribute('class', 'btn-primary');

        $this->assertSame('btn btn-primary', $obj->getHtmlAttribute('class'));
    }

    public function test_append_html_attribute_array(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute('data', ['a']);
        $obj->appendHtmlAttribute('data', 'b');

        $this->assertSame(['a', 'b'], $obj->getHtmlAttribute('data'));
    }

    public function test_forget_html_attribute(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute(['id' => 'test', 'class' => 'btn']);
        $obj->forgetHtmlAttribute('id');

        $this->assertFalse($obj->hasHtmlAttribute('id'));
        $this->assertTrue($obj->hasHtmlAttribute('class'));
    }

    public function test_has_html_attribute(): void
    {
        $obj = $this->makeInstance();

        $this->assertFalse($obj->hasHtmlAttribute('id'));

        $obj->setHtmlAttribute('id', 'test');

        $this->assertTrue($obj->hasHtmlAttribute('id'));
    }

    public function test_get_all_html_attributes(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute(['id' => 'test', 'class' => 'btn', 'name' => 'field']);

        $attrs = $obj->getHtmlAttributes();

        $this->assertCount(3, $attrs);
        $this->assertSame('test', $attrs['id']);
        $this->assertSame('btn', $attrs['class']);
        $this->assertSame('field', $attrs['name']);
    }

    public function test_format_html_attributes(): void
    {
        $obj = $this->makeInstance();

        $obj->setHtmlAttribute(['id' => 'test', 'class' => 'btn']);

        $formatted = $obj->formatHtmlAttributes();

        $this->assertIsString($formatted);
        $this->assertStringContainsString('id=', $formatted);
        $this->assertStringContainsString('class=', $formatted);
    }
}
