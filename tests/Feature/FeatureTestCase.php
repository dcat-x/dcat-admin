<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Feature;

use Dcat\Admin\Support\Composer;
use Dcat\Admin\Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        $this->injectComposerLoader();

        parent::setUp();

        $this->app['config']->set('admin.auth.enable', false);
        $this->app['config']->set('admin.permission.enable', false);
    }

    /**
     * Inject the real Composer autoloader so that AdminServiceProvider
     * can register extensions without hitting the testbench base_path issue.
     */
    protected function injectComposerLoader(): void
    {
        $ref = new \ReflectionProperty(Composer::class, 'loader');
        if (! $ref->getValue()) {
            $ref->setValue(null, require __DIR__.'/../../vendor/autoload.php');
        }
    }
}
