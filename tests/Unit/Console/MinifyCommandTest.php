<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\MinifyCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class MinifyCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(MinifyCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(MinifyCommand::class, Command::class));
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

        $this->assertArrayHasKey('DEFAULT', $constants);
        $this->assertEquals('default', $constants['DEFAULT']);
    }

    public function test_colors_is_array_with_four_entries(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertCount(4, $defaultValue);
    }

    public function test_colors_blue_value(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertArrayHasKey('blue', $defaultValue);
        $this->assertEquals('#6d8be6', $defaultValue['blue']);
    }

    public function test_colors_green_value(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertArrayHasKey('green', $defaultValue);
        $this->assertEquals('#4e9876', $defaultValue['green']);
    }

    public function test_colors_blue_light_value(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');
        $defaultValue = $ref->getDefaultValue();

        $this->assertArrayHasKey('blue-light', $defaultValue);
        $this->assertEquals('#62a8ea', $defaultValue['blue-light']);
    }

    public function test_has_all_required_methods(): void
    {
        $methods = [
            'handle',
            'compileAllColors',
            'publishAssets',
            'replaceFiles',
            'backupFiles',
            'resetFiles',
            'getMixFile',
            'getMixBakFile',
            'getColorFile',
            'getColorBakFile',
            'npmInstall',
            'getColor',
            'formatColor',
            'runProcess',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(MinifyCommand::class, $method),
                "MinifyCommand should have method '{$method}'"
            );
        }
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_format_color_is_protected(): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, 'formatColor');

        $this->assertTrue($ref->isProtected());
    }

    public function test_compile_all_colors_is_protected(): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, 'compileAllColors');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_mix_file_is_protected(): void
    {
        $ref = new \ReflectionMethod(MinifyCommand::class, 'getMixFile');

        $this->assertTrue($ref->isProtected());
    }

    public function test_colors_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(MinifyCommand::class, 'colors');

        $this->assertTrue($ref->isProtected());
    }
}
