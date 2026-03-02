<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExportSeedCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExportSeedCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExportSeedCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(ExportSeedCommand::class, Command::class));
    }

    public function test_signature_contains_admin_export_seed(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:export-seed', $defaultValue);
    }

    public function test_signature_contains_classname_argument_with_default(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{classname=AdminTablesSeeder}', $defaultValue);
    }

    public function test_signature_contains_users_option(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('--users', $defaultValue);
    }

    public function test_description_contains_export_seed(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('Export seed', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(ExportSeedCommand::class, 'handle'),
            'ExportSeedCommand should have method "handle"'
        );
    }

    public function test_has_get_table_name_method(): void
    {
        $this->assertTrue(
            method_exists(ExportSeedCommand::class, 'getTableName'),
            'ExportSeedCommand should have method "getTableName"'
        );
    }

    public function test_has_get_table_data_array_as_string_method(): void
    {
        $this->assertTrue(
            method_exists(ExportSeedCommand::class, 'getTableDataArrayAsString'),
            'ExportSeedCommand should have method "getTableDataArrayAsString"'
        );
    }

    public function test_has_get_stub_method(): void
    {
        $this->assertTrue(
            method_exists(ExportSeedCommand::class, 'getStub'),
            'ExportSeedCommand should have method "getStub"'
        );
    }

    public function test_has_var_export_method(): void
    {
        $this->assertTrue(
            method_exists(ExportSeedCommand::class, 'varExport'),
            'ExportSeedCommand should have method "varExport"'
        );
    }

    public function test_get_table_name_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExportSeedCommand::class, 'getTableName');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_table_data_array_as_string_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExportSeedCommand::class, 'getTableDataArrayAsString');

        $this->assertTrue($ref->isProtected());
    }

    public function test_var_export_is_protected(): void
    {
        $ref = new \ReflectionMethod(ExportSeedCommand::class, 'varExport');

        $this->assertTrue($ref->isProtected());
    }
}
