<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\MenuCacheCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class MenuCacheCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(MenuCacheCommand::class, new MenuCacheCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(MenuCacheCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(MenuCacheCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:menu-cache', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(MenuCacheCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Flush the menu cache', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(MenuCacheCommand::class, 'handle'),
            'MenuCacheCommand should have method "handle"'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(MenuCacheCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
