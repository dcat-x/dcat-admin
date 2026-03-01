<?php

namespace Dcat\Admin\Tests\Unit\Grid\ColumnSelector;

use Dcat\Admin\Grid\ColumnSelector\CacheStore;
use Dcat\Admin\Grid\ColumnSelector\SessionStore;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use ReflectionProperty;

class CacheStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(CacheStore::class));
    }

    public function test_extends_session_store(): void
    {
        $store = new CacheStore;

        $this->assertInstanceOf(SessionStore::class, $store);
    }

    public function test_has_store_method(): void
    {
        $this->assertTrue(method_exists(CacheStore::class, 'store'));
    }

    public function test_has_get_method(): void
    {
        $this->assertTrue(method_exists(CacheStore::class, 'get'));
    }

    public function test_has_forget_method(): void
    {
        $this->assertTrue(method_exists(CacheStore::class, 'forget'));
    }

    public function test_constructor_accepts_driver_and_ttl(): void
    {
        $store = new CacheStore('array', 3600);

        $ref = new ReflectionProperty($store, 'ttl');
        $ref->setAccessible(true);

        $this->assertEquals(3600, $ref->getValue($store));
    }

    public function test_constructor_default_ttl(): void
    {
        $store = new CacheStore;

        $ref = new ReflectionProperty($store, 'ttl');
        $ref->setAccessible(true);

        $this->assertEquals(25920000, $ref->getValue($store));
    }
}
