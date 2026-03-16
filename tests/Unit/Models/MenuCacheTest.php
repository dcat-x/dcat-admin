<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\MenuCache;
use Dcat\Admin\Tests\TestCase;

class MenuCacheTest extends TestCase
{
    protected function createMenuCacheUser(): object
    {
        return new class
        {
            use MenuCache;

            public static function withPermission()
            {
                return true;
            }
        };
    }

    public function test_enable_cache_reads_config(): void
    {
        $this->app['config']->set('admin.menu.cache.enable', true);

        $user = $this->createMenuCacheUser();
        $this->assertTrue($user->enableCache());
    }

    public function test_enable_cache_returns_false_when_disabled(): void
    {
        $this->app['config']->set('admin.menu.cache.enable', false);

        $user = $this->createMenuCacheUser();
        $this->assertFalse($user->enableCache());
    }

    public function test_cache_key_property_exists(): void
    {
        $user = $this->createMenuCacheUser();

        $reflection = new \ReflectionProperty($user, 'cacheKey');
        $reflection->setAccessible(true);
        $this->assertIsString($reflection->getValue($user));
        $this->assertStringContainsString('dcat-admin-menus', $reflection->getValue($user));
    }

    public function test_flush_cache_returns_void_when_disabled(): void
    {
        $this->app['config']->set('admin.menu.cache.enable', false);

        $user = $this->createMenuCacheUser();
        $this->assertNull($user->flushCache());
    }

    public function test_get_store_returns_cache_repository(): void
    {
        $user = $this->createMenuCacheUser();
        $store = $user->getStore();

        $this->assertInstanceOf(\Illuminate\Contracts\Cache\Repository::class, $store);
    }
}
