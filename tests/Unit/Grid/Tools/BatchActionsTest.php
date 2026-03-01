<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Grid\Tools\BatchActions;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class BatchActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_abstract_tool(): void
    {
        $this->assertTrue(is_subclass_of(BatchActions::class, AbstractTool::class));
    }

    public function test_constructor_creates_actions_collection(): void
    {
        $batch = new BatchActions;
        $ref = new \ReflectionProperty($batch, 'actions');
        $ref->setAccessible(true);
        $this->assertInstanceOf(Collection::class, $ref->getValue($batch));
    }

    public function test_constructor_appends_default_delete_action(): void
    {
        $batch = new BatchActions;
        $ref = new \ReflectionProperty($batch, 'actions');
        $ref->setAccessible(true);
        $actions = $ref->getValue($batch);
        $this->assertTrue($actions->has('_delete_'));
    }

    public function test_disable_delete(): void
    {
        $batch = new BatchActions;
        $result = $batch->disableDelete();
        $this->assertSame($batch, $result);

        $ref = new \ReflectionProperty($batch, 'enableDelete');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($batch));
    }

    public function test_disable_delete_false(): void
    {
        $batch = new BatchActions;
        $batch->disableDelete();
        $batch->disableDelete(false);

        $ref = new \ReflectionProperty($batch, 'enableDelete');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($batch));
    }

    public function test_disable_delete_and_hide_select_all(): void
    {
        $batch = new BatchActions;
        $result = $batch->disableDeleteAndHideSelectAll();
        $this->assertSame($batch, $result);

        $ref1 = new \ReflectionProperty($batch, 'enableDelete');
        $ref1->setAccessible(true);
        $this->assertFalse($ref1->getValue($batch));

        $ref2 = new \ReflectionProperty($batch, 'isHoldSelectAllCheckbox');
        $ref2->setAccessible(true);
        $this->assertTrue($ref2->getValue($batch));
    }

    public function test_has_view_property(): void
    {
        $ref = new \ReflectionProperty(BatchActions::class, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::grid.batch-actions', $ref->getDefaultValue());
    }
}
