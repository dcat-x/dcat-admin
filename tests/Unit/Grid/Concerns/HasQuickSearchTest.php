<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasQuickSearch;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasQuickSearchTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_allow_quick_search_returns_false_initially(): void
    {
        $helper = new HasQuickSearchTestHelper;
        $this->assertFalse($helper->allowQuickSearch());
    }

    public function test_get_quick_search_returns_null_initially(): void
    {
        $helper = new HasQuickSearchTestHelper;
        $this->assertNull($helper->getQuickSearch());
    }

    public function test_render_quick_search_returns_empty_when_not_set(): void
    {
        $helper = new HasQuickSearchTestHelper;
        $this->assertSame('', $helper->renderQuickSearch());
    }

    public function test_search_property_initially_null(): void
    {
        $helper = new HasQuickSearchTestHelper;
        $ref = new \ReflectionProperty($helper, 'search');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_quick_search_property_initially_null(): void
    {
        $helper = new HasQuickSearchTestHelper;
        $ref = new \ReflectionProperty($helper, 'quickSearch');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_quick_search_method_signature(): void
    {
        $method = new \ReflectionMethod(HasQuickSearchTestHelper::class, 'quickSearch');

        $this->assertSame(1, $method->getNumberOfParameters());
    }

    public function test_apply_quick_search_method_signature(): void
    {
        $method = new \ReflectionMethod(HasQuickSearchTestHelper::class, 'applyQuickSearch');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_add_where_bindings_is_protected(): void
    {
        $ref = new \ReflectionMethod(HasQuickSearchTestHelper::class, 'addWhereBindings');
        $this->assertTrue($ref->isProtected());
    }

    public function test_parse_query_bindings_is_protected(): void
    {
        $ref = new \ReflectionMethod(HasQuickSearchTestHelper::class, 'parseQueryBindings');
        $this->assertTrue($ref->isProtected());
    }
}

class HasQuickSearchTestHelper extends Grid
{
    use HasQuickSearch;

    public function __construct()
    {
        // Skip parent constructor
    }
}
