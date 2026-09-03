<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Views;

use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\MessageBag;
use PHPUnit\Framework\Attributes\DataProvider;

class FlashMessagePartialsTest extends TestCase
{
    protected function tearDown(): void
    {
        session()->forget(['error', 'success', 'info', 'warning', 'dcat-admin-toastr']);

        parent::tearDown();
    }

    #[DataProvider('alertTypeProvider')]
    public function test_alerts_render_json_serialized_message_bags(string $type, string $title, string $message): void
    {
        session()->put($type, $this->jsonSessionPayload([
            'title' => $title,
            'message' => $message,
        ]));

        $html = view('admin::partials.alerts')->render();

        $this->assertStringContainsString($title, $html);
        $this->assertStringContainsString($message, $html);
    }

    public function test_alerts_still_render_message_bag_objects(): void
    {
        session()->put('success', new MessageBag([
            'title' => 'Generated',
            'message' => 'Controller created',
        ]));

        $html = view('admin::partials.alerts')->render();

        $this->assertStringContainsString('Generated', $html);
        $this->assertStringContainsString('Controller created', $html);
    }

    public function test_toastr_renders_json_serialized_message_bag(): void
    {
        session()->put('dcat-admin-toastr', $this->jsonSessionPayload([
            'type' => 'warning',
            'message' => 'Generated with warnings',
            'options' => ['timeOut' => 1000],
        ]));

        $html = view('admin::partials.toastr')->render();

        $this->assertStringContainsString("toastr.warning('Generated with warnings'", $html);
        $this->assertStringContainsString('{"timeOut":1000}', $html);
    }

    public function test_toastr_still_renders_message_bag_objects(): void
    {
        session()->put('dcat-admin-toastr', new MessageBag([
            'type' => 'info',
            'message' => 'Generation queued',
        ]));

        $html = view('admin::partials.toastr')->render();

        $this->assertStringContainsString("toastr.info('Generation queued'", $html);
    }

    public static function alertTypeProvider(): array
    {
        return [
            'error' => ['error', 'Generation failed', 'Unable to create model'],
            'success' => ['success', 'Generation complete', 'Controller created'],
            'info' => ['info', 'Generation info', 'Migration skipped'],
            'warning' => ['warning', 'Generation warning', 'File already exists'],
        ];
    }

    private function jsonSessionPayload(array $messages): array
    {
        $payload = json_decode(
            (string) json_encode(new MessageBag($messages), JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertIsArray($payload);

        return $payload;
    }
}
