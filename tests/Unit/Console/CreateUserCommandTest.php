<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\CreateUserCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class CreateUserCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(CreateUserCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(CreateUserCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(CreateUserCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:create-user', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(CreateUserCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Create a admin user', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(CreateUserCommand::class, 'handle'),
            'CreateUserCommand should have method "handle"'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(CreateUserCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
