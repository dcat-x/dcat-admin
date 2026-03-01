<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Terminal;
use Dcat\Admin\Widgets\Widget;

class TerminalTest extends TestCase
{
    public function test_terminal_extends_widget(): void
    {
        $terminal = new Terminal;
        $this->assertInstanceOf(Widget::class, $terminal);
    }

    public function test_constructor_with_content(): void
    {
        $terminal = new Terminal('Hello World');

        $reflection = new \ReflectionProperty(Terminal::class, 'content');
        $reflection->setAccessible(true);
        $this->assertEquals('Hello World', $reflection->getValue($terminal));
    }

    public function test_content_method_sets_content(): void
    {
        $terminal = new Terminal;
        $result = $terminal->content('New content');

        $this->assertSame($terminal, $result);

        $reflection = new \ReflectionProperty(Terminal::class, 'content');
        $reflection->setAccessible(true);
        $this->assertEquals('New content', $reflection->getValue($terminal));
    }

    public function test_dark_returns_self(): void
    {
        $terminal = new Terminal('Test');
        $result = $terminal->dark();

        $this->assertSame($terminal, $result);
    }

    public function test_transparent_returns_self(): void
    {
        $terminal = new Terminal('Test');
        $result = $terminal->transparent();

        $this->assertSame($terminal, $result);
    }

    public function test_render_contains_pre_tag(): void
    {
        $terminal = new Terminal('Test output');
        $html = $terminal->render();

        $this->assertStringContainsString('<pre', $html);
        $this->assertStringContainsString('Test output', $html);
    }

    public function test_constructor_with_null_content(): void
    {
        $terminal = new Terminal(null);

        $reflection = new \ReflectionProperty(Terminal::class, 'content');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($terminal));
    }
}
