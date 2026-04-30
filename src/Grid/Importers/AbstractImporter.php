<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Importers;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Grid;
use Dcat\EasyExcel\Excel;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class AbstractImporter implements ImporterInterface
{
    /**
     * @var Grid|null
     */
    protected $grid;

    /**
     * @var Grid\Importer
     */
    protected $parent;

    /**
     * @var Repository|null
     */
    protected $repository;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var string|null
     */
    protected $upsertKey;

    /**
     * @var array
     */
    protected $titles = [];

    public function __construct(array $rules = [])
    {
        if ($rules) {
            $this->rules = $rules;
        }
    }

    /**
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
        $this->parent = $grid->importerManager();

        try {
            $repository = $grid->model()->repository();
            if ($repository instanceof Repository) {
                $this->setRepository($repository);
            }
        } catch (\Throwable) {
            // Grid may be set up without a model (e.g. unit tests with mocks);
            // repository will then be wired manually via setRepository().
        }

        return $this;
    }

    /**
     * 直接注入 repository。当 import 通过 generic endpoint 触发、
     * Grid 不可重建时使用。导入逻辑应优先依赖 repository，避免依赖 grid。
     *
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function repository(): ?Repository
    {
        return $this->repository;
    }

    /**
     * @param  array|null  $titles
     * @return $this|array
     */
    public function titles($titles = null)
    {
        if ($titles === null) {
            return $this->titles ?: ($this->titles = $this->defaultTitles());
        }

        $this->titles = $titles;

        return $this;
    }

    protected function defaultTitles(): array
    {
        if (! $this->grid) {
            return [];
        }

        return $this->grid
            ->columns()
            ->mapWithKeys(function (Grid\Column $column, $name) {
                return [$name => $column->getLabel()];
            })
            ->reject(function ($v, $k) {
                return in_array($k, ['#', Grid\Column::ACTION_COLUMN_NAME, Grid\Column::SELECT_COLUMN_NAME]);
            })
            ->toArray();
    }

    /**
     * @return $this|array
     */
    public function rules(?array $rules = null)
    {
        if ($rules === null) {
            return $this->rules;
        }

        $this->rules = $rules;

        return $this;
    }

    /**
     * @return $this|string|null
     */
    public function upsertKey(?string $key = null)
    {
        if (func_num_args() === 0) {
            return $this->upsertKey;
        }

        $this->upsertKey = $key;

        return $this;
    }

    public function template(): BinaryFileResponse
    {
        $titles = $this->titles();
        $filename = admin_trans_label().'-template.xlsx';

        return Excel::export($titles ? [array_values($titles)] : [])
            ->headings(false)
            ->download($filename);
    }

    abstract public function import(UploadedFile $file): ImportResult;

    /**
     * @return static
     */
    public static function make(array $rules = [])
    {
        return new static($rules);
    }
}
