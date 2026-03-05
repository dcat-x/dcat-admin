<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\Extensions\Uninstall;
use Dcat\Admin\Tests\TestCase;

class UninstallTest extends TestCase
{
    public function test_title_contains_uninstall_text(): void
    {
        $action = new Uninstall;

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringContainsString(trans('admin.uninstall'), $action->title());
    }

    public function test_confirm_returns_translated_message_and_key(): void
    {
        $action = new Uninstall;
        $action->setKey('demo-ext');

        $confirm = $action->confirm();

        $this->assertSame(trans('admin.confirm_uninstall'), $confirm[0]);
        $this->assertSame('demo-ext', $confirm[1]);
    }

    public function test_handle_rolls_back_and_uninstalls_extension(): void
    {
        $extension = new class
        {
            public bool $uninstalled = false;

            public function uninstall(): void
            {
                $this->uninstalled = true;
            }
        };

        $manager = new class($extension)
        {
            public array $notes = ['rollback-1', 'rollback-2'];

            public ?string $rollbackKey = null;

            public ?string $loadedKey = null;

            public function __construct(private object $extension) {}

            public function updateManager()
            {
                return $this;
            }

            public function rollback($key)
            {
                $this->rollbackKey = $key;

                return $this;
            }

            public function get($key): object
            {
                $this->loadedKey = $key;

                return $this->extension;
            }
        };

        $this->app->instance('admin.extend', $manager);

        $action = new Uninstall;
        $action->setKey('demo-ext');

        $response = $action->handle()->toArray();

        $this->assertSame('demo-ext', $manager->rollbackKey);
        $this->assertSame('demo-ext', $manager->loadedKey);
        $this->assertTrue($extension->uninstalled);
        $this->assertTrue($response['status']);
        $this->assertSame('refresh', $response['data']['then']['action']);
        $this->assertStringContainsString('rollback-1', $response['data']['message']);
        $this->assertStringContainsString('rollback-2', $response['data']['message']);
    }
}
