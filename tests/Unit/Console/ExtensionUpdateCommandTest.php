<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionUpdateCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionUpdateCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionUpdateCommand::class));
    }

    public function test_extends_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionUpdateCommand::class, Command::class));
    }

    public function test_signature_contains_admin_ext_update(): void
    {
        $ref = new \ReflectionProperty(ExtensionUpdateCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-update', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_ver_option(): void
    {
        $ref = new \ReflectionProperty(ExtensionUpdateCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('--ver=', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionUpdateCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Update an existing extension', $defaultValue);
    }

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionUpdateCommand::class, 'handle'));
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionUpdateCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_signature_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(ExtensionUpdateCommand::class, 'signature');

        $this->assertTrue($ref->isProtected());
    }
}
