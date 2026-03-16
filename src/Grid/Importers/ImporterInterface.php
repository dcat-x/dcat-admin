<?php

namespace Dcat\Admin\Grid\Importers;

use Illuminate\Http\UploadedFile;

interface ImporterInterface
{
    public function import(UploadedFile $file): ImportResult;
}
