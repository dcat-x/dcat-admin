<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;

class DashboardTest extends TestCase
{
    public function test_title_is_invokable_statically(): void
    {
        $this->assertTrue(is_callable([Dashboard::class, 'title']));
    }

    public function test_title_method_returns_view(): void
    {
        $result = Dashboard::title();

        $this->assertInstanceOf(View::class, $result);
    }

    public function test_title_method_uses_correct_view_name(): void
    {
        $result = Dashboard::title();

        $this->assertSame('admin::dashboard.title', $result->getName());
    }

    public function test_title_method_returns_renderable(): void
    {
        $result = Dashboard::title();

        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $result);
        $this->assertInstanceOf(Renderable::class, $result);
    }
}
