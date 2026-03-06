<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\CanImportMenu;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanImportMenuTestHelper
{
    use CanImportMenu;

    public function getName()
    {
        return 'test-extension';
    }

    protected function getMenuModel()
    {
        return null;
    }

    public function exposeMenu()
    {
        return $this->menu();
    }

    public function exposeMenuValidationRules()
    {
        return $this->menuValidationRules;
    }

    public function exposeGetParentMenuId($parent)
    {
        return $this->getParentMenuId($parent);
    }
}

class CanImportMenuTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(CanImportMenu::class));
    }

    public function test_menu_returns_empty_array(): void
    {
        $helper = new CanImportMenuTestHelper;

        $this->assertEquals([], $helper->exposeMenu());
    }

    public function test_menu_validation_rules_has_title_required(): void
    {
        $helper = new CanImportMenuTestHelper;

        $rules = $helper->exposeMenuValidationRules();

        $this->assertEquals('required', $rules['title'] ?? null);
    }

    public function test_validate_menu_passes_with_title(): void
    {
        $helper = new CanImportMenuTestHelper;

        $result = $helper->validateMenu([
            'title' => 'Test Menu',
            'uri' => '/test',
            'icon' => 'fa-home',
        ]);

        $this->assertTrue($result);
    }

    public function test_validate_menu_fails_without_title(): void
    {
        $helper = new CanImportMenuTestHelper;

        $result = $helper->validateMenu([
            'uri' => '/test',
            'icon' => 'fa-home',
        ]);

        $this->assertFalse($result);
    }

    public function test_get_parent_menu_id_with_numeric(): void
    {
        $helper = new CanImportMenuTestHelper;

        $this->assertEquals(5, $helper->exposeGetParentMenuId(5));
        $this->assertEquals(0, $helper->exposeGetParentMenuId(0));
        $this->assertEquals('123', $helper->exposeGetParentMenuId('123'));
    }
}
