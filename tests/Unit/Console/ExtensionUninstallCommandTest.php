<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionUninstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionUninstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionUninstallCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionUninstallCommand::class, Command::class));
    }

    public function test_signature_contains_command_name(): void
    {
        $ref = new \ReflectionProperty(ExtensionUninstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-uninstall', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(ExtensionUninstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionUninstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Uninstall an existing extension', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(ExtensionUninstallCommand::class, 'handle'),
            'ExtensionUninstallCommand should have method handle'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionUninstallCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
