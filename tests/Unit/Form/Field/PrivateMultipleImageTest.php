<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

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

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_disk_name_default_is_empty_string(): void
    {
        $reflection = new \ReflectionClass(PrivateMultipleImage::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertSame('', $defaults['diskName'] ?? null);
    }

    public function test_disk_sets_disk_name_and_returns_self(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);

        $result = $field->disk('oss-private');

        $this->assertSame($field, $result);
        $this->assertSame('oss-private', $this->getProtectedProperty($field, 'diskName'));
    }

    public function test_object_url_returns_original_path_for_valid_url(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);

        $url = 'https://cdn.example.com/path/image.jpg';

        $this->assertSame($url, $field->objectUrl($url));
    }

    public function test_object_url_uses_proxy_for_private_disk(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);
        config()->set('admin.upload.oss.private_disk', 'oss-private');

        $field->disk('oss-private');

        $result = $field->objectUrl('/foo/bar.jpg');

        $this->assertStringContainsString('dcat-api/oss/proxy/foo/bar.jpg', $result);
    }

    public function test_object_url_signature_is_expected(): void
    {
        $method = new \ReflectionMethod(PrivateMultipleImage::class, 'objectUrl');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('path', $params[0]->getName());

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('string', $returnType->getName());
    }

    public function test_sortable_updates_option_flag(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);

        $result = $field->sortable(false);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertFalse($options['sortable']);
    }

    public function test_limit_only_applies_when_at_least_two(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);
        $initialOptions = $this->getProtectedProperty($field, 'options');

        $field->limit(1);
        $optionsAfterOne = $this->getProtectedProperty($field, 'options');
        $this->assertSame($initialOptions, $optionsAfterOne);

        $field->limit(5);
        $optionsAfterFive = $this->getProtectedProperty($field, 'options');
        $this->assertSame(5, $optionsAfterFive['fileNumLimit']);
    }

    public function test_thumbnail_is_chainable(): void
    {
        $field = new PrivateMultipleImage('images', ['Images']);

        $result = $field->thumbnail('100', '120');

        $this->assertSame($field, $result);
    }
}
