<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\IdeHelperCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class IdeHelperCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(IdeHelperCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(IdeHelperCommand::class, Command::class));
    }

    public function test_signature_contains_admin_ide_helper(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ide-helper', $defaultValue);
    }

    public function test_signature_contains_controller_option(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('--c|controller=', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Create the ide-helper file', $defaultValue);
    }

    public function test_patterns_is_array_with_five_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'patterns');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(5, $defaultValue);
    }

    public function test_patterns_has_expected_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'patterns');
        $defaultValue = $ref->getDefaultValue();

        $expectedKeys = ['grid', 'show', 'grid-column', 'form-field', 'grid-filter'];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $defaultValue, "Patterns should have key '{$key}'");
        }
    }

    public function test_templates_is_array_with_six_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'templates');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(6, $defaultValue);
    }

    public function test_templates_has_expected_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'templates');
        $defaultValue = $ref->getDefaultValue();

        $expectedKeys = ['grid', 'show', 'form', 'grid-column', 'grid-filter', 'show-column'];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $defaultValue, "Templates should have key '{$key}'");
        }
    }

    public function test_path_default_value(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'path');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('dcat_admin_ide_helper.php', $defaultValue);
    }

    public function test_has_all_required_methods(): void
    {
        $methods = [
            'handle',
            'getFieldsFromDatabase',
            'getFieldsFromControllerFiles',
            'write',
            'generate',
            'generateGridFilters',
            'generateShowFields',
            'generateFormFields',
            'generateGridColumns',
            'getBuilderMethods',
            'getStub',
            'getAllControllers',
            'getClassContent',
            'getFileNameByClass',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(IdeHelperCommand::class, $method),
                "IdeHelperCommand should have method '{$method}'"
            );
        }
    }

    public function test_generate_is_public(): void
    {
        $ref = new \ReflectionMethod(IdeHelperCommand::class, 'generate');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_all_controllers_is_public(): void
    {
        $ref = new \ReflectionMethod(IdeHelperCommand::class, 'getAllControllers');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_class_content_is_public(): void
    {
        $ref = new \ReflectionMethod(IdeHelperCommand::class, 'getClassContent');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_file_name_by_class_is_public(): void
    {
        $ref = new \ReflectionMethod(IdeHelperCommand::class, 'getFileNameByClass');

        $this->assertTrue($ref->isPublic());
    }

    public function test_patterns_values_are_regex_strings(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'patterns');
        $defaultValue = $ref->getDefaultValue();

        foreach ($defaultValue as $key => $pattern) {
            $this->assertIsString($pattern, "Pattern '{$key}' should be a string");
            $this->assertStringStartsWith('/', $pattern, "Pattern '{$key}' should start with '/'");
        }
    }

    public function test_path_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'path');

        $this->assertTrue($ref->isProtected());
    }
}
