<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Scaffold\MigrationCreator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

class MigrationCreatorTest extends TestCase
{
    public function test_build_blue_print_with_fields(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'title', 'type' => 'string', 'key' => null, 'default' => null, 'nullable' => null, 'comment' => ''],
        ];

        $creator->buildBluePrint($fields, 'id', false, false);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString('title', $bluePrint);
        $this->assertStringContainsString('string', $bluePrint);
        $this->assertStringContainsString('bigIncrements', $bluePrint);
    }

    public function test_build_blue_print_throws_on_empty_fields(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage("Table fields can't be empty");

        $creator->buildBluePrint([], 'id', true, false);
    }

    public function test_build_blue_print_with_timestamps(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'title', 'type' => 'string', 'key' => null, 'default' => null, 'nullable' => null, 'comment' => ''],
        ];

        $creator->buildBluePrint($fields, 'id', true, false);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString('timestamps()', $bluePrint);
    }

    public function test_build_blue_print_with_soft_deletes(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'title', 'type' => 'string', 'key' => null, 'default' => null, 'nullable' => null, 'comment' => ''],
        ];

        $creator->buildBluePrint($fields, 'id', false, true);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString('softDeletes()', $bluePrint);
    }

    public function test_build_blue_print_with_nullable_field(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'content', 'type' => 'text', 'key' => null, 'default' => null, 'nullable' => 'on', 'comment' => ''],
        ];

        $creator->buildBluePrint($fields, 'id', false, false);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString('nullable()', $bluePrint);
    }

    public function test_build_blue_print_with_default_value(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'status', 'type' => 'integer', 'key' => null, 'default' => '1', 'nullable' => null, 'comment' => ''],
        ];

        $creator->buildBluePrint($fields, 'id', false, false);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString("default('1')", $bluePrint);
    }

    public function test_build_blue_print_with_comment(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'content', 'type' => 'text', 'key' => null, 'default' => null, 'nullable' => null, 'comment' => 'The content'],
        ];

        $creator->buildBluePrint($fields, 'id', false, false);

        $ref = new \ReflectionProperty($creator, 'bluePrint');
        $ref->setAccessible(true);
        $bluePrint = $ref->getValue($creator);

        $this->assertStringContainsString("comment('The content')", $bluePrint);
    }

    public function test_build_blue_print_returns_this(): void
    {
        $files = new Filesystem;
        $creator = new MigrationCreator($files);

        $fields = [
            ['name' => 'title', 'type' => 'string', 'key' => null, 'default' => null, 'nullable' => null, 'comment' => ''],
        ];

        $result = $creator->buildBluePrint($fields, 'id', true, false);

        $this->assertSame($creator, $result);
    }
}
