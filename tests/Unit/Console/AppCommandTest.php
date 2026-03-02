<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\AppCommand;
use Dcat\Admin\Console\InstallCommand;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AppCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AppCommand::class));
    }

    public function test_extends_install_command(): void
    {
        $this->assertTrue(is_subclass_of(AppCommand::class, InstallCommand::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(AppCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:app {name}', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(AppCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Create new application', $defaultValue);
    }

    public function test_has_required_methods(): void
    {
        $methods = [
            'handle',
            'addConfig',
            'setDirectory',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(AppCommand::class, $method),
                "AppCommand should have method '{$method}'"
            );
        }
    }

    public function test_add_config_is_protected(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'addConfig');

        $this->assertTrue($ref->isProtected());
    }

    public function test_set_directory_is_protected(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'setDirectory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
