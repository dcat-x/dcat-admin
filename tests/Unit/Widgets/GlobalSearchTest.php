<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\GlobalSearch;

class GlobalSearchTest extends TestCase
{
    public function test_providers_empty_by_default(): void
    {
        $search = new GlobalSearch;

        $this->assertSame([], $search->getProviders());
    }

    public function test_provider_adds_and_returns_self(): void
    {
        $search = new GlobalSearch;
        $provider = new TestSearchProvider;

        $result = $search->provider($provider);

        $this->assertSame($search, $result);
        $this->assertCount(1, $search->getProviders());
        $this->assertSame($provider, $search->getProviders()[0]);
    }

    public function test_multiple_providers(): void
    {
        $search = new GlobalSearch;
        $p1 = new TestSearchProvider;
        $p2 = new TestSearchProvider;

        $search->provider($p1)->provider($p2);

        $this->assertCount(2, $search->getProviders());
    }

    public function test_shortcut_returns_self(): void
    {
        $search = new GlobalSearch;
        $result = $search->shortcut('Cmd+K');

        $this->assertSame($search, $result);
    }

    public function test_shortcut_default_ctrl_k(): void
    {
        $search = new GlobalSearch;
        $ref = new \ReflectionProperty($search, 'shortcut');
        $ref->setAccessible(true);

        $this->assertSame('Ctrl+K', $ref->getValue($search));
    }

    public function test_shortcut_stores_value(): void
    {
        $search = new GlobalSearch;
        $search->shortcut('Cmd+K');

        $ref = new \ReflectionProperty($search, 'shortcut');
        $ref->setAccessible(true);

        $this->assertSame('Cmd+K', $ref->getValue($search));
    }

    public function test_render_returns_empty_when_no_providers(): void
    {
        $search = new GlobalSearch;

        $this->assertSame('', $search->render());
    }
}

class TestSearchProvider implements SearchProviderInterface
{
    public function title(): string
    {
        return 'Test';
    }

    public function search(string $keyword, int $limit = 5): array
    {
        return [
            ['title' => 'Result 1', 'url' => '/test/1'],
        ];
    }
}
