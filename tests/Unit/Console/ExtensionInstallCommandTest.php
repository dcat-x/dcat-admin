<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionInstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionInstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_command_is_instance_of_illuminate_command(): void
    {
        $command = new ExtensionInstallCommand;

        $this->assertInstanceOf(Command::class, $command);
    }

    public function test_signature_contains_admin_ext_install(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-install', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_path_option(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('--path=', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Install an extension', $defaultValue);
    }

    public function test_handle_signature_is_public_and_parameterless(): void
    {
        $ref = new \ReflectionMethod(ExtensionInstallCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
        $this->assertCount(0, $ref->getParameters());
    }

    public function test_signature_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'signature');

        $this->assertTrue($ref->isProtected());
    }
}
