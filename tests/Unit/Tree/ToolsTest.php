<?php

namespace Dcat\Admin\Tests\Unit\Tree;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
use Dcat\Admin\Tree\Tools;
use Illuminate\Support\Collection;
use ReflectionProperty;

class ToolsTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_constructor_initializes_tools_collection(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $collection = $this->getProtectedProperty($tools, 'tools');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertTrue($collection->isEmpty());
    }

    public function test_constructor_stores_tree_reference(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $storedTree = $this->getProtectedProperty($tools, 'tree');
        $this->assertSame($tree, $storedTree);
    }

    public function test_add_tool_returns_self(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $result = $tools->add('<button>Refresh</button>');

        $this->assertSame($tools, $result);
    }

    public function test_add_multiple_tools(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $tools->add('<button>Tool 1</button>');
        $tools->add('<button>Tool 2</button>');
        $tools->add('<button>Tool 3</button>');

        $collection = $this->getProtectedProperty($tools, 'tools');

        $this->assertCount(3, $collection);
    }

    public function test_render_returns_string(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $tools->add('Tool1');
        $tools->add('Tool2');

        $rendered = $tools->render();

        $this->assertIsString($rendered);
        $this->assertStringContainsString('Tool1', $rendered);
        $this->assertStringContainsString('Tool2', $rendered);
    }

    public function test_render_empty_tools(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $rendered = $tools->render();

        $this->assertSame('', $rendered);
    }

    public function test_render_joins_tools_with_space(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $tools->add('A');
        $tools->add('B');

        $rendered = $tools->render();

        $this->assertSame('A B', $rendered);
    }

    public function test_add_chaining(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $tools->add('A')->add('B')->add('C');

        $collection = $this->getProtectedProperty($tools, 'tools');

        $this->assertCount(3, $collection);
    }

    public function test_render_single_tool_no_extra_space(): void
    {
        $tree = \Mockery::mock(Tree::class);
        $tools = new Tools($tree);

        $tools->add('OnlyTool');

        $rendered = $tools->render();

        $this->assertSame('OnlyTool', $rendered);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
