<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\UpdateCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class UpdateCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(UpdateCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(UpdateCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(UpdateCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:update', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(UpdateCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Update the admin package', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(UpdateCommand::class, 'handle'),
            'UpdateCommand should have method handle'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(UpdateCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
