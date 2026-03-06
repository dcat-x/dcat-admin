<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\AdminServiceProvider;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AdminServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);

        $this->assertSame(AdminServiceProvider::class, $ref->getName());
    }

    public function test_extends_service_provider(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);
        $this->assertTrue($ref->isSubclassOf(\Illuminate\Support\ServiceProvider::class));
    }

    public function test_commands_property_is_protected_array(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'commands');
        $this->assertTrue($prop->isProtected());
        $defaultValue = $prop->getDefaultValue();
        $this->assertIsArray($defaultValue);
        $this->assertNotEmpty($defaultValue);
    }

    public function test_commands_property_contains_expected_commands(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'commands');
        $commands = $prop->getDefaultValue();

        $this->assertContains(\Dcat\Admin\Console\AdminCommand::class, $commands);
        $this->assertContains(\Dcat\Admin\Console\InstallCommand::class, $commands);
        $this->assertContains(\Dcat\Admin\Console\PublishCommand::class, $commands);
        $this->assertContains(\Dcat\Admin\Console\CreateUserCommand::class, $commands);
    }

    public function test_boot_method_is_public(): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, 'boot');
        $this->assertTrue($method->isPublic());
    }

    public function test_register_method_is_public(): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, 'register');
        $this->assertTrue($method->isPublic());
    }

    public function test_register_services_method_is_public(): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, 'registerServices');
        $this->assertTrue($method->isPublic());
    }

    public function test_register_route_middleware_is_protected(): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, 'registerRouteMiddleware');
        $this->assertTrue($method->isProtected());
    }

    public function test_route_middleware_property_is_protected_array(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'routeMiddleware');
        $this->assertTrue($prop->isProtected());
        $defaultValue = $prop->getDefaultValue();
        $this->assertIsArray($defaultValue);
        $this->assertArrayHasKey('admin.auth', $defaultValue);
        $this->assertArrayHasKey('admin.pjax', $defaultValue);
        $this->assertArrayHasKey('admin.permission', $defaultValue);
    }

    public function test_middleware_groups_property_is_protected_array(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'middlewareGroups');
        $this->assertTrue($prop->isProtected());
        $defaultValue = $prop->getDefaultValue();
        $this->assertIsArray($defaultValue);
        $this->assertArrayHasKey('admin', $defaultValue);
    }

    public function test_dev_commands_property_is_protected_array(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'devCommands');
        $this->assertTrue($prop->isProtected());
        $defaultValue = $prop->getDefaultValue();
        $this->assertIsArray($defaultValue);
    }

    public function test_register_services_binds_expected_singletons(): void
    {
        $provider = new AdminServiceProvider($this->app);
        $provider->registerServices();

        $this->assertTrue($this->app->bound('admin.asset'));
        $this->assertTrue($this->app->bound('admin.menu'));
        $this->assertTrue($this->app->bound('admin.navbar'));
        $this->assertTrue($this->app->bound('admin.setting'));
        $this->assertTrue($this->app->bound('admin.web-uploader'));
        $this->assertTrue($this->app->bound('admin.translator'));
    }

    public function test_register_route_middleware_adds_aliases_and_group(): void
    {
        config()->set('admin.permission.enable', true);

        $provider = new TestableAdminServiceProvider($this->app);
        $provider->exposeRegisterRouteMiddleware();

        $router = $this->app->make('router');
        $aliases = $router->getMiddleware();
        $groups = $router->getMiddlewareGroups();

        $this->assertArrayHasKey('admin.auth', $aliases);
        $this->assertArrayHasKey('admin.permission', $aliases);
        $this->assertArrayHasKey('admin', $groups);
        $this->assertContains('admin.permission', $groups['admin']);
    }

    public function test_register_route_middleware_removes_permission_middleware_when_disabled(): void
    {
        config()->set('admin.permission.enable', false);

        $provider = new TestableAdminServiceProvider($this->app);
        $provider->exposeRegisterRouteMiddleware();

        $groups = $this->app->make('router')->getMiddlewareGroups();

        $this->assertArrayHasKey('admin', $groups);
        $this->assertNotContains('admin.permission', $groups['admin']);
    }

    public function test_is_not_abstract(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);
        $this->assertFalse($ref->isAbstract());
    }
}

class TestableAdminServiceProvider extends AdminServiceProvider
{
    public function exposeRegisterRouteMiddleware(): void
    {
        $this->registerRouteMiddleware();
    }
}
