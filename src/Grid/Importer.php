<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importers\ImporterInterface;

/**
 * @mixin Importers\AbstractImporter
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
     * @var Importers\AbstractImporter|ImporterInterface|null
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
     * @param  string|Importers\AbstractImporter|ImporterInterface|null  $driver
     * @return Importers\AbstractImporter|ImporterInterface
     */
    public function resolve($driver = null)
    {
        if ($this->driver) {
            return $this->driver;
        }

        if ($driver && $driver instanceof Importers\AbstractImporter) {
            $this->driver = $driver->setGrid($this->grid);
        } elseif ($driver && $driver instanceof ImporterInterface) {
            $this->driver = $driver;
        } else {
            $this->driver = $this->newDriver($driver);
        }

        return $this->driver;
    }

    /**
     * @return Importers\AbstractImporter|ImporterInterface
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
     * @return Importers\ExcelImporter
     */
    public function makeDefaultDriver()
    {
        return Importers\ExcelImporter::make()->setGrid($this->grid);
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
