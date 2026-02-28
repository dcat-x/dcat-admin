<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\Manager;
use Dcat\Admin\Extend\VersionManager;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class VersionManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createVersionManager(?Manager $manager = null): VersionManager
    {
        $manager = $manager ?: Mockery::mock(Manager::class);

        return new VersionManager($manager);
    }

    public function test_constructor_creates_instance(): void
    {
        $manager = Mockery::mock(Manager::class);
        $vm = new VersionManager($manager);

        $this->assertInstanceOf(VersionManager::class, $vm);
    }

    public function test_constants_are_defined(): void
    {
        $this->assertSame(0, VersionManager::NO_VERSION_VALUE);
        $this->assertSame(1, VersionManager::HISTORY_TYPE_COMMENT);
        $this->assertSame(2, VersionManager::HISTORY_TYPE_SCRIPT);
    }

    public function test_extract_scripts_and_comments_separates_correctly(): void
    {
        $vm = $this->createVersionManager();

        $ref = new \ReflectionMethod(VersionManager::class, 'extractScriptsAndComments');
        $ref->setAccessible(true);

        $details = [
            'Initial release',
            'create_tables.php',
            'Added new feature',
            'update_schema.php',
        ];

        [$comments, $scripts] = $ref->invoke($vm, $details);

        $this->assertSame(['Initial release', 'Added new feature'], $comments);
        $this->assertSame(['create_tables.php', 'update_schema.php'], $scripts);
    }

    public function test_extract_scripts_and_comments_with_only_comments(): void
    {
        $vm = $this->createVersionManager();

        $ref = new \ReflectionMethod(VersionManager::class, 'extractScriptsAndComments');
        $ref->setAccessible(true);

        $details = ['First comment', 'Second comment'];

        [$comments, $scripts] = $ref->invoke($vm, $details);

        $this->assertSame(['First comment', 'Second comment'], $comments);
        $this->assertEmpty($scripts);
    }

    public function test_extract_scripts_and_comments_with_only_scripts(): void
    {
        $vm = $this->createVersionManager();

        $ref = new \ReflectionMethod(VersionManager::class, 'extractScriptsAndComments');
        $ref->setAccessible(true);

        $details = ['create_table.php', 'add_column.php'];

        [$comments, $scripts] = $ref->invoke($vm, $details);

        $this->assertEmpty($comments);
        $this->assertSame(['create_table.php', 'add_column.php'], $scripts);
    }

    public function test_extract_scripts_and_comments_with_string_input(): void
    {
        $vm = $this->createVersionManager();

        $ref = new \ReflectionMethod(VersionManager::class, 'extractScriptsAndComments');
        $ref->setAccessible(true);

        [$comments, $scripts] = $ref->invoke($vm, 'Just a comment');

        $this->assertSame(['Just a comment'], $comments);
        $this->assertEmpty($scripts);
    }

    public function test_extract_scripts_recognizes_path_separators(): void
    {
        $vm = $this->createVersionManager();

        $ref = new \ReflectionMethod(VersionManager::class, 'extractScriptsAndComments');
        $ref->setAccessible(true);

        $details = ['sub/dir/migration.php', 'sub\\dir\\migration.php'];

        [$comments, $scripts] = $ref->invoke($vm, $details);

        $this->assertEmpty($comments);
        $this->assertCount(2, $scripts);
    }

    public function test_get_latest_file_version_returns_no_version_for_empty(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');

        $vm = new VersionManager($manager);

        $ref = new \ReflectionMethod(VersionManager::class, 'getLatestFileVersion');
        $ref->setAccessible(true);

        // Set fileVersions to empty array for this extension
        $prop = new \ReflectionProperty(VersionManager::class, 'fileVersions');
        $prop->setAccessible(true);
        $prop->setValue($vm, ['test.extension' => []]);

        $result = $ref->invoke($vm, 'test.extension');

        $this->assertSame(VersionManager::NO_VERSION_VALUE, $result);
    }

    public function test_get_latest_file_version_returns_last_version(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');

        $vm = new VersionManager($manager);

        $prop = new \ReflectionProperty(VersionManager::class, 'fileVersions');
        $prop->setAccessible(true);
        $prop->setValue($vm, [
            'test.extension' => [
                '1.0.0' => ['Initial version'],
                '1.1.0' => ['Update'],
                '2.0.0' => ['Major update'],
            ],
        ]);

        $ref = new \ReflectionMethod(VersionManager::class, 'getLatestFileVersion');
        $ref->setAccessible(true);

        $result = $ref->invoke($vm, 'test.extension');

        $this->assertSame('2.0.0', $result);
    }

    public function test_get_new_file_versions_from_beginning(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');

        $vm = new VersionManager($manager);

        $prop = new \ReflectionProperty(VersionManager::class, 'fileVersions');
        $prop->setAccessible(true);
        $prop->setValue($vm, [
            'test.extension' => [
                '1.0.0' => ['Initial version'],
                '1.1.0' => ['Update'],
                '2.0.0' => ['Major update'],
            ],
        ]);

        $result = $vm->getNewFileVersions('test.extension', null);

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('1.0.0', $result);
        $this->assertArrayHasKey('2.0.0', $result);
    }

    public function test_get_new_file_versions_from_specific_version(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');

        $vm = new VersionManager($manager);

        $prop = new \ReflectionProperty(VersionManager::class, 'fileVersions');
        $prop->setAccessible(true);
        $prop->setValue($vm, [
            'test.extension' => [
                '1.0.0' => ['Initial version'],
                '1.1.0' => ['Update'],
                '2.0.0' => ['Major update'],
            ],
        ]);

        $result = $vm->getNewFileVersions('test.extension', '1.0.0');

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('1.1.0', $result);
        $this->assertArrayHasKey('2.0.0', $result);
        $this->assertArrayNotHasKey('1.0.0', $result);
    }

    public function test_get_new_file_versions_returns_all_when_version_not_found(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');

        $vm = new VersionManager($manager);

        $prop = new \ReflectionProperty(VersionManager::class, 'fileVersions');
        $prop->setAccessible(true);
        $prop->setValue($vm, [
            'test.extension' => [
                '1.0.0' => ['Initial version'],
                '1.1.0' => ['Update'],
            ],
        ]);

        $result = $vm->getNewFileVersions('test.extension', '0.5.0');

        $this->assertCount(2, $result);
    }

    public function test_has_version_file_returns_false_for_nonexistent(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('path')
            ->with('test.extension', 'version.php')
            ->andReturn('/nonexistent/path/version.php');

        $vm = new VersionManager($manager);

        $ref = new \ReflectionMethod(VersionManager::class, 'hasVersionFile');
        $ref->setAccessible(true);

        $this->assertFalse($ref->invoke($vm, 'test.extension'));
    }

    public function test_note_trait_stores_notes_without_output(): void
    {
        $vm = $this->createVersionManager();

        $vm->note('Test note 1');
        $vm->note('Test note 2');

        $this->assertCount(2, $vm->notes);
        $this->assertSame('Test note 1', $vm->notes[0]);
        $this->assertSame('Test note 2', $vm->notes[1]);
    }

    public function test_file_versions_are_cached(): void
    {
        $manager = Mockery::mock(Manager::class);
        $manager->shouldReceive('getName')->andReturn('test.extension');
        $manager->shouldReceive('path')
            ->with('test.extension', 'version.php')
            ->andReturn(null);

        $vm = new VersionManager($manager);

        // First call should parse
        $result1 = $vm->getFileVersions('test.extension');
        // Second call should use cache
        $result2 = $vm->getFileVersions('test.extension');

        $this->assertSame($result1, $result2);
        $this->assertIsArray($result1);
    }
}
