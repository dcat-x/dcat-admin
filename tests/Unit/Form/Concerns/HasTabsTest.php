<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasFields;
use Dcat\Admin\Form\Concerns\HasLayout;
use Dcat\Admin\Form\Concerns\HasRows;
use Dcat\Admin\Form\Concerns\HasTabs;
use Dcat\Admin\Form\Tab;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasTabsTestHelper
{
    use HasFields;
    use HasLayout;
    use HasRows;
    use HasTabs;

    public function __call($method, $arguments)
    {
        return null;
    }
}

class HasTabsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_has_tab_returns_false_initially(): void
    {
        $helper = new HasTabsTestHelper;

        $this->assertFalse($helper->hasTab());
    }

    public function test_get_tab_creates_tab_lazily(): void
    {
        $helper = new HasTabsTestHelper;

        $tab = $helper->getTab();

        $this->assertInstanceOf(Tab::class, $tab);
    }

    public function test_get_tab_returns_same_instance(): void
    {
        $helper = new HasTabsTestHelper;

        $first = $helper->getTab();
        $second = $helper->getTab();

        $this->assertSame($first, $second);
    }

    public function test_has_tab_returns_true_after_get_tab(): void
    {
        $helper = new HasTabsTestHelper;

        $helper->getTab();

        $this->assertTrue($helper->hasTab());
    }

    public function test_tab_returns_self(): void
    {
        $helper = new HasTabsTestHelper;

        $result = $helper->tab('Test Tab', function () {});

        $this->assertSame($helper, $result);
    }

    public function test_tab_property_initially_null(): void
    {
        $helper = new HasTabsTestHelper;

        $ref = new \ReflectionProperty($helper, 'tab');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($helper));
    }

    public function test_get_tab_returns_tab_instance(): void
    {
        $helper = new HasTabsTestHelper;

        $this->assertInstanceOf(Tab::class, $helper->getTab());
    }
}
