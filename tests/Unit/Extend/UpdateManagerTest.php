<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\Manager;
use Dcat\Admin\Extend\UpdateManager;
use Dcat\Admin\Extend\VersionManager;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class UpdateManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createUpdateManager(?Manager $manager = null): UpdateManager
    {
        if (! $manager) {
            $versionManager = Mockery::mock(VersionManager::class);

            $manager = Mockery::mock(Manager::class);
            $manager->shouldReceive('versionManager')->andReturn($versionManager);
        }

        return new UpdateManager($manager);
    }

    public function test_constructor_creates_instance(): void
    {
        $um = $this->createUpdateManager();

        $this->assertInstanceOf(UpdateManager::class, $um);
    }

    public function test_constructor_stores_manager(): void
    {
        $versionManager = Mockery::mock(VersionManager::class);

        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('versionManager')->andReturn($versionManager);

        $um = new UpdateManager($manager);

        $ref = new \ReflectionProperty(UpdateManager::class, 'manager');
        $ref->setAccessible(true);

        $this->assertSame($manager, $ref->getValue($um));
    }

    public function test_constructor_stores_version_manager(): void
    {
        $versionManager = Mockery::mock(VersionManager::class);

        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('versionManager')->andReturn($versionManager);

        $um = new UpdateManager($manager);

        $ref = new \ReflectionProperty(UpdateManager::class, 'versionManager');
        $ref->setAccessible(true);

        $this->assertSame($versionManager, $ref->getValue($um));
    }

    public function test_install_calls_update_internally(): void
    {
        $versionManager = Mockery::mock(VersionManager::class);

        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('versionManager')->andReturn($versionManager);
        $manager->shouldReceive('getName')->with('nonexistent')->andReturn('nonexistent');
        $manager->shouldReceive('get')->with('nonexistent')->andReturn(null);

        $um = new UpdateManager($manager);

        // install delegates to update; when extension not found, returns null
        $result = $um->install('nonexistent');

        $this->assertNull($result);
    }

    public function test_update_returns_null_when_extension_not_found(): void
    {
        $versionManager = Mockery::mock(VersionManager::class);

        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('versionManager')->andReturn($versionManager);
        $manager->shouldReceive('getName')->with('nonexistent')->andReturn('nonexistent');
        $manager->shouldReceive('get')->with('nonexistent')->andReturn(null);

        $um = new UpdateManager($manager);
        $result = $um->update('nonexistent');

        $this->assertNull($result);
    }

    public function test_note_trait_is_available(): void
    {
        $um = $this->createUpdateManager();

        $um->note('test note');

        $this->assertCount(1, $um->notes);
        $this->assertSame('test note', $um->notes[0]);
    }

    public function test_update_stores_notes(): void
    {
        $um = $this->createUpdateManager();

        $um->note('Note 1');
        $um->note('Note 2');
        $um->note('Note 3');

        $this->assertCount(3, $um->notes);
        $this->assertSame('Note 2', $um->notes[1]);
    }
}
