<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Admin;
use Dcat\Admin\Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_class_exists(): void
    {
        $this->assertTrue(class_exists(Admin::class));
    }

    public function test_admin_version(): void
    {
        $this->assertIsString(Admin::VERSION);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', Admin::VERSION);
    }

    public function test_long_version(): void
    {
        $longVersion = Admin::longVersion();
        $this->assertIsString($longVersion);
        $this->assertStringContainsString(Admin::VERSION, $longVersion);
        $this->assertStringContainsString('Dcat Admin', $longVersion);
    }

    public function test_section_constants(): void
    {
        $this->assertIsArray(Admin::SECTION);
        $this->assertArrayHasKey('HEAD', Admin::SECTION);
        $this->assertArrayHasKey('BODY_INNER_BEFORE', Admin::SECTION);
        $this->assertArrayHasKey('BODY_INNER_AFTER', Admin::SECTION);
        $this->assertArrayHasKey('APP_INNER_BEFORE', Admin::SECTION);
        $this->assertArrayHasKey('APP_INNER_AFTER', Admin::SECTION);
        $this->assertArrayHasKey('NAVBAR_USER_PANEL', Admin::SECTION);
        $this->assertArrayHasKey('LEFT_SIDEBAR_USER_PANEL', Admin::SECTION);
        $this->assertArrayHasKey('LEFT_SIDEBAR_MENU', Admin::SECTION);
    }

    public function test_pjax_container_id(): void
    {
        // 测试默认状态
        $pjaxId = Admin::getPjaxContainerId();
        $this->assertEquals('pjax-container', $pjaxId);

        // 测试禁用 pjax
        Admin::disablePjax();
        $this->assertNull(Admin::getPjaxContainerId());

        // 测试启用 pjax
        Admin::pjax(true);
        $this->assertEquals('pjax-container', Admin::getPjaxContainerId());
    }

    public function test_title(): void
    {
        // 设置 title
        Admin::title('Test Title');
        $this->assertEquals('Test Title', Admin::title());

        // 设置新 title
        Admin::title('New Title');
        $this->assertEquals('New Title', Admin::title());
    }

    public function test_favicon(): void
    {
        Admin::favicon('/favicon.ico');
        $this->assertEquals('/favicon.ico', Admin::favicon());

        Admin::favicon('/new-favicon.png');
        $this->assertEquals('/new-favicon.png', Admin::favicon());
    }

    public function test_context(): void
    {
        $context = Admin::context();
        $this->assertNotNull($context);
        $this->assertInstanceOf(\Dcat\Admin\Support\Context::class, $context);
    }

    public function test_should_prevent(): void
    {
        // 默认不应该阻止
        $this->assertFalse(Admin::shouldPrevent());

        // 添加内容后应该阻止
        Admin::prevent('test content');
        $this->assertTrue(Admin::shouldPrevent());
    }

    public function test_add_ignore_query_name(): void
    {
        Admin::addIgnoreQueryName('test_query');
        $ignoreNames = Admin::getIgnoreQueryNames();
        $this->assertContains('test_query', $ignoreNames);

        Admin::addIgnoreQueryName(['query1', 'query2']);
        $ignoreNames = Admin::getIgnoreQueryNames();
        $this->assertContains('query1', $ignoreNames);
        $this->assertContains('query2', $ignoreNames);
    }

    public function test_json(): void
    {
        $response = Admin::json(['status' => true, 'message' => 'success']);
        $this->assertInstanceOf(\Dcat\Admin\Http\JsonResponse::class, $response);
    }

    public function test_is_dark_mode(): void
    {
        $result = Admin::isDarkMode();
        $this->assertIsBool($result);
    }
}
