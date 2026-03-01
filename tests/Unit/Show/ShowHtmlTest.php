<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Html;
use Dcat\Admin\Tests\TestCase;

class ShowHtmlTest extends TestCase
{
    public function test_constructor_sets_html_property(): void
    {
        $html = new Html('<b>Hello</b>');

        $this->assertSame('<b>Hello</b>', $html->html);
    }

    public function test_html_is_public_property(): void
    {
        $html = new Html('test content');

        // public 属性可直接访问
        $this->assertSame('test content', $html->html);
    }

    public function test_extends_field(): void
    {
        $html = new Html('anything');

        $this->assertInstanceOf(Field::class, $html);
    }

    public function test_constructor_accepts_closure(): void
    {
        $closure = function () {
            return 'dynamic content';
        };

        $html = new Html($closure);

        $this->assertSame($closure, $html->html);
        $this->assertInstanceOf(\Closure::class, $html->html);
    }

    public function test_constructor_accepts_empty_string(): void
    {
        $html = new Html('');

        $this->assertSame('', $html->html);
    }

    public function test_html_can_be_overwritten_directly(): void
    {
        $html = new Html('<p>original</p>');
        $html->html = '<span>modified</span>';

        $this->assertSame('<span>modified</span>', $html->html);
    }
}
