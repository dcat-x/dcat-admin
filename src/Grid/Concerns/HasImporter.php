<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importer;
use Dcat\Admin\Grid\Tools;

trait HasImporter
{
    /**
     * @var Importer
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

        return (new Tools\ImportButton($this))->render();
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
