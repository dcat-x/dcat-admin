<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importers\ImporterInterface;

/**
 * @mixin Grid\Importers\AbstractImporter
 */
class Importer
{
    /**
     * @var array
     */
    protected static $drivers = [];

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Grid\Importers\AbstractImporter
     */
    protected $driver;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public static function extend($driver, $extend)
    {
        static::$drivers[$driver] = $extend;
    }

    /**
     * @param  string|Grid\Importers\AbstractImporter|null  $driver
     * @return Grid\Importers\AbstractImporter
     */
    public function resolve($driver = null)
    {
        if ($this->driver) {
            return $this->driver;
        }

        if ($driver && $driver instanceof Grid\Importers\AbstractImporter) {
            $this->driver = $driver->setGrid($this->grid);
        } elseif ($driver && $driver instanceof ImporterInterface) {
            $this->driver = $driver;
        } else {
            $this->driver = $this->newDriver($driver);
        }

        return $this->driver;
    }

    /**
     * @return Importers\AbstractImporter
     */
    public function driver()
    {
        return $this->driver ?: $this->resolve();
    }

    /**
     * @param  string  $driver
     * @return ImporterInterface
     */
    protected function newDriver($driver)
    {
        if (! $driver || ! array_key_exists($driver, static::$drivers)) {
            return $this->makeDefaultDriver();
        }

        $driver = new static::$drivers[$driver];

        if (method_exists($driver, 'setGrid')) {
            $driver->setGrid($this->grid);
        }

        return $driver;
    }

    /**
     * @return Grid\Importers\ExcelImporter
     */
    public function makeDefaultDriver()
    {
        return Grid\Importers\ExcelImporter::make()->setGrid($this->grid);
    }

    /**
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->driver()->$method(...$arguments);

        return $this;
    }
}
