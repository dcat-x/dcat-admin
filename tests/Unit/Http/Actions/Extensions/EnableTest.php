<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\Extensions\Enable;
use Dcat\Admin\Tests\TestCase;

class EnableTest extends TestCase
{
    public function test_title_contains_enable_text(): void
    {
        $action = new Enable;

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringContainsString(trans('admin.enable'), $action->title());
    }

    public function test_handle_enables_extension_and_returns_refresh_response(): void
    {
        $manager = new class
        {
            public ?string $enabledKey = null;

            public function enable($key): void
            {
                $this->enabledKey = $key;
            }
        };

        $this->app->instance('admin.extend', $manager);

        $action = new Enable;
        $action->setKey('demo-ext');

        $response = $action->handle()->toArray();

        $this->assertSame('demo-ext', $manager->enabledKey);
        $this->assertTrue($response['status']);
        $this->assertSame('refresh', $response['data']['then']['action']);
    }
}
