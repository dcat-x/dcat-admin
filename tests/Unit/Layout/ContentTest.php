<?php

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

        $ref = new \ReflectionProperty($content, 'title');
        $ref->setAccessible(true);
        $this->assertSame('My Title', $ref->getValue($content));
    }

    public function test_header_delegates_to_title(): void
    {
        $content = new Content;
        $result = $content->header('Header Text');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'title');
        $ref->setAccessible(true);
        $this->assertSame('Header Text', $ref->getValue($content));
    }

    public function test_description_sets_description(): void
    {
        $content = new Content;
        $result = $content->description('My Description');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'description');
        $ref->setAccessible(true);
        $this->assertSame('My Description', $ref->getValue($content));
    }

    public function test_view_sets_view(): void
    {
        $content = new Content;
        $result = $content->view('custom.view');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'view');
        $ref->setAccessible(true);
        $this->assertSame('custom.view', $ref->getValue($content));
    }

    public function test_body_delegates_to_row(): void
    {
        $content = new Content;
        $result = $content->body('some content');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'rows');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($content));
    }

    public function test_row_with_string_adds_row(): void
    {
        $content = new Content;
        $content->row('row content');

        $ref = new \ReflectionProperty($content, 'rows');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($content));
    }

    public function test_row_with_closure(): void
    {
        $content = new Content;
        $content->row(function ($row) {
            // no-op
        });

        $ref = new \ReflectionProperty($content, 'rows');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($content));
    }

    public function test_prepend_adds_row_at_beginning(): void
    {
        $content = new Content;
        $content->row('first');
        $content->prepend('before');

        $ref = new \ReflectionProperty($content, 'rows');
        $ref->setAccessible(true);
        $this->assertCount(2, $ref->getValue($content));
    }

    public function test_with_key_value(): void
    {
        $content = new Content;
        $result = $content->with('key', 'value');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'variables');
        $ref->setAccessible(true);
        $vars = $ref->getValue($content);
        $this->assertSame('value', $vars['key']);
    }

    public function test_with_array(): void
    {
        $content = new Content;
        $content->with(['a' => 1, 'b' => 2]);

        $ref = new \ReflectionProperty($content, 'variables');
        $ref->setAccessible(true);
        $vars = $ref->getValue($content);
        $this->assertSame(1, $vars['a']);
        $this->assertSame(2, $vars['b']);
    }

    public function test_with_config_key_value(): void
    {
        $content = new Content;
        $result = $content->withConfig('theme', 'dark');
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'config');
        $ref->setAccessible(true);
        $this->assertSame('dark', $ref->getValue($content)['theme']);
    }

    public function test_with_config_array(): void
    {
        $content = new Content;
        $content->withConfig(['theme' => 'blue', 'sidebar_collapsed' => true]);

        $ref = new \ReflectionProperty($content, 'config');
        $ref->setAccessible(true);
        $config = $ref->getValue($content);
        $this->assertSame('blue', $config['theme']);
        $this->assertTrue($config['sidebar_collapsed']);
    }

    public function test_full_sets_full_content_view(): void
    {
        $content = new Content;
        $result = $content->full();
        $this->assertSame($content, $result);

        $ref = new \ReflectionProperty($content, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::layouts.full-content', $ref->getValue($content));
    }

    public function test_make_static_factory(): void
    {
        $content = Content::make();
        $this->assertInstanceOf(Content::class, $content);
    }

    public function test_breadcrumb_default_empty(): void
    {
        $content = new Content;
        $ref = new \ReflectionProperty($content, 'breadcrumb');
        $ref->setAccessible(true);
        $this->assertEmpty($ref->getValue($content));
    }

    public function test_rows_default_empty(): void
    {
        $content = new Content;
        $ref = new \ReflectionProperty($content, 'rows');
        $ref->setAccessible(true);
        $this->assertEmpty($ref->getValue($content));
    }
}
