<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\ModelTree;
use Mockery;

class FakeModelTreeNode
{
    use ModelTree;

    public function getKeyName()
    {
        return 'id';
    }

    public function allNodes()
    {
        return collect();
    }

    public function exposeBuildSelectOptions(array $nodes, int $parentId = 0): array
    {
        return $this->buildSelectOptions($nodes, $parentId);
    }

    public function exposeCallQueryCallbacks($model)
    {
        return $this->callQueryCallbacks($model);
    }
}

class CustomizedModelTreeNode extends FakeModelTreeNode
{
    protected string $parentColumn = 'pid';

    protected string $titleColumn = 'name';

    protected string $orderColumn = 'sort';

    protected string $depthColumn = 'depth';

    protected string $defaultParentId = 'root';
}

class ModelTreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_default_column_names_are_returned_when_properties_not_defined(): void
    {
        $node = new FakeModelTreeNode;

        $this->assertSame('parent_id', $node->getParentColumn());
        $this->assertSame('title', $node->getTitleColumn());
        $this->assertSame('order', $node->getOrderColumn());
        $this->assertSame('', $node->getDepthColumn());
        $this->assertSame('0', $node->getDefaultParentId());
    }

    public function test_custom_column_names_are_respected(): void
    {
        $node = new CustomizedModelTreeNode;

        $this->assertSame('pid', $node->getParentColumn());
        $this->assertSame('name', $node->getTitleColumn());
        $this->assertSame('sort', $node->getOrderColumn());
        $this->assertSame('depth', $node->getDepthColumn());
        $this->assertSame('root', $node->getDefaultParentId());
    }

    public function test_with_query_registers_and_executes_callbacks_in_order(): void
    {
        $node = new FakeModelTreeNode;
        $trace = [];

        $model = new class($trace)
        {
            public array $trace;

            public function __construct(array &$trace)
            {
                $this->trace = &$trace;
            }
        };

        $node
            ->withQuery(function ($m) {
                $m->trace[] = 'first';

                return $m;
            })
            ->withQuery(function ($m) {
                $m->trace[] = 'second';

                return $m;
            });

        $result = $node->exposeCallQueryCallbacks($model);

        $this->assertSame($model, $result);
        $this->assertSame(['first', 'second'], $model->trace);
    }

    public function test_to_tree_builds_nested_structure_from_flat_nodes(): void
    {
        $node = new FakeModelTreeNode;
        $nodes = [
            ['id' => 1, 'parent_id' => 0, 'title' => 'Root'],
            ['id' => 2, 'parent_id' => 1, 'title' => 'Child A'],
            ['id' => 3, 'parent_id' => 1, 'title' => 'Child B'],
        ];

        $tree = $node->toTree($nodes);

        $this->assertCount(1, $tree);
        $this->assertSame(1, $tree[0]['id']);
        $this->assertCount(2, $tree[0]['children']);
    }

    public function test_build_select_options_generates_hierarchical_labels(): void
    {
        $node = new FakeModelTreeNode;
        $nodes = [
            ['id' => 1, 'parent_id' => 0, 'title' => 'Root'],
            ['id' => 2, 'parent_id' => 1, 'title' => 'Child'],
        ];

        $options = $node->exposeBuildSelectOptions($nodes);

        $this->assertArrayHasKey(1, $options);
        $this->assertArrayHasKey(2, $options);
        $this->assertStringContainsString('Root', $options[1]);
        $this->assertStringContainsString('Child', $options[2]);
    }
}
