<?php

namespace Dcat\Admin\Support\GlobalSearch;

abstract class ModelSearchProvider implements SearchProviderInterface
{
    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    abstract protected function model(): string;

    /**
     * @return string[]
     */
    abstract protected function searchColumns(): array;

    abstract protected function titleColumn(): string;

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    abstract protected function url($model): string;

    protected function icon(): string
    {
        return 'feather icon-circle';
    }

    protected function descriptionColumn(): ?string
    {
        return null;
    }

    public function search(string $keyword, int $limit = 5): array
    {
        $modelClass = $this->model();
        $query = $modelClass::query();

        $query->where(function ($q) use ($keyword) {
            foreach ($this->searchColumns() as $i => $column) {
                $method = $i === 0 ? 'where' : 'orWhere';
                $q->$method($column, 'like', "%{$keyword}%");
            }
        });

        return $query->limit($limit)->get()->map(function ($item) {
            $result = [
                'title' => $item->{$this->titleColumn()},
                'url' => $this->url($item),
                'icon' => $this->icon(),
            ];

            if ($desc = $this->descriptionColumn()) {
                $result['description'] = $item->{$desc};
            }

            return $result;
        })->toArray();
    }
}
