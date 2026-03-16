<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\ColumnSelector;

use Dcat\Admin\Contracts\Grid\ColumnSelectorStore;
use Dcat\Admin\Grid;
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

    public function test_implements_column_selector_store(): void
    {
        $store = new SessionStore;

        $this->assertInstanceOf(ColumnSelectorStore::class, $store);
    }

    public function test_set_grid_stores_grid(): void
    {
        $store = new SessionStore;

        $grid = Mockery::mock(Grid::class);
        $store->setGrid($grid);

        $ref = new \ReflectionProperty($store, 'grid');
        $ref->setAccessible(true);

        $this->assertSame($grid, $ref->getValue($store));
    }

    public function test_store_get_and_forget_use_session_storage(): void
    {
        $store = new TestableSessionStore;

        $payload = ['visible' => ['name', 'email']];
        $store->store($payload);

        $this->assertSame($payload, $store->get());

        $store->forget();

        $this->assertNull($store->get());
    }

    public function test_store_get_forget_signatures_are_public_and_parameter_counts_match(): void
    {
        $storeMethod = new \ReflectionMethod(SessionStore::class, 'store');
        $getMethod = new \ReflectionMethod(SessionStore::class, 'get');
        $forgetMethod = new \ReflectionMethod(SessionStore::class, 'forget');

        $this->assertTrue($storeMethod->isPublic());
        $this->assertCount(1, $storeMethod->getParameters());

        $this->assertTrue($getMethod->isPublic());
        $this->assertCount(0, $getMethod->getParameters());

        $this->assertTrue($forgetMethod->isPublic());
        $this->assertCount(0, $forgetMethod->getParameters());
    }
}

class TestableSessionStore extends SessionStore
{
    protected function getKey()
    {
        return 'test-grid-column-selector-session';
    }
}
