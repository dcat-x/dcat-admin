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

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ExtensionUninstallCommand::class);

        $this->assertSame(ExtensionUninstallCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(ExtensionUninstallCommand::class);

        $this->assertContains(Command::class, $parents);
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

        $this->assertSame('Uninstall an existing extension', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(ExtensionUninstallCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionUninstallCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
