<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Administrator;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AdministratorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Administrator::class));
    }

    public function test_is_subclass_of_eloquent_repository(): void
    {
        $this->assertTrue(is_subclass_of(Administrator::class, EloquentRepository::class));
    }

    public function test_method_get_exists(): void
    {
        $this->assertTrue(method_exists(Administrator::class, 'get'));
    }

    public function test_get_method_overrides_parent(): void
    {
        $reflection = new \ReflectionMethod(Administrator::class, 'get');
        $this->assertEquals(Administrator::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_constructor_exists(): void
    {
        $this->assertTrue(method_exists(Administrator::class, '__construct'));
    }
}
