<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Importers;

class ImportResult
{
    public int $success = 0;

    public int $failed = 0;

    public array $errors = [];

    public function addError(int $row, string $column, string $message): void
    {
        $this->errors[$row][$column] = $message;
    }
}
