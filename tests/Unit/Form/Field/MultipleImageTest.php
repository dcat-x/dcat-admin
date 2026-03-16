<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Image;
use Dcat\Admin\Form\Field\MultipleImage;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery;

class MultipleImageTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMultipleImageField(string $column = 'photos', string $label = 'Photos'): MultipleImage
    {
        $field = new MultipleImage($column, [$label]);

        $storage = Mockery::mock(Filesystem::class);
        $storage->shouldReceive('exists')->andReturn(false)->byDefault();
        $storage->shouldReceive('delete')->andReturn(true)->byDefault();
        $storage->shouldReceive('url')->andReturn('')->byDefault();

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        return $field;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // instanceof & inheritance
    // -------------------------------------------------------

    public function test_is_instance_of_image(): void
    {
        $field = $this->createMultipleImageField();

        $this->assertInstanceOf(Image::class, $field);
    }

    public function test_view_is_admin_form_file(): void
    {
        $field = $this->createMultipleImageField();

        $view = $this->getProtectedProperty($field, 'view');

        $this->assertSame('admin::form.file', $view);
    }

    public function test_image_options_are_set(): void
    {
        $field = $this->createMultipleImageField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertTrue($options['isImage']);
        $this->assertSame('image/*', $options['accept']['mimeTypes']);
    }

    // -------------------------------------------------------
    // sortable()
    // -------------------------------------------------------

    public function test_sortable_enables_sorting(): void
    {
        $field = $this->createMultipleImageField();

        $result = $field->sortable();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['sortable']);
    }

    public function test_sortable_can_be_disabled(): void
    {
        $field = $this->createMultipleImageField();

        $field->sortable(true);
        $field->sortable(false);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertFalse($options['sortable']);
    }

    // -------------------------------------------------------
    // limit()
    // -------------------------------------------------------

    public function test_limit_sets_file_num_limit(): void
    {
        $field = $this->createMultipleImageField();

        $result = $field->limit(10);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(10, $options['fileNumLimit']);
    }

    public function test_limit_ignores_values_less_than_two(): void
    {
        $field = $this->createMultipleImageField();

        $optionsBefore = $this->getProtectedProperty($field, 'options');
        $originalLimit = $optionsBefore['fileNumLimit'];

        $field->limit(1);

        $options = $this->getProtectedProperty($field, 'options');
        // limit < 2 should not change the existing fileNumLimit
        $this->assertSame($originalLimit, $options['fileNumLimit']);
    }

    // -------------------------------------------------------
    // forceOptions()
    // -------------------------------------------------------

    public function test_force_options_does_not_override_file_num_limit(): void
    {
        $field = $this->createMultipleImageField();

        // Set a custom limit first
        $field->limit(8);

        $reflection = new \ReflectionMethod($field, 'forceOptions');
        $reflection->setAccessible(true);
        $reflection->invoke($field);

        $options = $this->getProtectedProperty($field, 'options');
        // MultipleImage overrides forceOptions to be empty, so fileNumLimit should NOT be forced to 1
        $this->assertSame(8, $options['fileNumLimit']);
    }
}
