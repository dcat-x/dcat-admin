<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\Extensions\Disable;
use Dcat\Admin\Tests\TestCase;

class DisableTest extends TestCase
{
    public function test_title_contains_disable_text(): void
    {
        $action = new Disable;

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringContainsString(trans('admin.disable'), $action->title());
    }

    public function test_handle_disables_extension_and_returns_refresh_response(): void
    {
        $manager = new class
        {
            public ?string $name = null;

            public ?bool $enabled = null;

            public function enable($name, bool $enabled): void
            {
                $this->name = $name;
                $this->enabled = $enabled;
            }
        };

        $this->app->instance('admin.extend', $manager);

        $action = new Disable;
        $action->setKey('demo-ext');

        $response = $action->handle()->toArray();

        $this->assertSame('demo-ext', $manager->name);
        $this->assertFalse($manager->enabled);
        $this->assertTrue($response['status']);
        $this->assertSame('refresh', $response['data']['then']['action']);
    }
}
