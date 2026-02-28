<?php

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Scaffold\LangCreator;
use Dcat\Admin\Tests\TestCase;

class LangCreatorTest extends TestCase
{
    public function test_constructor_sets_fields(): void
    {
        $fields = [
            ['name' => 'title', 'translation' => 'Title'],
        ];

        $creator = new LangCreator($fields);

        $ref = new \ReflectionProperty($creator, 'fields');
        $ref->setAccessible(true);

        $this->assertEquals($fields, $ref->getValue($creator));
    }

    public function test_get_lang_path_method_exists(): void
    {
        $creator = new LangCreator([]);

        $ref = new \ReflectionMethod($creator, 'getLangPath');
        $ref->setAccessible(true);

        $this->assertTrue($ref->isProtected());

        $path = $ref->invoke($creator, 'User');

        $this->assertIsString($path);
        $this->assertStringEndsWith('.php', $path);
    }

    public function test_create_instance_with_empty_fields(): void
    {
        $creator = new LangCreator([]);

        $this->assertInstanceOf(LangCreator::class, $creator);

        $ref = new \ReflectionProperty($creator, 'fields');
        $ref->setAccessible(true);

        $this->assertIsArray($ref->getValue($creator));
        $this->assertEmpty($ref->getValue($creator));
    }

    public function test_create_instance_with_fields(): void
    {
        $fields = [
            ['name' => 'title', 'translation' => 'Title'],
            ['name' => 'content', 'translation' => 'Content'],
        ];

        $creator = new LangCreator($fields);

        $this->assertInstanceOf(LangCreator::class, $creator);

        $ref = new \ReflectionProperty($creator, 'fields');
        $ref->setAccessible(true);

        $this->assertCount(2, $ref->getValue($creator));
    }

    public function test_fields_stored_correctly(): void
    {
        $fields = [
            ['name' => 'name', 'translation' => 'Name'],
            ['name' => 'email', 'translation' => 'Email'],
            ['name' => 'status', 'translation' => 'Status'],
        ];

        $creator = new LangCreator($fields);

        $ref = new \ReflectionProperty($creator, 'fields');
        $ref->setAccessible(true);

        $storedFields = $ref->getValue($creator);

        $this->assertCount(3, $storedFields);
        $this->assertEquals('name', $storedFields[0]['name']);
        $this->assertEquals('Name', $storedFields[0]['translation']);
        $this->assertEquals('email', $storedFields[1]['name']);
        $this->assertEquals('Email', $storedFields[1]['translation']);
        $this->assertEquals('status', $storedFields[2]['name']);
        $this->assertEquals('Status', $storedFields[2]['translation']);
    }
}
