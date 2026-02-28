<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Composer;
use Dcat\Admin\Support\ComposerProperty;
use Dcat\Admin\Tests\TestCase;

class ComposerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear static $files cache between tests
        $ref = new \ReflectionProperty(Composer::class, 'files');
        $ref->setAccessible(true);
        $ref->setValue(null, []);
    }

    public function test_from_json_with_valid_file(): void
    {
        $path = $this->createTempJsonFile(['name' => 'vendor/package', 'version' => '1.0.0']);
        $result = Composer::fromJson($path);
        $this->assertIsArray($result);
        $this->assertEquals('vendor/package', $result['name']);
        $this->assertEquals('1.0.0', $result['version']);
    }

    public function test_from_json_caches_result(): void
    {
        $path = $this->createTempJsonFile(['name' => 'test']);
        $result1 = Composer::fromJson($path);
        // Modify file content - should still return cached version
        file_put_contents($path, json_encode(['name' => 'changed']));
        $result2 = Composer::fromJson($path);
        $this->assertEquals($result1, $result2);
    }

    public function test_from_json_with_null_path(): void
    {
        $result = Composer::fromJson(null);
        $this->assertEquals([], $result);
    }

    public function test_from_json_with_nonexistent_file(): void
    {
        $result = Composer::fromJson('/nonexistent/path/composer.json');
        $this->assertEquals([], $result);
    }

    public function test_from_json_with_empty_string_path(): void
    {
        $result = Composer::fromJson('');
        $this->assertEquals([], $result);
    }

    public function test_parse_returns_composer_property(): void
    {
        $path = $this->createTempJsonFile([
            'name' => 'vendor/package',
            'description' => 'A test package',
            'version' => '2.0.0',
        ]);

        $result = Composer::parse($path);
        $this->assertInstanceOf(ComposerProperty::class, $result);
        $this->assertEquals('vendor/package', $result->name);
        $this->assertEquals('A test package', $result->description);
    }

    public function test_parse_with_null_returns_empty_property(): void
    {
        $result = Composer::parse(null);
        $this->assertInstanceOf(ComposerProperty::class, $result);
        $this->assertEquals([], $result->toArray());
    }

    public function test_get_version_returns_version_string(): void
    {
        $lockContent = [
            'packages' => [
                ['name' => 'laravel/framework', 'version' => 'v10.0.0'],
                ['name' => 'dcat/laravel-admin', 'version' => 'v1.5.0'],
                ['name' => 'other/package', 'version' => 'v3.2.1'],
            ],
        ];
        $lockFile = $this->createTempJsonFile($lockContent);

        $result = Composer::getVersion('dcat/laravel-admin', $lockFile);
        $this->assertEquals('v1.5.0', $result);
    }

    public function test_get_version_returns_null_for_missing_package(): void
    {
        $lockContent = [
            'packages' => [
                ['name' => 'laravel/framework', 'version' => 'v10.0.0'],
            ],
        ];
        $lockFile = $this->createTempJsonFile($lockContent);

        $result = Composer::getVersion('nonexistent/package', $lockFile);
        $this->assertNull($result);
    }

    public function test_get_version_returns_null_for_null_package_name(): void
    {
        $result = Composer::getVersion(null);
        $this->assertNull($result);
    }

    public function test_get_version_returns_null_for_empty_lock_file(): void
    {
        $lockFile = $this->createTempJsonFile([]);
        $result = Composer::getVersion('some/package', $lockFile);
        $this->assertNull($result);
    }

    public function test_get_version_with_no_packages_key(): void
    {
        $lockFile = $this->createTempJsonFile(['other' => 'data']);
        $result = Composer::getVersion('some/package', $lockFile);
        $this->assertNull($result);
    }

    /**
     * Create a temporary JSON file and return its path.
     */
    protected function createTempJsonFile(array $data): string
    {
        $path = tempnam(sys_get_temp_dir(), 'composer_test_');
        file_put_contents($path, json_encode($data));

        return $path;
    }
}
