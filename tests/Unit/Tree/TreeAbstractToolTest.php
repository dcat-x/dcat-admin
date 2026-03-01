<?php

namespace Dcat\Admin\Tests\Unit\Tree;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
use Dcat\Admin\Tree\AbstractTool;
use Mockery;

class TreeAbstractToolTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createConcreteTool(): AbstractTool
    {
        return new class extends AbstractTool
        {
            public function html()
            {
                return '<button>Tree Tool</button>';
            }
        };
    }

    public function test_tool_extends_action(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertInstanceOf(Action::class, $tool);
    }

    public function test_set_parent(): void
    {
        $tool = $this->createConcreteTool();
        $tree = Mockery::mock(Tree::class);

        $tool->setParent($tree);

        $reflection = new \ReflectionProperty(AbstractTool::class, 'parent');
        $reflection->setAccessible(true);
        $this->assertSame($tree, $reflection->getValue($tool));
    }

    public function test_default_style(): void
    {
        $tool = $this->createConcreteTool();

        $reflection = new \ReflectionProperty(AbstractTool::class, 'style');
        $reflection->setAccessible(true);
        $this->assertEquals('btn btn-sm btn-primary', $reflection->getValue($tool));
    }

    public function test_parent_initially_null(): void
    {
        $tool = $this->createConcreteTool();

        $reflection = new \ReflectionProperty(AbstractTool::class, 'parent');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($tool));
    }
}
