<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Department extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = config('admin.database.departments_model');

        parent::__construct($relations);
    }
}
