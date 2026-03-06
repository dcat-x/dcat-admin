<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\MinifyCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class MinifyCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(MinifyCommand::class, new MinifyCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(MinifyCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_contains_admin_minify(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:minify', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_options(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name}', $defaultValue);
        $this->assertStringContainsString('--color=', $defaultValue);
        $this->assertStringContainsString('--publish', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Minify the CSS and JS', $defaultValue);
    }

    public function test_all_constant_equals_all(): void
    {
        $this->assertEquals('all', MinifyCommand::ALL);
    }

    public function test_default_constant_equals_default(): void
    {
        $ref = new \ReflectionClass(MinifyCommand::class);
        $constants = $ref->getConstants();

        $this->assertContains('DEFAULT', array_keys($constants));
        $this->assertEquals('default', $constants['DEFAULT']);
    }

    public function test_colors_is_array_with_four_entries(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(4, $defaultValue);
    }

    #[DataProvider('colorProvider')]
    public function test_colors_value(string $key, string $expected): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertContains($key, array_keys($defaultValue));
        $this->assertEquals($expected, $defaultValue[$key]);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_all_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(MinifyCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_protected_methods(string $method): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, $method);

        $this->assertTrue($ref->isProtected());
    }

    public function test_colors_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');

        $this->assertTrue($ref->isProtected());
    }

    public static function colorProvider(): array
    {
        return [
            ['blue', '#6d8be6'],
            ['green', '#4e9876'],
            ['blue-light', '#62a8ea'],
        ];
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['compileAllColors'],
            ['publishAssets'],
            ['replaceFiles'],
            ['backupFiles'],
            ['resetFiles'],
            ['getMixFile'],
            ['getMixBakFile'],
            ['getColorFile'],
            ['getColorBakFile'],
            ['npmInstall'],
            ['getColor'],
            ['formatColor'],
            ['runProcess'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['formatColor'],
            ['compileAllColors'],
            ['getMixFile'],
        ];
    }
}
