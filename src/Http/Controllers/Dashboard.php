<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

class Dashboard
{
    public static function title()
    {
        return view('admin::dashboard.title');
    }
}
