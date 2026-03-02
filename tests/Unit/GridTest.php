<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Grid;
use Dcat\Admin\Tests\TestCase;
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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Grid::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Illuminate\Support\Traits\Macroable::class, $traits);
    }

    public function test_uses_has_actions_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasActions::class, $traits);
    }

    public function test_uses_has_tools_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasTools::class, $traits);
    }

    public function test_uses_has_filter_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasFilter::class, $traits);
    }

    public function test_uses_has_paginator_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasPaginator::class, $traits);
    }

    public function test_uses_has_exporter_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasExporter::class, $traits);
    }

    public function test_uses_has_quick_search_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasQuickSearch::class, $traits);
    }

    public function test_uses_has_quick_create_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasQuickCreate::class, $traits);
    }

    public function test_uses_has_selector_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasSelector::class, $traits);
    }

    public function test_uses_can_fix_columns_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\CanFixColumns::class, $traits);
    }

    public function test_uses_can_hides_columns_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\CanHidesColumns::class, $traits);
    }

    public function test_uses_has_complex_headers_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasComplexHeaders::class, $traits);
    }

    public function test_uses_has_events_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasEvents::class, $traits);
    }

    public function test_uses_has_data_permission_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasDataPermission::class, $traits);
    }

    public function test_uses_has_names_concern(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Grid\Concerns\HasNames::class, $traits);
    }

    public function test_uses_has_builder_events_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Traits\HasBuilderEvents::class, $traits);
    }

    public function test_uses_has_variables_trait(): void
    {
        $ref = new ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Traits\HasVariables::class, $traits);
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

    public function test_method_exists_columns(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'columns'));
    }

    public function test_method_exists_rows(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'rows'));
    }

    public function test_method_exists_column(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'column'));
    }

    public function test_method_exists_option(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'option'));
    }

    public function test_method_exists_disable_row_selector(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'disableRowSelector'));
    }

    public function test_method_exists_show_row_selector(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'showRowSelector'));
    }

    public function test_method_exists_disable_create_button(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'disableCreateButton'));
    }

    public function test_method_exists_show_create_button(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'showCreateButton'));
    }

    public function test_method_exists_disable_pagination(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'disablePagination'));
    }

    public function test_method_exists_show_pagination(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'showPagination'));
    }

    public function test_method_exists_disable_actions(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'disableActions'));
    }

    public function test_method_exists_show_actions(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'showActions'));
    }

    public function test_method_exists_disable_filter(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'disableFilter'));
    }

    public function test_method_exists_show_filter(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'showFilter'));
    }

    public function test_method_exists_resource(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'resource'));
    }

    public function test_method_exists_model(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'model'));
    }

    public function test_method_exists_get_key_name(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'getKeyName'));
    }

    public function test_method_exists_set_key_name(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'setKeyName'));
    }

    public function test_method_exists_paginate(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'paginate'));
    }

    public function test_method_exists_wrap(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'wrap'));
    }

    public function test_method_exists_render(): void
    {
        $this->assertTrue(method_exists(Grid::class, 'render'));
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
        $this->assertArrayHasKey('pagination', $options);
        $this->assertTrue($options['pagination']);
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
