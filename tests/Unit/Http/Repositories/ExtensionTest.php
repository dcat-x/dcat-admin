<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Extension;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExtensionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Extension::class));
    }

    public function test_is_subclass_of_repository(): void
    {
        $this->assertTrue(is_subclass_of(Extension::class, Repository::class));
    }

    public function test_method_get_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'get'));
    }

    public function test_method_each_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'each'));
    }

    public function test_method_edit_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'edit'));
    }

    public function test_method_update_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'update'));
    }

    public function test_method_updating_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'updating'));
    }

    public function test_method_detail_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'detail'));
    }

    public function test_method_delete_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'delete'));
    }

    public function test_method_store_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'store'));
    }

    public function test_method_deleting_exists(): void
    {
        $this->assertTrue(method_exists(Extension::class, 'deleting'));
    }

    public function test_each_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(Extension::class, 'each');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_get_method_declared_in_extension(): void
    {
        $reflection = new \ReflectionMethod(Extension::class, 'get');
        $this->assertEquals(Extension::class, $reflection->getDeclaringClass()->getName());
    }
}
