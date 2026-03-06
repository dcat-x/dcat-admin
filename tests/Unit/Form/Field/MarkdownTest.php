<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Markdown;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MarkdownTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_options_default_has_height_500(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('options', $defaults);
        $this->assertArrayHasKey('height', $defaults['options']);
        $this->assertSame(500, $defaults['options']['height']);
    }

    public function test_options_default_has_code_fold_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['codeFold']);
    }

    public function test_options_default_has_save_html_to_textarea_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['saveHTMLToTextarea']);
    }

    public function test_options_default_has_search_replace_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['searchReplace']);
    }

    public function test_options_default_has_emoji_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['emoji']);
    }

    public function test_options_default_has_task_list_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['taskList']);
    }

    public function test_options_default_has_tocm_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['tocm']);
    }

    public function test_options_default_has_tex_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['tex']);
    }

    public function test_options_default_has_flow_chart_false(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertFalse($defaults['options']['flowChart']);
    }

    public function test_options_default_has_sequence_diagram_false(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertFalse($defaults['options']['sequenceDiagram']);
    }

    public function test_options_default_has_image_upload_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['imageUpload']);
    }

    public function test_options_default_has_auto_focus_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['autoFocus']);
    }

    public function test_image_upload_directory_default_is_markdown_images(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('imageUploadDirectory', $defaults);
        $this->assertSame('markdown/images', $defaults['imageUploadDirectory']);
    }

    public function test_language_default_is_null(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('language', $defaults);
        $this->assertNull($defaults['language']);
    }

    public function test_disk_default_is_null(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('disk', $defaults);
        $this->assertNull($defaults['disk']);
    }

    public function test_html_decode_updates_options_and_returns_self(): void
    {
        $field = new Markdown('content');

        $result = $field->htmlDecode('style,script,iframe');

        $reflection = new \ReflectionProperty($field, 'options');
        $reflection->setAccessible(true);
        $options = $reflection->getValue($field);

        $this->assertSame($field, $result);
        $this->assertSame('style,script,iframe', $options['htmlDecode']);
    }

    public function test_height_updates_options_and_returns_self(): void
    {
        $field = new Markdown('content');

        $result = $field->height(680);

        $reflection = new \ReflectionProperty($field, 'options');
        $reflection->setAccessible(true);
        $options = $reflection->getValue($field);

        $this->assertSame($field, $result);
        $this->assertSame(680, $options['height']);
    }

    public function test_disk_updates_property_and_returns_self(): void
    {
        $field = new Markdown('content');

        $result = $field->disk('s3');

        $reflection = new \ReflectionProperty($field, 'disk');
        $reflection->setAccessible(true);

        $this->assertSame($field, $result);
        $this->assertSame('s3', $reflection->getValue($field));
    }

    public function test_image_directory_updates_property_and_returns_self(): void
    {
        $field = new Markdown('content');

        $result = $field->imageDirectory('custom/markdown');

        $reflection = new \ReflectionProperty($field, 'imageUploadDirectory');
        $reflection->setAccessible(true);

        $this->assertSame($field, $result);
        $this->assertSame('custom/markdown', $reflection->getValue($field));
    }

    public function test_image_url_sets_formatted_upload_url(): void
    {
        $field = new Markdown('content');
        $field->disk('public')->imageDirectory('docs/images');

        $result = $field->imageUrl('/custom/upload');

        $reflection = new \ReflectionProperty($field, 'options');
        $reflection->setAccessible(true);
        $options = $reflection->getValue($field);

        $this->assertSame($field, $result);
        $this->assertArrayHasKey('imageUploadURL', $options);
        $this->assertStringContainsString('/custom/upload', $options['imageUploadURL']);
        $this->assertStringContainsString('disk=public', $options['imageUploadURL']);
        $this->assertStringContainsString('dir=docs%2Fimages', $options['imageUploadURL']);
    }

    public function test_language_url_updates_language_property_and_returns_self(): void
    {
        $field = new Markdown('content');

        $result = $field->languageUrl('/lang/editor-md/ja.js');

        $reflection = new \ReflectionProperty($field, 'language');
        $reflection->setAccessible(true);

        $this->assertSame($field, $result);
        $this->assertSame('/lang/editor-md/ja.js', $reflection->getValue($field));
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_html_decode_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'htmlDecode');
        $this->assertTrue($method->isPublic());
    }

    public function test_height_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'height');
        $this->assertTrue($method->isPublic());
    }

    public function test_disk_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'disk');
        $this->assertTrue($method->isPublic());
    }

    public function test_image_directory_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageDirectory');
        $this->assertTrue($method->isPublic());
    }

    public function test_image_url_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_language_url_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'languageUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'render');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Protected methods
    // -------------------------------------------------------

    public function test_default_image_upload_url_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'defaultImageUploadUrl');
        $this->assertTrue($method->isProtected());
    }

    public function test_format_url_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'formatUrl');
        $this->assertTrue($method->isProtected());
    }

    public function test_require_lang_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'requireLang');
        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------
    // Parameter checks
    // -------------------------------------------------------

    public function test_html_decode_has_decode_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'htmlDecode');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('decode', $params[0]->getName());
    }

    public function test_height_has_height_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'height');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('height', $params[0]->getName());
    }

    public function test_disk_has_string_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'disk');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('disk', $params[0]->getName());
    }

    public function test_image_directory_has_dir_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageDirectory');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('dir', $params[0]->getName());
    }

    public function test_default_langs_contains_en(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('defaultLangs', $defaults);
        $this->assertArrayHasKey('en', $defaults['defaultLangs']);
    }

    public function test_default_langs_contains_zh_tw(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('zh_TW', $defaults['defaultLangs']);
    }
}
