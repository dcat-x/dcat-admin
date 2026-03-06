<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\DataRule;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DataRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.data_rules_model', \Dcat\Admin\Models\DataRule::class);
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
        $repository = new DataRule;

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function test_constructor_sets_eloquent_class_from_config(): void
    {
        $repository = new DataRule;

        $this->assertSame(
            \Dcat\Admin\Models\DataRule::class,
            $this->getProtectedProperty($repository, 'eloquentClass')
        );
    }

    public function test_constructor_signature_accepts_optional_relations_array(): void
    {
        $reflection = new \ReflectionMethod(DataRule::class, '__construct');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('relations', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertSame([], $params[0]->getDefaultValue());
    }
}
