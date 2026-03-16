<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\CanFixColumns;
use Dcat\Admin\Grid\Concerns\CanHidesColumns;
use Dcat\Admin\Grid\Concerns\HasActions;
use Dcat\Admin\Grid\Concerns\HasComplexHeaders;
use Dcat\Admin\Grid\Concerns\HasDataPermission;
use Dcat\Admin\Grid\Concerns\HasEvents;
use Dcat\Admin\Grid\Concerns\HasExporter;
use Dcat\Admin\Grid\Concerns\HasFilter;
use Dcat\Admin\Grid\Concerns\HasNames;
use Dcat\Admin\Grid\Concerns\HasPaginator;
use Dcat\Admin\Grid\Concerns\HasQuickCreate;
use Dcat\Admin\Grid\Concerns\HasQuickSearch;
use Dcat\Admin\Grid\Concerns\HasSelector;
use Dcat\Admin\Grid\Concerns\HasTools;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasVariables;
use Illuminate\Support\Traits\Macroable;
use Mockery;
use ReflectionClass;
use ReflectionProperty;

class GridTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_uses_macroable_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(Macroable::class, $traits);
    }

    public function test_uses_has_actions_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasActions::class, $traits);
    }

    public function test_uses_has_tools_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasTools::class, $traits);
    }

    public function test_uses_has_filter_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasFilter::class, $traits);
    }

    public function test_uses_has_paginator_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasPaginator::class, $traits);
    }

    public function test_uses_has_exporter_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasExporter::class, $traits);
    }

    public function test_uses_has_quick_search_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasQuickSearch::class, $traits);
    }

    public function test_uses_has_quick_create_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasQuickCreate::class, $traits);
    }

    public function test_uses_has_selector_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasSelector::class, $traits);
    }

    public function test_uses_can_fix_columns_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(CanFixColumns::class, $traits);
    }

    public function test_uses_can_hides_columns_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(CanHidesColumns::class, $traits);
    }

    public function test_uses_has_complex_headers_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasComplexHeaders::class, $traits);
    }

    public function test_uses_has_events_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasEvents::class, $traits);
    }

    public function test_uses_has_data_permission_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasDataPermission::class, $traits);
    }

    public function test_uses_has_names_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasNames::class, $traits);
    }

    public function test_uses_has_builder_events_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasBuilderEvents::class, $traits);
    }

    public function test_uses_has_variables_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasVariables::class, $traits);
    }

    public function test_create_mode_default_constant(): void
    {
        $this->assertSame('default', Grid::CREATE_MODE_DEFAULT);
    }

    public function test_create_mode_dialog_constant(): void
    {
        $this->assertSame('dialog', Grid::CREATE_MODE_DIALOG);
    }

    public function test_async_name_constant(): void
    {
        $this->assertSame('_async_', Grid::ASYNC_NAME);
    }

    public function test_column_and_columns_append_to_grid_collection(): void
    {
        $grid = new Grid;

        $grid->column('name', 'Name');
        $grid->columns(['email' => 'Email']);
        $grid->columns('age');

        $this->assertCount(3, $grid->columns());
        $this->assertTrue($grid->columns()->has('name'));
        $this->assertTrue($grid->columns()->has('email'));
        $this->assertTrue($grid->columns()->has('age'));
    }

    public function test_option_and_toggle_methods_update_grid_state(): void
    {
        $grid = new Grid;

        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableFilter();

        $this->assertFalse($grid->option('row_selector'));
        $this->assertFalse($grid->allowCreateButton());
        $this->assertFalse($grid->option('actions'));
        $this->assertFalse($grid->option('filter'));

        $grid->showRowSelector();
        $grid->showCreateButton();
        $grid->showActions();
        $grid->showFilter();

        $this->assertTrue($grid->option('row_selector'));
        $this->assertTrue($grid->allowCreateButton());
        $this->assertTrue($grid->option('actions'));
        $this->assertTrue($grid->option('filter'));
    }

    public function test_paginate_and_key_name_setters_apply_values(): void
    {
        $grid = new Grid;

        $grid->setKeyName('uuid');
        $grid->paginate(50);

        $this->assertSame('uuid', $grid->getKeyName());
        $this->assertSame(50, $grid->getPerPage());
    }

    public function test_rows_callback_runs_when_rows_are_built(): void
    {
        $grid = new class extends Grid
        {
            public function buildRowsForTest(array $rows): void
            {
                $this->buildRows(collect($rows));
            }
        };

        $called = false;
        $grid->rows(function ($rows) use (&$called) {
            $called = true;
        });

        $grid->buildRowsForTest([['id' => 1], ['id' => 2]]);

        $this->assertTrue($called);
        $this->assertCount(2, $grid->rows());
    }

    public function test_wrap_marks_grid_as_having_wrapper(): void
    {
        $grid = new Grid;

        $this->assertFalse($grid->hasWrapper());

        $grid->wrap(fn () => 'wrapped');

        $this->assertTrue($grid->hasWrapper());
        $this->assertIsString($grid->resource());
        $this->assertNotNull($grid->model());
    }

    public function test_rows_callbacks_default_empty_array(): void
    {
        $prop = new ReflectionProperty(Grid::class, 'rowsCallbacks');
        $this->assertSame([], $prop->getDefaultValue());
    }

    public function test_columns_property_exists(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $this->assertTrue($ref->hasProperty('columns'));
    }

    public function test_view_default_value(): void
    {
        $prop = new ReflectionProperty(Grid::class, 'view');
        $this->assertSame('admin::grid.table', $prop->getDefaultValue());
    }

    public function test_options_default_has_pagination(): void
    {
        $prop = new ReflectionProperty(Grid::class, 'options');
        $options = $prop->getDefaultValue();
        $this->assertIsArray($options);
        $this->assertTrue($options['pagination'] ?? false);
    }

    public function test_options_default_has_create_mode(): void
    {
        $prop = new ReflectionProperty(Grid::class, 'options');
        $options = $prop->getDefaultValue();
        $this->assertSame(Grid::CREATE_MODE_DEFAULT, $options['create_mode']);
    }

    /**
     * Recursively collect all trait names used by a ReflectionClass.
     */
    private function getAllTraits(ReflectionClass $ref): array
    {
        $traits = [];
        foreach ($ref->getTraits() as $trait) {
            $traits[] = $trait->getName();
            $traits = array_merge($traits, $this->getAllTraits($trait));
        }

        return $traits;
    }
}
