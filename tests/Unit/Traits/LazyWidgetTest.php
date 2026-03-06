<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\LazyWidget;
use Mockery;

class LazyWidgetTestHelper
{
    use LazyWidget;

    public function getPayload(): array
    {
        return $this->payload;
    }
}

class LazyWidgetTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_helper_uses_lazy_widget_trait(): void
    {
        $traits = class_uses(LazyWidgetTestHelper::class);

        $this->assertContains(LazyWidget::class, $traits);
    }

    public function test_payload_default_empty(): void
    {
        $helper = new LazyWidgetTestHelper;

        $this->assertSame([], $helper->getPayload());
    }

    public function test_payload_merges_data(): void
    {
        $helper = new LazyWidgetTestHelper;

        $helper->payload(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $helper->getPayload());
    }

    public function test_payload_returns_self(): void
    {
        $helper = new LazyWidgetTestHelper;

        $result = $helper->payload(['key' => 'value']);

        $this->assertSame($helper, $result);
    }

    public function test_payload_merges_multiple_calls(): void
    {
        $helper = new LazyWidgetTestHelper;

        $helper->payload(['a' => 1]);
        $helper->payload(['b' => 2]);

        $this->assertSame(['a' => 1, 'b' => 2], $helper->getPayload());
    }

    public function test_payload_overwrites_existing_keys(): void
    {
        $helper = new LazyWidgetTestHelper;

        $helper->payload(['key' => 'old']);
        $helper->payload(['key' => 'new']);

        $this->assertSame(['key' => 'new'], $helper->getPayload());
    }

    public function test_get_renderable_name_replaces_backslash(): void
    {
        $helper = new LazyWidgetTestHelper;

        $reflection = new \ReflectionMethod($helper, 'getRenderableName');

        $result = $reflection->invoke($helper);

        $this->assertStringNotContainsString('\\', $result);
    }

    public function test_get_renderable_name_uses_static_class(): void
    {
        $helper = new LazyWidgetTestHelper;

        $reflection = new \ReflectionMethod($helper, 'getRenderableName');

        $result = $reflection->invoke($helper);

        $expected = str_replace('\\', '_', LazyWidgetTestHelper::class);
        $this->assertSame($expected, $result);
    }

    public function test_translation_method_signature(): void
    {
        $method = new \ReflectionMethod(LazyWidgetTestHelper::class, 'translation');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_get_url_method_signature(): void
    {
        $method = new \ReflectionMethod(LazyWidgetTestHelper::class, 'getUrl');

        $this->assertSame(0, $method->getNumberOfParameters());
    }
}
