<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Application;
use Dcat\Admin\Tests\TestCase;
use ReflectionProperty;

class ApplicationTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    public function test_constructor_sets_container(): void
    {
        $app = new Application($this->app);

        $this->assertSame($this->app, $this->getProtectedProperty($app, 'container'));
    }

    public function test_default_constant(): void
    {
        $this->assertSame('admin', Application::DEFAULT);
    }

    public function test_get_name_returns_default_when_no_name_set(): void
    {
        $app = new Application($this->app);

        $this->assertSame('admin', $app->getName());
    }

    public function test_with_name_sets_name(): void
    {
        $app = new Application($this->app);
        $app->withName('backend');

        $this->assertSame('backend', $app->getName());
    }

    public function test_get_route_prefix_with_default(): void
    {
        $app = new Application($this->app);

        $this->assertSame('dcat.admin.', $app->getRoutePrefix());
    }

    public function test_get_route_prefix_with_custom_app(): void
    {
        $app = new Application($this->app);

        $this->assertSame('dcat.api.', $app->getRoutePrefix('api'));
    }

    public function test_get_route_prefix_uses_current_name(): void
    {
        $app = new Application($this->app);
        $app->withName('dashboard');

        $this->assertSame('dcat.dashboard.', $app->getRoutePrefix());
    }

    public function test_get_api_route_prefix_default(): void
    {
        $app = new Application($this->app);

        $this->assertSame('dcat.admin.dcat-api.', $app->getApiRoutePrefix());
    }

    public function test_get_api_route_prefix_with_custom_app(): void
    {
        $app = new Application($this->app);

        $this->assertSame('dcat.backend.dcat-api.', $app->getApiRoutePrefix('backend'));
    }

    public function test_get_current_api_route_prefix(): void
    {
        $app = new Application($this->app);

        $this->assertSame('dcat.admin.dcat-api.', $app->getCurrentApiRoutePrefix());
    }

    public function test_get_current_api_route_prefix_after_name_change(): void
    {
        $app = new Application($this->app);
        $app->withName('dashboard');

        $this->assertSame('dcat.dashboard.dcat-api.', $app->getCurrentApiRoutePrefix());
    }

    public function test_get_apps_returns_config(): void
    {
        $this->app['config']->set('admin.multi_app', ['api' => true, 'shop' => false]);

        $app = new Application($this->app);
        $apps = $app->getApps();

        $this->assertIsArray($apps);
        $this->assertTrue($apps['api'] ?? false);
        $this->assertFalse($apps['shop'] ?? true);
    }

    public function test_get_enabled_apps_filters_disabled(): void
    {
        $this->app['config']->set('admin.multi_app', ['api' => true, 'shop' => false]);

        $app = new Application($this->app);
        $enabled = $app->getEnabledApps();

        $this->assertTrue($enabled['api'] ?? false);
        $this->assertArrayNotHasKey('shop', $enabled);
    }

    public function test_get_apps_returns_empty_array_when_null(): void
    {
        $this->app['config']->set('admin.multi_app', null);

        $app = new Application($this->app);
        $apps = $app->getApps();

        $this->assertIsArray($apps);
        $this->assertEmpty($apps);
    }

    public function test_get_apps_caches_result(): void
    {
        $this->app['config']->set('admin.multi_app', ['api' => true]);

        $app = new Application($this->app);
        $first = $app->getApps();

        // Change config after first call - should still return cached result
        $this->app['config']->set('admin.multi_app', ['api' => true, 'shop' => true]);
        $second = $app->getApps();

        $this->assertSame($first, $second);
        $this->assertArrayNotHasKey('shop', $second);
    }

    public function test_get_enabled_apps_returns_empty_when_all_disabled(): void
    {
        $this->app['config']->set('admin.multi_app', ['api' => false, 'shop' => false]);

        $app = new Application($this->app);
        $enabled = $app->getEnabledApps();

        $this->assertEmpty($enabled);
    }
}
