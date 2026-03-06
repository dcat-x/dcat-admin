<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Editor;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EditorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createEditor(string $column = 'content', string $label = 'Content'): Editor
    {
        return new Editor($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // Construction & defaults
    // -------------------------------------------------------

    public function test_default_options_contain_plugins(): void
    {
        $editor = $this->createEditor();

        $options = $this->getProtectedProperty($editor, 'options');

        $plugins = $options['plugins'] ?? null;
        $this->assertIsArray($plugins);
        $this->assertContains('advlist', $plugins);
        $this->assertContains('autolink', $plugins);
        $this->assertContains('link', $plugins);
        $this->assertContains('image', $plugins);
        $this->assertContains('table', $plugins);
        $this->assertContains('code', $plugins);
    }

    public function test_default_options_contain_toolbar(): void
    {
        $editor = $this->createEditor();

        $options = $this->getProtectedProperty($editor, 'options');

        $toolbar = $options['toolbar'] ?? null;
        $this->assertIsArray($toolbar);
        $this->assertCount(2, $toolbar);
    }

    public function test_default_min_height(): void
    {
        $editor = $this->createEditor();

        $options = $this->getProtectedProperty($editor, 'options');

        $this->assertSame(400, $options['min_height']);
    }

    public function test_default_save_enablewhendirty(): void
    {
        $editor = $this->createEditor();

        $options = $this->getProtectedProperty($editor, 'options');

        $this->assertTrue($options['save_enablewhendirty']);
    }

    public function test_default_convert_urls_is_false(): void
    {
        $editor = $this->createEditor();

        $options = $this->getProtectedProperty($editor, 'options');

        $this->assertFalse($options['convert_urls']);
    }

    public function test_default_image_upload_directory(): void
    {
        $editor = $this->createEditor();

        $dir = $this->getProtectedProperty($editor, 'imageUploadDirectory');

        $this->assertSame('tinymce/images', $dir);
    }

    public function test_default_disk_is_null(): void
    {
        $editor = $this->createEditor();

        $disk = $this->getProtectedProperty($editor, 'disk');

        $this->assertNull($disk);
    }

    // -------------------------------------------------------
    // disk()
    // -------------------------------------------------------

    public function test_disk_sets_value(): void
    {
        $editor = $this->createEditor();

        $result = $editor->disk('public');

        $this->assertSame($editor, $result);
        $this->assertSame('public', $this->getProtectedProperty($editor, 'disk'));
    }

    public function test_disk_can_be_changed(): void
    {
        $editor = $this->createEditor();

        $editor->disk('public');
        $editor->disk('s3');

        $this->assertSame('s3', $this->getProtectedProperty($editor, 'disk'));
    }

    // -------------------------------------------------------
    // imageDirectory()
    // -------------------------------------------------------

    public function test_image_directory_sets_value(): void
    {
        $editor = $this->createEditor();

        $result = $editor->imageDirectory('uploads/editor');

        $this->assertSame($editor, $result);
        $this->assertSame('uploads/editor', $this->getProtectedProperty($editor, 'imageUploadDirectory'));
    }

    public function test_image_directory_overwrites_previous(): void
    {
        $editor = $this->createEditor();

        $editor->imageDirectory('dir1');
        $editor->imageDirectory('dir2');

        $this->assertSame('dir2', $this->getProtectedProperty($editor, 'imageUploadDirectory'));
    }

    // -------------------------------------------------------
    // height()
    // -------------------------------------------------------

    public function test_height_sets_min_height_option(): void
    {
        $editor = $this->createEditor();

        $result = $editor->height(600);

        $this->assertSame($editor, $result);
        $options = $this->getProtectedProperty($editor, 'options');
        $this->assertSame(600, $options['min_height']);
    }

    public function test_height_overwrites_default(): void
    {
        $editor = $this->createEditor();

        $editor->height(200);

        $options = $this->getProtectedProperty($editor, 'options');
        $this->assertSame(200, $options['min_height']);
    }

    // -------------------------------------------------------
    // languageUrl()
    // -------------------------------------------------------

    public function test_language_url_sets_option(): void
    {
        $editor = $this->createEditor();

        $result = $editor->languageUrl('/langs/zh_CN.js');

        $this->assertSame($editor, $result);
        $options = $this->getProtectedProperty($editor, 'options');
        $this->assertSame('/langs/zh_CN.js', $options['language_url']);
    }

    // -------------------------------------------------------
    // Chaining
    // -------------------------------------------------------

    public function test_methods_can_be_chained(): void
    {
        $editor = $this->createEditor();

        $result = $editor
            ->disk('local')
            ->imageDirectory('images')
            ->height(500)
            ->languageUrl('/langs/en.js');

        $this->assertSame($editor, $result);
        $this->assertSame('local', $this->getProtectedProperty($editor, 'disk'));
        $this->assertSame('images', $this->getProtectedProperty($editor, 'imageUploadDirectory'));

        $options = $this->getProtectedProperty($editor, 'options');
        $this->assertSame(500, $options['min_height']);
        $this->assertSame('/langs/en.js', $options['language_url']);
    }

    // -------------------------------------------------------
    // Column & label
    // -------------------------------------------------------

    public function test_column_is_set(): void
    {
        $editor = $this->createEditor('body', 'Body');

        $this->assertSame('body', $editor->column());
    }

    public function test_label_is_set(): void
    {
        $editor = $this->createEditor('body', 'Body');

        $this->assertSame('Body', $editor->label());
    }
}
