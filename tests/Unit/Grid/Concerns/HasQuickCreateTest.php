<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasQuickCreate;
use Dcat\Admin\Grid\Tools\QuickCreate;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasQuickCreateTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_has_quick_create_returns_false_initially(): void
    {
        $helper = new HasQuickCreateTestHelper;
        $this->assertFalse($helper->hasQuickCreate());
    }

    public function test_quick_create_initializes_quick_create_instance_and_returns_self(): void
    {
        $helper = new HasQuickCreateTestHelper;

        $received = null;
        $result = $helper->quickCreate(function ($quickCreate) use (&$received) {
            $received = $quickCreate;
        });

        $this->assertSame($helper, $result);
        $this->assertTrue($helper->hasQuickCreate());
        $this->assertInstanceOf(QuickCreate::class, $received);
    }

    public function test_render_quick_create_uses_column_count_and_returns_rendered_content(): void
    {
        $helper = new HasQuickCreateTestHelper;
        $helper->columns = collect(['id', 'name', 'email']);

        $quickCreate = Mockery::mock();
        $quickCreate->shouldReceive('render')->once()->with(3)->andReturn('quick-create-html');

        $ref = new \ReflectionProperty($helper, 'quickCreate');
        $ref->setAccessible(true);
        $ref->setValue($helper, $quickCreate);

        $this->assertSame('quick-create-html', $helper->renderQuickCreate());
    }

    public function test_quick_create_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasQuickCreateTestHelper::class, 'quickCreate');
        $this->assertTrue($ref->isProtected());
    }
}

class HasQuickCreateTestHelper extends Grid
{
    use HasQuickCreate;

    public $columns;

    public function __construct()
    {
        // Skip parent constructor
    }
}
