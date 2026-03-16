<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid\Concerns\HasTree;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class HasTreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): HasTreeTestHelper
    {
        return new HasTreeTestHelper;
    }

    public function test_parent_id_query_name_default(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'parentIdQueryName');
        $ref->setAccessible(true);
        $this->assertSame('_parent_id_', $ref->getValue($helper));
    }

    public function test_depth_query_name_default(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'depthQueryName');
        $ref->setAccessible(true);
        $this->assertSame('_depth_', $ref->getValue($helper));
    }

    public function test_show_all_children_nodes_default(): void
    {
        $helper = $this->makeHelper();
        $this->assertFalse($helper->showAllChildrenNodes());
    }

    public function test_allowed_tree_query_default(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'allowedTreeQuery');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($helper));
    }

    public function test_disable_bind_tree_query(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->disableBindTreeQuery();
        // Returns $this from Model trait
        $this->assertNotNull($result);

        $ref = new \ReflectionProperty($helper, 'allowedTreeQuery');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($helper));
    }

    public function test_tree_url_without_query(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->treeUrlWithoutQuery('test_key');

        $ref = new \ReflectionProperty($helper, 'treeIgnoreQueryNames');
        $ref->setAccessible(true);
        $this->assertContains('test_key', $ref->getValue($helper));
    }

    public function test_tree_url_without_query_array(): void
    {
        $helper = $this->makeHelper();
        $helper->treeUrlWithoutQuery(['key1', 'key2']);

        $ref = new \ReflectionProperty($helper, 'treeIgnoreQueryNames');
        $ref->setAccessible(true);
        $values = $ref->getValue($helper);
        $this->assertContains('key1', $values);
        $this->assertContains('key2', $values);
    }

    public function test_default_parent_id_initially_null(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'defaultParentId');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_enable_tree_method_signature(): void
    {
        $method = new \ReflectionMethod(HasTreeTestHelper::class, 'enableTree');

        $this->assertSame(3, $method->getNumberOfParameters());
    }

    public function test_get_children_page_name(): void
    {
        $helper = $this->makeHelper();
        $name = $helper->getChildrenPageName(5);
        $this->assertStringContainsString('_children_page_5', $name);
    }

    public function test_get_depth_query_name(): void
    {
        $helper = $this->makeHelper();
        $name = $helper->getDepthQueryName();
        $this->assertStringContainsString('_depth_', $name);
    }
}

class HasTreeTestHelper extends Model
{
    use HasTree;

    public function __construct()
    {
        // Skip parent constructor
        $this->request = request();
        $this->queries = new Collection;
    }

    public function getChildrenQueryNamePrefix()
    {
        return 'test_grid';
    }
}
