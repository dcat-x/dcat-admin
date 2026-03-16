<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Support\ComposerProperty;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class TestExtensionServiceProvider extends ServiceProvider
{
    protected $type = self::TYPE_THEME;

    public function register() {}

    public function exposeSerializeConfig(array $config): string
    {
        return $this->serializeConfig($config);
    }

    public function exposeUnserializeConfig(string $config): array
    {
        return $this->unserializeConfig($config);
    }
}

class ServiceProviderTest extends TestCase
{
    protected function makeProvider(): TestExtensionServiceProvider
    {
        return new TestExtensionServiceProvider($this->app);
    }

    public function test_provider_extends_laravel_service_provider(): void
    {
        $provider = $this->makeProvider();

        $this->assertInstanceOf(LaravelServiceProvider::class, $provider);
    }

    public function test_type_theme_constant_and_get_type_are_consistent(): void
    {
        $provider = $this->makeProvider();

        $this->assertSame('theme', ServiceProvider::TYPE_THEME);
        $this->assertSame(ServiceProvider::TYPE_THEME, $provider->getType());
    }

    public function test_with_composer_property_sets_package_alias_and_name(): void
    {
        $provider = $this->makeProvider();
        $composer = new ComposerProperty([
            'name' => 'vendor/demo-extension',
            'alias' => 'demo',
        ]);

        $result = $provider->withComposerProperty($composer);

        $this->assertSame($provider, $result);
        $this->assertSame('vendor/demo-extension', $provider->getPackageName());
        $this->assertSame('vendor.demo-extension', $provider->getName());
        $this->assertSame('demo', $provider->getAlias());
    }

    public function test_path_and_resource_paths_are_resolved_from_provider_location(): void
    {
        $provider = $this->makeProvider();
        $basePath = $provider->path();

        $this->assertIsString($basePath);
        $this->assertTrue(is_dir($basePath));
        $this->assertStringEndsWith('resources/assets', $provider->getAssetPath());
        $this->assertStringEndsWith('resources/views', $provider->getViewPath());
        $this->assertStringEndsWith('resources/lang', $provider->getLangPath());
        $this->assertStringEndsWith('foo/bar', $provider->path('foo/bar'));
    }

    public function test_get_routes_returns_null_when_routes_file_missing(): void
    {
        $provider = $this->makeProvider();

        $this->assertNull($provider->getRoutes());
    }

    public function test_instance_returns_bound_provider_instance(): void
    {
        $provider = $this->makeProvider();
        $this->app->instance(TestExtensionServiceProvider::class, $provider);

        $this->assertSame($provider, TestExtensionServiceProvider::instance());
    }

    public function test_trans_builds_extension_translation_key(): void
    {
        $provider = $this->makeProvider()->withComposerProperty(new ComposerProperty([
            'name' => 'vendor/demo-extension',
        ]));
        $this->app->instance(TestExtensionServiceProvider::class, $provider);

        $result = TestExtensionServiceProvider::trans('messages.title');

        $this->assertSame('vendor.demo-extension::messages.title', $result);
    }

    public function test_serialize_and_unserialize_config_roundtrip(): void
    {
        $provider = $this->makeProvider();
        $data = ['foo' => 'bar', 'list' => [1, 2], 'nested' => ['ok' => true]];

        $serialized = $provider->exposeSerializeConfig($data);
        $restored = $provider->exposeUnserializeConfig($serialized);

        $this->assertIsString($serialized);
        $this->assertSame($data, $restored);
    }
}
