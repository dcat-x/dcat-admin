<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Radio;

class RadioTest extends TestCase
{
    public function test_constructor(): void
    {
        $radio = new Radio('gender', [1 => 'Male', 2 => 'Female']);
        $this->assertInstanceOf(Radio::class, $radio);
    }

    public function test_options_set(): void
    {
        $radio = new Radio;
        $radio->options([1 => 'A', 2 => 'B']);
        $vars = $radio->defaultVariables();
        $this->assertSame([1 => 'A', 2 => 'B'], $vars['options']);
    }

    public function test_check(): void
    {
        $radio = new Radio;
        $result = $radio->check(1);
        $this->assertSame($radio, $result);
        $vars = $radio->defaultVariables();
        $this->assertSame(1, $vars['checked']);
    }

    public function test_inline(): void
    {
        $radio = new Radio;
        $radio->inline();
        $vars = $radio->defaultVariables();
        $this->assertTrue($vars['inline']);
    }

    public function test_inline_false(): void
    {
        $radio = new Radio;
        $radio->inline(false);
        $vars = $radio->defaultVariables();
        $this->assertFalse($vars['inline']);
    }

    public function test_size(): void
    {
        $radio = new Radio;
        $result = $radio->size('lg');
        $this->assertSame($radio, $result);
        $vars = $radio->defaultVariables();
        $this->assertSame('lg', $vars['size']);
    }

    public function test_style(): void
    {
        $radio = new Radio;
        $radio->style('danger');
        $vars = $radio->defaultVariables();
        $this->assertSame('danger', $vars['style']);
    }

    public function test_default_style_primary(): void
    {
        $radio = new Radio;
        $vars = $radio->defaultVariables();
        $this->assertSame('primary', $vars['style']);
    }

    public function test_disable_with_values(): void
    {
        $radio = new Radio;
        $result = $radio->disable([1, 3]);
        $this->assertSame($radio, $result);
        $vars = $radio->defaultVariables();
        $this->assertSame([1, 3], $vars['disabled']);
    }

    public function test_right(): void
    {
        $radio = new Radio;
        $radio->right('20px');
        $vars = $radio->defaultVariables();
        $this->assertSame('20px', $vars['right']);
    }

    public function test_default_right(): void
    {
        $radio = new Radio;
        $vars = $radio->defaultVariables();
        $this->assertSame('16px', $vars['right']);
    }
}
