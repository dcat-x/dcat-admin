<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Tooltip;

class TooltipTest extends TestCase
{
    public function test_tooltip_creation(): void
    {
        $tooltip = new Tooltip('.my-btn');
        $this->assertInstanceOf(Tooltip::class, $tooltip);
    }

    public function test_tooltip_selector(): void
    {
        $tooltip = new Tooltip;
        $result = $tooltip->selector('.target-element');

        $this->assertSame($tooltip, $result);

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('selector');
        $property->setAccessible(true);

        $this->assertSame('.target-element', $property->getValue($tooltip));
    }

    public function test_tooltip_constructor_sets_selector(): void
    {
        $tooltip = new Tooltip('#my-id');

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('selector');
        $property->setAccessible(true);

        $this->assertSame('#my-id', $property->getValue($tooltip));
    }

    public function test_tooltip_title(): void
    {
        $tooltip = new Tooltip('.btn');
        $result = $tooltip->title('Hover text');

        $this->assertSame($tooltip, $result);

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('title');
        $property->setAccessible(true);

        $this->assertSame('Hover text', $property->getValue($tooltip));
    }

    public function test_tooltip_max_width(): void
    {
        $tooltip = new Tooltip('.btn');
        $result = $tooltip->maxWidth(300);

        $this->assertSame($tooltip, $result);

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('maxWidth');
        $property->setAccessible(true);

        $this->assertSame(300, $property->getValue($tooltip));
    }

    public function test_tooltip_default_max_width(): void
    {
        $tooltip = new Tooltip;

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('maxWidth');
        $property->setAccessible(true);

        $this->assertSame(210, $property->getValue($tooltip));
    }

    public function test_tooltip_background(): void
    {
        $tooltip = new Tooltip('.btn');
        $result = $tooltip->background('#ff0000');

        $this->assertSame($tooltip, $result);

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('background');
        $property->setAccessible(true);

        $this->assertSame('#ff0000', $property->getValue($tooltip));
    }

    public function test_tooltip_placement_top(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->top();

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(1, $property->getValue($tooltip));
    }

    public function test_tooltip_placement_right(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->right();

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(2, $property->getValue($tooltip));
    }

    public function test_tooltip_placement_bottom(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->bottom();

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(3, $property->getValue($tooltip));
    }

    public function test_tooltip_placement_left(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->left();

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(4, $property->getValue($tooltip));
    }

    public function test_tooltip_placement_unknown_defaults_to_top(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->placement('unknown');

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(1, $property->getValue($tooltip));
    }

    public function test_tooltip_default_placement(): void
    {
        $tooltip = new Tooltip;

        $reflection = new \ReflectionClass($tooltip);
        $property = $reflection->getProperty('placement');
        $property->setAccessible(true);

        $this->assertSame(1, $property->getValue($tooltip));
    }

    public function test_tooltip_chaining(): void
    {
        $tooltip = (new Tooltip('.btn'))
            ->title('My tip')
            ->maxWidth(400)
            ->background('#333')
            ->bottom();

        $reflection = new \ReflectionClass($tooltip);

        $titleProp = $reflection->getProperty('title');
        $titleProp->setAccessible(true);
        $this->assertSame('My tip', $titleProp->getValue($tooltip));

        $maxWidthProp = $reflection->getProperty('maxWidth');
        $maxWidthProp->setAccessible(true);
        $this->assertSame(400, $maxWidthProp->getValue($tooltip));

        $bgProp = $reflection->getProperty('background');
        $bgProp->setAccessible(true);
        $this->assertSame('#333', $bgProp->getValue($tooltip));

        $placementProp = $reflection->getProperty('placement');
        $placementProp->setAccessible(true);
        $this->assertSame(3, $placementProp->getValue($tooltip));
    }

    public function test_tooltip_render_sets_built_flag(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->title('test');

        $reflection = new \ReflectionClass($tooltip);
        $builtProp = $reflection->getProperty('built');
        $builtProp->setAccessible(true);

        $this->assertNull($builtProp->getValue($tooltip));

        $tooltip->render();

        $this->assertTrue($builtProp->getValue($tooltip));
    }

    public function test_tooltip_render_only_once(): void
    {
        $tooltip = new Tooltip('.btn');
        $tooltip->title('test');

        $tooltip->render();
        // second render should return early
        $result = $tooltip->render();

        $this->assertNull($result);
    }
}
