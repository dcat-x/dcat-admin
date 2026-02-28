<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_class_exists(): void
    {
        $this->assertTrue(class_exists(Dashboard::class));
    }

    public function test_title_method_is_static(): void
    {
        $reflection = new \ReflectionMethod(Dashboard::class, 'title');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function test_title_method_returns_view(): void
    {
        $result = Dashboard::title();

        $this->assertInstanceOf(\Illuminate\View\View::class, $result);
    }

    public function test_title_method_uses_correct_view_name(): void
    {
        $result = Dashboard::title();

        $this->assertEquals('admin::dashboard.title', $result->getName());
    }

    public function test_title_method_returns_renderable(): void
    {
        $result = Dashboard::title();

        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $result);
        $this->assertInstanceOf(\Illuminate\Contracts\Support\Renderable::class, $result);
    }

    public function test_dashboard_has_no_constructor(): void
    {
        $reflection = new \ReflectionClass(Dashboard::class);

        // Dashboard should not define its own constructor
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $this->assertNotEquals(Dashboard::class, $constructor->getDeclaringClass()->getName());
        } else {
            $this->assertNull($constructor);
        }
    }
}
