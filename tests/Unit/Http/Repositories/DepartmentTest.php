<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Department;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DepartmentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Department::class));
    }

    public function test_is_subclass_of_eloquent_repository(): void
    {
        $this->assertTrue(is_subclass_of(Department::class, EloquentRepository::class));
    }

    public function test_constructor_exists(): void
    {
        $this->assertTrue(method_exists(Department::class, '__construct'));
    }
}
