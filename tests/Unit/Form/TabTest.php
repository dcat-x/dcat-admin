<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Layout;
use Dcat\Admin\Form\Tab;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class TabTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockForm(): Form
    {
        $form = Mockery::mock(Form::class);
        $form->shouldReceive('fields')->andReturn(new Collection);
        $form->shouldReceive('rows')->andReturn([]);
        $layout = Mockery::mock(Layout::class);
        $layout->shouldReceive('reset')->andReturnNull();
        $form->shouldReceive('layout')->andReturn($layout);

        return $form;
    }

    public function test_constructor_creates_instance(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);
        $this->assertInstanceOf(Tab::class, $tab);
    }

    public function test_is_empty_initially(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);
        $this->assertTrue($tab->isEmpty());
    }

    public function test_has_rows_false_by_default(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);
        $this->assertFalse($tab->hasRows);
    }

    public function test_append_adds_tab(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $result = $tab->append('Tab 1', function () {});

        $this->assertSame($tab, $result);
        $this->assertFalse($tab->isEmpty());
    }

    public function test_append_multiple_tabs(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {});
        $tab->append('Tab 2', function () {});

        $tabs = $tab->getTabs();
        $this->assertCount(2, $tabs);
    }

    public function test_get_tabs_auto_activates_first(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {});
        $tab->append('Tab 2', function () {});

        $tabs = $tab->getTabs();
        $this->assertTrue($tabs->first()['active']);
    }

    public function test_append_with_active_flag(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {});
        $tab->append('Tab 2', function () {}, true);

        $tabs = $tab->getTabs();
        $this->assertTrue($tabs->last()['active']);
    }

    public function test_append_with_custom_id(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {}, false, 'custom-tab-id');

        $tabs = $tab->getTabs();
        $this->assertSame('custom-tab-id', $tabs->first()['id']);
    }

    public function test_active_by_title(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {});
        $tab->append('Tab 2', function () {});

        $tab->active('Tab 2');

        $tabs = $tab->getTabs();
        $this->assertFalse($tabs->get(0)['active']);
        $this->assertTrue($tabs->get(1)['active']);
    }

    public function test_active_by_index(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('Tab 1', function () {});
        $tab->append('Tab 2', function () {});

        $tab->activeByIndex(1);

        $tabs = $tab->getTabs();
        $this->assertFalse($tabs->get(0)['active']);
        $this->assertTrue($tabs->get(1)['active']);
    }

    public function test_tab_title_stored(): void
    {
        $form = $this->createMockForm();
        $tab = new Tab($form);

        $tab->append('My Tab Title', function () {});

        $tabs = $tab->getTabs();
        $this->assertSame('My Tab Title', $tabs->first()['title']);
    }
}
