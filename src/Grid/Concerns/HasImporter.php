<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importer;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Http\Controllers\ImportController;

trait HasImporter
{
    /**
     * @var Importer|null
     */
    protected $importer;

    /**
     * @var bool
     */
    protected $enableImporter = false;

    /**
     * Set importer driver for Grid to import.
     *
     * @param  string|Grid\Importers\AbstractImporter|null  $importerDriver
     * @return Importer
     */
    public function import($importerDriver = null)
    {
        $this->enableImporter = true;

        $importer = $this->importerManager();

        if ($importerDriver) {
            $importer->resolve($importerDriver);
        }

        return $importer;
    }

    /**
     * @return Importer
     */
    public function importerManager()
    {
        return $this->importer ?: ($this->importer = new Importer($this));
    }

    /**
     * Render import button.
     *
     * @return string
     */
    public function renderImportButton()
    {
        if (! $this->allowImporter()) {
            return '';
        }

        $this->registerImporterConfig();

        return (new Tools\ImportButton($this))->render();
    }

    protected function registerImporterConfig(): void
    {
        $driver = $this->importerManager()->driver();
        $config = [];

        if ($driver instanceof Grid\Importers\AbstractImporter) {
            $titles = $driver->titles();
            if ($titles) {
                $config['titles'] = $titles;
            }

            $rules = $driver->rules();
            if ($rules) {
                $config['rules'] = $rules;
            }

            $upsertKey = $driver->upsertKey();
            if ($upsertKey) {
                $config['upsert_key'] = $upsertKey;
            }
        }

        ImportController::registerImporter(
            $this->getName(),
            $config
        );
    }

    /**
     * If grid shows import button.
     *
     * @return bool
     */
    public function allowImporter()
    {
        return $this->enableImporter;
    }
}
