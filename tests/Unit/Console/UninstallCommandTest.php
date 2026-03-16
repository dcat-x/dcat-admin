<?php

declare(strict_types=1);

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

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(UninstallCommand::class, new UninstallCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(UninstallCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(UninstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:uninstall', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(UninstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Uninstall the admin package', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $ref = new \ReflectionMethod(UninstallCommand::class, 'handle');

        $this->assertSame(0, $ref->getNumberOfParameters());
    }

    public function test_remove_files_and_directories_method_signature(): void
    {
        $ref = new \ReflectionMethod(UninstallCommand::class, 'removeFilesAndDirectories');

        $this->assertSame(0, $ref->getNumberOfParameters());
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
