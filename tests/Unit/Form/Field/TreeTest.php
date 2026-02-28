<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Tree;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Arrayable;
use Mockery;

/**
 * 测试 Tree 表单字段的关键方法。
 */
class TreeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createTree(string $column = 'permissions', string $label = 'Permissions'): Tree
    {
        return new Tree($column, [$label]);
    }

    /**
     * 通过 Reflection 读取 protected/private 属性值。
     */
    protected function getProperty(Tree $tree, string $property): mixed
    {
        $ref = new \ReflectionProperty($tree, $property);
        $ref->setAccessible(true);

        return $ref->getValue($tree);
    }

    /**
     * 通过 Reflection 设置 protected/private 属性值。
     */
    protected function setProperty(Tree $tree, string $property, mixed $value): void
    {
        $ref = new \ReflectionProperty($tree, $property);
        $ref->setAccessible(true);
        $ref->setValue($tree, $value);
    }

    /**
     * 通过 Reflection 调用 protected 方法。
     */
    protected function invokeMethod(Tree $tree, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($tree, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($tree, $args);
    }

    // ---------------------------------------------------------------
    // 1. 构造函数 — 默认选项
    // ---------------------------------------------------------------

    public function test_constructor_sets_default_plugins(): void
    {
        $tree = $this->createTree();
        $options = $this->getProperty($tree, 'options');

        $this->assertSame(['checkbox', 'types'], $options['plugins']);
    }

    public function test_constructor_sets_default_core(): void
    {
        $tree = $this->createTree();
        $options = $this->getProperty($tree, 'options');

        $expected = [
            'check_callback' => true,
            'themes' => [
                'name' => 'proton',
                'responsive' => true,
            ],
        ];
        $this->assertSame($expected, $options['core']);
    }

    public function test_constructor_sets_default_checkbox(): void
    {
        $tree = $this->createTree();
        $options = $this->getProperty($tree, 'options');

        $this->assertSame(['keep_selected_style' => false, 'three_state' => true], $options['checkbox']);
    }

    public function test_constructor_sets_default_types(): void
    {
        $tree = $this->createTree();
        $options = $this->getProperty($tree, 'options');

        $this->assertSame(['default' => ['icon' => false]], $options['types']);
    }

    public function test_constructor_sets_default_property_values(): void
    {
        $tree = $this->createTree();

        $this->assertSame([], $this->getProperty($tree, 'nodes'));
        $this->assertSame([], $this->getProperty($tree, 'parents'));
        $this->assertTrue($this->getProperty($tree, 'expand'));
        $this->assertTrue($this->getProperty($tree, 'exceptParents'));
        $this->assertFalse($this->getProperty($tree, 'readOnly'));
        $this->assertSame(0, $this->getProperty($tree, 'rootParentId'));
        $this->assertSame(
            ['id' => 'id', 'text' => 'name', 'parent' => 'parent_id'],
            $this->getProperty($tree, 'columnNames')
        );
    }

    // ---------------------------------------------------------------
    // 2. nodes() — 设置节点数据
    // ---------------------------------------------------------------

    public function test_nodes_with_array(): void
    {
        $tree = $this->createTree();
        $data = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
        ];

        $result = $tree->nodes($data);

        $this->assertSame($tree, $result, 'nodes() should return $this for chaining');
        $this->assertSame($data, $this->getProperty($tree, 'nodes'));
    }

    public function test_nodes_with_arrayable(): void
    {
        $tree = $this->createTree();
        $data = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
        ];

        $arrayable = Mockery::mock(Arrayable::class);
        $arrayable->shouldReceive('toArray')->once()->andReturn($data);

        $tree->nodes($arrayable);

        $this->assertSame($data, $this->getProperty($tree, 'nodes'));
    }

    public function test_nodes_with_empty_array(): void
    {
        $tree = $this->createTree();
        $tree->nodes([]);

        $this->assertSame([], $this->getProperty($tree, 'nodes'));
    }

    // ---------------------------------------------------------------
    // 3. treeState() — 设置三态复选框
    // ---------------------------------------------------------------

    public function test_tree_state_enables_three_state_and_except_parents(): void
    {
        $tree = $this->createTree();

        $result = $tree->treeState(true);

        $this->assertSame($tree, $result);
        $options = $this->getProperty($tree, 'options');
        $this->assertTrue($options['checkbox']['three_state']);
        $this->assertTrue($this->getProperty($tree, 'exceptParents'));
    }

    public function test_tree_state_disables_three_state_and_except_parents(): void
    {
        $tree = $this->createTree();

        $tree->treeState(false);

        $options = $this->getProperty($tree, 'options');
        $this->assertFalse($options['checkbox']['three_state']);
        $this->assertFalse($this->getProperty($tree, 'exceptParents'));
    }

    public function test_tree_state_default_parameter_is_true(): void
    {
        $tree = $this->createTree();
        // 先设为 false 再调用无参数版本
        $tree->treeState(false);
        $tree->treeState();

        $options = $this->getProperty($tree, 'options');
        $this->assertTrue($options['checkbox']['three_state']);
        $this->assertTrue($this->getProperty($tree, 'exceptParents'));
    }

    // ---------------------------------------------------------------
    // 4. exceptParentNode() — 过滤父节点
    // ---------------------------------------------------------------

    public function test_except_parent_node_enables(): void
    {
        $tree = $this->createTree();
        // 先设为 false
        $this->setProperty($tree, 'exceptParents', false);

        $result = $tree->exceptParentNode(true);

        $this->assertSame($tree, $result);
        $this->assertTrue($this->getProperty($tree, 'exceptParents'));
    }

    public function test_except_parent_node_disables(): void
    {
        $tree = $this->createTree();

        $tree->exceptParentNode(false);

        $this->assertFalse($this->getProperty($tree, 'exceptParents'));
    }

    public function test_except_parent_node_default_is_true(): void
    {
        $tree = $this->createTree();
        $this->setProperty($tree, 'exceptParents', false);

        $tree->exceptParentNode();

        $this->assertTrue($this->getProperty($tree, 'exceptParents'));
    }

    // ---------------------------------------------------------------
    // 5. rootParentId() — 设置根父 ID
    // ---------------------------------------------------------------

    public function test_root_parent_id(): void
    {
        $tree = $this->createTree();

        $result = $tree->rootParentId(99);

        $this->assertSame($tree, $result);
        $this->assertSame(99, $this->getProperty($tree, 'rootParentId'));
    }

    public function test_root_parent_id_accepts_string(): void
    {
        $tree = $this->createTree();

        $tree->rootParentId('root');

        $this->assertSame('root', $this->getProperty($tree, 'rootParentId'));
    }

    // ---------------------------------------------------------------
    // 6. readOnly() — 只读模式
    // ---------------------------------------------------------------

    public function test_read_only_sets_true(): void
    {
        $tree = $this->createTree();

        $result = $tree->readOnly();

        $this->assertSame($tree, $result);
        $this->assertTrue($this->getProperty($tree, 'readOnly'));
    }

    public function test_read_only_with_explicit_true(): void
    {
        $tree = $this->createTree();

        $tree->readOnly(true);

        $this->assertTrue($this->getProperty($tree, 'readOnly'));
    }

    // ---------------------------------------------------------------
    // 7. setIdColumn() / setTitleColumn() / setParentColumn()
    // ---------------------------------------------------------------

    public function test_set_id_column(): void
    {
        $tree = $this->createTree();

        $result = $tree->setIdColumn('node_id');

        $this->assertSame($tree, $result);
        $columns = $this->getProperty($tree, 'columnNames');
        $this->assertSame('node_id', $columns['id']);
        // 其他列不变
        $this->assertSame('name', $columns['text']);
        $this->assertSame('parent_id', $columns['parent']);
    }

    public function test_set_title_column(): void
    {
        $tree = $this->createTree();

        $result = $tree->setTitleColumn('label');

        $this->assertSame($tree, $result);
        $columns = $this->getProperty($tree, 'columnNames');
        $this->assertSame('label', $columns['text']);
        // 其他列不变
        $this->assertSame('id', $columns['id']);
        $this->assertSame('parent_id', $columns['parent']);
    }

    public function test_set_parent_column(): void
    {
        $tree = $this->createTree();

        $result = $tree->setParentColumn('pid');

        $this->assertSame($tree, $result);
        $columns = $this->getProperty($tree, 'columnNames');
        $this->assertSame('pid', $columns['parent']);
        // 其他列不变
        $this->assertSame('id', $columns['id']);
        $this->assertSame('name', $columns['text']);
    }

    public function test_set_all_columns(): void
    {
        $tree = $this->createTree();

        $tree->setIdColumn('nid')
            ->setTitleColumn('title')
            ->setParentColumn('pid');

        $columns = $this->getProperty($tree, 'columnNames');
        $this->assertSame(['id' => 'nid', 'text' => 'title', 'parent' => 'pid'], $columns);
    }

    // ---------------------------------------------------------------
    // 8. type() — 合并类型配置
    // ---------------------------------------------------------------

    public function test_type_merges_config(): void
    {
        $tree = $this->createTree();

        $result = $tree->type(['folder' => ['icon' => 'fa fa-folder']]);

        $this->assertSame($tree, $result);
        $options = $this->getProperty($tree, 'options');
        $this->assertSame([
            'default' => ['icon' => false],
            'folder' => ['icon' => 'fa fa-folder'],
        ], $options['types']);
    }

    public function test_type_overwrites_existing_key(): void
    {
        $tree = $this->createTree();

        $tree->type(['default' => ['icon' => 'fa fa-file']]);

        $options = $this->getProperty($tree, 'options');
        $this->assertSame(['default' => ['icon' => 'fa fa-file']], $options['types']);
    }

    // ---------------------------------------------------------------
    // 9. plugins() — 设置插件列表
    // ---------------------------------------------------------------

    public function test_plugins_replaces_list(): void
    {
        $tree = $this->createTree();

        $result = $tree->plugins(['checkbox', 'search', 'types']);

        $this->assertSame($tree, $result);
        $options = $this->getProperty($tree, 'options');
        $this->assertSame(['checkbox', 'search', 'types'], $options['plugins']);
    }

    public function test_plugins_can_set_empty(): void
    {
        $tree = $this->createTree();

        $tree->plugins([]);

        $options = $this->getProperty($tree, 'options');
        $this->assertSame([], $options['plugins']);
    }

    // ---------------------------------------------------------------
    // 10. expand() — 展开/收起
    // ---------------------------------------------------------------

    public function test_expand_default_true(): void
    {
        $tree = $this->createTree();

        // 先设为 false，再调用无参数版本
        $this->setProperty($tree, 'expand', false);

        $result = $tree->expand();

        $this->assertSame($tree, $result);
        $this->assertTrue($this->getProperty($tree, 'expand'));
    }

    public function test_expand_false(): void
    {
        $tree = $this->createTree();

        $tree->expand(false);

        $this->assertFalse($this->getProperty($tree, 'expand'));
    }

    public function test_expand_true(): void
    {
        $tree = $this->createTree();

        $tree->expand(true);

        $this->assertTrue($this->getProperty($tree, 'expand'));
    }

    // ---------------------------------------------------------------
    // 11. formatFieldData() — 返回数组
    // ---------------------------------------------------------------

    public function test_format_field_data_with_comma_string(): void
    {
        $tree = $this->createTree('permissions');
        $data = ['permissions' => '1,2,3'];

        $result = $this->invokeMethod($tree, 'formatFieldData', [$data]);

        $this->assertIsArray($result);
        $this->assertSame(['1', '2', '3'], $result);
    }

    public function test_format_field_data_with_array(): void
    {
        $tree = $this->createTree('permissions');
        $data = ['permissions' => [1, 2, 3]];

        $result = $this->invokeMethod($tree, 'formatFieldData', [$data]);

        $this->assertIsArray($result);
        $this->assertSame([1, 2, 3], $result);
    }

    public function test_format_field_data_with_null(): void
    {
        $tree = $this->createTree('permissions');
        $data = ['permissions' => null];

        $result = $this->invokeMethod($tree, 'formatFieldData', [$data]);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function test_format_field_data_with_missing_key(): void
    {
        $tree = $this->createTree('permissions');
        $data = ['other_field' => 'value'];

        $result = $this->invokeMethod($tree, 'formatFieldData', [$data]);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // 12. prepareInputValue() — 返回数组
    // ---------------------------------------------------------------

    public function test_prepare_input_value_with_comma_string(): void
    {
        $tree = $this->createTree();

        $result = $this->invokeMethod($tree, 'prepareInputValue', ['10,20,30']);

        $this->assertIsArray($result);
        $this->assertSame(['10', '20', '30'], $result);
    }

    public function test_prepare_input_value_with_array(): void
    {
        $tree = $this->createTree();

        $result = $this->invokeMethod($tree, 'prepareInputValue', [[5, 10, 15]]);

        $this->assertIsArray($result);
        $this->assertSame([5, 10, 15], $result);
    }

    public function test_prepare_input_value_with_null(): void
    {
        $tree = $this->createTree();

        $result = $this->invokeMethod($tree, 'prepareInputValue', [null]);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function test_prepare_input_value_with_empty_string(): void
    {
        $tree = $this->createTree();

        $result = $this->invokeMethod($tree, 'prepareInputValue', ['']);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // 13. formatNodes() — 格式化节点数据 (protected)
    // ---------------------------------------------------------------

    public function test_format_nodes_transforms_data_correctly(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child A', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Child B', 'parent_id' => 1],
        ];

        $tree->nodes($nodes);
        // 无选中值
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        $this->assertCount(3, $formattedNodes);

        // Root 节点的 parent 应为 '#'（因为 parent_id=0 等于 rootParentId=0）
        $this->assertSame(1, $formattedNodes[0]['id']);
        $this->assertSame('Root', $formattedNodes[0]['text']);
        $this->assertSame('#', $formattedNodes[0]['parent']);
        $this->assertSame([], $formattedNodes[0]['state']);

        // Child A 节点
        $this->assertSame(2, $formattedNodes[1]['id']);
        $this->assertSame('Child A', $formattedNodes[1]['text']);
        $this->assertSame(1, $formattedNodes[1]['parent']);

        // Child B 节点
        $this->assertSame(3, $formattedNodes[2]['id']);
        $this->assertSame('Child B', $formattedNodes[2]['text']);
        $this->assertSame(1, $formattedNodes[2]['parent']);
    }

    public function test_format_nodes_marks_selected_values(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child A', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Child B', 'parent_id' => 1],
        ];

        $tree->nodes($nodes);
        $this->setProperty($tree, 'value', [2, 3]);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        // Root 未选中
        $this->assertArrayNotHasKey('selected', $formattedNodes[0]['state']);
        // Child A 已选中
        $this->assertTrue($formattedNodes[1]['state']['selected']);
        // Child B 已选中
        $this->assertTrue($formattedNodes[2]['state']['selected']);
    }

    public function test_format_nodes_marks_disabled_when_read_only(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
        ];

        $tree->nodes($nodes);
        $tree->readOnly();
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        $this->assertTrue($formattedNodes[0]['state']['disabled']);
        $this->assertTrue($formattedNodes[1]['state']['disabled']);
    }

    public function test_format_nodes_collects_parent_ids_when_except_parents_enabled(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child A', 'parent_id' => 1],
            ['id' => 3, 'name' => 'Child B', 'parent_id' => 1],
        ];

        $tree->nodes($nodes);
        $tree->exceptParentNode(true);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $parents = $this->getProperty($tree, 'parents');

        // parentId=1 出现了两次，但 array_unique 后只有一个
        $this->assertSame([1], array_values($parents));
    }

    public function test_format_nodes_does_not_collect_parents_when_except_parents_disabled(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
        ];

        $tree->nodes($nodes);
        $tree->exceptParentNode(false);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $parents = $this->getProperty($tree, 'parents');
        $this->assertSame([], $parents);
    }

    public function test_format_nodes_with_custom_columns(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['nid' => 1, 'title' => 'Root', 'pid' => 0],
            ['nid' => 2, 'title' => 'Child', 'pid' => 1],
        ];

        $tree->nodes($nodes);
        $tree->setIdColumn('nid')
            ->setTitleColumn('title')
            ->setParentColumn('pid');
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        $this->assertCount(2, $formattedNodes);
        $this->assertSame(1, $formattedNodes[0]['id']);
        $this->assertSame('Root', $formattedNodes[0]['text']);
        $this->assertSame('#', $formattedNodes[0]['parent']);

        $this->assertSame(2, $formattedNodes[1]['id']);
        $this->assertSame('Child', $formattedNodes[1]['text']);
        $this->assertSame(1, $formattedNodes[1]['parent']);
    }

    public function test_format_nodes_with_custom_root_parent_id(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 10, 'name' => 'Root', 'parent_id' => 99],
            ['id' => 20, 'name' => 'Child', 'parent_id' => 10],
        ];

        $tree->nodes($nodes);
        $tree->rootParentId(99);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        // parent_id=99 等于 rootParentId，应转为 '#'
        $this->assertSame('#', $formattedNodes[0]['parent']);
        $this->assertSame(10, $formattedNodes[1]['parent']);
    }

    public function test_format_nodes_skips_entries_without_id(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Valid', 'parent_id' => 0],
            ['name' => 'No ID', 'parent_id' => 0],  // 缺少 id
            ['id' => null, 'name' => 'Null ID', 'parent_id' => 0],  // id 为 null（empty）
        ];

        $tree->nodes($nodes);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        // 只有第一个有效条目
        $this->assertCount(1, $formattedNodes);
        $this->assertSame(1, $formattedNodes[0]['id']);
    }

    public function test_format_nodes_with_empty_nodes(): void
    {
        $tree = $this->createTree('permissions');
        $tree->nodes([]);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');
        $this->assertSame([], $formattedNodes);
    }

    public function test_format_nodes_with_empty_parent_id_treated_as_root(): void
    {
        $tree = $this->createTree('permissions');
        $nodes = [
            ['id' => 1, 'name' => 'Root', 'parent_id' => ''],
            ['id' => 2, 'name' => 'Also Root', 'parent_id' => null],
        ];

        $tree->nodes($nodes);
        $this->setProperty($tree, 'value', []);

        $this->invokeMethod($tree, 'formatNodes');

        $formattedNodes = $this->getProperty($tree, 'nodes');

        // empty parentId 应被视为根节点（'#'）
        $this->assertSame('#', $formattedNodes[0]['parent']);
        $this->assertSame('#', $formattedNodes[1]['parent']);
    }
}
