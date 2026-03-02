<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Mockery;

class ServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class structure
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ServiceProvider::class));
    }

    public function test_is_abstract(): void
    {
        $ref = new \ReflectionClass(ServiceProvider::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_is_subclass_of_laravel_service_provider(): void
    {
        $this->assertTrue(is_subclass_of(ServiceProvider::class, LaravelServiceProvider::class));
    }

    public function test_uses_can_import_menu_trait(): void
    {
        $traits = class_uses(ServiceProvider::class);

        $this->assertContains('Dcat\Admin\Extend\CanImportMenu', $traits);
    }

    // -------------------------------------------------------
    // Constants
    // -------------------------------------------------------

    public function test_type_theme_constant(): void
    {
        $this->assertSame('theme', ServiceProvider::TYPE_THEME);
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_method_get_name_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getName'));
    }

    public function test_method_get_alias_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getAlias'));
    }

    public function test_method_get_package_name_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getPackageName'));
    }

    public function test_method_get_type_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getType'));
    }

    public function test_method_get_version_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getVersion'));
    }

    public function test_method_get_latest_version_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getLatestVersion'));
    }

    public function test_method_get_local_latest_version_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getLocalLatestVersion'));
    }

    public function test_method_path_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'path'));
    }

    public function test_method_get_logo_path_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getLogoPath'));
    }

    public function test_method_get_logo_base64_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getLogoBase64'));
    }

    public function test_method_enabled_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'enabled'));
    }

    public function test_method_disabled_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'disabled'));
    }

    public function test_method_config_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'config'));
    }

    public function test_method_save_config_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'saveConfig'));
    }

    public function test_method_update_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'update'));
    }

    public function test_method_uninstall_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'uninstall'));
    }

    public function test_method_publishable_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'publishable'));
    }

    public function test_method_register_routes_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'registerRoutes'));
    }

    public function test_method_with_composer_property_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'withComposerProperty'));
    }

    public function test_method_setting_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'setting'));
    }

    public function test_method_trans_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'trans'));
    }

    public function test_method_instance_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'instance'));
    }

    public function test_method_get_asset_path_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getAssetPath'));
    }

    public function test_method_get_view_path_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getViewPath'));
    }

    public function test_method_get_lang_path_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getLangPath'));
    }

    public function test_method_get_routes_exists(): void
    {
        $this->assertTrue(method_exists(ServiceProvider::class, 'getRoutes'));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_js_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ServiceProvider::class, 'js');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_css_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ServiceProvider::class, 'css');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_middleware_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ServiceProvider::class, 'middleware');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_except_routes_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(ServiceProvider::class, 'exceptRoutes');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    // -------------------------------------------------------
    // Final methods
    // -------------------------------------------------------

    public function test_boot_is_final(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'boot');

        $this->assertTrue($ref->isFinal());
    }

    public function test_get_asset_path_is_final(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'getAssetPath');

        $this->assertTrue($ref->isFinal());
    }

    public function test_get_view_path_is_final(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'getViewPath');

        $this->assertTrue($ref->isFinal());
    }

    public function test_get_lang_path_is_final(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'getLangPath');

        $this->assertTrue($ref->isFinal());
    }

    public function test_get_routes_is_final(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'getRoutes');

        $this->assertTrue($ref->isFinal());
    }

    // -------------------------------------------------------
    // serializeConfig / unserializeConfig roundtrip
    // -------------------------------------------------------

    public function test_serialize_config_returns_json(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'serializeConfig');
        $ref->setAccessible(true);

        // Create a concrete subclass via anonymous class
        $provider = new class($this->app) extends ServiceProvider
        {
            public function register() {}
        };

        $data = ['key' => 'value', 'nested' => ['a' => 1]];
        $result = $ref->invoke($provider, $data);

        $this->assertIsString($result);
        $this->assertSame(json_encode($data), $result);
    }

    public function test_unserialize_config_parses_json(): void
    {
        $ref = new \ReflectionMethod(ServiceProvider::class, 'unserializeConfig');
        $ref->setAccessible(true);

        $provider = new class($this->app) extends ServiceProvider
        {
            public function register() {}
        };

        $data = ['key' => 'value', 'nested' => ['a' => 1]];
        $json = json_encode($data);
        $result = $ref->invoke($provider, $json);

        $this->assertIsArray($result);
        $this->assertSame($data, $result);
    }

    public function test_serialize_unserialize_roundtrip(): void
    {
        $serialize = new \ReflectionMethod(ServiceProvider::class, 'serializeConfig');
        $serialize->setAccessible(true);

        $unserialize = new \ReflectionMethod(ServiceProvider::class, 'unserializeConfig');
        $unserialize->setAccessible(true);

        $provider = new class($this->app) extends ServiceProvider
        {
            public function register() {}
        };

        $original = ['foo' => 'bar', 'list' => [1, 2, 3], 'empty' => []];
        $serialized = $serialize->invoke($provider, $original);
        $restored = $unserialize->invoke($provider, $serialized);

        $this->assertSame($original, $restored);
    }
}
