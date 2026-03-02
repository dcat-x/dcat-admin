<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\InstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class InstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(InstallCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(InstallCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:install', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Install the admin package', $defaultValue);
    }

    public function test_directory_property_exists_and_is_protected(): void
    {
        $this->assertTrue(property_exists(InstallCommand::class, 'directory'));

        $ref = new \ReflectionProperty(InstallCommand::class, 'directory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_directory_default_value_is_empty_string(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'directory');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'handle'),
            'InstallCommand should have method "handle"'
        );
    }

    public function test_has_init_database_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'initDatabase'),
            'InstallCommand should have method "initDatabase"'
        );
    }

    public function test_has_set_directory_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'setDirectory'),
            'InstallCommand should have method "setDirectory"'
        );
    }

    public function test_has_init_admin_directory_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'initAdminDirectory'),
            'InstallCommand should have method "initAdminDirectory"'
        );
    }

    public function test_has_create_home_controller_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'createHomeController'),
            'InstallCommand should have method "createHomeController"'
        );
    }

    public function test_has_create_auth_controller_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'createAuthController'),
            'InstallCommand should have method "createAuthController"'
        );
    }

    public function test_has_create_metric_cards_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'createMetricCards'),
            'InstallCommand should have method "createMetricCards"'
        );
    }

    public function test_has_namespace_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'namespace'),
            'InstallCommand should have method "namespace"'
        );
    }

    public function test_has_create_bootstrap_file_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'createBootstrapFile'),
            'InstallCommand should have method "createBootstrapFile"'
        );
    }

    public function test_has_create_routes_file_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'createRoutesFile'),
            'InstallCommand should have method "createRoutesFile"'
        );
    }

    public function test_has_get_stub_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'getStub'),
            'InstallCommand should have method "getStub"'
        );
    }

    public function test_has_make_dir_method(): void
    {
        $this->assertTrue(
            method_exists(InstallCommand::class, 'makeDir'),
            'InstallCommand should have method "makeDir"'
        );
    }

    public function test_init_database_is_public(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'initDatabase');

        $this->assertTrue($ref->isPublic());
    }

    public function test_create_home_controller_is_public(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'createHomeController');

        $this->assertTrue($ref->isPublic());
    }

    public function test_create_auth_controller_is_public(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'createAuthController');

        $this->assertTrue($ref->isPublic());
    }

    public function test_create_metric_cards_is_public(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'createMetricCards');

        $this->assertTrue($ref->isPublic());
    }

    public function test_set_directory_is_protected(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'setDirectory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_stub_is_protected(): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, 'getStub');

        $this->assertTrue($ref->isProtected());
    }
}
