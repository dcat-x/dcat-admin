<?php

namespace Dcat\Admin\Tests\Unit\Http\Auth;

use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class PermissionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Permission::class));
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

    public function test_method_check_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'check'));
    }

    public function test_method_allow_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'allow'));
    }

    public function test_method_free_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'free'));
    }

    public function test_method_deny_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'deny'));
    }

    public function test_method_is_administrator_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'isAdministrator'));
    }

    public function test_method_error_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'error'));
    }

    public function test_method_register_error_handler_exists(): void
    {
        $this->assertTrue(method_exists(Permission::class, 'registerErrorHandler'));
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
        $this->assertEquals('callback', $params[0]->getName());
        $this->assertNotNull($params[0]->getType());
        $this->assertEquals('Closure', $params[0]->getType()->getName());
    }
}
