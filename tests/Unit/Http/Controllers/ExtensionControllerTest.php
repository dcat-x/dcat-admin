<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\ExtensionController;
use Dcat\Admin\Http\Controllers\HasResourceActions;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Routing\Controller;
use Mockery;

class ExtensionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionController::class));
    }

    public function test_is_subclass_of_illuminate_controller(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionController::class, Controller::class));
    }

    public function test_uses_has_resource_actions_trait(): void
    {
        $traits = class_uses_recursive(ExtensionController::class);

        $this->assertArrayHasKey(HasResourceActions::class, $traits);
    }

    public function test_uses_has_resource_actions_trait_via_reflection(): void
    {
        $ref = new \ReflectionClass(ExtensionController::class);
        $traitNames = array_map(function (\ReflectionClass $trait) {
            return $trait->getName();
        }, $ref->getTraits());

        $this->assertContains(HasResourceActions::class, $traitNames);
    }

    public function test_method_index_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'index'));
    }

    public function test_method_grid_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'grid'));
    }

    public function test_method_form_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'form'));
    }

    public function test_index_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionController::class, 'index');

        $this->assertTrue($ref->isPublic());
    }

    public function test_grid_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExtensionController::class, 'grid');

        $this->assertTrue($ref->isProtected());
    }

    public function test_form_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionController::class, 'form');

        $this->assertTrue($ref->isPublic());
    }

    public function test_inherits_update_from_trait(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'update'));
    }

    public function test_inherits_store_from_trait(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'store'));
    }

    public function test_inherits_destroy_from_trait(): void
    {
        $this->assertTrue(method_exists(ExtensionController::class, 'destroy'));
    }

    public function test_index_accepts_content_parameter(): void
    {
        $ref = new \ReflectionMethod(ExtensionController::class, 'index');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('content', $params[0]->getName());
    }
}
