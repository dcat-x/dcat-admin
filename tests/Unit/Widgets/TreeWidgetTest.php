<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Tree;

class TreeWidgetTest extends TestCase
{
    public function test_tree_creation(): void
    {
        $tree = new Tree;
        $this->assertInstanceOf(Tree::class, $tree);
    }

    public function test_tree_creation_with_nodes(): void
    {
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
        ];
        $tree = new Tree($nodes);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('nodes');
        $property->setAccessible(true);

        $this->assertCount(1, $property->getValue($tree));
    }

    public function test_tree_has_auto_generated_id(): void
    {
        $tree = new Tree;

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);

        $id = $property->getValue($tree);
        $this->assertStringStartsWith('widget-tree-', $id);
    }

    public function test_tree_nodes_method(): void
    {
        $tree = new Tree;
        $nodes = [
            ['id' => 1, 'name' => 'Node 1', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Node 2', 'parent_id' => 1],
        ];

        $result = $tree->nodes($nodes);

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('nodes');
        $property->setAccessible(true);

        $this->assertCount(2, $property->getValue($tree));
    }

    public function test_tree_check_all(): void
    {
        $tree = new Tree;
        $result = $tree->checkAll();

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('checkAll');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($tree));
    }

    public function test_tree_check_with_values(): void
    {
        $tree = new Tree;
        $result = $tree->check([1, 3, 5]);

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('value');
        $property->setAccessible(true);

        $this->assertSame([1, 3, 5], $property->getValue($tree));
    }

    public function test_tree_set_id_column(): void
    {
        $tree = new Tree;
        $result = $tree->setIdColumn('uid');

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('columnNames');
        $property->setAccessible(true);

        $columns = $property->getValue($tree);
        $this->assertSame('uid', $columns['id']);
    }

    public function test_tree_set_title_column(): void
    {
        $tree = new Tree;
        $result = $tree->setTitleColumn('label');

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('columnNames');
        $property->setAccessible(true);

        $columns = $property->getValue($tree);
        $this->assertSame('label', $columns['text']);
    }

    public function test_tree_set_parent_column(): void
    {
        $tree = new Tree;
        $result = $tree->setParentColumn('pid');

        $this->assertSame($tree, $result);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('columnNames');
        $property->setAccessible(true);

        $columns = $property->getValue($tree);
        $this->assertSame('pid', $columns['parent']);
    }

    public function test_tree_default_column_names(): void
    {
        $tree = new Tree;

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('columnNames');
        $property->setAccessible(true);

        $columns = $property->getValue($tree);
        $this->assertSame('id', $columns['id']);
        $this->assertSame('name', $columns['text']);
        $this->assertSame('parent_id', $columns['parent']);
    }

    public function test_tree_default_options(): void
    {
        $tree = new Tree;

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($tree);
        $this->assertContains('checkbox', $options['plugins']);
        $this->assertContains('types', $options['plugins']);
        $this->assertTrue($options['core']['check_callback']);
    }

    public function test_tree_format_nodes_with_check_all(): void
    {
        $nodes = [
            ['id' => 1, 'name' => 'Node 1', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Node 2', 'parent_id' => 1],
        ];

        $tree = new Tree($nodes);
        $tree->checkAll();

        // Call formatNodes via reflection
        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);
        $formatted = $nodesProperty->getValue($tree);

        $this->assertCount(2, $formatted);
        foreach ($formatted as $node) {
            $this->assertTrue($node['state']['selected']);
            $this->assertTrue($node['state']['disabled']);
        }
    }

    public function test_tree_format_nodes_with_selected_values(): void
    {
        $nodes = [
            ['id' => 1, 'name' => 'Node 1', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Node 2', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Node 3', 'parent_id' => 0],
        ];

        $tree = new Tree($nodes);
        $tree->check([1, 3]);

        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);
        $formatted = $nodesProperty->getValue($tree);

        // Node 1 (id=1) should be selected
        $this->assertTrue($formatted[0]['state']['selected']);
        // Node 2 (id=2) should not be selected
        $this->assertArrayNotHasKey('selected', $formatted[1]['state']);
        // Node 3 (id=3) should be selected
        $this->assertTrue($formatted[2]['state']['selected']);
    }

    public function test_tree_format_nodes_sets_parent_to_hash_for_root(): void
    {
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
        ];

        $tree = new Tree($nodes);

        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);
        $formatted = $nodesProperty->getValue($tree);

        // parent_id = 0 (empty) becomes '#'
        $this->assertSame('#', $formatted[0]['parent']);
        $this->assertSame(1, $formatted[1]['parent']);
    }

    public function test_tree_format_nodes_skips_empty_id(): void
    {
        $nodes = [
            ['id' => null, 'name' => 'No ID', 'parent_id' => 0],
            ['id' => 1, 'name' => 'Valid', 'parent_id' => 0],
        ];

        $tree = new Tree($nodes);

        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);
        $formatted = $nodesProperty->getValue($tree);

        $this->assertCount(1, $formatted);
        $this->assertSame(1, $formatted[0]['id']);
    }

    public function test_tree_format_nodes_with_empty_array(): void
    {
        $tree = new Tree([]);

        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);

        $this->assertEmpty($nodesProperty->getValue($tree));
    }

    public function test_tree_static_make(): void
    {
        $tree = Tree::make();
        $this->assertInstanceOf(Tree::class, $tree);
    }

    public function test_tree_nodes_with_arrayable(): void
    {
        $collection = collect([
            ['id' => 1, 'name' => 'Item 1', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Item 2', 'parent_id' => 0],
        ]);

        $tree = new Tree;
        $tree->nodes($collection);

        $reflection = new \ReflectionClass($tree);
        $property = $reflection->getProperty('nodes');
        $property->setAccessible(true);

        $this->assertCount(2, $property->getValue($tree));
    }

    public function test_tree_custom_columns_in_format(): void
    {
        $nodes = [
            ['uid' => 10, 'label' => 'Custom Node', 'pid' => 0],
        ];

        $tree = new Tree($nodes);
        $tree->setIdColumn('uid');
        $tree->setTitleColumn('label');
        $tree->setParentColumn('pid');

        $reflection = new \ReflectionClass($tree);
        $method = $reflection->getMethod('formatNodes');
        $method->setAccessible(true);
        $method->invoke($tree);

        $nodesProperty = $reflection->getProperty('nodes');
        $nodesProperty->setAccessible(true);
        $formatted = $nodesProperty->getValue($tree);

        $this->assertCount(1, $formatted);
        $this->assertSame(10, $formatted[0]['id']);
        $this->assertSame('Custom Node', $formatted[0]['text']);
        $this->assertSame('#', $formatted[0]['parent']);
    }
}
