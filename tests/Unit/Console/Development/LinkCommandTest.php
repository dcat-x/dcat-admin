<?php

namespace Dcat\Admin\Tests\Unit\Console\Development;

use Dcat\Admin\Console\Development\LinkCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class LinkCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(LinkCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(LinkCommand::class, Command::class));
    }

    public function test_signature_equals_admin_dev(): void
    {
        $ref = new \ReflectionProperty(LinkCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:dev', $defaultValue);
    }

    public function test_description_property_not_defined(): void
    {
        $ref = new \ReflectionClass(LinkCommand::class);

        // The $description property is inherited from Command but not overridden in LinkCommand
        $prop = $ref->getProperty('description');

        // If the property is declared in the parent class, it was not overridden
        $this->assertNotEquals(
            LinkCommand::class,
            $prop->getDeclaringClass()->getName(),
            'LinkCommand should not define its own $description property'
        );
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(method_exists(LinkCommand::class, 'handle'));
    }

    public function test_has_link_tests_method(): void
    {
        $this->assertTrue(method_exists(LinkCommand::class, 'linkTests'));
    }

    public function test_has_link_assets_method(): void
    {
        $this->assertTrue(method_exists(LinkCommand::class, 'linkAssets'));
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(LinkCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_link_tests_is_protected(): void
    {
        $ref = new \ReflectionMethod(LinkCommand::class, 'linkTests');

        $this->assertTrue($ref->isProtected());
    }

    public function test_link_assets_is_protected(): void
    {
        $ref = new \ReflectionMethod(LinkCommand::class, 'linkAssets');

        $this->assertTrue($ref->isProtected());
    }
}
