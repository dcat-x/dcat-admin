<?php

namespace Dcat\Admin\Tests\Unit\Grid\ColumnSelector;

use Dcat\Admin\Contracts\Grid\ColumnSelectorStore;
use Dcat\Admin\Grid\ColumnSelector\SessionStore;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SessionStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(SessionStore::class));
    }

    public function test_implements_column_selector_store(): void
    {
        $store = new SessionStore;

        $this->assertInstanceOf(ColumnSelectorStore::class, $store);
    }

    public function test_set_grid_stores_grid(): void
    {
        $store = new SessionStore;

        $grid = Mockery::mock(\Dcat\Admin\Grid::class);
        $store->setGrid($grid);

        $ref = new \ReflectionProperty($store, 'grid');
        $ref->setAccessible(true);

        $this->assertSame($grid, $ref->getValue($store));
    }

    public function test_has_store_method(): void
    {
        $this->assertTrue(method_exists(SessionStore::class, 'store'));
    }

    public function test_has_get_method(): void
    {
        $this->assertTrue(method_exists(SessionStore::class, 'get'));
    }

    public function test_has_forget_method(): void
    {
        $this->assertTrue(method_exists(SessionStore::class, 'forget'));
    }
}
