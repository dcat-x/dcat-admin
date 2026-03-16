<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Scaffold\GridCreator;
use Dcat\Admin\Tests\TestCase;

class GridCreatorTest extends TestCase
{
    protected function createGridCreator(): object
    {
        return new class
        {
            use GridCreator {
                generateGrid as public;
            }
        };
    }

    public function test_generate_grid_with_primary_key(): void
    {
        $creator = $this->createGridCreator();
        $result = $creator->generateGrid('id', []);

        $this->assertStringContainsString("\$grid->column('id')->sortable()", $result);
    }

    public function test_generate_grid_with_fields(): void
    {
        $creator = $this->createGridCreator();
        $fields = [
            ['name' => 'title'],
            ['name' => 'status'],
        ];
        $result = $creator->generateGrid('id', $fields);

        $this->assertStringContainsString("\$grid->column('title')", $result);
        $this->assertStringContainsString("\$grid->column('status')", $result);
    }

    public function test_generate_grid_skips_primary_key_field(): void
    {
        $creator = $this->createGridCreator();
        $fields = [
            ['name' => 'id'],
            ['name' => 'title'],
        ];
        $result = $creator->generateGrid('id', $fields);

        $this->assertStringContainsString("\$grid->column('title')", $result);
    }

    public function test_generate_grid_includes_filter(): void
    {
        $creator = $this->createGridCreator();
        $result = $creator->generateGrid('id', []);

        $this->assertStringContainsString('$grid->filter', $result);
        $this->assertStringContainsString("\$filter->equal('id')", $result);
    }

    public function test_generate_grid_with_timestamps(): void
    {
        $creator = $this->createGridCreator();
        $result = $creator->generateGrid('id', [], true);

        $this->assertStringContainsString("\$grid->column('created_at')", $result);
        $this->assertStringContainsString("\$grid->column('updated_at')->sortable()", $result);
    }

    public function test_generate_grid_without_timestamps(): void
    {
        $creator = $this->createGridCreator();
        $result = $creator->generateGrid('id', [], false);

        $this->assertStringNotContainsString('created_at', $result);
        $this->assertStringNotContainsString('updated_at', $result);
    }

    public function test_generate_grid_skips_empty_name_fields(): void
    {
        $creator = $this->createGridCreator();
        $fields = [
            ['name' => ''],
            ['name' => 'name'],
        ];
        $result = $creator->generateGrid('id', $fields);

        $this->assertStringContainsString("\$grid->column('name')", $result);
    }
}
