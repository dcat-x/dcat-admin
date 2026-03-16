<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Form\Field\MultipleFile;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MultipleFileTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMultipleFileField(string $column = 'documents', string $label = 'Documents'): MultipleFile
    {
        $field = new MultipleFile($column, [$label]);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
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

    public function test_is_instance_of_file(): void
    {
        $field = $this->createMultipleFileField();

        $this->assertInstanceOf(File::class, $field);
    }

    public function test_view_is_admin_form_file(): void
    {
        $field = $this->createMultipleFileField();

        $view = $this->getProtectedProperty($field, 'view');

        $this->assertSame('admin::form.file', $view);
    }

    // -------------------------------------------------------
    // sortable()
    // -------------------------------------------------------

    public function test_sortable_enables_sorting(): void
    {
        $field = $this->createMultipleFileField();

        $result = $field->sortable();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['sortable']);
    }

    public function test_sortable_can_be_disabled(): void
    {
        $field = $this->createMultipleFileField();

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
        $field = $this->createMultipleFileField();

        $result = $field->limit(5);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(5, $options['fileNumLimit']);
    }

    public function test_limit_ignores_values_less_than_two(): void
    {
        $field = $this->createMultipleFileField();

        $optionsBefore = $this->getProtectedProperty($field, 'options');
        $originalLimit = $optionsBefore['fileNumLimit'];

        $result = $field->limit(1);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        // limit < 2 should not change the existing fileNumLimit
        $this->assertSame($originalLimit, $options['fileNumLimit']);
    }

    public function test_limit_ignores_zero(): void
    {
        $field = $this->createMultipleFileField();

        $optionsBefore = $this->getProtectedProperty($field, 'options');
        $originalLimit = $optionsBefore['fileNumLimit'];

        $field->limit(0);

        $options = $this->getProtectedProperty($field, 'options');
        // limit < 2 should not change the existing fileNumLimit
        $this->assertSame($originalLimit, $options['fileNumLimit']);
    }

    // -------------------------------------------------------
    // forceOptions()
    // -------------------------------------------------------

    public function test_force_options_does_not_override_file_num_limit(): void
    {
        $field = $this->createMultipleFileField();

        // Set a custom limit first
        $field->limit(5);

        $reflection = new \ReflectionMethod($field, 'forceOptions');
        $reflection->setAccessible(true);
        $reflection->invoke($field);

        $options = $this->getProtectedProperty($field, 'options');
        // MultipleFile overrides forceOptions to be empty, so fileNumLimit should NOT be forced to 1
        $this->assertSame(5, $options['fileNumLimit']);
    }
}
