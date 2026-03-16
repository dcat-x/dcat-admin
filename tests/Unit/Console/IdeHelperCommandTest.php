<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\IdeHelperCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class IdeHelperCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(IdeHelperCommand::class, new IdeHelperCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(IdeHelperCommand::class);

        $this->assertContains(Command::class, $parents);
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

        $this->assertSame('Create the ide-helper file', $defaultValue);
    }

    public function test_patterns_is_array_with_five_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'patterns');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(5, $defaultValue);
    }

    #[DataProvider('patternKeyProvider')]
    public function test_patterns_has_expected_keys(string $key): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'patterns');
        $defaultValue = $ref->getDefaultValue();

        $this->assertContains($key, array_keys($defaultValue), "Patterns should have key '{$key}'");
    }

    public function test_templates_is_array_with_six_keys(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'templates');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(6, $defaultValue);
    }

    #[DataProvider('templateKeyProvider')]
    public function test_templates_has_expected_keys(string $key): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'templates');
        $defaultValue = $ref->getDefaultValue();

        $this->assertContains($key, array_keys($defaultValue), "Templates should have key '{$key}'");
    }

    public function test_path_default_value(): void
    {
        $ref = new \ReflectionProperty(IdeHelperCommand::class, 'path');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('dcat_admin_ide_helper.php', $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_all_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(IdeHelperCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    #[DataProvider('publicMethodProvider')]
    public function test_public_methods(string $method): void
    {
        $ref = new \ReflectionMethod(IdeHelperCommand::class, $method);

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

    public static function patternKeyProvider(): array
    {
        return [
            ['grid'],
            ['show'],
            ['grid-column'],
            ['form-field'],
            ['grid-filter'],
        ];
    }

    public static function templateKeyProvider(): array
    {
        return [
            ['grid'],
            ['show'],
            ['form'],
            ['grid-column'],
            ['grid-filter'],
            ['show-column'],
        ];
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['getFieldsFromDatabase'],
            ['getFieldsFromControllerFiles'],
            ['write'],
            ['generate'],
            ['generateGridFilters'],
            ['generateShowFields'],
            ['generateFormFields'],
            ['generateGridColumns'],
            ['getBuilderMethods'],
            ['getStub'],
            ['getAllControllers'],
            ['getClassContent'],
            ['getFileNameByClass'],
        ];
    }

    public static function publicMethodProvider(): array
    {
        return [
            ['generate'],
            ['getAllControllers'],
            ['getClassContent'],
            ['getFileNameByClass'],
        ];
    }
}
