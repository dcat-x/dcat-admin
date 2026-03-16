<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Show\AbstractTool;
use Dcat\Admin\Tests\TestCase;

class ShowAbstractToolTest extends TestCase
{
    protected function createConcreteTool(): AbstractTool
    {
        return new class extends AbstractTool
        {
            public function html()
            {
                return '<button>Test</button>';
            }
        };
    }

    public function test_tool_extends_action(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertInstanceOf(Action::class, $tool);
    }

    public function test_tool_is_abstract(): void
    {
        $reflection = new \ReflectionClass(AbstractTool::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function test_get_key_returns_null_without_parent(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertNull($tool->getKey());
    }

    public function test_get_key_returns_primary_key_when_set(): void
    {
        $tool = $this->createConcreteTool();
        $tool->setKey(99);

        $this->assertSame(99, $tool->getKey());
    }

    public function test_default_style(): void
    {
        $reflection = new \ReflectionProperty(AbstractTool::class, 'style');
        $reflection->setAccessible(true);

        $tool = $this->createConcreteTool();
        $this->assertSame('btn btn-sm btn-primary', $reflection->getValue($tool));
    }

    public function test_parent_initially_null(): void
    {
        $tool = $this->createConcreteTool();

        $reflection = new \ReflectionProperty(AbstractTool::class, 'parent');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($tool));
    }

    public function test_set_parent_method_signature(): void
    {
        $method = new \ReflectionMethod(AbstractTool::class, 'setParent');

        $this->assertSame(1, $method->getNumberOfParameters());
    }
}
