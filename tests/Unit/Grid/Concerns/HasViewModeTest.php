<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasViewMode;
use Dcat\Admin\Tests\TestCase;

class HasViewModeTest extends TestCase
{
    public function test_grid_uses_has_view_mode_trait(): void
    {
        $ref = new \ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);

        $this->assertContains(HasViewMode::class, $traits);
    }

    public function test_view_mode_disabled_by_default(): void
    {
        $helper = new HasViewModeTestHelper;

        $this->assertFalse($helper->allowViewMode());
    }

    public function test_view_mode_enabled_by_default_property(): void
    {
        $helper = new HasViewModeTestHelper;
        $ref = new \ReflectionProperty($helper, 'viewModeEnabled');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($helper));
    }

    public function test_default_view_mode_is_table(): void
    {
        $helper = new HasViewModeTestHelper;
        $ref = new \ReflectionProperty($helper, 'defaultViewMode');
        $ref->setAccessible(true);

        $this->assertSame('table', $ref->getValue($helper));
    }

    public function test_view_mode_enables_feature(): void
    {
        $helper = new HasViewModeTestHelper;
        $result = $helper->viewMode('table', ['table', 'card']);

        $this->assertSame($helper, $result);
        $this->assertTrue($helper->allowViewMode());
    }

    public function test_view_mode_sets_available_modes(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->viewMode('table', ['table', 'card', 'list']);

        $this->assertSame(['table', 'card', 'list'], $helper->getAvailableViewModes());
    }

    public function test_view_mode_sets_default_mode(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->viewMode('card', ['table', 'card']);

        $ref = new \ReflectionProperty($helper, 'defaultViewMode');
        $ref->setAccessible(true);
        $this->assertSame('card', $ref->getValue($helper));
    }

    public function test_get_current_view_mode_returns_table_when_disabled(): void
    {
        $helper = new HasViewModeTestHelper;

        $this->assertSame('table', $helper->getCurrentViewMode());
    }

    public function test_get_current_view_mode_returns_default_when_no_request_param(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->viewMode('card', ['table', 'card']);

        $this->assertSame('card', $helper->getCurrentViewMode());
    }

    public function test_get_current_view_mode_falls_back_for_invalid_mode(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->viewMode('table', ['table', 'card']);

        request()->merge(['_view_' => 'invalid']);

        $this->assertSame('table', $helper->getCurrentViewMode());
    }

    public function test_get_current_view_mode_reads_from_request(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->viewMode('table', ['table', 'card']);

        request()->merge(['_view_' => 'card']);

        $this->assertSame('card', $helper->getCurrentViewMode());
    }

    public function test_render_view_mode_button_returns_empty_when_disabled(): void
    {
        $helper = new HasViewModeTestHelper;

        $this->assertSame('', $helper->renderViewModeButton());
    }

    public function test_apply_view_mode_does_nothing_when_disabled(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->view = 'admin::grid.table';

        $helper->applyViewModePublic();

        $this->assertSame('admin::grid.table', $helper->view);
    }

    public function test_apply_view_mode_keeps_table_view(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->view = 'admin::grid.table';
        $helper->viewMode('table', ['table', 'card']);

        $helper->applyViewModePublic();

        $this->assertSame('admin::grid.table', $helper->view);
    }

    public function test_apply_view_mode_switches_to_card(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->view = 'admin::grid.table';
        $helper->viewMode('table', ['table', 'card']);

        request()->merge(['_view_' => 'card']);
        $helper->applyViewModePublic();

        $this->assertSame('admin::grid.card', $helper->view);
    }

    public function test_apply_view_mode_switches_to_list(): void
    {
        $helper = new HasViewModeTestHelper;
        $helper->view = 'admin::grid.table';
        $helper->viewMode('table', ['table', 'card', 'list']);

        request()->merge(['_view_' => 'list']);
        $helper->applyViewModePublic();

        $this->assertSame('admin::grid.list', $helper->view);
    }

    public function test_mode_constants(): void
    {
        $this->assertSame('table', HasViewModeTestHelper::MODE_TABLE);
        $this->assertSame('card', HasViewModeTestHelper::MODE_CARD);
        $this->assertSame('list', HasViewModeTestHelper::MODE_LIST);
    }

    protected function tearDown(): void
    {
        request()->replace([]);
        parent::tearDown();
    }

    private function getAllTraits(\ReflectionClass $ref): array
    {
        $traits = array_keys($ref->getTraits());
        foreach ($ref->getTraits() as $trait) {
            $traits = array_merge($traits, $this->getAllTraits($trait));
        }
        if ($parent = $ref->getParentClass()) {
            $traits = array_merge($traits, $this->getAllTraits($parent));
        }

        return array_unique($traits);
    }
}

class HasViewModeTestHelper
{
    use HasViewMode;

    public $view = 'admin::grid.table';

    public function applyViewModePublic(): void
    {
        $this->applyViewMode();
    }

    public function tools()
    {
        return new class
        {
            public function format($content)
            {
                return $content;
            }
        };
    }

    public function resource()
    {
        return '/admin/test';
    }
}
