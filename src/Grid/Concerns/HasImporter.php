<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Importer;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Repositories\EloquentRepository;

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

    /**
     * @return array{
     *     titles?: array,
     *     rules?: array,
     *     upsert_key?: string|null,
     *     importer?: string,
     *     repository?: string,
     *     model?: string,
     *     user_id?: int|string,
     *     source?: string,
     *     expires_at?: int,
     * }
     */
    public function buildImporterConfig(): array
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

            $config['importer'] = get_class($driver);
        }

        try {
            $repository = $this->model()->repository();
            if ($repository) {
                $config['repository'] = get_class($repository);

                // 默认 EloquentRepository 基类不带类型信息，必须额外携带
                // model 类名才能在下一次请求中正确重建。
                if ($repository instanceof EloquentRepository && $modelClass = $repository->eloquentClass()) {
                    $config['model'] = $modelClass;
                }
            }
        } catch (\Throwable) {
            // Grid set up without a model (typical in unit tests) — repository
            // signal is best-effort metadata, callers that need it must wire
            // grid->model()->repository() before rendering.
        }

        // 防重放：绑定到当前用户、来源页面、过期时间。
        // 这些字段在 ImportController::execute() 中被严格校验。
        try {
            $user = Admin::user();
            if ($user) {
                $config['user_id'] = $user->getKey();
            }
        } catch (\Throwable) {
            // 测试或 CLI 环境下 auth guard 可能未配置；
            // 用户绑定字段仅在能识别当前用户时才写入。
        }

        $request = request();
        if ($request) {
            $routeName = $request->route()?->getName();
            $source = $routeName ?: $request->path();
            if ($source) {
                $config['source'] = $source;
            }
        }

        $config['expires_at'] = time() + (int) (config('admin.import.config_ttl') ?: 3600);

        return $config;
    }

    protected function registerImporterConfig(): void
    {
        ImportController::registerImporter(
            $this->getName(),
            $this->buildImporterConfig()
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
