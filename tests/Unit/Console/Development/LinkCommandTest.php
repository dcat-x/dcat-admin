<?php

namespace Dcat\Admin\Tests\Unit\Console\Development;

use Dcat\Admin\Console\Development\LinkCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Mockery;

class LinkCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_command_is_instance_of_illuminate_console_command(): void
    {
        $command = new LinkCommand;

        $this->assertInstanceOf(Command::class, $command);
    }

    public function test_signature_equals_admin_dev(): void
    {
        $ref = new \ReflectionProperty(LinkCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:dev', $defaultValue);
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

    public function test_handle_invokes_link_assets_and_link_tests_with_filesystem(): void
    {
        $this->app->instance('files', new Filesystem);

        $command = new TestableLinkCommand;
        $command->setLaravel($this->app);

        $command->handle();

        $this->assertTrue($command->linkAssetsCalled);
        $this->assertTrue($command->linkTestsCalled);
        $this->assertInstanceOf(Filesystem::class, $command->receivedFilesInAssets);
        $this->assertInstanceOf(Filesystem::class, $command->receivedFilesInTests);
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

class TestableLinkCommand extends LinkCommand
{
    public bool $linkAssetsCalled = false;

    public bool $linkTestsCalled = false;

    public $receivedFilesInAssets;

    public $receivedFilesInTests;

    protected function linkTests($files)
    {
        $this->linkTestsCalled = true;
        $this->receivedFilesInTests = $files;
    }

    protected function linkAssets($files)
    {
        $this->linkAssetsCalled = true;
        $this->receivedFilesInAssets = $files;
    }
}
