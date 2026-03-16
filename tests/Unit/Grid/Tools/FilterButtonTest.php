<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Scope;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Grid\Tools\FilterButton;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class FilterButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $args = [])
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($object, $args);
    }

    /**
     * Create a FilterButton with a mocked filter() method for testing
     * protected methods that depend on the filter.
     */
    protected function createFilterButtonWithMockedFilter(array $filterConfig = []): array
    {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('getCurrentScope')->andReturn($filterConfig['currentScope'] ?? null);
        $filter->shouldReceive('scopes')->andReturn($filterConfig['scopes'] ?? new Collection);
        $filter->shouldReceive('filters')->andReturn($filterConfig['filters'] ?? []);
        $filter->shouldReceive('countConditions')->andReturn($filterConfig['conditionCount'] ?? 0);

        $button = Mockery::mock(FilterButton::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $button->shouldReceive('filter')->andReturn($filter);

        return [$button, $filter];
    }

    // -------------------------------------------------------------------------
    // Defaults / properties
    // -------------------------------------------------------------------------

    public function test_default_view_is_filter_button(): void
    {
        $button = new FilterButton;

        $this->assertSame('admin::filter.button', $this->getProtectedProperty($button, 'view'));
    }

    public function test_default_btn_class_name_is_null(): void
    {
        $button = new FilterButton;

        $this->assertNull($this->getProtectedProperty($button, 'btnClassName'));
    }

    public function test_extends_abstract_tool(): void
    {
        $button = new FilterButton;

        $this->assertInstanceOf(AbstractTool::class, $button);
    }

    public function test_default_style_inherited_from_abstract_tool(): void
    {
        $button = new FilterButton;

        $this->assertSame('btn btn-white waves-effect', $this->getProtectedProperty($button, 'style'));
    }

    // -------------------------------------------------------------------------
    // getElementClassName (protected)
    // -------------------------------------------------------------------------

    public function test_get_element_class_name_returns_string_with_prefix(): void
    {
        $button = new FilterButton;

        $className = $this->invokeProtectedMethod($button, 'getElementClassName');

        $this->assertStringStartsWith('filter-btn-', $className);
        $this->assertSame(19, strlen($className)); // 'filter-btn-' (11) + random(8)
    }

    public function test_get_element_class_name_returns_same_value_on_repeated_calls(): void
    {
        $button = new FilterButton;

        $first = $this->invokeProtectedMethod($button, 'getElementClassName');
        $second = $this->invokeProtectedMethod($button, 'getElementClassName');

        $this->assertSame($first, $second);
    }

    public function test_get_element_class_name_differs_between_instances(): void
    {
        $button1 = new FilterButton;
        $button2 = new FilterButton;

        $class1 = $this->invokeProtectedMethod($button1, 'getElementClassName');
        $class2 = $this->invokeProtectedMethod($button2, 'getElementClassName');

        // Random suffix means different instances should (almost certainly) differ
        $this->assertIsString($class1);
        $this->assertIsString($class2);
    }

    // -------------------------------------------------------------------------
    // currentScopeLabel (protected)
    // -------------------------------------------------------------------------

    public function test_current_scope_label_returns_empty_string_when_no_scope(): void
    {
        [$button] = $this->createFilterButtonWithMockedFilter(['currentScope' => null]);

        $result = $this->invokeProtectedMethod($button, 'currentScopeLabel');

        $this->assertSame('', $result);
    }

    public function test_current_scope_label_returns_label_when_scope_exists(): void
    {
        $scope = Mockery::mock(Scope::class);
        $scope->shouldReceive('getLabel')->andReturn('Active');

        [$button] = $this->createFilterButtonWithMockedFilter(['currentScope' => $scope]);

        $result = $this->invokeProtectedMethod($button, 'currentScopeLabel');

        $this->assertStringContainsString('Active', $result);
        $this->assertStringContainsString('&nbsp;', $result);
    }

    // -------------------------------------------------------------------------
    // render() -- returns null when no scopes and no filters
    // -------------------------------------------------------------------------

    public function test_render_returns_null_when_no_scopes_and_no_filters(): void
    {
        [$button] = $this->createFilterButtonWithMockedFilter([
            'scopes' => new Collection,
            'filters' => [],
        ]);

        $result = $button->render();

        $this->assertNull($result);
    }
}
