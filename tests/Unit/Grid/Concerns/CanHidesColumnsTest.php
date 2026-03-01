<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\CanHidesColumns;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanHidesColumnsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): CanHidesColumnsTestHelper
    {
        return new CanHidesColumnsTestHelper;
    }

    public function test_hidden_columns_initially_empty(): void
    {
        $helper = $this->makeHelper();
        $this->assertEmpty($helper->hiddenColumns);
    }

    public function test_hide_columns_with_array(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->hideColumns(['col_a', 'col_b']);
        $this->assertSame($helper, $result);
        $this->assertContains('col_a', $helper->hiddenColumns);
        $this->assertContains('col_b', $helper->hiddenColumns);
    }

    public function test_hide_columns_with_string(): void
    {
        $helper = $this->makeHelper();
        $helper->hideColumns('single_col');
        $this->assertContains('single_col', $helper->hiddenColumns);
    }

    public function test_hide_columns_merges(): void
    {
        $helper = $this->makeHelper();
        $helper->hideColumns(['a']);
        $helper->hideColumns(['b']);
        $this->assertContains('a', $helper->hiddenColumns);
        $this->assertContains('b', $helper->hiddenColumns);
    }

    public function test_disable_column_selector(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->disableColumnSelector();
        $this->assertSame($helper, $result);
        $this->assertFalse($helper->options['show_column_selector']);
    }

    public function test_disable_column_selector_false_enables(): void
    {
        $helper = $this->makeHelper();
        $helper->disableColumnSelector();
        $helper->disableColumnSelector(false);
        $this->assertTrue($helper->options['show_column_selector']);
    }

    public function test_show_column_selector(): void
    {
        $helper = $this->makeHelper();
        $helper->disableColumnSelector();
        $helper->showColumnSelector();
        $this->assertTrue($helper->options['show_column_selector']);
    }

    public function test_show_column_selector_false_disables(): void
    {
        $helper = $this->makeHelper();
        $helper->showColumnSelector(false);
        $this->assertFalse($helper->options['show_column_selector']);
    }

    public function test_allow_column_selector_default(): void
    {
        $helper = $this->makeHelper();
        $this->assertTrue($helper->allowColumnSelector());
    }

    public function test_allow_column_selector_after_disable(): void
    {
        $helper = $this->makeHelper();
        $helper->disableColumnSelector();
        $this->assertFalse($helper->allowColumnSelector());
    }

    public function test_render_column_selector_returns_empty_when_not_allowed(): void
    {
        $helper = $this->makeHelper();
        $helper->disableColumnSelector();
        $this->assertSame('', $helper->renderColumnSelector());
    }

    public function test_column_selector_storage_initially_null(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'columnSelectorStorage');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }
}

class CanHidesColumnsTestHelper extends Grid
{
    use CanHidesColumns;

    public $options = ['show_column_selector' => true];

    public function __construct()
    {
        // Skip parent constructor
    }

    public function option($key, $value = null)
    {
        if ($value !== null) {
            $this->options[$key] = $value;

            return $this;
        }

        return $this->options[$key] ?? null;
    }

    public function makeName($name)
    {
        return 'test_'.$name;
    }
}
