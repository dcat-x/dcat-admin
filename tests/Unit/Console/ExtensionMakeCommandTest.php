<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionMakeCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class ExtensionMakeCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ExtensionMakeCommand::class);

        $this->assertSame(ExtensionMakeCommand::class, $ref->getName());
    }

    public function test_extends_command(): void
    {
        $parents = class_parents(ExtensionMakeCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_contains_admin_ext_make(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-make', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_options(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('--namespace=', $defaultValue);
        $this->assertStringContainsString('--theme', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Build a dcat-admin extension', $defaultValue);
    }

    public function test_base_path_default_is_empty_string(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'basePath');

        $this->assertTrue($ref->isProtected());
        $this->assertSame('', $ref->getDefaultValue());
    }

    #[DataProvider('dirsProvider')]
    public function test_dirs_is_array_with_expected_entries(string $dir): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'dirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertContains($dir, $defaultValue, "dirs should contain '{$dir}'");
    }

    #[DataProvider('themeDirsProvider')]
    public function test_theme_dirs_is_array_with_expected_entries(string $dir): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'themeDirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertContains($dir, $defaultValue, "themeDirs should contain '{$dir}'");
    }

    public function test_dirs_count_is_eight(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'dirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertCount(8, $defaultValue);
    }

    public function test_theme_dirs_count_is_four(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'themeDirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertCount(4, $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_all_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(ExtensionMakeCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_protected_methods(string $method): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, $method);

        $this->assertTrue($ref->isProtected());
    }

    #[DataProvider('protectedPropertyProvider')]
    public function test_has_protected_properties(string $property): void
    {
        $this->assertTrue(property_exists(ExtensionMakeCommand::class, $property), "ExtensionMakeCommand should have property '{$property}'");

        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, $property);
        $this->assertTrue($ref->isProtected(), "Property '{$property}' should be protected");
    }

    public static function dirsProvider(): array
    {
        return [
            ['updates'],
            ['resources/assets/css'],
            ['resources/assets/js'],
            ['resources/views'],
            ['resources/lang'],
            ['src/Models'],
            ['src/Http/Controllers'],
            ['src/Http/Middleware'],
        ];
    }

    public static function themeDirsProvider(): array
    {
        return [
            ['updates'],
            ['resources/assets/css'],
            ['resources/views'],
            ['src'],
        ];
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['showTree'],
            ['makeFiles'],
            ['makeProviderContent'],
            ['makeRegisterThemeContent'],
            ['copyFiles'],
            ['getRootNameSpace'],
            ['getClassName'],
            ['makeDirs'],
            ['extensionPath'],
            ['putFile'],
            ['copy'],
            ['makeDir'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['getRootNameSpace'],
            ['getClassName'],
            ['extensionPath'],
        ];
    }

    public static function protectedPropertyProvider(): array
    {
        return [
            ['basePath'],
            ['filesystem'],
            ['namespace'],
            ['className'],
            ['extensionName'],
            ['package'],
            ['extensionDir'],
            ['dirs'],
            ['themeDirs'],
        ];
    }
}
