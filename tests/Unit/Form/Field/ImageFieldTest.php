<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\ImageField;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ImageFieldTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Trait existence
    // -------------------------------------------------------

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(ImageField::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_default_directory_method_exists(): void
    {
        $this->assertTrue(method_exists(ImageField::class, 'defaultDirectory'));
    }

    public function test_call_intervention_methods_method_exists(): void
    {
        $this->assertTrue(method_exists(ImageField::class, 'callInterventionMethods'));
    }

    public function test_thumbnail_method_exists(): void
    {
        $this->assertTrue(method_exists(ImageField::class, 'thumbnail'));
    }

    public function test_destroy_thumbnail_method_exists(): void
    {
        $this->assertTrue(method_exists(ImageField::class, 'destroyThumbnail'));
    }

    public function test_upload_and_delete_original_thumbnail_method_exists(): void
    {
        $this->assertTrue(method_exists(ImageField::class, 'uploadAndDeleteOriginalThumbnail'));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_intervention_calls_default_is_empty_array(): void
    {
        $reflection = new \ReflectionClass(ImageField::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('interventionCalls', $properties);
        $this->assertSame([], $properties['interventionCalls']);
    }

    public function test_thumbnails_default_is_empty_array(): void
    {
        $reflection = new \ReflectionClass(ImageField::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('thumbnails', $properties);
        $this->assertSame([], $properties['thumbnails']);
    }

    public function test_intervention_alias_contains_filling_to_fill(): void
    {
        $reflection = new \ReflectionClass(ImageField::class);
        $property = $reflection->getProperty('interventionAlias');
        $property->setAccessible(true);

        $value = $property->getValue();

        $this->assertIsArray($value);
        $this->assertArrayHasKey('filling', $value);
        $this->assertSame('fill', $value['filling']);
    }

    // -------------------------------------------------------
    // Method visibility checks
    // -------------------------------------------------------

    public function test_default_directory_is_public(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'defaultDirectory');
        $this->assertTrue($method->isPublic());
    }

    public function test_call_intervention_methods_is_public(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'callInterventionMethods');
        $this->assertTrue($method->isPublic());
    }

    public function test_thumbnail_is_public(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'thumbnail');
        $this->assertTrue($method->isPublic());
    }

    public function test_destroy_thumbnail_is_public(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'destroyThumbnail');
        $this->assertTrue($method->isPublic());
    }

    public function test_upload_and_delete_original_thumbnail_is_protected(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'uploadAndDeleteOriginalThumbnail');
        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------
    // Parameter checks
    // -------------------------------------------------------

    public function test_thumbnail_has_three_parameters(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'thumbnail');
        $params = $method->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('name', $params[0]->getName());
        $this->assertSame('width', $params[1]->getName());
        $this->assertSame('height', $params[2]->getName());
    }

    public function test_call_intervention_methods_has_two_parameters(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'callInterventionMethods');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('target', $params[0]->getName());
        $this->assertSame('mime', $params[1]->getName());
    }

    public function test_destroy_thumbnail_has_two_parameters(): void
    {
        $method = new \ReflectionMethod(ImageField::class, 'destroyThumbnail');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('file', $params[0]->getName());
        $this->assertSame('force', $params[1]->getName());
    }
}
