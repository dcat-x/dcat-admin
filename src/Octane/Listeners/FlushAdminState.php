<?php

declare(strict_types=1);

namespace Dcat\Admin\Octane\Listeners;

use Dcat\Admin\AdminServiceProvider;
use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Support\JavaScript;
use Illuminate\Container\Container;

class FlushAdminState
{
    protected $adminServices = [
        'admin.app',
        'admin.asset',
        'admin.color',
        'admin.sections',
        'admin.extend',
        'admin.extend.update',
        'admin.extend.version',
        'admin.navbar',
        'admin.menu',
        'admin.context',
        'admin.setting',
        'admin.web-uploader',
        'admin.translator',
    ];

    /** @var array<int, class-string<Resettable>> */
    protected static array $resettables = [
        ImportController::class,
        JavaScript::class,
        Form::class,
        Column::class,
    ];

    protected $app;

    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    public function handle($event): void
    {
        $this->flushStaticState();

        $provider = new AdminServiceProvider($this->app);

        $this->forgetServiceInstances();

        $provider->registerServices();
        $provider->registerExtensions();
        $provider->boot();
    }

    protected function flushStaticState(): void
    {
        foreach (static::$resettables as $class) {
            $class::resetState();
        }
    }

    protected function forgetServiceInstances(): void
    {
        foreach ($this->adminServices as $service) {
            $this->app->forgetInstance($service);
        }
    }
}
