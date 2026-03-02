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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AdminServiceProvider::class));
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

    public function test_method_exists_register(): void
    {
        $this->assertTrue(method_exists(AdminServiceProvider::class, 'register'));
    }

    public function test_method_exists_boot(): void
    {
        $this->assertTrue(method_exists(AdminServiceProvider::class, 'boot'));
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

    public function test_has_register_services_method(): void
    {
        $this->assertTrue(method_exists(AdminServiceProvider::class, 'registerServices'));
    }

    public function test_register_services_method_is_public(): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, 'registerServices');
        $this->assertTrue($method->isPublic());
    }

    public function test_has_register_route_middleware_method(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);
        $this->assertTrue($ref->hasMethod('registerRouteMiddleware'));
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

    public function test_has_register_extensions_method(): void
    {
        $this->assertTrue(method_exists(AdminServiceProvider::class, 'registerExtensions'));
    }

    public function test_has_boot_extensions_method(): void
    {
        $this->assertTrue(method_exists(AdminServiceProvider::class, 'bootExtensions'));
    }

    public function test_is_not_abstract(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);
        $this->assertFalse($ref->isAbstract());
    }
}
