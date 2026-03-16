<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Dropdown;

class DropdownTest extends TestCase
{
    public function test_constructor_with_options(): void
    {
        $dropdown = new Dropdown(['Option 1', 'Option 2']);
        $this->assertInstanceOf(Dropdown::class, $dropdown);
    }

    public function test_button_sets_text(): void
    {
        $dropdown = new Dropdown;
        $result = $dropdown->button('Click Me');
        $this->assertSame($dropdown, $result);
        $ref = new \ReflectionProperty($dropdown, 'button');
        $ref->setAccessible(true);
        $this->assertSame('Click Me', $ref->getValue($dropdown)['text']);
    }

    public function test_button_class(): void
    {
        $dropdown = new Dropdown;
        $result = $dropdown->buttonClass('btn-primary');
        $this->assertSame($dropdown, $result);
        $ref = new \ReflectionProperty($dropdown, 'button');
        $ref->setAccessible(true);
        $this->assertSame('btn-primary', $ref->getValue($dropdown)['class']);
    }

    public function test_button_style(): void
    {
        $dropdown = new Dropdown;
        $dropdown->buttonStyle('color:red');
        $ref = new \ReflectionProperty($dropdown, 'button');
        $ref->setAccessible(true);
        $this->assertSame('color:red', $ref->getValue($dropdown)['style']);
    }

    public function test_direction_default_down(): void
    {
        $dropdown = new Dropdown;
        $ref = new \ReflectionProperty($dropdown, 'direction');
        $ref->setAccessible(true);
        $this->assertSame('down', $ref->getValue($dropdown));
    }

    public function test_up(): void
    {
        $dropdown = new Dropdown;
        $result = $dropdown->up();
        $this->assertSame($dropdown, $result);
        $ref = new \ReflectionProperty($dropdown, 'direction');
        $ref->setAccessible(true);
        $this->assertSame('up', $ref->getValue($dropdown));
    }

    public function test_down(): void
    {
        $dropdown = new Dropdown;
        $dropdown->up();
        $dropdown->down();
        $ref = new \ReflectionProperty($dropdown, 'direction');
        $ref->setAccessible(true);
        $this->assertSame('down', $ref->getValue($dropdown));
    }

    public function test_divider(): void
    {
        $dropdown = new Dropdown;
        $result = $dropdown->divider();
        $this->assertSame($dropdown, $result);
        $ref = new \ReflectionProperty($dropdown, 'divider');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($dropdown));
    }

    public function test_click(): void
    {
        $dropdown = new Dropdown;
        $result = $dropdown->click('Select');
        $this->assertSame($dropdown, $result);
        $ref = new \ReflectionProperty($dropdown, 'click');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($dropdown));
        $this->assertNotNull($dropdown->getButtonId());
    }

    public function test_map_sets_builder(): void
    {
        $dropdown = new Dropdown;
        $fn = function ($v, $k) {
            return strtoupper($v);
        };
        $result = $dropdown->map($fn);
        $this->assertSame($dropdown, $result);
    }

    public function test_options_with_title(): void
    {
        $dropdown = new Dropdown;
        $dropdown->options(['A', 'B'], 'Group');
        $ref = new \ReflectionProperty($dropdown, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dropdown);
        $this->assertCount(1, $options);
        $this->assertSame('Group', $options[0][0]);
    }
}
