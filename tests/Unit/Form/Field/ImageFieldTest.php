<?php

declare(strict_types=1);

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

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_intervention_calls_and_thumbnails_default_empty(): void
    {
        $reflection = new \ReflectionClass(ImageField::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertSame([], $properties['interventionCalls']);
        $this->assertSame([], $properties['thumbnails']);
    }

    public function test_intervention_alias_contains_filling_to_fill(): void
    {
        $reflection = new \ReflectionClass(ImageField::class);
        $property = $reflection->getProperty('interventionAlias');
        $property->setAccessible(true);

        $value = $property->getValue();

        $this->assertSame('fill', $value['filling'] ?? null);
    }

    public function test_default_directory_reads_admin_upload_image_directory_config(): void
    {
        config()->set('admin.upload.directory.image', 'images/custom');

        $field = new ImageFieldTestDouble;

        $this->assertSame('images/custom', $field->defaultDirectory());
    }

    public function test_thumbnail_accepts_single_definition(): void
    {
        $field = new ImageFieldTestDouble;

        $result = $field->thumbnail('small', 100, 80);

        $this->assertSame($field, $result);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');
        $this->assertSame([100, 80], $thumbnails['small']);
    }

    public function test_thumbnail_accepts_batch_definitions_and_ignores_invalid_items(): void
    {
        $field = new ImageFieldTestDouble;

        $result = $field->thumbnail([
            'small' => [100, 80],
            'large' => [400, 300, 'resize'],
            'invalid' => [100],
        ]);

        $this->assertSame($field, $result);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');
        $this->assertSame([100, 80], $thumbnails['small'] ?? null);
        $this->assertSame([400, 300, 'resize'], $thumbnails['large'] ?? null);
        $this->assertArrayNotHasKey('invalid', $thumbnails);
    }

    public function test_call_intervention_methods_returns_target_when_no_calls_defined(): void
    {
        $field = new ImageFieldTestDouble;

        $target = '/tmp/demo.jpg';

        $this->assertSame($target, $field->callInterventionMethods($target, 'image/jpeg'));
    }

    public function test_destroy_thumbnail_returns_early_when_retainable_and_not_forced(): void
    {
        $field = new ImageFieldTestDouble;
        $field->retainable = true;
        $field->thumbnail('small', 100, 80);

        $field->destroyThumbnail('avatar.jpg');

        $this->assertSame([], $field->storage->deleted);
    }

    public function test_destroy_thumbnail_deletes_existing_thumbnail_files(): void
    {
        $field = new ImageFieldTestDouble;
        $field->thumbnail('small', 100, 80)
            ->thumbnail('large', 400, 300);

        $field->storage->existing = [
            'avatar-small.jpg',
            'avatar-large.jpg',
        ];

        $field->destroyThumbnail('avatar.jpg', true);

        $this->assertSame(['avatar-small.jpg', 'avatar-large.jpg'], $field->storage->deleted);
    }

    public function test_destroy_thumbnail_accepts_array_input(): void
    {
        $field = new ImageFieldTestDouble;
        $field->thumbnail('small', 100, 80);
        $field->storage->existing = ['a-small.jpg', 'b-small.jpg'];

        $field->destroyThumbnail(['a.jpg', 'b.jpg'], true);

        $this->assertSame(['a-small.jpg', 'b-small.jpg'], $field->storage->deleted);
    }

    public function test_method_signatures_are_expected(): void
    {
        $thumbnail = new \ReflectionMethod(ImageField::class, 'thumbnail');
        $callIntervention = new \ReflectionMethod(ImageField::class, 'callInterventionMethods');
        $destroyThumbnail = new \ReflectionMethod(ImageField::class, 'destroyThumbnail');

        $this->assertCount(3, $thumbnail->getParameters());
        $this->assertCount(2, $callIntervention->getParameters());
        $this->assertCount(2, $destroyThumbnail->getParameters());
        $this->assertTrue((new \ReflectionMethod(ImageField::class, 'uploadAndDeleteOriginalThumbnail'))->isProtected());
    }
}

class ImageFieldTestDouble
{
    use ImageField;

    public array $original = [];

    public bool $retainable = false;

    public string $name = 'avatar.jpg';

    public ?string $storagePermission = null;

    public FakeImageFieldStorage $storage;

    public function __construct()
    {
        $this->storage = new FakeImageFieldStorage;
    }

    public function getStorage(): FakeImageFieldStorage
    {
        return $this->storage;
    }

    public function getDirectory(): string
    {
        return 'uploads';
    }

    public static function hasMacro($method): bool
    {
        return false;
    }
}

class FakeImageFieldStorage
{
    public array $existing = [];

    public array $deleted = [];

    public function exists(string $path): bool
    {
        return in_array($path, $this->existing, true);
    }

    public function delete(string $path): void
    {
        $this->deleted[] = $path;
    }
}
