<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\RenderableController;
use Dcat\Admin\Tests\Fixtures\Http\Controllers\FakeLazyRenderable;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

class RenderableControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        FakeLazyRenderable::reset();
    }

    public function test_handle_returns_failed_authorization_response_when_not_authorized(): void
    {
        FakeLazyRenderable::$authorized = false;
        FakeLazyRenderable::$failedAuthorizationResponse = 'denied';

        $controller = new RenderableController;
        $request = Request::create('/_handle_renderable_', 'GET', [
            'renderable' => 'Dcat_Admin_Tests_Fixtures_Http_Controllers_FakeLazyRenderable',
        ]);

        $result = $controller->handle($request);

        $this->assertSame('denied', $result);
    }

    public function test_handle_builds_renderable_with_payload_and_require_assets(): void
    {
        $controller = new RenderableController;
        $request = Request::create('/_handle_renderable_', 'GET', [
            '_trans_' => 'admin.test.path',
            'renderable' => 'Dcat_Admin_Tests_Fixtures_Http_Controllers_FakeLazyRenderable',
            'foo' => 'bar',
        ]);

        $result = $controller->handle($request);

        $this->assertIsString($result);
        $this->assertStringContainsString('fake-lazy-renderable', $result);
        $this->assertSame('bar', FakeLazyRenderable::$payload['foo'] ?? null);
        $this->assertTrue(FakeLazyRenderable::$requireAssetsCalled);
    }

    public function test_handle_supports_underscore_class_name_mapping(): void
    {
        $controller = new RenderableController;
        $request = Request::create('/_handle_renderable_', 'GET', [
            'renderable' => 'Dcat_Admin_Tests_Fixtures_Http_Controllers_FakeLazyRenderable',
        ]);

        $result = $controller->handle($request);

        $this->assertIsString($result);
        $this->assertStringContainsString('fake-lazy-renderable', $result);
    }
}
