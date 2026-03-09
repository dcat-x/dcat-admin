<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\CanFixColumns;
use Dcat\Admin\Grid\FixColumns;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanFixColumnsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): CanFixColumnsTestHelper
    {
        return new CanFixColumnsTestHelper;
    }

    // -------------------------------------------------------------------------
    // hasFixColumns
    // -------------------------------------------------------------------------

    public function test_has_fix_columns_returns_null_initially(): void
    {
        $helper = $this->makeHelper();

        $this->assertNull($helper->hasFixColumns());
    }

    public function test_has_fix_columns_returns_truthy_after_fix(): void
    {
        $helper = $this->makeHelper();

        $helper->fixColumns(2, 1);

        $this->assertNotNull($helper->hasFixColumns());
    }

    // -------------------------------------------------------------------------
    // fixColumns
    // -------------------------------------------------------------------------

    public function test_fix_columns_creates_instance(): void
    {
        $helper = $this->makeHelper();

        $result = $helper->fixColumns(3);

        $this->assertInstanceOf(FixColumns::class, $result);
    }

    public function test_fix_columns_returns_fix_columns_instance(): void
    {
        $helper = $this->makeHelper();

        $fixColumns = $helper->fixColumns(2, 1);

        $this->assertInstanceOf(FixColumns::class, $fixColumns);
        $this->assertSame($fixColumns, $helper->hasFixColumns());
    }

    public function test_fix_columns_sets_head_value(): void
    {
        $helper = $this->makeHelper();

        $fixColumns = $helper->fixColumns(5, 2);

        $this->assertSame(5, $fixColumns->head);
    }

    public function test_fix_columns_sets_tail_value(): void
    {
        $helper = $this->makeHelper();

        $fixColumns = $helper->fixColumns(3, 4);

        $this->assertSame(4, $fixColumns->tail);
    }

    public function test_fix_columns_default_tail_is_minus_one(): void
    {
        $helper = $this->makeHelper();

        $fixColumns = $helper->fixColumns(2);

        $this->assertSame(-1, $fixColumns->tail);
    }

    public function test_fix_columns_does_not_call_reset_actions(): void
    {
        $helper = $this->makeHelper();

        $helper->fixColumns(1, 1);

        $this->assertFalse($helper->resetActionsCalled);
    }

    // -------------------------------------------------------------------------
    // property
    // -------------------------------------------------------------------------

    public function test_has_fix_columns_property(): void
    {
        $property = new \ReflectionProperty(CanFixColumnsTestHelper::class, 'fixColumns');

        $this->assertTrue($property->isProtected());
    }

    public function test_apply_fix_columns_method_signature(): void
    {
        $method = new \ReflectionMethod(CanFixColumnsTestHelper::class, 'applyFixColumns');

        $this->assertSame(0, $method->getNumberOfParameters());
    }
}

class CanFixColumnsTestHelper extends Grid
{
    use CanFixColumns;

    public $resetActionsCalled = false;

    public function __construct()
    {
        // Skip parent constructor
    }

    protected function resetActions()
    {
        $this->resetActionsCalled = true;
    }
}
