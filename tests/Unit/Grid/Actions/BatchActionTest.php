<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Grid\BatchAction;
use Dcat\Admin\Grid\Tools\BatchActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class BatchActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_creates_instance(): void
    {
        $batchActions = new BatchActions;
        $this->assertInstanceOf(BatchActions::class, $batchActions);
    }

    public function test_constructor_adds_default_delete_action(): void
    {
        $batchActions = new BatchActions;
        $ref = new \ReflectionProperty($batchActions, 'actions');
        $ref->setAccessible(true);
        $actions = $ref->getValue($batchActions);

        $this->assertTrue($actions->has('_delete_'));
    }

    public function test_disable_delete(): void
    {
        $batchActions = new BatchActions;
        $result = $batchActions->disableDelete();

        $this->assertSame($batchActions, $result);

        $ref = new \ReflectionProperty($batchActions, 'enableDelete');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($batchActions));
    }

    public function test_disable_delete_with_false_re_enables(): void
    {
        $batchActions = new BatchActions;
        $batchActions->disableDelete();
        $batchActions->disableDelete(false);

        $ref = new \ReflectionProperty($batchActions, 'enableDelete');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($batchActions));
    }

    public function test_disable_delete_and_hide_select_all(): void
    {
        $batchActions = new BatchActions;
        $result = $batchActions->disableDeleteAndHideSelectAll();

        $this->assertSame($batchActions, $result);

        $refDelete = new \ReflectionProperty($batchActions, 'enableDelete');
        $refDelete->setAccessible(true);
        $this->assertFalse($refDelete->getValue($batchActions));

        $refCheckbox = new \ReflectionProperty($batchActions, 'isHoldSelectAllCheckbox');
        $refCheckbox->setAccessible(true);
        $this->assertTrue($refCheckbox->getValue($batchActions));
    }

    public function test_add_batch_action_with_key(): void
    {
        $batchActions = new BatchActions;
        $action = Mockery::mock(BatchAction::class);
        $action->selectorPrefix = '';

        $result = $batchActions->add($action, 'custom_action');

        $this->assertSame($batchActions, $result);

        $ref = new \ReflectionProperty($batchActions, 'actions');
        $ref->setAccessible(true);
        $actions = $ref->getValue($batchActions);
        $this->assertTrue($actions->has('custom_action'));
    }

    public function test_add_batch_action_without_key(): void
    {
        $batchActions = new BatchActions;
        $initialCount = $this->getActionsCount($batchActions);

        $action = Mockery::mock(BatchAction::class);
        $action->selectorPrefix = '';

        $batchActions->add($action);

        $this->assertSame($initialCount + 1, $this->getActionsCount($batchActions));
    }

    public function test_add_sets_selector_prefix(): void
    {
        $batchActions = new BatchActions;
        $action = Mockery::mock(BatchAction::class);
        $action->selectorPrefix = '';

        $batchActions->add($action);

        $this->assertStringStartsWith('.grid-batch-action-', $action->selectorPrefix);
    }

    public function test_divider_adds_html_to_actions(): void
    {
        $batchActions = new BatchActions;
        $initialCount = $this->getActionsCount($batchActions);

        $batchActions->divider();

        $ref = new \ReflectionProperty($batchActions, 'actions');
        $ref->setAccessible(true);
        $actions = $ref->getValue($batchActions);

        $this->assertSame($initialCount + 1, $actions->count());

        // The divider adds an ActionDivider instance
        $lastItem = $actions->last();
        $this->assertInstanceOf(\Dcat\Admin\Grid\Tools\ActionDivider::class, $lastItem);
    }

    public function test_divider_returns_this(): void
    {
        $batchActions = new BatchActions;

        $result = $batchActions->divider();

        $this->assertSame($batchActions, $result);
    }

    protected function getActionsCount(BatchActions $batchActions): int
    {
        $ref = new \ReflectionProperty($batchActions, 'actions');
        $ref->setAccessible(true);

        return $ref->getValue($batchActions)->count();
    }
}
