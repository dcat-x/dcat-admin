<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Markdown;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class MarkdownTest extends TestCase
{
    protected function defaultProperties(): array
    {
        $reflection = new \ReflectionClass(Markdown::class);

        return $reflection->getDefaultProperties();
    }

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
        $defaults = $this->defaultProperties();

        $this->assertIsArray($defaults['options'] ?? null);
        $this->assertSame(500, $defaults['options']['height'] ?? null);
    }

    #[DataProvider('optionDefaultProvider')]
    public function test_options_default_values(string $key, mixed $expected): void
    {
        $defaults = $this->defaultProperties();

        $this->assertSame($expected, $defaults['options'][$key] ?? null);
    }

    #[DataProvider('defaultPropertyProvider')]
    public function test_default_properties(string $key, mixed $expected): void
    {
        $defaults = $this->defaultProperties();

        $this->assertSame($expected, $defaults[$key] ?? null);
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
        $uploadUrl = $options['imageUploadURL'] ?? '';

        $this->assertSame($field, $result);
        $this->assertStringContainsString('/custom/upload', $uploadUrl);
        $this->assertStringContainsString('disk=public', $uploadUrl);
        $this->assertStringContainsString('dir=docs%2Fimages', $uploadUrl);
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

    #[DataProvider('publicMethodProvider')]
    public function test_methods_are_public(string $methodName): void
    {
        $method = new \ReflectionMethod(Markdown::class, $methodName);
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Protected methods
    // -------------------------------------------------------

    #[DataProvider('protectedMethodProvider')]
    public function test_methods_are_protected(string $methodName): void
    {
        $method = new \ReflectionMethod(Markdown::class, $methodName);
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

    #[DataProvider('defaultLangKeyProvider')]
    public function test_default_langs_contains_expected_keys(string $lang): void
    {
        $defaults = $this->defaultProperties();

        $this->assertIsArray($defaults['defaultLangs'] ?? null);
        $this->assertIsString($defaults['defaultLangs'][$lang] ?? null);
    }

    public static function optionDefaultProvider(): array
    {
        return [
            ['height', 500],
            ['codeFold', true],
            ['saveHTMLToTextarea', true],
            ['searchReplace', true],
            ['emoji', true],
            ['taskList', true],
            ['tocm', true],
            ['tex', true],
            ['flowChart', false],
            ['sequenceDiagram', false],
            ['imageUpload', true],
            ['autoFocus', true],
        ];
    }

    public static function defaultPropertyProvider(): array
    {
        return [
            ['imageUploadDirectory', 'markdown/images'],
            ['language', null],
            ['disk', null],
        ];
    }

    public static function defaultLangKeyProvider(): array
    {
        return [
            ['en'],
            ['zh_TW'],
        ];
    }

    public static function publicMethodProvider(): array
    {
        return [
            ['htmlDecode'],
            ['height'],
            ['disk'],
            ['imageDirectory'],
            ['imageUrl'],
            ['languageUrl'],
            ['render'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['defaultImageUploadUrl'],
            ['formatUrl'],
            ['requireLang'],
        ];
    }
}
