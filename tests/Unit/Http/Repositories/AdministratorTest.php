<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Administrator;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AdministratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.users_model', \Dcat\Admin\Models\Administrator::class);
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

    protected function callProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_is_instance_of_eloquent_repository(): void
    {
        $repository = new Administrator;

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function test_constructor_sets_eloquent_class_from_config(): void
    {
        $repository = new Administrator;

        $this->assertSame(
            \Dcat\Admin\Models\Administrator::class,
            $this->getProtectedProperty($repository, 'eloquentClass')
        );
    }

    public function test_get_method_is_declared_in_administrator_repository(): void
    {
        $reflection = new \ReflectionMethod(Administrator::class, 'get');

        $this->assertSame(Administrator::class, $reflection->getDeclaringClass()->getName());
        $this->assertCount(1, $reflection->getParameters());
        $this->assertSame('model', $reflection->getParameters()[0]->getName());
    }

    public function test_constructor_signature_accepts_optional_relations_array(): void
    {
        $reflection = new \ReflectionMethod(Administrator::class, '__construct');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('relations', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
        $this->assertSame([], $parameters[0]->getDefaultValue());
    }

    public function test_collect_role_ids_returns_unique_ids_from_mixed_items(): void
    {
        $repository = new Administrator;
        $items = collect([
            ['roles' => collect([(object) ['id' => 1], (object) ['id' => 2]])],
            ['roles' => collect([(object) ['id' => 2], (object) ['id' => 3]])],
            ['roles' => collect([['id' => 3], ['id' => 4]])],
            ['roles' => collect([(object) ['name' => 'no-id']])],
        ]);

        $ids = $this->callProtectedMethod($repository, 'collectRoleIds', [$items, 'id']);
        sort($ids);

        $this->assertSame([1, 2, 3, 4], $ids);
    }

    public function test_collect_permissions_for_roles_returns_unique_permission_ids(): void
    {
        $repository = new Administrator;
        $roles = collect([(object) ['id' => 10], ['id' => 11], (object) ['id' => 12]]);
        $permissions = collect([
            10 => [1, 2],
            11 => [2, 3],
            12 => [],
        ]);

        $result = $this->callProtectedMethod($repository, 'collectPermissionsForRoles', [$roles, 'id', $permissions]);
        sort($result);

        $this->assertSame([1, 2, 3], $result);
    }
}
