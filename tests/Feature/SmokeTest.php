<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Feature;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\GlobalSearchController;
use Dcat\Admin\Support\ClassSigner;
use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;

class SmokeTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Admin::registerApiRoutes();
    }

    // ── Global Search ──

    public function test_global_search_returns_empty_for_short_keyword(): void
    {
        $this->registerGlobalSearchRoute();

        $response = $this->getJson(route('dcat.global-search', ['q' => 'a']));

        $response->assertOk();
        $response->assertJson(['groups' => []]);
    }

    public function test_global_search_clamps_limit(): void
    {
        $provider = new class implements SearchProviderInterface
        {
            public ?int $receivedLimit = null;

            public function title(): string
            {
                return 'Test';
            }

            public function search(string $keyword, int $limit = 5): array
            {
                $this->receivedLimit = $limit;

                return [];
            }
        };

        Admin::globalSearch()->provider($provider);
        $this->registerGlobalSearchRoute();

        $this->getJson(route('dcat.global-search', ['q' => 'test', 'limit' => '999']));

        $this->assertSame(50, $provider->receivedLimit);
    }

    public function test_global_search_isolates_provider_exception(): void
    {
        $failing = new class implements SearchProviderInterface
        {
            public function title(): string
            {
                return 'Failing';
            }

            public function search(string $keyword, int $limit = 5): array
            {
                throw new \RuntimeException('boom');
            }
        };

        $working = new class implements SearchProviderInterface
        {
            public function title(): string
            {
                return 'Working';
            }

            public function search(string $keyword, int $limit = 5): array
            {
                return [['title' => 'Hit', 'url' => '/hit']];
            }
        };

        Admin::globalSearch()->provider($failing)->provider($working);
        $this->registerGlobalSearchRoute();

        $response = $this->getJson(route('dcat.global-search', ['q' => 'test']));

        $response->assertOk();
        $response->assertJsonCount(1, 'groups');
        $response->assertJsonPath('groups.0.title', 'Working');
    }

    // ── Action Signature ──

    public function test_action_rejects_nonexistent_unsigned_class(): void
    {
        // Unsigned class falls back but class doesn't exist → 500
        $response = $this->postJson(route('dcat-api.action'), [
            '_action' => 'App_Actions_FakeAction',
        ]);

        $response->assertStatus(500);
    }

    public function test_action_rejects_tampered_signature(): void
    {
        $signed = ClassSigner::sign('App\\Actions\\FakeAction');
        $tampered = 'App\\Actions\\EvilAction|'.explode('|', $signed)[1];
        $encoded = str_replace('\\', '_', $tampered);

        $response = $this->postJson(route('dcat-api.action'), [
            '_action' => $encoded,
        ]);

        $response->assertStatus(500);
    }

    // ── Form Signature ──

    public function test_form_rejects_unsigned_class(): void
    {
        $response = $this->postJson(route('dcat-api.form'), [
            '_form_' => 'App\\Widgets\\FakeForm',
        ]);

        $response->assertStatus(500);
    }

    // ── Import ──

    public function test_import_execute_validates_file(): void
    {
        $response = $this->postJson(route('dcat-api.import.execute'), [
            '_grid' => 'test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('import_file');
    }

    // ── Helpers ──

    protected function registerGlobalSearchRoute(): void
    {
        $this->app['router']->get(
            config('admin.route.prefix', 'admin').'/_global-search',
            [GlobalSearchController::class, 'search']
        )->name('dcat.global-search');
    }

    protected function tearDown(): void
    {
        // Reset global search singleton
        $ref = new \ReflectionProperty(Admin::class, 'globalSearch');
        $ref->setValue(null, null);

        parent::tearDown();
    }
}
