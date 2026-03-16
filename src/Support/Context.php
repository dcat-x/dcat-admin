<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Class Context.
 *
 * @property string $favicon
 * @property string $metaTitle
 * @property string $pjaxContainerId
 * @property array|null $html
 * @property array|null $ignoreQueries
 * @property array|null $jsVariables
 * @property string $translation
 * @property array $contents
 */
class Context extends Fluent
{
    public function set($key, $value = null)
    {
        $data = is_array($key) ? $key : [$key => $value];

        foreach ($data as $key => $value) {
            if (is_string($key) && strpos($key, '.') === false) {
                $this->attributes[$key] = $value;
            } else {
                Arr::set($this->attributes, $key, $value);
            }
        }

        return $this;
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    public function remember($key, \Closure $callback)
    {
        if (($value = $this->get($key)) !== null) {
            return $value;
        }

        return tap($callback(), function ($value) use ($key) {
            $this->set($key, $value);
        });
    }

    public function getArray($key, $default = null)
    {
        return Helper::array($this->get($key, $default), false);
    }

    public function add($key, $value, $k = null)
    {
        $results = $this->getArray($key);

        if ($k === null) {
            $results[] = $value;
        } else {
            $results[$k] = $value;
        }

        return $this->set($key, $results);
    }

    public function merge($key, array $value)
    {
        if ($value === []) {
            return $this;
        }

        $results = $this->getArray($key);

        return $this->set($key, array_merge($results, $value));
    }

    public function forget($keys)
    {
        Arr::forget($this->attributes, $keys);
    }

    public function flush()
    {
        $this->attributes = [];
    }
}
