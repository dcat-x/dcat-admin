<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasSelector;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasSelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): HasSelectorTestHelper
    {
        return new HasSelectorTestHelper;
    }

    // -------------------------------------------------------------------------
    // selector()
    // -------------------------------------------------------------------------

    public function test_selector_returns_null_initially(): void
    {
        $helper = $this->makeHelper();

        $this->assertNull($helper->selector());
    }

    public function test_selector_without_args_returns_null_when_not_set(): void
    {
        $helper = $this->makeHelper();

        $result = $helper->selector();

        $this->assertNull($result);
    }

    public function test_selector_with_closure_returns_self(): void
    {
        $helper = $this->makeHelper();

        $result = $helper->selector(function ($selector) {
            // no-op
        });

        $this->assertSame($helper, $result);
    }

    public function test_selector_with_closure_creates_selector_instance(): void
    {
        $helper = $this->makeHelper();

        $helper->selector(function ($selector) {
            // no-op
        });

        $this->assertInstanceOf(Selector::class, $helper->selector());
    }

    public function test_selector_closure_receives_selector_instance(): void
    {
        $helper = $this->makeHelper();
        $receivedSelector = null;

        $helper->selector(function ($selector) use (&$receivedSelector) {
            $receivedSelector = $selector;
        });

        $this->assertInstanceOf(Selector::class, $receivedSelector);
        $this->assertSame($helper->selector(), $receivedSelector);
    }

    public function test_selector_with_closure_adds_header(): void
    {
        $helper = $this->makeHelper();

        $helper->selector(function ($selector) {
            // no-op
        });

        $this->assertTrue($helper->headerCalled);
    }

    // -------------------------------------------------------------------------
    // applySelectorQuery
    // -------------------------------------------------------------------------

    public function test_apply_selector_query_returns_self_when_no_selector(): void
    {
        $helper = $this->makeHelper();

        $method = new \ReflectionMethod($helper, 'applySelectorQuery');
        $method->setAccessible(true);

        $result = $method->invoke($helper);

        $this->assertSame($helper, $result);
    }

    public function test_apply_selector_query_is_protected(): void
    {
        $method = new \ReflectionMethod(HasSelectorTestHelper::class, 'applySelectorQuery');

        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------------------------
    // property
    // -------------------------------------------------------------------------

    public function test_has_selector_property(): void
    {
        $property = new \ReflectionProperty(HasSelectorTestHelper::class, '_selector');

        $this->assertTrue($property->isProtected());
    }
}

/**
 * @mixin Grid
 */
class HasSelectorTestHelper extends Grid
{
    use HasSelector;

    public $headerCalled = false;

    public function __construct()
    {
        // Skip parent constructor
    }

    public function header($closure)
    {
        $this->headerCalled = true;

        return $this;
    }
}
