<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\HealthCheckCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;

class HealthCheckCommandTest extends TestCase
{
    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(HealthCheckCommand::class, new HealthCheckCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(HealthCheckCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(HealthCheckCommand::class, 'signature');

        $this->assertSame('admin:health-check {--json : Output issues as JSON}', $ref->getDefaultValue());
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(HealthCheckCommand::class, 'description');

        $this->assertSame('Check admin menu/permission configuration health', $ref->getDefaultValue());
    }
}
