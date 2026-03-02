<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\PublishCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class PublishCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(PublishCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(PublishCommand::class, Command::class));
    }

    public function test_signature_contains_admin_publish(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:publish', $defaultValue);
    }

    public function test_signature_contains_expected_options(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('--force', $defaultValue);
        $this->assertStringContainsString('--lang', $defaultValue);
        $this->assertStringContainsString('--assets', $defaultValue);
        $this->assertStringContainsString('--migrations', $defaultValue);
        $this->assertStringContainsString('--config', $defaultValue);
    }

    public function test_description_contains_re_publish(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('Re-publish', $defaultValue);
    }

    public function test_tags_default_is_empty_array(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'tags');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertEmpty($defaultValue);
    }

    public function test_has_all_required_methods(): void
    {
        $methods = [
            'handle',
            'getTags',
            'publishTag',
            'pathsToPublish',
            'publishItem',
            'publishFile',
            'publishDirectory',
            'moveManagedFiles',
            'isExceptPath',
            'createParentDirectory',
            'status',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(PublishCommand::class, $method),
                "PublishCommand should have method '{$method}'"
            );
        }
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_tags_is_protected(): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, 'getTags');

        $this->assertTrue($ref->isProtected());
    }

    public function test_publish_tag_is_protected(): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, 'publishTag');

        $this->assertTrue($ref->isProtected());
    }

    public function test_is_except_path_is_protected(): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, 'isExceptPath');

        $this->assertTrue($ref->isProtected());
    }

    public function test_files_property_exists(): void
    {
        $this->assertTrue(property_exists(PublishCommand::class, 'files'));

        $ref = new \ReflectionProperty(PublishCommand::class, 'files');

        $this->assertTrue($ref->isProtected());
    }
}
