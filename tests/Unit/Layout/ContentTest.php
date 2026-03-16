<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;

class ContentTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function getPropertyValue(object $object, string $property): mixed
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_implements_renderable(): void
    {
        $interfaces = class_implements(Content::class);

        $this->assertContains(Renderable::class, $interfaces);
    }

    public function test_constructor_with_closure(): void
    {
        $called = false;
        $content = new Content(function ($c) use (&$called) {
            $called = true;
            $this->assertInstanceOf(Content::class, $c);
        });
        $this->assertTrue($called);
    }

    public function test_constructor_without_closure(): void
    {
        $content = new Content;
        $this->assertInstanceOf(Content::class, $content);
    }

    public function test_title_sets_title(): void
    {
        $content = new Content;
        $result = $content->title('My Title');
        $this->assertSame($content, $result);
        $this->assertSame('My Title', $this->getPropertyValue($content, 'title'));
    }

    public function test_header_delegates_to_title(): void
    {
        $content = new Content;
        $result = $content->header('Header Text');
        $this->assertSame($content, $result);
        $this->assertSame('Header Text', $this->getPropertyValue($content, 'title'));
    }

    public function test_description_sets_description(): void
    {
        $content = new Content;
        $result = $content->description('My Description');
        $this->assertSame($content, $result);
        $this->assertSame('My Description', $this->getPropertyValue($content, 'description'));
    }

    public function test_view_sets_view(): void
    {
        $content = new Content;
        $result = $content->view('custom.view');
        $this->assertSame($content, $result);
        $this->assertSame('custom.view', $this->getPropertyValue($content, 'view'));
    }

    public function test_body_delegates_to_row(): void
    {
        $content = new Content;
        $result = $content->body('some content');
        $this->assertSame($content, $result);
        $this->assertCount(1, $this->getPropertyValue($content, 'rows'));
    }

    public function test_row_with_string_adds_row(): void
    {
        $content = new Content;
        $content->row('row content');
        $this->assertCount(1, $this->getPropertyValue($content, 'rows'));
    }

    public function test_row_with_closure(): void
    {
        $content = new Content;
        $content->row(function ($row) {
            // no-op
        });
        $this->assertCount(1, $this->getPropertyValue($content, 'rows'));
    }

    public function test_prepend_adds_row_at_beginning(): void
    {
        $content = new Content;
        $content->row('first');
        $content->prepend('before');
        $this->assertCount(2, $this->getPropertyValue($content, 'rows'));
    }

    public function test_with_key_value(): void
    {
        $content = new Content;
        $result = $content->with('key', 'value');
        $this->assertSame($content, $result);
        $vars = $this->getPropertyValue($content, 'variables');
        $this->assertSame('value', $vars['key']);
    }

    public function test_with_array(): void
    {
        $content = new Content;
        $content->with(['a' => 1, 'b' => 2]);
        $vars = $this->getPropertyValue($content, 'variables');
        $this->assertSame(1, $vars['a']);
        $this->assertSame(2, $vars['b']);
    }

    public function test_with_config_key_value(): void
    {
        $content = new Content;
        $result = $content->withConfig('theme', 'dark');
        $this->assertSame($content, $result);
        $this->assertSame('dark', $this->getPropertyValue($content, 'config')['theme']);
    }

    public function test_with_config_array(): void
    {
        $content = new Content;
        $content->withConfig(['theme' => 'blue', 'sidebar_collapsed' => true]);
        $config = $this->getPropertyValue($content, 'config');
        $this->assertSame('blue', $config['theme']);
        $this->assertTrue($config['sidebar_collapsed']);
    }

    public function test_full_sets_full_content_view(): void
    {
        $content = new Content;
        $result = $content->full();
        $this->assertSame($content, $result);
        $this->assertSame('admin::layouts.full-content', $this->getPropertyValue($content, 'view'));
    }

    public function test_make_static_factory(): void
    {
        $content = Content::make();
        $this->assertInstanceOf(Content::class, $content);
    }

    public function test_breadcrumb_default_empty(): void
    {
        $content = new Content;
        $this->assertEmpty($this->getPropertyValue($content, 'breadcrumb'));
    }

    public function test_rows_default_empty(): void
    {
        $content = new Content;
        $this->assertEmpty($this->getPropertyValue($content, 'rows'));
    }
}
