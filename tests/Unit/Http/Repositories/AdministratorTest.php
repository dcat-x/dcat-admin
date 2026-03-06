<?php

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
}
