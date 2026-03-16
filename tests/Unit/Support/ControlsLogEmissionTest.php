<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Concerns\ControlsLogEmission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

class ControlsLogEmissionTestHelper
{
    use ControlsLogEmission;

    public function emit(string $group, ?string $path = null): bool
    {
        return $this->shouldEmitLog($group, $path);
    }

    public function traceId(): ?string
    {
        return $this->resolveTraceId();
    }
}

class ControlsLogEmissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.log_control.audit', [
            'sample_rate' => 1.0,
            'only_paths' => [],
            'except_paths' => [],
        ]);
    }

    public function test_emit_returns_true_when_sample_rate_is_one(): void
    {
        $helper = new ControlsLogEmissionTestHelper;

        $this->assertTrue($helper->emit('audit', 'admin/users'));
    }

    public function test_emit_returns_false_when_sample_rate_is_zero(): void
    {
        $this->app['config']->set('admin.log_control.audit.sample_rate', 0);
        $helper = new ControlsLogEmissionTestHelper;

        $this->assertFalse($helper->emit('audit', 'admin/users'));
    }

    public function test_emit_respects_only_paths(): void
    {
        $this->app['config']->set('admin.log_control.audit.only_paths', ['admin/users*']);
        $helper = new ControlsLogEmissionTestHelper;

        $this->assertTrue($helper->emit('audit', 'admin/users/1'));
        $this->assertFalse($helper->emit('audit', 'admin/roles/1'));
    }

    public function test_emit_respects_except_paths(): void
    {
        $this->app['config']->set('admin.log_control.audit.except_paths', ['admin/users*']);
        $helper = new ControlsLogEmissionTestHelper;

        $this->assertFalse($helper->emit('audit', 'admin/users/1'));
        $this->assertTrue($helper->emit('audit', 'admin/roles/1'));
    }

    public function test_sampling_is_deterministic_for_same_trace_id(): void
    {
        $this->app['config']->set('admin.log_control.audit.sample_rate', 0.5);

        $request = Request::create('/admin/users', 'GET');
        $request->headers->set('X-Trace-Id', 'trace-123');
        $this->app->instance('request', $request);

        $helper = new ControlsLogEmissionTestHelper;

        $first = $helper->emit('audit', 'admin/users');
        $second = $helper->emit('audit', 'admin/users');

        $this->assertSame($first, $second);
    }

    public function test_trace_id_generated_when_missing(): void
    {
        $request = Request::create('/admin/users', 'GET');
        $this->app->instance('request', $request);

        $helper = new ControlsLogEmissionTestHelper;

        $traceId = $helper->traceId();
        $this->assertNotEmpty($traceId);
        $this->assertSame($traceId, $helper->traceId());
    }
}
