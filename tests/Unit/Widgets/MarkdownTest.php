<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Markdown;
use ReflectionProperty;

class MarkdownTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_constructor_with_content(): void
    {
        $md = new Markdown('# Hello');

        $this->assertSame('# Hello', $this->getProtectedProperty($md, 'content'));
    }

    public function test_constructor_without_content(): void
    {
        $md = new Markdown;

        $this->assertNull($this->getProtectedProperty($md, 'content'));
    }

    public function test_content_method_sets_content(): void
    {
        $md = new Markdown;
        $result = $md->content('## World');

        $this->assertSame($md, $result);
        $this->assertSame('## World', $this->getProtectedProperty($md, 'content'));
    }

    public function test_content_method_overwrites_previous_content(): void
    {
        $md = new Markdown('Initial');
        $md->content('Replaced');

        $this->assertSame('Replaced', $this->getProtectedProperty($md, 'content'));
    }

    public function test_constructor_sets_id_with_prefix(): void
    {
        $md = new Markdown;

        $id = $md->id();

        $this->assertStringStartsWith('mkd-', $id);
        $this->assertSame(12, strlen($id)); // mkd- + 8 random chars
    }

    public function test_each_instance_gets_unique_id(): void
    {
        $md1 = new Markdown;
        $md2 = new Markdown;

        $this->assertNotEquals($md1->id(), $md2->id());
    }

    public function test_default_options(): void
    {
        $md = new Markdown;
        $options = $md->getOptions();

        $this->assertSame('style,script,iframe', $options['htmlDecode']);
        $this->assertTrue($options['emoji']);
        $this->assertTrue($options['taskList']);
        $this->assertTrue($options['tex']);
        $this->assertTrue($options['flowChart']);
        $this->assertTrue($options['sequenceDiagram']);
    }

    public function test_static_make(): void
    {
        $md = Markdown::make('# Test');

        $this->assertInstanceOf(Markdown::class, $md);
        $this->assertSame('# Test', $this->getProtectedProperty($md, 'content'));
    }

    public function test_static_make_without_content(): void
    {
        $md = Markdown::make();

        $this->assertInstanceOf(Markdown::class, $md);
        $this->assertNull($this->getProtectedProperty($md, 'content'));
    }

    public function test_view_is_set(): void
    {
        $md = new Markdown;

        $this->assertSame('admin::widgets.markdown', $this->getProtectedProperty($md, 'view'));
    }
}
