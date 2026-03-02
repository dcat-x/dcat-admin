<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\HasNestedResource;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasNestedResourceTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasNestedResource::class));
    }

    public function test_method_show_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'show'));
    }

    public function test_method_edit_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'edit'));
    }

    public function test_method_update_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'update'));
    }

    public function test_method_destroy_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'destroy'));
    }

    public function test_method_get_nested_resource_id_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'getNestedResourceId'));
    }

    public function test_method_set_nested_resource_id_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'setNestedResourceId'));
    }

    public function test_method_get_route_parameter_name_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'getRouteParameterName'));
    }

    public function test_method_set_route_parameter_name_exists(): void
    {
        $this->assertTrue(method_exists(HasNestedResource::class, 'setRouteParameterName'));
    }

    public function test_nested_resource_id_property_exists(): void
    {
        $ref = new \ReflectionClass(HasNestedResource::class);

        $this->assertTrue($ref->hasProperty('nestedResourceId'));
    }

    public function test_nested_resource_id_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasNestedResource::class, 'nestedResourceId');

        $this->assertTrue($ref->isProtected());
    }

    public function test_route_parameter_name_property_exists(): void
    {
        $ref = new \ReflectionClass(HasNestedResource::class);

        $this->assertTrue($ref->hasProperty('routeParameterName'));
    }

    public function test_route_parameter_name_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasNestedResource::class, 'routeParameterName');

        $this->assertTrue($ref->isProtected());
    }

    public function test_show_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'show');

        $this->assertTrue($ref->isPublic());
    }

    public function test_edit_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'edit');

        $this->assertTrue($ref->isPublic());
    }

    public function test_update_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'update');

        $this->assertTrue($ref->isPublic());
    }

    public function test_destroy_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'destroy');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_nested_resource_id_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'getNestedResourceId');

        $this->assertTrue($ref->isPublic());
    }

    public function test_set_nested_resource_id_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'setNestedResourceId');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_route_parameter_name_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'getRouteParameterName');

        $this->assertTrue($ref->isPublic());
    }

    public function test_set_route_parameter_name_is_public(): void
    {
        $ref = new \ReflectionMethod(HasNestedResource::class, 'setRouteParameterName');

        $this->assertTrue($ref->isPublic());
    }
}
