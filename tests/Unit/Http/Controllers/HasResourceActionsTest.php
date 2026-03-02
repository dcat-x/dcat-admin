<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\HasResourceActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasResourceActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasResourceActions::class));
    }

    public function test_method_update_exists(): void
    {
        $this->assertTrue(method_exists(HasResourceActions::class, 'update'));
    }

    public function test_method_store_exists(): void
    {
        $this->assertTrue(method_exists(HasResourceActions::class, 'store'));
    }

    public function test_method_destroy_exists(): void
    {
        $this->assertTrue(method_exists(HasResourceActions::class, 'destroy'));
    }

    public function test_update_is_public(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'update');

        $this->assertTrue($ref->isPublic());
    }

    public function test_update_has_one_parameter(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'update');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('id', $params[0]->getName());
    }

    public function test_store_is_public(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'store');

        $this->assertTrue($ref->isPublic());
    }

    public function test_store_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'store');

        $this->assertCount(0, $ref->getParameters());
    }

    public function test_destroy_is_public(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'destroy');

        $this->assertTrue($ref->isPublic());
    }

    public function test_destroy_has_one_parameter(): void
    {
        $ref = new \ReflectionMethod(HasResourceActions::class, 'destroy');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('id', $params[0]->getName());
    }
}
