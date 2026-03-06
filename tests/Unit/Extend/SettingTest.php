<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Extend\Setting;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Mockery;

class SettingTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_setting_extends_form_and_implements_lazy_renderable(): void
    {
        $setting = new TestSetting;

        $this->assertInstanceOf(Form::class, $setting);
        $this->assertInstanceOf(LazyRenderable::class, $setting);
    }

    public function test_setting_uses_lazy_widget_trait(): void
    {
        $traits = class_uses(Setting::class);

        $this->assertContains(LazyWidget::class, array_keys($traits));
    }

    public function test_constructor_with_extension_initializes_payload_and_extension(): void
    {
        $extension = Mockery::mock(ServiceProvider::class, [$this->app])->makePartial();
        $extension->shouldReceive('getName')->once()->andReturn('vendor.package');

        $setting = new TestSetting($extension);

        $this->assertSame($extension, $this->getProtectedProperty($setting, 'extension'));

        $payload = $this->getProtectedProperty($setting, 'payload');
        $this->assertSame('vendor.package', $payload['_extension_']);
    }

    public function test_default_returns_extension_config_or_empty_array(): void
    {
        $extension = Mockery::mock(ServiceProvider::class, [$this->app])->makePartial();
        $extension->shouldReceive('config')->once()->withNoArgs()->andReturn(['enabled' => true]);

        $setting = new TestSetting($extension);
        $this->assertSame(['enabled' => true], $setting->default());

        $extensionEmpty = Mockery::mock(ServiceProvider::class, [$this->app])->makePartial();
        $extensionEmpty->shouldReceive('config')->once()->withNoArgs()->andReturn([]);

        $settingEmpty = new TestSetting($extensionEmpty);
        $this->assertSame([], $settingEmpty->default());
    }

    public function test_handle_saves_formatted_config_and_returns_success_refresh_response(): void
    {
        $extension = Mockery::mock(ServiceProvider::class, [$this->app])->makePartial();
        $extension->shouldReceive('config')
            ->once()
            ->with(['formatted' => true, 'foo' => 'bar']);

        $setting = new TestSetting($extension);

        $response = $setting->handle(['foo' => 'bar']);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $payload = $response->toArray();
        $this->assertTrue($payload['status']);
        $this->assertSame('success', $payload['data']['type']);
        $this->assertSame(trans('admin.save_succeeded'), $payload['data']['message']);
        $this->assertSame('refresh', $payload['data']['then']['action']);
        $this->assertTrue($payload['data']['then']['value']);
    }

    public function test_title_and_form_methods_are_callable_on_concrete_setting(): void
    {
        $setting = new TestSetting;

        $this->assertSame('Test Setting', $setting->title());

        $result = $setting->form();
        $this->assertNull($result);
    }
}

class TestSetting extends Setting
{
    public function form() {}

    public function title()
    {
        return 'Test Setting';
    }

    protected function formatInput(array $input)
    {
        return ['formatted' => true] + $input;
    }
}
