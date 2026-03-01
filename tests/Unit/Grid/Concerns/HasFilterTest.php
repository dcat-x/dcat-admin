<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasFilter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasFilterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_filter_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasFilterTestHelper::class, 'filter');
        $this->assertTrue($ref->isProtected());
    }

    public function test_set_up_filter_is_protected(): void
    {
        $ref = new \ReflectionMethod(HasFilterTestHelper::class, 'setUpFilter');
        $this->assertTrue($ref->isProtected());
    }

    public function test_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'filter'));
    }

    public function test_render_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'renderFilter'));
    }

    public function test_expand_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'expandFilter'));
    }

    public function test_disable_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'disableFilter'));
    }

    public function test_show_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'showFilter'));
    }

    public function test_disable_filter_button_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'disableFilterButton'));
    }

    public function test_show_filter_button_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'showFilterButton'));
    }

    public function test_process_filter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasFilterTestHelper::class, 'processFilter'));
    }
}

class HasFilterTestHelper extends Grid
{
    use HasFilter;

    public function __construct()
    {
        // Skip parent constructor
    }
}
