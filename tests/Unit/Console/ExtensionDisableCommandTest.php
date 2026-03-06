<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionDiableCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionDisableCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_command_is_instance_of_illuminate_command(): void
    {
        $command = new ExtensionDiableCommand;

        $this->assertInstanceOf(Command::class, $command);
    }

    public function test_signature_contains_admin_ext_disable(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-disable', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Disable an existing extension', $defaultValue);
    }

    public function test_handle_signature_is_public_and_parameterless(): void
    {
        $ref = new \ReflectionMethod(ExtensionDiableCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
        $this->assertCount(0, $ref->getParameters());
    }

    public function test_signature_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'signature');

        $this->assertTrue($ref->isProtected());
    }
}
