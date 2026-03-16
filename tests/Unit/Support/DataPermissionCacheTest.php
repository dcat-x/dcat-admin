<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Support\DataPermission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;

class DataPermissionCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => Administrator::class,
        ]);

        DataPermission::clearCache();
    }

    protected function tearDown(): void
    {
        DataPermission::clearCache();

        parent::tearDown();
    }

    protected function createMockUser(int $id = 1)
    {
        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->id = $id;
        $user->shouldReceive('allRoles')->andReturn(collect());

        return $user;
    }

    public function test_clear_cache_resets_rules_cache(): void
    {
        $rulesCacheRef = new \ReflectionProperty(DataPermission::class, 'rulesCache');
        $rulesCacheRef->setAccessible(true);
        $rulesCacheRef->setValue(null, ['some_key' => collect(['data'])]);

        $this->assertNotEmpty($rulesCacheRef->getValue());

        DataPermission::clearCache();

        $this->assertEmpty($rulesCacheRef->getValue());
    }

    public function test_cache_invalidates_on_new_request_object(): void
    {
        // 第一个请求
        $request1 = Request::create('/admin/first', 'GET');
        $this->app->instance('request', $request1);

        $rulesCacheRef = new \ReflectionProperty(DataPermission::class, 'rulesCache');
        $rulesCacheRef->setAccessible(true);

        $hashRef = new \ReflectionProperty(DataPermission::class, 'cacheRequestHash');
        $hashRef->setAccessible(true);

        // 模拟已有缓存数据和对应的请求哈希
        $rulesCacheRef->setValue(null, ['old_data' => collect()]);
        $hashRef->setValue(null, spl_object_id($request1));

        $this->assertNotEmpty($rulesCacheRef->getValue());

        // 模拟新请求
        $request2 = Request::create('/admin/second', 'GET');
        $this->app->instance('request', $request2);
        $this->assertNotEquals(spl_object_id($request1), spl_object_id($request2));

        // 使用带用户的 DataPermission 来触发缓存检查
        // （没有用户时 getRulesForMenu 在缓存检查前就返回了）
        $user = $this->createMockUser();
        $dp = new DataPermission($user);
        $dp->getRulesForMenu(1);

        // 旧缓存应该已被清除
        $cache = $rulesCacheRef->getValue();
        $this->assertArrayNotHasKey('old_data', $cache);

        // 新请求哈希应更新
        $this->assertSame(spl_object_id($request2), $hashRef->getValue());
    }

    public function test_cache_persists_within_same_request(): void
    {
        $request = Request::create('/admin/test', 'GET');
        $this->app->instance('request', $request);

        $hashRef = new \ReflectionProperty(DataPermission::class, 'cacheRequestHash');
        $hashRef->setAccessible(true);
        $hashRef->setValue(null, spl_object_id($request));

        $rulesCacheRef = new \ReflectionProperty(DataPermission::class, 'rulesCache');
        $rulesCacheRef->setAccessible(true);

        // 预设缓存
        $cacheKey = '1_99';
        $cachedResult = collect(['cached_rule']);
        $rulesCacheRef->setValue(null, [$cacheKey => $cachedResult]);

        // 同一请求内调用 getRulesForMenu — 应命中缓存
        $user = $this->createMockUser(1);
        $dp = new DataPermission($user);
        $result = $dp->getRulesForMenu(99);

        // 返回的应该是缓存的集合
        $this->assertSame($cachedResult, $result);
        $this->assertCount(1, $result);
        $this->assertSame('cached_rule', $result->first());
    }

    public function test_cache_key_is_user_and_menu_specific(): void
    {
        $request = Request::create('/admin/test', 'GET');
        $this->app->instance('request', $request);

        $hashRef = new \ReflectionProperty(DataPermission::class, 'cacheRequestHash');
        $hashRef->setAccessible(true);
        $hashRef->setValue(null, spl_object_id($request));

        $rulesCacheRef = new \ReflectionProperty(DataPermission::class, 'rulesCache');
        $rulesCacheRef->setAccessible(true);

        // 为用户 1 菜单 10 设置缓存
        $cache10 = collect(['rule_for_menu_10']);
        $cache20 = collect(['rule_for_menu_20']);
        $rulesCacheRef->setValue(null, [
            '1_10' => $cache10,
            '1_20' => $cache20,
        ]);

        $user = $this->createMockUser(1);
        $dp = new DataPermission($user);

        // 不同的菜单 ID 应返回不同的缓存结果
        $this->assertSame($cache10, $dp->getRulesForMenu(10));
        $this->assertSame($cache20, $dp->getRulesForMenu(20));
    }

    public function test_make_factory_method(): void
    {
        $dp = DataPermission::make(null);

        $this->assertInstanceOf(DataPermission::class, $dp);
    }
}
