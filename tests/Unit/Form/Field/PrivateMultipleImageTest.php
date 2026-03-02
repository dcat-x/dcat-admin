<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\MultipleImage;
use Dcat\Admin\Form\Field\PrivateMultipleImage;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class PrivateMultipleImageTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(PrivateMultipleImage::class));
    }

    public function test_extends_multiple_image(): void
    {
        $this->assertTrue(is_subclass_of(PrivateMultipleImage::class, MultipleImage::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_disk_name_default_is_empty_string(): void
    {
        $reflection = new \ReflectionClass(PrivateMultipleImage::class);
        $property = $reflection->getProperty('diskName');
        $property->setAccessible(true);

        // diskName has a typed default of '' on the class definition
        $defaults = $reflection->getDefaultProperties();
        $this->assertArrayHasKey('diskName', $defaults);
        $this->assertSame('', $defaults['diskName']);
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_disk_method_exists(): void
    {
        $this->assertTrue(method_exists(PrivateMultipleImage::class, 'disk'));
    }

    public function test_object_url_method_exists(): void
    {
        $this->assertTrue(method_exists(PrivateMultipleImage::class, 'objectUrl'));
    }

    // -------------------------------------------------------
    // Method visibility and signature
    // -------------------------------------------------------

    public function test_disk_is_public(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'disk');
        $this->assertTrue($method->isPublic());
    }

    public function test_object_url_is_public(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'objectUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_object_url_has_path_parameter(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'objectUrl');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('path', $params[0]->getName());
    }

    public function test_object_url_return_type_is_string(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'objectUrl');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('string', $returnType->getName());
    }

    public function test_disk_has_disk_parameter(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'disk');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('disk', $params[0]->getName());
    }

    // -------------------------------------------------------
    // Inheritance chain
    // -------------------------------------------------------

    public function test_inherits_sortable_from_multiple_image(): void
    {
        $this->assertTrue(method_exists(PrivateMultipleImage::class, 'sortable'));
    }

    public function test_inherits_limit_from_multiple_image(): void
    {
        $this->assertTrue(method_exists(PrivateMultipleImage::class, 'limit'));
    }

    public function test_inherits_thumbnail_from_image(): void
    {
        $this->assertTrue(method_exists(PrivateMultipleImage::class, 'thumbnail'));
    }
}
