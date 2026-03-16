<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\AdminServiceProvider;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('expectedCommandProvider')]
    public function test_commands_property_contains_expected_commands(string $commandClass): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'commands');
        $commands = $prop->getDefaultValue();

        $this->assertContains($commandClass, $commands);
    }

    #[DataProvider('publicMethodProvider')]
    public function test_public_methods(string $methodName): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, $methodName);
        $this->assertTrue($method->isPublic());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_protected_methods(string $methodName): void
    {
        $method = new ReflectionMethod(AdminServiceProvider::class, $methodName);
        $this->assertTrue($method->isProtected());
    }

    #[DataProvider('protectedPropertyProvider')]
    public function test_protected_array_properties(string $propertyName): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, $propertyName);
        $this->assertTrue($prop->isProtected());
        $this->assertIsArray($prop->getDefaultValue());
    }

    #[DataProvider('routeMiddlewareKeyProvider')]
    public function test_route_middleware_property_contains_expected_keys(string $key): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'routeMiddleware');
        $defaultValue = $prop->getDefaultValue();

        $this->assertContains($key, array_keys($defaultValue));
    }

    public function test_middleware_groups_property_is_protected_array(): void
    {
        $prop = new ReflectionProperty(AdminServiceProvider::class, 'middlewareGroups');
        $this->assertTrue($prop->isProtected());
        $defaultValue = $prop->getDefaultValue();
        $this->assertIsArray($defaultValue);
        $this->assertIsArray($defaultValue['admin'] ?? null);
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

        foreach (self::serviceBindingProvider() as [$binding]) {
            $this->assertTrue($this->app->bound($binding));
        }
    }

    public function test_register_route_middleware_adds_aliases_and_group(): void
    {
        config()->set('admin.permission.enable', true);

        $provider = new TestableAdminServiceProvider($this->app);
        $provider->exposeRegisterRouteMiddleware();

        $router = $this->app->make('router');
        $aliases = $router->getMiddleware();
        $groups = $router->getMiddlewareGroups();

        $this->assertArrayContainsKeys(['admin.auth', 'admin.permission'], $aliases);
        $this->assertArrayContainsKeys(['admin'], $groups);
        $this->assertContains('admin.permission', $groups['admin']);
    }

    public function test_register_route_middleware_removes_permission_middleware_when_disabled(): void
    {
        config()->set('admin.permission.enable', false);

        $provider = new TestableAdminServiceProvider($this->app);
        $provider->exposeRegisterRouteMiddleware();

        $groups = $this->app->make('router')->getMiddlewareGroups();

        $this->assertArrayContainsKeys(['admin'], $groups);
        $this->assertNotContains('admin.permission', $groups['admin']);
    }

    public function test_is_not_abstract(): void
    {
        $ref = new ReflectionClass(AdminServiceProvider::class);
        $this->assertFalse($ref->isAbstract());
    }

    public static function publicMethodProvider(): array
    {
        return [
            ['boot'],
            ['register'],
            ['registerServices'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['registerRouteMiddleware'],
        ];
    }

    public static function protectedPropertyProvider(): array
    {
        return [
            ['routeMiddleware'],
            ['middlewareGroups'],
            ['devCommands'],
        ];
    }

    public static function routeMiddlewareKeyProvider(): array
    {
        return [
            ['admin.auth'],
            ['admin.pjax'],
            ['admin.permission'],
        ];
    }

    public static function serviceBindingProvider(): array
    {
        return [
            ['admin.asset'],
            ['admin.menu'],
            ['admin.navbar'],
            ['admin.setting'],
            ['admin.web-uploader'],
            ['admin.translator'],
        ];
    }

    public static function expectedCommandProvider(): array
    {
        return [
            [\Dcat\Admin\Console\AdminCommand::class],
            [\Dcat\Admin\Console\InstallCommand::class],
            [\Dcat\Admin\Console\PublishCommand::class],
            [\Dcat\Admin\Console\CreateUserCommand::class],
        ];
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        $actualKeys = array_keys($actual);
        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $actualKeys);
        }
    }
}

class TestableAdminServiceProvider extends AdminServiceProvider
{
    public function exposeRegisterRouteMiddleware(): void
    {
        $this->registerRouteMiddleware();
    }
}
