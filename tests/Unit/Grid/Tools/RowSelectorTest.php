<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\RowSelector;
use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class RowSelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getSelectAllName')->andReturn('grid-select-all');
        $grid->shouldReceive('getRowName')->andReturn('grid-row');
        $grid->shouldReceive('getName')->andReturn('test-grid');

        return $grid;
    }

    protected function createMockRow(array $data = []): object
    {
        $defaults = [
            '_index' => 0,
            'name' => '',
            'title' => '',
            'username' => '',
        ];

        $merged = array_merge($defaults, $data);

        return new class($merged)
        {
            private array $attributes;

            public function __construct(array $attributes)
            {
                $this->attributes = $attributes;
                foreach ($attributes as $key => $value) {
                    $this->{$key} = $value;
                }
            }

            public function toArray(): array
            {
                return $this->attributes;
            }
        };
    }

    /**
     * Set up the container bindings needed by renderColumn / addScript.
     *
     * Admin::color() resolves 'admin.color' and Admin::script() resolves 'admin.asset'.
     * Color cannot be mocked with Mockery due to __call signature mismatch,
     * so we use a lightweight anonymous stub.
     */
    protected function bindAdminServices(): void
    {
        // Stub for Admin::color() — only dark20() is called inside addScript
        $color = new class
        {
            public function dark20(): string
            {
                return '#f6fbff';
            }
        };
        $this->app->instance('admin.color', $color);

        // Real Asset instance is sufficient — Admin::script() just appends to an array
        $this->app->instance('admin.asset', new Asset);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $args = [])
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($object, $args);
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function test_constructor_accepts_grid_and_stores_it(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $storedGrid = $this->getProtectedProperty($selector, 'grid');

        $this->assertSame($grid, $storedGrid);
    }

    public function test_constructor_sets_default_style_to_primary(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $this->assertSame('primary', $this->getProtectedProperty($selector, 'style'));
    }

    public function test_constructor_sets_default_background_to_null(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $this->assertNull($this->getProtectedProperty($selector, 'background'));
    }

    public function test_constructor_sets_default_row_clickable_to_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $this->assertFalse($this->getProtectedProperty($selector, 'rowClickable'));
    }

    public function test_constructor_sets_default_checked_to_empty_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $this->assertSame([], $this->getProtectedProperty($selector, 'checked'));
    }

    public function test_constructor_sets_default_disabled_to_empty_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $this->assertSame([], $this->getProtectedProperty($selector, 'disabled'));
    }

    // -------------------------------------------------------------------------
    // style()
    // -------------------------------------------------------------------------

    public function test_style_sets_value(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->style('danger');

        $this->assertSame('danger', $this->getProtectedProperty($selector, 'style'));
    }

    public function test_style_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->style('success');

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // background()
    // -------------------------------------------------------------------------

    public function test_background_sets_value(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->background('#ff0000');

        $this->assertSame('#ff0000', $this->getProtectedProperty($selector, 'background'));
    }

    public function test_background_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->background('#00ff00');

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // click()
    // -------------------------------------------------------------------------

    public function test_click_enables_row_clickable(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->click();

        $this->assertTrue($this->getProtectedProperty($selector, 'rowClickable'));
    }

    public function test_click_with_false_disables_row_clickable(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->click(true);
        $selector->click(false);

        $this->assertFalse($this->getProtectedProperty($selector, 'rowClickable'));
    }

    public function test_click_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->click(true);

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // check()
    // -------------------------------------------------------------------------

    public function test_check_with_array_sets_checked(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->check([1, 2, 3]);

        $this->assertSame([1, 2, 3], $this->getProtectedProperty($selector, 'checked'));
    }

    public function test_check_with_closure_sets_checked(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $closure = function ($row) {
            return $row->_index === 0;
        };
        $selector->check($closure);

        $this->assertSame($closure, $this->getProtectedProperty($selector, 'checked'));
    }

    public function test_check_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->check([]);

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // disable()
    // -------------------------------------------------------------------------

    public function test_disable_with_array_sets_disabled(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->disable([0, 5]);

        $this->assertSame([0, 5], $this->getProtectedProperty($selector, 'disabled'));
    }

    public function test_disable_with_closure_sets_disabled(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $closure = function ($row) {
            return $row->_index > 3;
        };
        $selector->disable($closure);

        $this->assertSame($closure, $this->getProtectedProperty($selector, 'disabled'));
    }

    public function test_disable_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->disable([]);

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // idColumn()
    // -------------------------------------------------------------------------

    public function test_id_column_sets_value(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->idColumn('custom_id');

        $this->assertSame('custom_id', $this->getProtectedProperty($selector, 'idColumn'));
    }

    public function test_id_column_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->idColumn('uid');

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // titleColumn()
    // -------------------------------------------------------------------------

    public function test_title_column_sets_value(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $selector->titleColumn('display_name');

        $this->assertSame('display_name', $this->getProtectedProperty($selector, 'titleColumn'));
    }

    public function test_title_column_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector->titleColumn('label');

        $this->assertSame($selector, $result);
    }

    // -------------------------------------------------------------------------
    // renderHeader()
    // -------------------------------------------------------------------------

    public function test_render_header_returns_html_string(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertIsString($html);
    }

    public function test_render_header_contains_select_all_class(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertStringContainsString('grid-select-all', $html);
    }

    public function test_render_header_contains_checkbox_input(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertStringContainsString('<input type="checkbox"', $html);
        $this->assertStringContainsString('class="select-all', $html);
    }

    public function test_render_header_contains_default_primary_style(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertStringContainsString('vs-checkbox-primary', $html);
    }

    public function test_render_header_reflects_custom_style(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->style('danger');

        $html = $selector->renderHeader();

        $this->assertStringContainsString('vs-checkbox-danger', $html);
    }

    public function test_render_header_contains_checkbox_grid_header_class(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertStringContainsString('checkbox-grid-header', $html);
    }

    public function test_render_header_contains_icon_check(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $html = $selector->renderHeader();

        $this->assertStringContainsString('icon-check', $html);
    }

    // -------------------------------------------------------------------------
    // renderColumn()
    // -------------------------------------------------------------------------

    public function test_render_column_returns_html_string(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test Row']);
        $html = $selector->renderColumn($row, 1);

        $this->assertIsString($html);
    }

    public function test_render_column_contains_row_checkbox_class(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('grid-row-checkbox', $html);
    }

    public function test_render_column_contains_data_id_attribute(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 42);

        $this->assertStringContainsString('data-id="42"', $html);
    }

    public function test_render_column_uses_custom_id_column(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->idColumn('custom_id');

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test', 'custom_id' => 99]);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('data-id="99"', $html);
    }

    public function test_render_column_uses_name_as_title_by_default(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Alice']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('data-label="Alice"', $html);
    }

    public function test_render_column_falls_back_to_title_when_name_is_empty(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => '', 'title' => 'My Title']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('data-label="My Title"', $html);
    }

    public function test_render_column_falls_back_to_username_when_name_and_title_empty(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => '', 'title' => '', 'username' => 'bob']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('data-label="bob"', $html);
    }

    public function test_render_column_falls_back_to_id_when_all_labels_empty(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => '', 'title' => '', 'username' => '']);
        $html = $selector->renderColumn($row, 7);

        $this->assertStringContainsString('data-label="7"', $html);
    }

    public function test_render_column_uses_custom_title_column(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->titleColumn('display_name');

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Alice', 'display_name' => 'Alice Smith']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('data-label="Alice Smith"', $html);
    }

    public function test_render_column_title_column_falls_back_to_id_when_value_empty(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->titleColumn('display_name');

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Alice', 'display_name' => '']);
        $html = $selector->renderColumn($row, 5);

        $this->assertStringContainsString('data-label="5"', $html);
    }

    public function test_render_column_contains_checkbox_grid_column_class(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('checkbox-grid-column', $html);
    }

    public function test_render_column_reflects_custom_style(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->style('warning');

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('vs-checkbox-warning', $html);
    }

    public function test_render_column_includes_checked_attribute_when_row_is_checked(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check([0]);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('checked="true"', $html);
    }

    public function test_render_column_excludes_checked_attribute_when_row_not_checked(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check([5, 6]);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringNotContainsString('checked="true"', $html);
    }

    public function test_render_column_includes_disabled_attribute_when_row_is_disabled(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable([0]);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertMatchesRegularExpression('/\bdisabled\b/', $html);
    }

    public function test_render_column_excludes_disabled_attribute_when_row_not_disabled(): void
    {
        $this->bindAdminServices();
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable([5]);

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertDoesNotMatchRegularExpression('/\bdisabled\b/', $html);
    }

    public function test_render_column_uses_custom_background_in_script(): void
    {
        // When background is explicitly set, Admin::color()->dark20() is NOT called,
        // so we only need admin.asset bound (not admin.color).
        $this->app->instance('admin.asset', new Asset);

        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->background('#abcdef');

        $row = $this->createMockRow(['_index' => 0, 'name' => 'Test']);
        $html = $selector->renderColumn($row, 1);

        $this->assertStringContainsString('checkbox-grid-column', $html);
    }

    // -------------------------------------------------------------------------
    // shouldChecked() -- protected method
    // -------------------------------------------------------------------------

    public function test_should_checked_returns_true_when_index_in_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check([0, 2, 4]);

        $row = $this->createMockRow(['_index' => 2]);

        $result = $this->invokeProtectedMethod($selector, 'shouldChecked', [$row]);

        $this->assertTrue($result);
    }

    public function test_should_checked_returns_false_when_index_not_in_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check([0, 2, 4]);

        $row = $this->createMockRow(['_index' => 3]);

        $result = $this->invokeProtectedMethod($selector, 'shouldChecked', [$row]);

        $this->assertFalse($result);
    }

    public function test_should_checked_with_closure_returning_true(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check(function ($row) {
            return $row->_index === 1;
        });

        $row = $this->createMockRow(['_index' => 1]);

        $result = $this->invokeProtectedMethod($selector, 'shouldChecked', [$row]);

        $this->assertTrue($result);
    }

    public function test_should_checked_with_closure_returning_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check(function ($row) {
            return $row->_index === 1;
        });

        $row = $this->createMockRow(['_index' => 99]);

        $result = $this->invokeProtectedMethod($selector, 'shouldChecked', [$row]);

        $this->assertFalse($result);
    }

    public function test_should_checked_with_empty_array_returns_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->check([]);

        $row = $this->createMockRow(['_index' => 0]);

        $result = $this->invokeProtectedMethod($selector, 'shouldChecked', [$row]);

        $this->assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // shouldDisable() -- protected method
    // -------------------------------------------------------------------------

    public function test_should_disable_returns_true_when_index_in_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable([1, 3]);

        $row = $this->createMockRow(['_index' => 3]);

        $result = $this->invokeProtectedMethod($selector, 'shouldDisable', [$row]);

        $this->assertTrue($result);
    }

    public function test_should_disable_returns_false_when_index_not_in_array(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable([1, 3]);

        $row = $this->createMockRow(['_index' => 2]);

        $result = $this->invokeProtectedMethod($selector, 'shouldDisable', [$row]);

        $this->assertFalse($result);
    }

    public function test_should_disable_with_closure_returning_true(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable(function ($row) {
            return $row->_index > 5;
        });

        $row = $this->createMockRow(['_index' => 10]);

        $result = $this->invokeProtectedMethod($selector, 'shouldDisable', [$row]);

        $this->assertTrue($result);
    }

    public function test_should_disable_with_closure_returning_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);
        $selector->disable(function ($row) {
            return $row->_index > 5;
        });

        $row = $this->createMockRow(['_index' => 2]);

        $result = $this->invokeProtectedMethod($selector, 'shouldDisable', [$row]);

        $this->assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // isSelectedRow() -- protected method
    // -------------------------------------------------------------------------

    public function test_is_selected_row_with_array_matches_index_as_int(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 3]);

        // String "3" should match _index 3 via (int) cast
        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, ['3']]);

        $this->assertTrue($result);
    }

    public function test_is_selected_row_with_array_no_match(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 3]);

        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, [0, 1, 2]]);

        $this->assertFalse($result);
    }

    public function test_is_selected_row_with_closure_calls_closure(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 7]);

        $closure = function ($r) {
            return $r->_index === 7;
        };

        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, $closure]);

        $this->assertTrue($result);
    }

    public function test_is_selected_row_with_empty_array_returns_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0]);

        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, []]);

        $this->assertFalse($result);
    }

    public function test_is_selected_row_with_multiple_matching_values(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 2]);

        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, [1, 2, 3]]);

        $this->assertTrue($result);
    }

    public function test_is_selected_row_closure_returning_false(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $row = $this->createMockRow(['_index' => 0]);

        $closure = function ($r) {
            return $r->_index === 99;
        };

        $result = $this->invokeProtectedMethod($selector, 'isSelectedRow', [$row, $closure]);

        $this->assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // Fluent API chaining
    // -------------------------------------------------------------------------

    public function test_fluent_api_chaining(): void
    {
        $grid = $this->createMockGrid();
        $selector = new RowSelector($grid);

        $result = $selector
            ->style('info')
            ->background('#ffffff')
            ->click(true)
            ->check([0, 1])
            ->disable([2])
            ->idColumn('uid')
            ->titleColumn('label');

        $this->assertSame($selector, $result);
        $this->assertSame('info', $this->getProtectedProperty($selector, 'style'));
        $this->assertSame('#ffffff', $this->getProtectedProperty($selector, 'background'));
        $this->assertTrue($this->getProtectedProperty($selector, 'rowClickable'));
        $this->assertSame([0, 1], $this->getProtectedProperty($selector, 'checked'));
        $this->assertSame([2], $this->getProtectedProperty($selector, 'disabled'));
        $this->assertSame('uid', $this->getProtectedProperty($selector, 'idColumn'));
        $this->assertSame('label', $this->getProtectedProperty($selector, 'titleColumn'));
    }
}
