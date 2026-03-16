<?php

declare(strict_types=1);

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

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(CreateUserCommand::class);

        $this->assertSame(CreateUserCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(CreateUserCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(CreateUserCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:create-user', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(CreateUserCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Create a admin user', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(CreateUserCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(CreateUserCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
