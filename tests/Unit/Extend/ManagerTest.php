<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\Manager;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createManager(): Manager
    {
        return new Manager($this->app);
    }

    public function test_constructor_creates_instance(): void
    {
        $manager = $this->createManager();

        $this->assertInstanceOf(Manager::class, $manager);
    }

    public function test_all_returns_empty_collection(): void
    {
        $manager = $this->createManager();

        $all = $manager->all();

        $this->assertInstanceOf(Collection::class, $all);
        $this->assertTrue($all->isEmpty());
    }

    public function test_has_returns_false_for_nonexistent(): void
    {
        $manager = $this->createManager();

        $this->assertFalse($manager->has('nonexistent.extension'));
    }

    public function test_get_returns_null_for_nonexistent(): void
    {
        $manager = $this->createManager();

        $this->assertNull($manager->get('nonexistent.extension'));
    }

    public function test_format_name_replaces_slash(): void
    {
        $manager = $this->createManager();

        $ref = new \ReflectionMethod(Manager::class, 'formatName');
        $ref->setAccessible(true);

        $this->assertSame('vendor.package', $ref->invoke($manager, 'vendor/package'));
    }

    public function test_get_name_with_string_returns_formatted(): void
    {
        $manager = $this->createManager();

        $this->assertSame('vendor.package', $manager->getName('vendor/package'));
    }

    public function test_get_extension_directories_returns_empty_for_nonexistent_dir(): void
    {
        $manager = $this->createManager();

        $result = $manager->getExtensionDirectories('/nonexistent/path/to/extensions');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_all_returns_collection_instance(): void
    {
        $manager = $this->createManager();

        $this->assertInstanceOf(Collection::class, $manager->all());
    }

    public function test_has_with_formatted_name(): void
    {
        $manager = $this->createManager();

        // Both slash and dot formats should resolve the same way
        $this->assertFalse($manager->has('vendor/package'));
        $this->assertFalse($manager->has('vendor.package'));
    }

    public function test_constructor_initializes_files(): void
    {
        $manager = $this->createManager();

        $ref = new \ReflectionProperty(Manager::class, 'files');
        $ref->setAccessible(true);

        $this->assertNotNull($ref->getValue($manager));
    }
}
