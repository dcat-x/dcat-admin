<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column\Help;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;

class HelpTest extends TestCase
{
    public function test_implements_renderable(): void
    {
        $help = new Help('Test message');
        $this->assertInstanceOf(Renderable::class, $help);
    }

    public function test_render_contains_message(): void
    {
        $help = new Help('Help text here');
        $html = $help->render();

        $this->assertStringContainsString('data-title="Help text here"', $html);
    }

    public function test_render_contains_help_icon_class(): void
    {
        $help = new Help('Test');
        $html = $help->render();

        $this->assertStringContainsString('icon-help-circle', $html);
    }

    public function test_render_contains_grid_column_help_class(): void
    {
        $help = new Help('Test');
        $html = $help->render();

        $this->assertStringContainsString('grid-column-help-', $html);
    }

    public function test_default_message_empty_string(): void
    {
        $help = new Help;
        $html = $help->render();

        $this->assertStringContainsString('data-title=""', $html);
    }

    public function test_constructor_stores_properties(): void
    {
        $help = new Help('msg', 'green', 'top');

        $messageRef = new \ReflectionProperty(Help::class, 'message');
        $messageRef->setAccessible(true);
        $this->assertSame('msg', $messageRef->getValue($help));

        $styleRef = new \ReflectionProperty(Help::class, 'style');
        $styleRef->setAccessible(true);
        $this->assertSame('green', $styleRef->getValue($help));

        $placementRef = new \ReflectionProperty(Help::class, 'placement');
        $placementRef->setAccessible(true);
        $this->assertSame('top', $placementRef->getValue($help));
    }
}
