<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasQuickCreate;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasQuickCreateTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_has_quick_create_returns_false_initially(): void
    {
        $helper = new HasQuickCreateTestHelper;
        $this->assertFalse($helper->hasQuickCreate());
    }

    public function test_quick_create_property_is_null_initially(): void
    {
        $helper = new HasQuickCreateTestHelper;
        $ref = new \ReflectionProperty($helper, 'quickCreate');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_has_quick_create_method_exists(): void
    {
        $this->assertTrue(method_exists(HasQuickCreateTestHelper::class, 'hasQuickCreate'));
    }

    public function test_quick_create_method_exists(): void
    {
        $this->assertTrue(method_exists(HasQuickCreateTestHelper::class, 'quickCreate'));
    }

    public function test_render_quick_create_method_exists(): void
    {
        $this->assertTrue(method_exists(HasQuickCreateTestHelper::class, 'renderQuickCreate'));
    }

    public function test_quick_create_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasQuickCreateTestHelper::class, 'quickCreate');
        $this->assertTrue($ref->isProtected());
    }
}

class HasQuickCreateTestHelper extends Grid
{
    use HasQuickCreate;

    public function __construct()
    {
        // Skip parent constructor
    }
}
