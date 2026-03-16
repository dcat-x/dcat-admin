<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionEnableCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionEnableCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_command_is_instance_of_illuminate_command(): void
    {
        $command = new ExtensionEnableCommand;

        $this->assertInstanceOf(Command::class, $command);
    }

    public function test_signature_contains_admin_ext_enable(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-enable', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Enable an existing extension', $defaultValue);
    }

    public function test_handle_signature_is_public_and_parameterless(): void
    {
        $ref = new \ReflectionMethod(ExtensionEnableCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
        $this->assertCount(0, $ref->getParameters());
    }

    public function test_signature_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'signature');

        $this->assertTrue($ref->isProtected());
    }
}
