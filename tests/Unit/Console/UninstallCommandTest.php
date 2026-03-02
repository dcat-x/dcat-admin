<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\UninstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class UninstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(UninstallCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(UninstallCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(UninstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:uninstall', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(UninstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Uninstall the admin package', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(UninstallCommand::class, 'handle'),
            'UninstallCommand should have method handle'
        );
    }

    public function test_has_remove_files_and_directories_method(): void
    {
        $this->assertTrue(
            method_exists(UninstallCommand::class, 'removeFilesAndDirectories'),
            'UninstallCommand should have method removeFilesAndDirectories'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(UninstallCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_remove_files_and_directories_is_protected(): void
    {
        $ref = new \ReflectionMethod(UninstallCommand::class, 'removeFilesAndDirectories');

        $this->assertTrue($ref->isProtected());
    }
}
