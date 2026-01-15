<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class DataRule extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = config('admin.database.data_rules_model') ?: \Dcat\Admin\Models\DataRule::class;

        parent::__construct($relations);
    }
}
