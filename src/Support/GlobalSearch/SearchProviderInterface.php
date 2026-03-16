<?php

namespace Dcat\Admin\Support\GlobalSearch;

interface SearchProviderInterface
{
    public function title(): string;

    /**
     * @return array<int, array{title: string, url: string, icon?: string, description?: string}>
     */
    public function search(string $keyword, int $limit = 5): array;
}
