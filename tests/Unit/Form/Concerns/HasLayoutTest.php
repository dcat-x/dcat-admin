<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasLayout;
use Dcat\Admin\Form\Layout;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasLayoutTestHelper
{
    use HasLayout;
}

class HasLayoutTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_layout_creates_lazily(): void
    {
        $helper = new HasLayoutTestHelper;

        $layout = $helper->layout();

        $this->assertInstanceOf(Layout::class, $layout);
    }

    public function test_layout_returns_same_instance(): void
    {
        $helper = new HasLayoutTestHelper;

        $first = $helper->layout();
        $second = $helper->layout();

        $this->assertSame($first, $second);
    }

    public function test_layout_initially_null(): void
    {
        $helper = new HasLayoutTestHelper;

        $ref = new \ReflectionProperty($helper, 'layout');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($helper));
    }

    public function test_column_returns_self(): void
    {
        $helper = new HasLayoutTestHelper;

        $result = $helper->column(6, function () {});

        $this->assertSame($helper, $result);
    }

    public function test_layout_and_column_method_signatures(): void
    {
        $layoutMethod = new \ReflectionMethod(HasLayoutTestHelper::class, 'layout');
        $columnMethod = new \ReflectionMethod(HasLayoutTestHelper::class, 'column');

        $this->assertSame(0, $layoutMethod->getNumberOfParameters());
        $this->assertSame(2, $columnMethod->getNumberOfParameters());
    }
}
