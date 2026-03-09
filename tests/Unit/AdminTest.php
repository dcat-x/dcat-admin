<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Admin;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AdminTest extends TestCase
{
    public function test_reflection_can_load_admin_class_metadata(): void
    {
        $reflection = new \ReflectionClass(Admin::class);

        $this->assertSame(Admin::class, $reflection->getName());
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

    #[DataProvider('sectionKeyProvider')]
    public function test_section_constants(string $key): void
    {
        $this->assertIsArray(Admin::SECTION);
        $this->assertContains($key, array_keys(Admin::SECTION));
    }

    public function test_pjax_container_id(): void
    {
        // 测试默认状态
        $pjaxId = Admin::getPjaxContainerId();
        $this->assertSame('pjax-container', $pjaxId);

        // 测试禁用 pjax
        Admin::disablePjax();
        $this->assertNull(Admin::getPjaxContainerId());

        // 测试启用 pjax
        Admin::pjax(true);
        $this->assertSame('pjax-container', Admin::getPjaxContainerId());
    }

    public function test_title(): void
    {
        // 设置 title
        Admin::title('Test Title');
        $this->assertSame('Test Title', Admin::title());

        // 设置新 title
        Admin::title('New Title');
        $this->assertSame('New Title', Admin::title());
    }

    public function test_favicon(): void
    {
        Admin::favicon('/favicon.ico');
        $this->assertSame('/favicon.ico', Admin::favicon());

        Admin::favicon('/new-favicon.png');
        $this->assertSame('/new-favicon.png', Admin::favicon());
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

    public function test_mix_middleware_group_inserts_before_admin_permission(): void
    {
        $router = $this->app->make('router');
        $original = $router->getMiddlewareGroups()['admin'] ?? null;

        $router->middlewareGroup('admin', ['a', 'admin.permission', 'b']);
        Admin::mixMiddlewareGroup(['x', 'y']);

        $groups = $router->getMiddlewareGroups();
        $this->assertSame(['a', 'x', 'y', 'admin.permission', 'b'], $groups['admin']);

        if ($original !== null) {
            $router->middlewareGroup('admin', $original);
        }
    }

    public function test_mix_middleware_group_appends_when_permission_not_exists(): void
    {
        $router = $this->app->make('router');
        $original = $router->getMiddlewareGroups()['admin'] ?? null;

        $router->middlewareGroup('admin', ['a', 'b']);
        Admin::mixMiddlewareGroup(['x', 'y']);

        $groups = $router->getMiddlewareGroups();
        $this->assertSame(['a', 'b', 'x', 'y'], $groups['admin']);

        if ($original !== null) {
            $router->middlewareGroup('admin', $original);
        }
    }

    public static function sectionKeyProvider(): array
    {
        return [
            ['HEAD'],
            ['BODY_INNER_BEFORE'],
            ['BODY_INNER_AFTER'],
            ['APP_INNER_BEFORE'],
            ['APP_INNER_AFTER'],
            ['NAVBAR_USER_PANEL'],
            ['LEFT_SIDEBAR_USER_PANEL'],
            ['LEFT_SIDEBAR_MENU'],
        ];
    }
}
