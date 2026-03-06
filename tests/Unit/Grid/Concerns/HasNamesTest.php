<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid\Concerns\HasNames;
use Dcat\Admin\Tests\TestCase;

class HasNamesTest extends TestCase
{
    protected function createHasNamesUser(): object
    {
        return new class
        {
            use HasNames;

            public $tableId = 'grid-table';

            public $request;

            public function __construct()
            {
                $this->request = request();
            }
        };
    }

    public function test_set_name_and_get_name(): void
    {
        $user = $this->createHasNamesUser();
        $result = $user->setName('test');

        $this->assertSame($user, $result);
        $this->assertSame('test', $user->getName());
    }

    public function test_get_name_returns_null_by_default(): void
    {
        $user = $this->createHasNamesUser();
        $this->assertNull($user->getName());
    }

    public function test_make_name_with_prefix(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('prefix');

        $this->assertSame('prefix_some-key', $user->makeName('some-key'));
    }

    public function test_make_name_without_prefix(): void
    {
        $user = $this->createHasNamesUser();
        $this->assertSame('some-key', $user->makeName('some-key'));
    }

    public function test_get_name_prefix(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('test');

        $this->assertSame('test_', $user->getNamePrefix());
    }

    public function test_get_name_prefix_returns_null_without_name(): void
    {
        $user = $this->createHasNamesUser();
        $this->assertNull($user->getNamePrefix());
    }

    public function test_get_row_name(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('test');

        $this->assertSame('test_grid-row', $user->getRowName());
    }

    public function test_get_select_all_name(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('test');

        $this->assertSame('test_grid-select-all', $user->getSelectAllName());
    }

    public function test_get_per_page_name(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('test');

        $this->assertSame('test_grid-per-page', $user->getPerPageName());
    }

    public function test_get_export_selected_name(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('test');

        $this->assertSame('test_export-selected', $user->getExportSelectedName());
    }

    public function test_set_name_updates_table_id(): void
    {
        $user = $this->createHasNamesUser();
        $user->setName('prefix');

        $this->assertSame('prefix_grid-table', $user->tableId);
    }
}
