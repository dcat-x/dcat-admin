<?php

namespace Dcat\Admin\Tests\Unit\Grid\ColumnSelector;

use Dcat\Admin\Grid\ColumnSelector\CacheStore;
use Dcat\Admin\Grid\ColumnSelector\SessionStore;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CacheStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extends_session_store(): void
    {
        $store = new CacheStore;

        $this->assertInstanceOf(SessionStore::class, $store);
    }

    public function test_constructor_accepts_driver_and_ttl(): void
    {
        $store = new CacheStore('array', 3600);

        $ref = new \ReflectionProperty($store, 'ttl');
        $ref->setAccessible(true);

        $this->assertEquals(3600, $ref->getValue($store));
    }

    public function test_constructor_default_ttl(): void
    {
        $store = new CacheStore;

        $ref = new \ReflectionProperty($store, 'ttl');
        $ref->setAccessible(true);

        $this->assertEquals(25920000, $ref->getValue($store));
    }

    public function test_store_get_and_forget_use_cache_driver(): void
    {
        $store = new TestableCacheStore('array', 300);

        $payload = ['visible' => ['name', 'created_at']];
        $store->store($payload);

        $this->assertSame($payload, $store->get());

        $store->forget();

        $this->assertNull($store->get());
    }

    public function test_store_get_forget_signatures_are_public_and_parameter_counts_match(): void
    {
        $storeMethod = new \ReflectionMethod(CacheStore::class, 'store');
        $getMethod = new \ReflectionMethod(CacheStore::class, 'get');
        $forgetMethod = new \ReflectionMethod(CacheStore::class, 'forget');

        $this->assertTrue($storeMethod->isPublic());
        $this->assertCount(1, $storeMethod->getParameters());

        $this->assertTrue($getMethod->isPublic());
        $this->assertCount(0, $getMethod->getParameters());

        $this->assertTrue($forgetMethod->isPublic());
        $this->assertCount(0, $forgetMethod->getParameters());
    }
}

class TestableCacheStore extends CacheStore
{
    protected function getKey()
    {
        return 'test-grid-column-selector-cache';
    }
}
