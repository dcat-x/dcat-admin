<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\GridAction;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Tests\TestCase;

class AbstractToolTest extends TestCase
{
    public function test_extends_grid_action(): void
    {
        $parents = class_parents(AbstractTool::class);

        $this->assertContains(GridAction::class, $parents);
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

    public function test_html_method_signature(): void
    {
        $ref = new \ReflectionMethod(AbstractTool::class, 'html');

        $this->assertSame(0, $ref->getNumberOfParameters());
    }

    public function test_html_method_is_protected(): void
    {
        $ref = new \ReflectionMethod(AbstractTool::class, 'html');
        $this->assertTrue($ref->isProtected());
    }
}
