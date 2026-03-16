<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Menu extends EloquentRepository
{
    public function __construct($modelOrRelations = [])
    {
        $this->eloquentClass = config('admin.database.menu_model');

        parent::__construct($modelOrRelations);
    }
}
