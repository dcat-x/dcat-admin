<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\Scope;
use Dcat\Admin\Tests\TestCase;

class ScopeTest extends TestCase
{
    protected function makeScope(string $key, string $label = ''): Scope
    {
        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);

        return new Scope($parentFilter, $key, $label);
    }

    public function test_constructor_sets_key(): void
    {
        $scope = $this->makeScope('active');
        $this->assertEquals('active', $scope->key);
    }

    public function test_constructor_sets_label(): void
    {
        $scope = $this->makeScope('active', 'Active Users');
        $this->assertEquals('Active Users', $scope->getLabel());
    }

    public function test_condition_returns_empty_array_when_no_queries(): void
    {
        $scope = $this->makeScope('all');
        $conditions = $scope->condition();

        $this->assertIsArray($conditions);
        $this->assertEmpty($conditions);
    }

    public function test_magic_call_adds_query(): void
    {
        $scope = $this->makeScope('active');
        $result = $scope->where('status', 'active');

        $this->assertInstanceOf(Scope::class, $result);

        $conditions = $scope->condition();
        $this->assertCount(1, $conditions);
        $this->assertArrayHasKey('where', $conditions[0]);
        $this->assertEquals(['status', 'active'], $conditions[0]['where']);
    }

    public function test_chained_query_methods(): void
    {
        $scope = $this->makeScope('admin');
        $scope->where('role', 'admin')->where('active', 1);

        $conditions = $scope->condition();
        $this->assertCount(2, $conditions);
        $this->assertEquals(['role', 'admin'], $conditions[0]['where']);
        $this->assertEquals(['active', 1], $conditions[1]['where']);
    }

    public function test_where_between_query(): void
    {
        $scope = $this->makeScope('recent');
        $scope->whereBetween('created_at', ['2024-01-01', '2024-12-31']);

        $conditions = $scope->condition();
        $this->assertCount(1, $conditions);
        $this->assertArrayHasKey('whereBetween', $conditions[0]);
    }

    public function test_order_by_query(): void
    {
        $scope = $this->makeScope('newest');
        $scope->orderBy('created_at', 'desc');

        $conditions = $scope->condition();
        $this->assertCount(1, $conditions);
        $this->assertArrayHasKey('orderBy', $conditions[0]);
        $this->assertEquals(['created_at', 'desc'], $conditions[0]['orderBy']);
    }

    public function test_multiple_different_query_types(): void
    {
        $scope = $this->makeScope('complex');
        $scope->where('status', 'active')
            ->whereNotNull('email')
            ->orderBy('name', 'asc');

        $conditions = $scope->condition();
        $this->assertCount(3, $conditions);
        $this->assertArrayHasKey('where', $conditions[0]);
        $this->assertArrayHasKey('whereNotNull', $conditions[1]);
        $this->assertArrayHasKey('orderBy', $conditions[2]);
    }

    public function test_get_label(): void
    {
        $scope = $this->makeScope('trashed', 'Trashed Items');
        $this->assertEquals('Trashed Items', $scope->getLabel());
    }
}
