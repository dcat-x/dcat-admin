<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\Extensions\Update;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;

class UpdateTest extends TestCase
{
    public function test_title_uses_latest_version_from_row_extension(): void
    {
        $extension = new class
        {
            public function getLocalLatestVersion(): string
            {
                return '2.1.0';
            }
        };

        $action = new Update;
        $action->setRow(new Fluent(['extension' => $extension]));

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringStartsWith('<b>', $action->title());
        $this->assertStringEndsWith('</b>', $action->title());
    }

    public function test_handle_updates_extension_and_returns_refresh_response_with_notes(): void
    {
        $manager = new class
        {
            public array $notes = ['note-1', 'note-2'];

            public ?string $updatedKey = null;

            public function updateManager()
            {
                return $this;
            }

            public function update($key)
            {
                $this->updatedKey = $key;

                return $this;
            }
        };

        $this->app->instance('admin.extend', $manager);

        $action = new Update;
        $action->setKey('demo-ext');

        $response = $action->handle()->toArray();

        $this->assertSame('demo-ext', $manager->updatedKey);
        $this->assertTrue($response['status']);
        $this->assertSame('refresh', $response['data']['then']['action']);
        $this->assertStringContainsString('note-1', $response['data']['message']);
        $this->assertStringContainsString('note-2', $response['data']['message']);
    }
}
