<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\GlobalSearchController;
use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

class GlobalSearchControllerTest extends TestCase
{
    public function test_empty_keyword_returns_empty_groups(): void
    {
        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => '']);

        $response = $controller->search($request);
        $data = $response->getData(true);

        $this->assertSame(['groups' => []], $data);
    }

    public function test_short_keyword_returns_empty_groups(): void
    {
        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => 'a']);

        $response = $controller->search($request);
        $data = $response->getData(true);

        $this->assertSame(['groups' => []], $data);
    }

    public function test_limit_is_clamped_to_max_50(): void
    {
        $provider = new FakeSearchProvider('Test', []);
        Admin::globalSearch()->provider($provider);

        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => 'test', 'limit' => '999']);

        $controller->search($request);

        $this->assertSame(50, $provider->lastLimit);
    }

    public function test_limit_is_clamped_to_min_1(): void
    {
        $provider = new FakeSearchProvider('Test', []);
        Admin::globalSearch()->provider($provider);

        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => 'test', 'limit' => '0']);

        $controller->search($request);

        $this->assertSame(1, $provider->lastLimit);
    }

    public function test_provider_exception_is_isolated(): void
    {
        $failing = new FailingSearchProvider;
        $working = new FakeSearchProvider('Working', [
            ['title' => 'Result', 'url' => '/result'],
        ]);

        Admin::globalSearch()->provider($failing)->provider($working);

        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => 'test']);

        $response = $controller->search($request);
        $data = $response->getData(true);

        $this->assertCount(1, $data['groups']);
        $this->assertSame('Working', $data['groups'][0]['title']);
    }

    public function test_successful_search_returns_grouped_results(): void
    {
        $provider = new FakeSearchProvider('Menus', [
            ['title' => 'Users', 'url' => '/admin/users'],
            ['title' => 'Settings', 'url' => '/admin/settings'],
        ]);
        Admin::globalSearch()->provider($provider);

        $controller = new GlobalSearchController;
        $request = Request::create('/_global-search', 'GET', ['q' => 'test']);

        $response = $controller->search($request);
        $data = $response->getData(true);

        $this->assertCount(1, $data['groups']);
        $this->assertSame('Menus', $data['groups'][0]['title']);
        $this->assertCount(2, $data['groups'][0]['items']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Reset global search singleton to get a fresh instance
        $ref = new \ReflectionProperty(Admin::class, 'globalSearch');
        $ref->setValue(null, null);
    }

    protected function tearDown(): void
    {
        $ref = new \ReflectionProperty(Admin::class, 'globalSearch');
        $ref->setValue(null, null);

        parent::tearDown();
    }
}

class FakeSearchProvider implements SearchProviderInterface
{
    public ?int $lastLimit = null;

    public function __construct(
        private readonly string $title,
        private readonly array $results,
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    public function search(string $keyword, int $limit = 5): array
    {
        $this->lastLimit = $limit;

        return $this->results;
    }
}

class FailingSearchProvider implements SearchProviderInterface
{
    public function title(): string
    {
        return 'Failing';
    }

    public function search(string $keyword, int $limit = 5): array
    {
        throw new \RuntimeException('Provider error');
    }
}
