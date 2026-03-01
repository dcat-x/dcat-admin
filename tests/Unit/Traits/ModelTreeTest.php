<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\ModelTree;
use Mockery;

class ModelTreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(ModelTree::class));
    }

    public function test_has_get_parent_column_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'getParentColumn'));
    }

    public function test_has_get_title_column_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'getTitleColumn'));
    }

    public function test_has_get_order_column_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'getOrderColumn'));
    }

    public function test_has_get_depth_column_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'getDepthColumn'));
    }

    public function test_has_get_default_parent_id_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'getDefaultParentId'));
    }

    public function test_has_with_query_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'withQuery'));
    }

    public function test_has_to_tree_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'toTree'));
    }

    public function test_has_all_nodes_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'allNodes'));
    }

    public function test_has_save_order_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'saveOrder'));
    }

    public function test_has_select_options_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'selectOptions'));
    }

    public function test_has_move_order_down_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'moveOrderDown'));
    }

    public function test_has_move_order_up_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'moveOrderUp'));
    }

    public function test_has_move_to_start_method(): void
    {
        $this->assertTrue(method_exists(ModelTree::class, 'moveToStart'));
    }

    public function test_branch_order_is_static(): void
    {
        $ref = new \ReflectionProperty(ModelTree::class, 'branchOrder');
        $ref->setAccessible(true);
        $this->assertTrue($ref->isStatic());
    }

    public function test_branch_order_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ModelTree::class, 'branchOrder');
        $ref->setAccessible(true);
        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_query_callbacks_is_protected(): void
    {
        $ref = new \ReflectionProperty(ModelTree::class, 'queryCallbacks');
        $ref->setAccessible(true);
        $this->assertTrue($ref->isProtected());
    }

    public function test_query_callbacks_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ModelTree::class, 'queryCallbacks');
        $ref->setAccessible(true);
        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_set_branch_order_is_protected_static(): void
    {
        $ref = new \ReflectionMethod(ModelTree::class, 'setBranchOrder');
        $this->assertTrue($ref->isProtected());
        $this->assertTrue($ref->isStatic());
    }

    public function test_build_select_options_is_protected(): void
    {
        $ref = new \ReflectionMethod(ModelTree::class, 'buildSelectOptions');
        $this->assertTrue($ref->isProtected());
    }

    public function test_call_query_callbacks_is_protected(): void
    {
        $ref = new \ReflectionMethod(ModelTree::class, 'callQueryCallbacks');
        $this->assertTrue($ref->isProtected());
    }

    public function test_determine_order_column_name_is_protected(): void
    {
        $ref = new \ReflectionMethod(ModelTree::class, 'determineOrderColumnName');
        $this->assertTrue($ref->isProtected());
    }
}
