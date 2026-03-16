<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Permission;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.permissions_model', \Dcat\Admin\Models\Permission::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_is_instance_of_eloquent_repository(): void
    {
        $repository = new Permission;

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function test_constructor_sets_eloquent_class_from_config(): void
    {
        $repository = new Permission;

        $this->assertSame(
            \Dcat\Admin\Models\Permission::class,
            $this->getProtectedProperty($repository, 'eloquentClass')
        );
    }

    public function test_constructor_signature_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(Permission::class, '__construct');

        $this->assertCount(0, $reflection->getParameters());
    }
}
