<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Dump;

class DumpTest extends TestCase
{
    public function test_constructor_with_string(): void
    {
        $dump = new Dump('Hello World');
        $this->assertInstanceOf(Dump::class, $dump);
    }

    public function test_content_string(): void
    {
        $dump = new Dump('Test Content');
        $html = $dump->render();
        $this->assertStringContainsString('Test Content', $html);
    }

    public function test_content_array(): void
    {
        $dump = new Dump(['key' => 'value']);
        $html = $dump->render();
        $this->assertStringContainsString('key', $html);
        $this->assertStringContainsString('value', $html);
    }

    public function test_content_json_string(): void
    {
        $dump = new Dump('{"name":"test"}');
        $html = $dump->render();
        $this->assertStringContainsString('name', $html);
    }

    public function test_padding_default(): void
    {
        $dump = new Dump('content');
        $html = $dump->render();
        $this->assertStringContainsString('padding:10px', $html);
    }

    public function test_custom_padding(): void
    {
        $dump = new Dump('content', '20px');
        $html = $dump->render();
        $this->assertStringContainsString('padding:20px', $html);
    }

    public function test_max_width(): void
    {
        $dump = new Dump('content');
        $result = $dump->maxWidth('500px');
        $this->assertSame($dump, $result);
        $html = $dump->render();
        $this->assertStringContainsString('max-width:500px', $html);
    }

    public function test_render_contains_pre_tag(): void
    {
        $dump = new Dump('content');
        $html = $dump->render();
        $this->assertStringContainsString('<pre', $html);
        $this->assertStringContainsString('class="dump"', $html);
    }

    public function test_content_method_fluent(): void
    {
        $dump = new Dump('initial');
        $result = $dump->content('updated');
        $this->assertSame($dump, $result);
        $html = $dump->render();
        $this->assertStringContainsString('updated', $html);
    }
}
