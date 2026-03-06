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

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(UpdateCommand::class, new UpdateCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(UpdateCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(UpdateCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:update', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(UpdateCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Update the admin package', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(UpdateCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(UpdateCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
