<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\AliImage;
use Dcat\Admin\Form\Field\Image;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AliImageTest extends TestCase
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
        $this->assertTrue(class_exists(AliImage::class));
    }

    public function test_extends_image(): void
    {
        $this->assertTrue(is_subclass_of(AliImage::class, Image::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_object_url_method_exists(): void
    {
        $this->assertTrue(method_exists(AliImage::class, 'objectUrl'));
    }

    // -------------------------------------------------------
    // Method visibility and signature
    // -------------------------------------------------------

    public function test_object_url_is_public(): void
    {
        $method = new \ReflectionMethod(AliImage::class, 'objectUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_object_url_has_path_parameter(): void
    {
        $method = new \ReflectionMethod(AliImage::class, 'objectUrl');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('path', $params[0]->getName());
    }

    public function test_object_url_return_type_is_string(): void
    {
        $method = new \ReflectionMethod(AliImage::class, 'objectUrl');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('string', $returnType->getName());
    }

    // -------------------------------------------------------
    // objectUrl behavior: fallback to path when ali_sign_url not defined
    // -------------------------------------------------------

    public function test_object_url_returns_path_when_ali_sign_url_not_defined(): void
    {
        // ali_sign_url is unlikely to be defined in test environment;
        // if it is not, objectUrl should return the path as-is.
        if (function_exists('ali_sign_url')) {
            $this->markTestSkipped('ali_sign_url function is defined, cannot test fallback behavior.');
        }

        $field = $this->createAliImageField();
        $result = $field->objectUrl('uploads/photo.jpg');

        $this->assertSame('uploads/photo.jpg', $result);
    }

    public function test_object_url_returns_path_unchanged_for_empty_string(): void
    {
        if (function_exists('ali_sign_url')) {
            $this->markTestSkipped('ali_sign_url function is defined, cannot test fallback behavior.');
        }

        $field = $this->createAliImageField();
        $result = $field->objectUrl('');

        $this->assertSame('', $result);
    }

    // -------------------------------------------------------
    // Inheritance chain
    // -------------------------------------------------------

    public function test_inherits_default_directory_from_image(): void
    {
        $this->assertTrue(method_exists(AliImage::class, 'defaultDirectory'));
    }

    public function test_inherits_thumbnail_from_image(): void
    {
        $this->assertTrue(method_exists(AliImage::class, 'thumbnail'));
    }

    // -------------------------------------------------------
    // Helper
    // -------------------------------------------------------

    protected function createAliImageField(string $column = 'image', string $label = 'Image'): AliImage
    {
        $field = new AliImage($column, [$label]);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')->andReturn(false)->byDefault();
        $storage->shouldReceive('delete')->andReturn(true)->byDefault();
        $storage->shouldReceive('url')->andReturn('')->byDefault();

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        return $field;
    }
}
