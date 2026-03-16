<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Scope;
use Dcat\Admin\Tests\TestCase;

class ScopeTest extends TestCase
{
    protected function makeScope(string $key, string $label = ''): Scope
    {
        $parentFilter = $this->createMock(Filter::class);

        return new Scope($parentFilter, $key, $label);
    }

    public function test_constructor_sets_key(): void
    {
        $scope = $this->makeScope('active');
        $this->assertSame('active', $scope->key);
    }

    public function test_constructor_sets_label(): void
    {
        $scope = $this->makeScope('active', 'Active Users');
        $this->assertSame('Active Users', $scope->getLabel());
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
        $this->assertConditionEntryHasKey($conditions, 0, 'where');
        $this->assertSame(['status', 'active'], $conditions[0]['where']);
    }

    public function test_chained_query_methods(): void
    {
        $scope = $this->makeScope('admin');
        $scope->where('role', 'admin')->where('active', 1);

        $conditions = $scope->condition();
        $this->assertCount(2, $conditions);
        $this->assertSame(['role', 'admin'], $conditions[0]['where']);
        $this->assertSame(['active', 1], $conditions[1]['where']);
    }

    public function test_where_between_query(): void
    {
        $scope = $this->makeScope('recent');
        $scope->whereBetween('created_at', ['2024-01-01', '2024-12-31']);

        $conditions = $scope->condition();
        $this->assertCount(1, $conditions);
        $this->assertConditionEntryHasKey($conditions, 0, 'whereBetween');
    }

    public function test_order_by_query(): void
    {
        $scope = $this->makeScope('newest');
        $scope->orderBy('created_at', 'desc');

        $conditions = $scope->condition();
        $this->assertCount(1, $conditions);
        $this->assertConditionEntryHasKey($conditions, 0, 'orderBy');
        $this->assertSame(['created_at', 'desc'], $conditions[0]['orderBy']);
    }

    public function test_multiple_different_query_types(): void
    {
        $scope = $this->makeScope('complex');
        $scope->where('status', 'active')
            ->whereNotNull('email')
            ->orderBy('name', 'asc');

        $conditions = $scope->condition();
        $this->assertCount(3, $conditions);
        $this->assertConditionEntryHasKey($conditions, 0, 'where');
        $this->assertConditionEntryHasKey($conditions, 1, 'whereNotNull');
        $this->assertConditionEntryHasKey($conditions, 2, 'orderBy');
    }

    public function test_get_label(): void
    {
        $scope = $this->makeScope('trashed', 'Trashed Items');
        $this->assertSame('Trashed Items', $scope->getLabel());
    }

    private function assertConditionEntryHasKey(array $conditions, int $index, string $key): void
    {
        $this->assertIsArray($conditions[$index] ?? null);
        $this->assertContains($key, array_keys($conditions[$index]));
    }
}
