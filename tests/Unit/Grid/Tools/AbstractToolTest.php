<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\GridAction;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Tests\TestCase;

class AbstractToolTest extends TestCase
{
    public function test_extends_grid_action(): void
    {
        $this->assertTrue(is_subclass_of(AbstractTool::class, GridAction::class));
    }

    public function test_class_is_abstract(): void
    {
        $ref = new \ReflectionClass(AbstractTool::class);
        $this->assertTrue($ref->isAbstract());
    }

    public function test_has_style_property(): void
    {
        $ref = new \ReflectionProperty(AbstractTool::class, 'style');
        $ref->setAccessible(true);
        $this->assertTrue($ref->isProtected());
    }

    public function test_default_style_value(): void
    {
        $ref = new \ReflectionProperty(AbstractTool::class, 'style');
        $ref->setAccessible(true);
        $this->assertSame('btn btn-white waves-effect', $ref->getDefaultValue());
    }

    public function test_has_html_method(): void
    {
        $this->assertTrue(method_exists(AbstractTool::class, 'html'));
    }

    public function test_html_method_is_protected(): void
    {
        $ref = new \ReflectionMethod(AbstractTool::class, 'html');
        $this->assertTrue($ref->isProtected());
    }
}
