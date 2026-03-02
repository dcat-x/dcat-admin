<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Role;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RoleTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Role::class));
    }

    public function test_is_subclass_of_eloquent_repository(): void
    {
        $this->assertTrue(is_subclass_of(Role::class, EloquentRepository::class));
    }

    public function test_constructor_exists(): void
    {
        $this->assertTrue(method_exists(Role::class, '__construct'));
    }
}
