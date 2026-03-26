<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Importers;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\EasyExcel\Excel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class ExcelImporter extends AbstractImporter
{
    public function __construct(array $rules = [])
    {
        parent::__construct($rules);

        if (! class_exists(Excel::class)) {
            throw new RuntimeException('To use importer, please install [dcat/easy-excel] first.');
        }
    }

    public function import(UploadedFile $file): ImportResult
    {
        $result = new ImportResult;
        $rules = $this->rules();
        $titles = $this->titles();
        $columns = array_keys($titles);
        $upsertKey = $this->upsertKey();
        /** @var EloquentRepository $repository */
        $repository = $this->grid->model()->repository();
        $model = $repository->model();
        $rowIndex = 0;

        Excel::import($file)->first()->each(function ($row) use (&$result, $rules, $columns, $upsertKey, $model, &$rowIndex) {
            $rowIndex++;
            $data = $row->toArray();

            $mapped = [];
            $i = 0;
            foreach ($data as $value) {
                if (isset($columns[$i])) {
                    $mapped[$columns[$i]] = $value;
                }
                $i++;
            }

            if (! empty($rules)) {
                $validator = Validator::make($mapped, $rules);
                if ($validator->fails()) {
                    $result->failed++;
                    foreach ($validator->errors()->toArray() as $field => $messages) {
                        $result->addError($rowIndex, $field, implode(', ', $messages));
                    }

                    return;
                }
            }

            try {
                if ($upsertKey && isset($mapped[$upsertKey])) {
                    $model->newQuery()->updateOrCreate(
                        [$upsertKey => $mapped[$upsertKey]],
                        $mapped
                    );
                } else {
                    $model->newQuery()->create($mapped);
                }
                $result->success++;
            } catch (\Throwable $e) {
                $result->failed++;
                $result->addError($rowIndex, '_error', $e->getMessage());
            }
        });

        return $result;
    }
}
