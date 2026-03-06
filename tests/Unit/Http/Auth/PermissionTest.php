<?php

namespace Dcat\Admin\Tests\Unit\Http\Auth;

use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class PermissionTest extends TestCase
{
    protected function tearDown(): void
    {
        $ref = new \ReflectionProperty(Permission::class, 'errorHandler');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        parent::tearDown();
        Mockery::close();
    }

    public function test_error_handler_static_property_exists(): void
    {
        $ref = new \ReflectionClass(Permission::class);

        $this->assertTrue($ref->hasProperty('errorHandler'));
    }

    public function test_error_handler_property_is_static(): void
    {
        $ref = new \ReflectionProperty(Permission::class, 'errorHandler');

        $this->assertTrue($ref->isStatic());
    }

    public function test_error_handler_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(Permission::class, 'errorHandler');

        $this->assertTrue($ref->isProtected());
    }

    public function test_register_error_handler_updates_static_property(): void
    {
        $callback = static fn () => 'handled';

        Permission::registerErrorHandler($callback);

        $ref = new \ReflectionProperty(Permission::class, 'errorHandler');
        $ref->setAccessible(true);

        $this->assertSame($callback, $ref->getValue());
    }

    public function test_check_accepts_permission_parameter(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'check');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('permission', $parameters[0]->getName());
    }

    public function test_allow_accepts_roles_parameter(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'allow');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('roles', $parameters[0]->getName());
    }

    public function test_deny_accepts_roles_parameter(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'deny');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('roles', $parameters[0]->getName());
    }

    public function test_is_administrator_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'isAdministrator');

        $this->assertCount(0, $method->getParameters());
    }

    public function test_check_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'check');

        $this->assertTrue($ref->isStatic());
    }

    public function test_allow_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'allow');

        $this->assertTrue($ref->isStatic());
    }

    public function test_free_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'free');

        $this->assertTrue($ref->isStatic());
    }

    public function test_deny_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'deny');

        $this->assertTrue($ref->isStatic());
    }

    public function test_is_administrator_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'isAdministrator');

        $this->assertTrue($ref->isStatic());
    }

    public function test_error_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'error');

        $this->assertTrue($ref->isStatic());
    }

    public function test_register_error_handler_is_static(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'registerErrorHandler');

        $this->assertTrue($ref->isStatic());
    }

    public function test_free_returns_true(): void
    {
        $this->assertTrue(Permission::free());
    }

    public function test_all_static_methods_are_public(): void
    {
        $methods = ['check', 'allow', 'free', 'deny', 'isAdministrator', 'error', 'registerErrorHandler'];

        foreach ($methods as $method) {
            $ref = new \ReflectionMethod(Permission::class, $method);
            $this->assertTrue($ref->isPublic(), "Method {$method} should be public");
        }
    }

    public function test_register_error_handler_accepts_closure(): void
    {
        $ref = new \ReflectionMethod(Permission::class, 'registerErrorHandler');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('callback', $params[0]->getName());
        $this->assertNotNull($params[0]->getType());
        $this->assertSame('Closure', $params[0]->getType()->getName());
    }
}
