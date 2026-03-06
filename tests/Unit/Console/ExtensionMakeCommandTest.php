<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionMakeCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

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

        $this->assertEquals('Build a dcat-admin extension', $defaultValue);
    }

    public function test_base_path_default_is_empty_string(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'basePath');

        $this->assertTrue($ref->isProtected());
        $this->assertEquals('', $ref->getDefaultValue());
    }

    public function test_dirs_is_array_with_expected_entries(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'dirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);

        $expectedDirs = [
            'updates',
            'resources/assets/css',
            'resources/assets/js',
            'resources/views',
            'resources/lang',
            'src/Models',
            'src/Http/Controllers',
            'src/Http/Middleware',
        ];

        foreach ($expectedDirs as $dir) {
            $this->assertContains($dir, $defaultValue, "dirs should contain '{$dir}'");
        }
    }

    public function test_theme_dirs_is_array_with_expected_entries(): void
    {
        $ref = new \ReflectionProperty(ExtensionMakeCommand::class, 'themeDirs');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);

        $expectedDirs = [
            'updates',
            'resources/assets/css',
            'resources/views',
            'src',
        ];

        foreach ($expectedDirs as $dir) {
            $this->assertContains($dir, $defaultValue, "themeDirs should contain '{$dir}'");
        }
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

    public function test_has_all_required_methods(): void
    {
        $methods = [
            'handle',
            'showTree',
            'makeFiles',
            'makeProviderContent',
            'makeRegisterThemeContent',
            'copyFiles',
            'getRootNameSpace',
            'getClassName',
            'makeDirs',
            'extensionPath',
            'putFile',
            'copy',
            'makeDir',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(ExtensionMakeCommand::class, $method),
                "ExtensionMakeCommand should have method '{$method}'"
            );
        }
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_root_namespace_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, 'getRootNameSpace');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_class_name_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, 'getClassName');

        $this->assertTrue($ref->isProtected());
    }

    public function test_extension_path_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExtensionMakeCommand::class, 'extensionPath');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_protected_properties(): void
    {
        $protectedProperties = [
            'basePath',
            'filesystem',
            'namespace',
            'className',
            'extensionName',
            'package',
            'extensionDir',
            'dirs',
            'themeDirs',
        ];

        foreach ($protectedProperties as $property) {
            $this->assertTrue(
                property_exists(ExtensionMakeCommand::class, $property),
                "ExtensionMakeCommand should have property '{$property}'"
            );

            $ref = new \ReflectionProperty(ExtensionMakeCommand::class, $property);
            $this->assertTrue(
                $ref->isProtected(),
                "Property '{$property}' should be protected"
            );
        }
    }
}
