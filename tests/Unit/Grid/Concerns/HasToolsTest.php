<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasTools;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasToolsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): HasToolsTestHelper
    {
        return new HasToolsTestHelper;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    // -------------------------------------------------------------------------
    // setUpTools
    // -------------------------------------------------------------------------

    public function test_set_up_tools_creates_tools_instance(): void
    {
        $helper = $this->makeHelper();

        $this->assertNull($this->getProtectedProperty($helper, 'tools'));

        $helper->setUpTools();

        $this->assertInstanceOf(Tools::class, $this->getProtectedProperty($helper, 'tools'));
    }

    // -------------------------------------------------------------------------
    // tools()
    // -------------------------------------------------------------------------

    public function test_tools_returns_tools_when_no_argument(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $this->assertSame($mockTools, $helper->tools());
    }

    public function test_tools_with_closure_calls_closure(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $called = false;
        $receivedTools = null;
        $result = $helper->tools(function ($tools) use (&$called, &$receivedTools) {
            $called = true;
            $receivedTools = $tools;
        });

        $this->assertTrue($called);
        $this->assertSame($mockTools, $receivedTools);
        $this->assertSame($helper, $result);
    }

    public function test_tools_with_array_appends_each(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('append')->with('tool-a')->once()->andReturnSelf();
        $mockTools->shouldReceive('append')->with('tool-b')->once()->andReturnSelf();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->tools(['tool-a', 'tool-b']);

        $this->assertSame($helper, $result);
    }

    public function test_tools_with_string_wraps_in_array(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('append')->with('single-tool')->once()->andReturnSelf();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->tools('single-tool');

        $this->assertSame($helper, $result);
    }

    // -------------------------------------------------------------------------
    // toolsWithOutline
    // -------------------------------------------------------------------------

    public function test_tools_with_outline_delegates_to_tools(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('withOutline')->with(true)->once()->andReturnSelf();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->toolsWithOutline(true);

        $this->assertSame($helper, $result);
    }

    public function test_tools_with_outline_false(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('withOutline')->with(false)->once()->andReturnSelf();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->toolsWithOutline(false);

        $this->assertSame($helper, $result);
    }

    // -------------------------------------------------------------------------
    // disableToolbar / showToolbar
    // -------------------------------------------------------------------------

    public function test_disable_toolbar(): void
    {
        $helper = $this->makeHelper();

        $helper->disableToolbar();

        $this->assertFalse($helper->options['toolbar']);
    }

    public function test_disable_toolbar_with_false_enables(): void
    {
        $helper = $this->makeHelper();
        $helper->disableToolbar();
        $helper->disableToolbar(false);

        $this->assertTrue($helper->options['toolbar']);
    }

    public function test_show_toolbar(): void
    {
        $helper = $this->makeHelper();
        $helper->disableToolbar();

        $helper->showToolbar();

        $this->assertTrue($helper->options['toolbar']);
    }

    public function test_show_toolbar_with_false_disables(): void
    {
        $helper = $this->makeHelper();

        $helper->showToolbar(false);

        $this->assertFalse($helper->options['toolbar']);
    }

    // -------------------------------------------------------------------------
    // disableBatchActions / showBatchActions
    // -------------------------------------------------------------------------

    public function test_disable_batch_actions_delegates_to_tools(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('disableBatchActions')->with(true)->once();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->disableBatchActions();

        $this->assertSame($helper, $result);
    }

    public function test_show_batch_actions(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('disableBatchActions')->with(false)->once();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->showBatchActions();

        $this->assertSame($helper, $result);
    }

    // -------------------------------------------------------------------------
    // disableRefreshButton / showRefreshButton
    // -------------------------------------------------------------------------

    public function test_disable_refresh_button_delegates_to_tools(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('disableRefreshButton')->with(true)->once();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->disableRefreshButton();

        $this->assertSame($helper, $result);
    }

    public function test_show_refresh_button(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('disableRefreshButton')->with(false)->once();
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $result = $helper->showRefreshButton();

        $this->assertSame($helper, $result);
    }

    // -------------------------------------------------------------------------
    // renderTools
    // -------------------------------------------------------------------------

    public function test_render_tools_delegates_to_tools_render(): void
    {
        $helper = $this->makeHelper();
        $mockTools = Mockery::mock(Tools::class);
        $mockTools->shouldReceive('render')->once()->andReturn('<div>tools</div>');
        $this->setProtectedProperty($helper, 'tools', $mockTools);

        $this->assertSame('<div>tools</div>', $helper->renderTools());
    }
}

class HasToolsTestHelper extends Grid
{
    use HasTools;

    public $options = ['toolbar' => true];

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
}
