<?php

namespace Dcat\Admin\Tests\Unit\Tree;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
use Dcat\Admin\Tree\Tools;
use Mockery;

class TreeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createTree(): Tree
    {
        $repo = Mockery::mock(TreeRepository::class);
        $repo->shouldReceive('getPrimaryKeyColumn')->andReturn('id');
        $repo->shouldReceive('getTitleColumn')->andReturn('title');
        $repo->shouldReceive('getKeyName')->andReturn('id');
        $repo->shouldReceive('withQuery')->andReturnSelf();
        $repo->shouldReceive('toTree')->andReturn(collect());

        return new Tree($repo);
    }

    public function test_save_order_name_constant(): void
    {
        $this->assertSame('_order', Tree::SAVE_ORDER_NAME);
    }

    public function test_default_use_create_is_true(): void
    {
        $tree = $this->createTree();

        $this->assertTrue($tree->useCreate);
    }

    public function test_default_expand_is_true(): void
    {
        $tree = $this->createTree();

        $this->assertTrue($tree->expand);
    }

    public function test_default_use_quick_create_is_true(): void
    {
        $tree = $this->createTree();

        $this->assertTrue($tree->useQuickCreate);
    }

    public function test_default_use_save_is_true(): void
    {
        $tree = $this->createTree();

        $this->assertTrue($tree->useSave);
    }

    public function test_default_use_refresh_is_true(): void
    {
        $tree = $this->createTree();

        $this->assertTrue($tree->useRefresh);
    }

    public function test_default_dialog_form_dimensions(): void
    {
        $tree = $this->createTree();

        $this->assertSame(['700px', '670px'], $tree->dialogFormDimensions);
    }

    public function test_disable_create_button(): void
    {
        $tree = $this->createTree();

        $tree->disableCreateButton();

        $this->assertFalse($tree->useCreate);
    }

    public function test_show_create_button(): void
    {
        $tree = $this->createTree();

        $tree->disableCreateButton();
        $this->assertFalse($tree->useCreate);

        $tree->showCreateButton();
        $this->assertTrue($tree->useCreate);
    }

    public function test_disable_save_button(): void
    {
        $tree = $this->createTree();

        $tree->disableSaveButton();

        $this->assertFalse($tree->useSave);
    }

    public function test_disable_refresh_button(): void
    {
        $tree = $this->createTree();

        $tree->disableRefreshButton();

        $this->assertFalse($tree->useRefresh);
    }

    public function test_expand_sets_value(): void
    {
        $tree = $this->createTree();

        $tree->expand(false);
        $this->assertFalse($tree->expand);

        $tree->expand(true);
        $this->assertTrue($tree->expand);
    }

    public function test_set_dialog_form_dimensions_returns_this(): void
    {
        $tree = $this->createTree();

        $result = $tree->setDialogFormDimensions('800px', '600px');

        $this->assertSame($tree, $result);
        $this->assertSame(['800px', '600px'], $tree->dialogFormDimensions);
    }

    public function test_nestable_merges_options(): void
    {
        $tree = $this->createTree();

        $ref = new \ReflectionProperty(Tree::class, 'nestableOptions');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getValue($tree));

        $tree->nestable(['maxDepth' => 3]);
        $this->assertSame(['maxDepth' => 3], $ref->getValue($tree));

        $tree->nestable(['group' => 1]);
        $this->assertSame(['maxDepth' => 3, 'group' => 1], $ref->getValue($tree));
    }

    public function test_max_depth_sets_nestable_option(): void
    {
        $tree = $this->createTree();

        $result = $tree->maxDepth(10);

        $this->assertSame($tree, $result);

        $ref = new \ReflectionProperty(Tree::class, 'nestableOptions');
        $ref->setAccessible(true);

        $this->assertSame(['maxDepth' => 10], $ref->getValue($tree));
    }

    public function test_view_sets_view(): void
    {
        $tree = $this->createTree();

        $result = $tree->view('custom::tree.view');

        $this->assertSame($tree, $result);

        $ref = new \ReflectionProperty(Tree::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('custom::tree.view', $ref->getValue($tree));
    }

    public function test_branch_view_sets_view(): void
    {
        $tree = $this->createTree();

        $result = $tree->branchView('custom::tree.branch');

        $this->assertSame($tree, $result);

        $ref = new \ReflectionProperty(Tree::class, 'branchView');
        $ref->setAccessible(true);

        $this->assertSame('custom::tree.branch', $ref->getValue($tree));
    }

    public function test_wrap_sets_wrapper(): void
    {
        $tree = $this->createTree();

        $result = $tree->wrap(function ($view) {
            return $view;
        });

        $this->assertSame($tree, $result);
    }

    public function test_has_wrapper_returns_false_by_default(): void
    {
        $tree = $this->createTree();

        $this->assertFalse($tree->hasWrapper());
    }

    public function test_has_wrapper_returns_true_after_wrap(): void
    {
        $tree = $this->createTree();

        $tree->wrap(function ($view) {
            return $view;
        });

        $this->assertTrue($tree->hasWrapper());
    }

    public function test_query_returns_this(): void
    {
        $tree = $this->createTree();

        $result = $tree->query(function ($query) {
            // no-op
        });

        $this->assertSame($tree, $result);
    }

    public function test_branch_returns_this(): void
    {
        $tree = $this->createTree();

        $result = $tree->branch(function ($branch) {
            return $branch['title'];
        });

        $this->assertSame($tree, $result);
    }

    public function test_set_action_class_returns_this(): void
    {
        $tree = $this->createTree();

        $result = $tree->setActionClass('App\\Actions\\CustomAction');

        $this->assertSame($tree, $result);
    }

    public function test_actions_with_closure_returns_this(): void
    {
        $tree = $this->createTree();

        $result = $tree->actions(function ($actions) {
            // no-op
        });

        $this->assertSame($tree, $result);
    }

    public function test_tools_without_args_returns_tools_instance(): void
    {
        $tree = $this->createTree();

        $tools = $tree->tools();

        $this->assertInstanceOf(Tools::class, $tools);
    }
}
