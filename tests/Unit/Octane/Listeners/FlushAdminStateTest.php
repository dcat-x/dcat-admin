<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Octane\Listeners;

use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Octane\Listeners\FlushAdminState;
use Dcat\Admin\Support\JavaScript;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;

class FlushAdminStateTest extends TestCase
{
    public function test_constructor_sets_app(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'app');
        $reflection->setAccessible(true);
        $this->assertSame($this->app, $reflection->getValue($listener));
    }

    public function test_admin_services_is_array(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'adminServices');
        $reflection->setAccessible(true);
        $services = $reflection->getValue($listener);

        $this->assertIsArray($services);
        $this->assertNotEmpty($services);
    }

    public function test_admin_services_contains_expected_keys(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'adminServices');
        $reflection->setAccessible(true);
        $services = $reflection->getValue($listener);

        $this->assertContains('admin.app', $services);
        $this->assertContains('admin.asset', $services);
        $this->assertContains('admin.color', $services);
        $this->assertContains('admin.context', $services);
        $this->assertContains('admin.menu', $services);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(FlushAdminState::class, 'handle');

        $this->assertSame(1, $method->getNumberOfParameters());
    }

    public function test_resettables_list_contains_all_required_classes(): void
    {
        $ref = new \ReflectionProperty(FlushAdminState::class, 'resettables');
        $resettables = $ref->getValue();

        $this->assertContains(ImportController::class, $resettables);
        $this->assertContains(JavaScript::class, $resettables);
        $this->assertContains(Form::class, $resettables);
        $this->assertContains(Column::class, $resettables);
    }

    public function test_resettables_all_implement_interface(): void
    {
        $ref = new \ReflectionProperty(FlushAdminState::class, 'resettables');
        $resettables = $ref->getValue();

        foreach ($resettables as $class) {
            $this->assertTrue(
                is_subclass_of($class, Resettable::class),
                "{$class} does not implement Resettable"
            );
        }
    }

    public function test_handle_flushes_static_state(): void
    {
        // Populate static state
        ImportController::registerImporter('octane-test', ['titles' => ['a' => 'A']]);
        JavaScript::make('alert("leak")');
        Column::setOriginalGridModels(new Collection([['id' => 1]]));

        // Trigger flush
        $listener = new FlushAdminState($this->app);
        $listener->handle((object) []);

        // Verify all cleared
        $importerRef = new \ReflectionProperty(ImportController::class, 'importerRegistry');
        $this->assertSame([], $importerRef->getValue());

        $this->assertSame([], JavaScript::all());

        $columnRef = new \ReflectionProperty(Column::class, 'originalGridModels');
        $this->assertNull($columnRef->getValue());
    }
}
