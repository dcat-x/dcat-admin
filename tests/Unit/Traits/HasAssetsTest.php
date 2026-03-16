<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasAssets;
use Mockery;

class HasAssetsTestClass
{
    use HasAssets;
}

class HasAssetsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_asset_returns_asset_instance(): void
    {
        $asset = HasAssetsTestClass::asset();

        $this->assertInstanceOf(Asset::class, $asset);
    }

    public function test_css_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('css')->once()->with('test.css');
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::css('test.css');

        $this->addToAssertionCount(1);
    }

    public function test_js_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('js')->once()->with('test.js');
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::js('test.js');

        $this->addToAssertionCount(1);
    }

    public function test_script_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('script')->once()->with('console.log("test")', false);
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::script('console.log("test")');

        $this->addToAssertionCount(1);
    }

    public function test_style_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('style')->once()->with('.test { color: red; }');
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::style('.test { color: red; }');

        $this->addToAssertionCount(1);
    }

    public function test_base_css_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('baseCss')->once()->with(['base.css'], true);
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::baseCss(['base.css']);

        $this->addToAssertionCount(1);
    }

    public function test_base_js_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('baseJs')->once()->with(['base.js'], true);
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::baseJs(['base.js']);

        $this->addToAssertionCount(1);
    }

    public function test_header_js_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('headerJs')->once()->with('header.js', true);
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::headerJs('header.js');

        $this->addToAssertionCount(1);
    }

    public function test_require_assets_delegates_to_asset(): void
    {
        $mock = Mockery::mock(Asset::class);
        $mock->shouldReceive('require')->once()->with('@jquery', []);
        $this->app->instance('admin.asset', $mock);

        HasAssetsTestClass::requireAssets('@jquery');

        $this->addToAssertionCount(1);
    }
}
