<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\TinymceController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TinymceControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(TinymceController::class));
    }

    public function test_method_upload_exists(): void
    {
        $this->assertTrue(method_exists(TinymceController::class, 'upload'));
    }

    public function test_method_generate_new_name_exists(): void
    {
        $this->assertTrue(method_exists(TinymceController::class, 'generateNewName'));
    }

    public function test_method_disk_exists(): void
    {
        $this->assertTrue(method_exists(TinymceController::class, 'disk'));
    }

    public function test_upload_is_public(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'upload');

        $this->assertTrue($ref->isPublic());
    }

    public function test_generate_new_name_is_protected(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'generateNewName');

        $this->assertTrue($ref->isProtected());
    }

    public function test_disk_is_protected(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'disk');

        $this->assertTrue($ref->isProtected());
    }

    public function test_upload_accepts_request_parameter(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'upload');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('request', $params[0]->getName());
    }

    public function test_upload_request_parameter_is_typed(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'upload');
        $params = $ref->getParameters();

        $this->assertNotNull($params[0]->getType());
        $this->assertEquals('Illuminate\Http\Request', $params[0]->getType()->getName());
    }

    public function test_generate_new_name_accepts_uploaded_file_parameter(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'generateNewName');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('file', $params[0]->getName());
    }

    public function test_disk_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(TinymceController::class, 'disk');

        $this->assertCount(0, $ref->getParameters());
    }
}
