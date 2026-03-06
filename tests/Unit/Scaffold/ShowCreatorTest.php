<?php

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Scaffold\ShowCreator;
use Dcat\Admin\Tests\TestCase;

class ShowCreatorTest extends TestCase
{
    protected function createShowCreator(): object
    {
        return new class
        {
            use ShowCreator {
                generateShow as public;
            }
        };
    }

    public function test_generate_show_with_primary_key(): void
    {
        $creator = $this->createShowCreator();
        $result = $creator->generateShow('id', []);

        $this->assertStringContainsString("\$show->field('id')", $result);
    }

    public function test_generate_show_with_fields(): void
    {
        $creator = $this->createShowCreator();
        $fields = [
            ['name' => 'title'],
            ['name' => 'content'],
        ];
        $result = $creator->generateShow('id', $fields);

        $this->assertStringContainsString("\$show->field('title')", $result);
        $this->assertStringContainsString("\$show->field('content')", $result);
    }

    public function test_generate_show_with_timestamps(): void
    {
        $creator = $this->createShowCreator();
        $result = $creator->generateShow('id', [], true);

        $this->assertStringContainsString("\$show->field('created_at')", $result);
        $this->assertStringContainsString("\$show->field('updated_at')", $result);
    }

    public function test_generate_show_without_timestamps(): void
    {
        $creator = $this->createShowCreator();
        $result = $creator->generateShow('id', [], false);

        $this->assertStringNotContainsString('created_at', $result);
        $this->assertStringNotContainsString('updated_at', $result);
    }

    public function test_generate_show_skips_empty_name_fields(): void
    {
        $creator = $this->createShowCreator();
        $fields = [
            ['name' => ''],
            ['name' => 'name'],
        ];
        $result = $creator->generateShow('id', $fields);

        $this->assertStringContainsString("\$show->field('name')", $result);
    }

    public function test_generate_show_returns_trimmed_string(): void
    {
        $creator = $this->createShowCreator();
        $result = $creator->generateShow('id', []);

        $this->assertSame($result, trim($result));
    }
}
