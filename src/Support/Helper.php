<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Dcat\Admin\Grid;
use Dcat\Laravel\Database\WhereHasInServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Helper
{
    /**
     * @var array
     */
    public static $fileTypes = [
        'image' => 'png|jpg|jpeg|tmp|gif',
        'word' => 'doc|docx',
        'excel' => 'xls|xlsx|csv',
        'powerpoint' => 'ppt|pptx',
        'pdf' => 'pdf',
        'code' => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'archive' => 'zip|tar\.gz|rar|rpm',
        'txt' => 'txt|pac|log|md',
        'audio' => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video' => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
    ];

    protected static $controllerNames = [];

    /**
     * 把给定的值转化为数组.
     */
    public static function array($value, bool $filter = true): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (! is_array($value)) {
            if ($value instanceof Jsonable) {
                $value = json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                $value = $value->toArray();
            } elseif (is_string($value)) {
                $array = json_decode($value, true);
                $value = is_array($array) ? $array : explode(',', $value);
            } else {
                $value = (array) $value;
            }
        }

        if (! $filter) {
            return $value;
        }

        $result = [];
        foreach ($value as $key => $item) {
            if ($item !== '' && $item !== null) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * 把给定的值转化为字符串.
     *
     * @param  string|Grid|\Closure|Renderable|Htmlable  $value
     * @param  array  $params
     * @param  object  $newThis
     */
    public static function render($value, $params = [], $newThis = null): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof \Closure) {
            $newThis && ($value = $value->bindTo($newThis));

            $value = $value(...(array) $params);
        }

        if ($value instanceof Renderable) {
            return (string) $value->render();
        }

        if ($value instanceof Htmlable) {
            return (string) $value->toHtml();
        }

        return (string) $value;
    }

    /**
     * 获取当前控制器名称.
     *
     * @return mixed|string
     */
    public static function getControllerName()
    {
        $router = app('router');

        if (! $router->current()) {
            return 'undefined';
        }

        $actionName = $router->current()->getActionName();

        if (! isset(static::$controllerNames[$actionName])) {
            $controllerClass = strstr($actionName, '@', true) ?: $actionName;
            $controller = class_basename($controllerClass);

            static::$controllerNames[$actionName] = str_replace('Controller', '', $controller);
        }

        return static::$controllerNames[$actionName];
    }

    /**
     * @param  array  $attributes
     * @return string
     */
    public static function buildHtmlAttributes($attributes)
    {
        $elements = [];

        foreach ((array) $attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            if (is_numeric($key)) {
                $key = $value;
            }

            if ($value === null) {
                continue;
            }

            $elements[] = $key.'="'.htmlentities((string) $value, ENT_QUOTES, 'UTF-8').'"';
        }

        return $elements ? implode(' ', $elements).' ' : '';
    }

    /**
     * @return string
     */
    public static function urlWithQuery(?string $url, array $query = [])
    {
        if (! $url || ! $query) {
            return $url;
        }

        $fragment = '';
        $hashPos = strpos($url, '#');
        if ($hashPos !== false) {
            $fragment = substr($url, $hashPos);
            $url = substr($url, 0, $hashPos);
        }

        if (strpos($url, '?') === false) {
            return $url.'?'.http_build_query($query).$fragment;
        }

        [$baseUrl, $queryString] = explode('?', $url, 2);
        parse_str($queryString, $originalQuery);
        $finalQuery = $originalQuery ? array_merge($originalQuery, $query) : $query;

        return $baseUrl.'?'.http_build_query($finalQuery).$fragment;
    }

    /**
     * @param  string  $url
     * @param  string|array|Arrayable  $keys
     * @return string
     */
    public static function urlWithoutQuery($url, $keys)
    {
        $url = (string) $url;

        if (strpos($url, '?') === false || ! $keys) {
            return $url;
        }

        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $keys = (array) $keys;
        if ($keys === []) {
            return $url;
        }

        [$baseUrl, $queryString] = explode('?', $url, 2);
        parse_str($queryString, $query);

        Arr::forget($query, $keys);

        return $query
            ? $baseUrl.'?'.http_build_query($query)
            : $baseUrl;
    }

    /**
     * @param  Arrayable|array|string  $keys
     * @return string
     */
    public static function fullUrlWithoutQuery($keys)
    {
        return static::urlWithoutQuery(request()->fullUrl(), $keys);
    }

    /**
     * @param  string|array  $keys
     * @return bool
     */
    public static function urlHasQuery(string $url, $keys)
    {
        $keys = (array) $keys;
        if ($keys === []) {
            return false;
        }

        $position = strpos($url, '?');
        if ($position === false) {
            return false;
        }

        $queryString = substr($url, $position + 1);
        if ($queryString === '') {
            return false;
        }

        parse_str($queryString, $query);

        foreach ($keys as $key) {
            if (Arr::has($query, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 匹配请求路径.
     *
     * @example
     *      Helper::matchRequestPath(admin_base_path('auth/user'))
     *      Helper::matchRequestPath(admin_base_path('auth/user*'))
     *      Helper::matchRequestPath(admin_base_path('auth/user/* /edit'))
     *      Helper::matchRequestPath('GET,POST:auth/user')
     *
     * @param  string  $path
     * @return bool
     */
    public static function matchRequestPath($path, ?string $current = null)
    {
        $request = request();
        $path = (string) $path;
        $current = $current ?: $request->decodedPath();

        if (strpos($path, ':') !== false) {
            [$methods, $path] = explode(':', $path, 2);
            $requestMethod = strtoupper($request->method());
            $allowMethods = array_flip(array_map('strtoupper', explode(',', $methods)));

            if (! isset($allowMethods[$requestMethod])) {
                return false;
            }
        }

        // 判断路由名称
        if ($request->routeIs($path)) {
            return true;
        }

        $adminRoute = admin_route_name($path);
        if ($adminRoute !== $path && $request->routeIs($adminRoute)) {
            return true;
        }

        if (strpos($path, '*') === false) {
            return $path === $current;
        }

        $path = strtr($path, [
            '*' => '([0-9a-z-_,])*',
            '/' => '\/',
        ]);

        return (bool) preg_match("/$path/i", $current);
    }

    /**
     * 生成层级数据.
     *
     * @param  array  $nodes
     * @param  int|string  $parentId
     * @return array
     */
    public static function buildNestedArray(
        $nodes = [],
        $parentId = 0,
        ?string $primaryKeyName = null,
        ?string $parentKeyName = null,
        ?string $childrenKeyName = null
    ) {
        $primaryKeyName = $primaryKeyName ?: 'id';
        $parentKeyName = $parentKeyName ?: 'parent_id';
        $childrenKeyName = $childrenKeyName ?: 'children';

        $normalizeId = static function ($value): string {
            if (is_numeric($value)) {
                return 'n:'.(int) $value;
            }
            if ($value === null) {
                return 'null';
            }

            return 's:'.$value;
        };

        $childrenByParent = [];
        foreach ($nodes as $node) {
            $pk = $node[$parentKeyName] ?? null;
            $childrenByParent[$normalizeId($pk)][] = $node;
        }

        $build = static function ($currentParentId) use (&$build, $childrenByParent, $normalizeId, $primaryKeyName, $childrenKeyName) {
            $branch = [];
            $currentNodes = $childrenByParent[$normalizeId($currentParentId)] ?? [];

            foreach ($currentNodes as $node) {
                $children = $build($node[$primaryKeyName] ?? null);
                if ($children) {
                    $node[$childrenKeyName] = $children;
                }
                $branch[] = $node;
            }

            return $branch;
        };

        return $build($parentId);
    }

    /**
     * @return mixed
     */
    public static function slug(string $name, string $symbol = '-')
    {
        if (strpos($name, '_') === false && ! preg_match('/[A-Z]/', $name)) {
            return $name;
        }

        $text = preg_replace('/([A-Z])/', $symbol.'$1', $name);
        $text = strtolower($text);

        return str_replace('_', $symbol, ltrim($text, $symbol));
    }

    /**
     * @param  int  $level
     * @return string
     */
    public static function exportArray(array &$array, $level = 1)
    {
        $start = '[';
        $end = ']';

        $txt = "$start\n";

        foreach ($array as $k => &$v) {
            if (is_array($v)) {
                $pre = is_string($k) ? "'$k' => " : "$k => ";

                $txt .= str_repeat(' ', $level * 4).$pre.static::exportArray($v, $level + 1).",\n";

                continue;
            }
            $t = $v;

            if ($v === true) {
                $t = 'true';
            } elseif ($v === false) {
                $t = 'false';
            } elseif ($v === null) {
                $t = 'null';
            } elseif (is_string($v)) {
                $v = str_replace("'", "\\'", $v);
                $t = "'$v'";
            }

            $pre = is_string($k) ? "'$k' => " : "$k => ";

            $txt .= str_repeat(' ', $level * 4)."{$pre}{$t},\n";
        }

        return $txt.str_repeat(' ', ($level - 1) * 4).$end;
    }

    /**
     * @return string
     */
    public static function exportArrayPhp(array $array)
    {
        return "<?php \nreturn ".static::exportArray($array).";\n";
    }

    /**
     * 删除数组中的元素.
     *
     * @param  array  $array
     * @param  mixed  $value
     */
    public static function deleteByValue(&$array, $value, bool $strict = false)
    {
        $values = (array) $value;

        if ($values === []) {
            return;
        }

        $lookup = [];
        $fallback = [];

        if ($strict) {
            foreach ($values as $candidate) {
                if (is_scalar($candidate) || $candidate === null) {
                    $lookup[gettype($candidate).':'.(string) $candidate] = true;
                } else {
                    $fallback[] = $candidate;
                }
            }

            foreach ($array as $index => $item) {
                if (is_scalar($item) || $item === null) {
                    if (isset($lookup[gettype($item).':'.(string) $item])) {
                        unset($array[$index]);
                    }

                    continue;
                }

                if ($fallback && in_array($item, $fallback, true)) {
                    unset($array[$index]);
                }
            }

            return;
        }

        foreach ($values as $candidate) {
            if (is_scalar($candidate) || $candidate === null) {
                $lookup[(string) $candidate] = true;
            } else {
                $fallback[] = $candidate;
            }
        }

        foreach ($array as $index => $item) {
            if (is_scalar($item) || $item === null) {
                if (isset($lookup[(string) $item])) {
                    unset($array[$index]);
                }

                continue;
            }

            if ($fallback && in_array($item, $fallback, false)) {
                unset($array[$index]);
            }
        }
    }

    /**
     * @param  array  $array
     * @param  mixed  $value
     */
    public static function deleteContains(&$array, $value)
    {
        $needles = array_values(array_filter((array) $value, static function ($needle) {
            return $needle !== null && $needle !== '';
        }));

        if ($needles === []) {
            return;
        }

        if (count($needles) === 1) {
            $needle = $needles[0];

            foreach ($array as $index => $item) {
                if (Str::contains($item, $needle)) {
                    unset($array[$index]);
                }
            }

            return;
        }

        foreach ($array as $index => $item) {
            if (Str::contains($item, $needles)) {
                unset($array[$index]);
            }
        }
    }

    /**
     * 颜色转亮.
     *
     * @return string
     */
    public static function colorLighten(string $color, int $amt)
    {
        if (! $amt) {
            return $color;
        }

        $hasPrefix = false;

        if (mb_strpos($color, '#') === 0) {
            $color = mb_substr($color, 1);

            $hasPrefix = true;
        }

        [$red, $blue, $green] = static::colorToRBG($color, $amt);

        return ($hasPrefix ? '#' : '').dechex($green + ($blue << 8) + ($red << 16));
    }

    /**
     * 颜色转暗.
     *
     * @return string
     */
    public static function colorDarken(string $color, int $amt)
    {
        return static::colorLighten($color, -$amt);
    }

    /**
     * 颜色透明度.
     *
     * @param  float|string  $alpha
     * @return string
     */
    public static function colorAlpha(string $color, $alpha)
    {
        if ($alpha >= 1) {
            return $color;
        }

        if (mb_strpos($color, '#') === 0) {
            $color = mb_substr($color, 1);
        }

        [$red, $blue, $green] = static::colorToRBG($color);

        return "rgba($red, $blue, $green, $alpha)";
    }

    /**
     * @return array
     */
    public static function colorToRBG(string $color, int $amt = 0)
    {
        $num = hexdec($color);

        $red = ($num >> 16) + $amt;
        $blue = (($num >> 8) & 0x00FF) + $amt;
        $green = ($num & 0x0000FF) + $amt;

        $red = max(0, min(255, $red));
        $blue = max(0, min(255, $blue));
        $green = max(0, min(255, $green));

        return [$red, $blue, $green];
    }

    /**
     * 验证扩展包名称.
     *
     * @param  string  $name
     * @return int
     */
    public static function validateExtensionName($name)
    {
        return preg_match('/^[\w\-_]+\/[\w\-_]+$/', $name);
    }

    /**
     * Get file icon.
     *
     * @param  string  $file
     * @return string
     */
    public static function getFileIcon($file = '')
    {
        $extension = File::extension($file);

        foreach (static::$fileTypes as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0) {
                return "fa fa-file-{$type}-o";
            }
        }

        return 'fa fa-file-o';
    }

    /**
     * 判断是否是ajax请求.
     *
     * @return bool
     */
    public static function isAjaxRequest(?Request $request = null)
    {
        /* @var Request $request */
        $request = $request ?: request();

        return $request->ajax() && ! $request->pjax();
    }

    /**
     * 判断是否是IE浏览器.
     *
     * @return bool
     */
    public static function isIEBrowser()
    {
        return (bool) preg_match('/Mozilla\/5\.0 \(Windows NT 10\.0; WOW64; Trident\/7\.0; rv:[0-9\.]*\) like Gecko/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
    }

    /**
     * 判断是否QQ浏览器.
     *
     * @return bool
     */
    public static function isQQBrowser()
    {
        return mb_strpos(mb_strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''), 'qqbrowser') !== false;
    }

    /**
     * @param  string  $url
     * @return void
     */
    public static function setPreviousUrl($url)
    {
        session()->flash('admin.prev.url', static::urlWithoutQuery((string) $url, '_pjax'));
    }

    /**
     * @return string
     */
    public static function getPreviousUrl()
    {
        $previousUrl = session()->get('admin.prev.url');

        return (string) ($previousUrl ? url($previousUrl) : url()->previous());
    }

    /**
     * @param  mixed  $command
     * @param  int  $timeout
     * @param  null  $input
     * @param  null  $cwd
     * @return Process
     */
    public static function process($command, $timeout = 100, $input = null, $cwd = null)
    {
        $parameters = [
            $command,
            $cwd,
            [],
            $input,
            $timeout,
        ];

        return is_string($command)
            ? Process::fromShellCommandline(...$parameters)
            : new Process(...$parameters);
    }

    /**
     * 判断两个值是否相等.
     *
     * @return bool
     */
    public static function equal($value1, $value2)
    {
        if ($value1 === null || $value2 === null) {
            return false;
        }

        if ($value1 === $value2) {
            return true;
        }

        if (! is_scalar($value1) || ! is_scalar($value2)) {
            return $value1 === $value2;
        }

        return (string) $value1 === (string) $value2;
    }

    /**
     * 判断给定的数组是是否包含给定元素.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function inArray($value, array $array)
    {
        $target = (string) $value;

        foreach ($array as $item) {
            if (is_scalar($item) || $item === null) {
                if ((string) $item === $target) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    public static function strLimit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

    /**
     * 获取类名或对象的文件路径.
     *
     * @param  string|object  $class
     * @return string
     *
     * @throws \ReflectionException
     */
    public static function guessClassFileName($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        try {
            if (class_exists($class)) {
                return (new \ReflectionClass($class))->getFileName();
            }
        } catch (\Throwable $e) {
        }

        $class = trim($class, '\\');

        $composer = Composer::parse(base_path('composer.json'));

        $map = collect($composer->autoload['psr-4'] ?? [])->mapWithKeys(function ($path, $namespace) {
            $namespace = trim($namespace, '\\').'\\';

            return [$namespace => [$namespace, $path]];
        })->sortBy(function ($_, $namespace) {
            return strlen($namespace);
        }, SORT_REGULAR, true);

        $prefix = explode('\\', $class)[0];

        if ($map->isEmpty()) {
            if (Str::startsWith($class, 'App\\')) {
                $values = ['App\\', 'app/'];
            }
        } else {
            $values = $map->filter(function ($_, $k) use ($class) {
                return Str::startsWith($class, $k);
            })->first();
        }

        if (empty($values)) {
            $values = [$prefix.'\\', self::slug($prefix).'/'];
        }

        [$namespace, $path] = $values;

        return base_path(str_replace([$namespace, '\\'], [$path, '/'], $class)).'.php';
    }

    /**
     * Is input data is has-one relation.
     */
    public static function prepareHasOneRelation(Collection $fields, array &$input)
    {
        $relations = [];
        foreach ($fields as $field) {
            $column = $field->column();

            if (is_array($column)) {
                foreach ($column as $v) {
                    if (Str::contains($v, '.')) {
                        $first = strstr($v, '.', true) ?: $v;
                        $relations[$first] = null;
                    }
                }

                continue;
            }

            if (Str::contains($column, '.')) {
                $first = strstr($column, '.', true) ?: $column;
                $relations[$first] = null;
            }
        }

        foreach ($relations as $first => $_) {
            if (! isset($input[$first]) || ! is_array($input[$first])) {
                continue;
            }

            foreach ($input[$first] as $key => $value) {
                if (is_array($value)) {
                    $input["$first.$key"] = $value;
                }
            }

            foreach (Arr::dot([$first => $input[$first]]) as $key => $value) {
                $input[$key] = $value;
            }
        }
    }

    /**
     * 设置查询条件.
     *
     * @param  mixed  $model
     * @return void
     */
    public static function withQueryCondition($model, ?string $column, string $query, array $params)
    {
        if (! Str::contains($column, '.')) {
            $model->$query($column, ...$params);

            return;
        }

        $method = $query === 'orWhere' ? 'orWhere' : 'where';
        $subQuery = $query === 'orWhere' ? 'where' : $query;

        $model->$method(function ($q) use ($column, $subQuery, $params) {
            static::withRelationQuery($q, $column, $subQuery, $params);
        });
    }

    /**
     * 设置关联关系查询条件.
     *
     * @param  mixed  $model
     * @return void
     */
    public static function withRelationQuery($model, ?string $column, string $query, array $params)
    {
        $column = explode('.', $column);

        $relColumn = array_pop($column);

        // 增加对whereHasIn的支持
        $method = class_exists(WhereHasInServiceProvider::class) ? 'whereHasIn' : 'whereHas';

        $model->$method(implode('.', $column), function ($relation) use ($relColumn, $params, $query) {
            $table = $relation->getModel()->getTable();
            $relation->$query("{$table}.{$relColumn}", ...$params);
        });
    }

    /**
     * Html转义.
     *
     * @param  array|string|object  $item
     * @return mixed
     */
    public static function htmlEntityEncode($item)
    {
        if (is_object($item)) {
            return $item;
        }
        if (is_array($item)) {
            if ($item === []) {
                return $item;
            }

            array_walk_recursive($item, function (&$value) {
                $value = htmlentities($value ?? '');
            });
        } else {
            $item = htmlentities((string) $item);
        }

        return $item;
    }

    /**
     * 格式化表单元素 name 属性.
     *
     * @param  string|array  $name
     * @return mixed|string
     */
    public static function formatElementName($name)
    {
        if (! $name) {
            return $name;
        }

        if (is_array($name)) {
            return array_map([static::class, 'formatElementName'], $name);
        }

        if (strpos($name, '.') === false) {
            return $name;
        }

        [$first, $rest] = explode('.', $name, 2);

        return $first.'['.str_replace('.', '][', $rest).']';
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array|\ArrayAccess  $array
     * @param  string|null  $key
     * @param  mixed  $value
     * @return array|\ArrayAccess
     */
    public static function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        $lastIndex = count($keys) - 1;

        for ($index = 0; $index < $lastIndex; $index++) {
            $key = $keys[$index];

            if (! isset($array[$key]) || (! is_array($array[$key]) && ! $array[$key] instanceof \ArrayAccess)) {
                $array[$key] = [];
            }

            if (is_array($array)) {
                $array = &$array[$key];
            } else {
                $remaining = implode('.', array_slice($keys, $index + 1));

                if (is_object($array[$key])) {
                    $array[$key] = static::arraySet($array[$key], $remaining, $value);
                } else {
                    $mid = $array[$key];

                    $array[$key] = static::arraySet($mid, $remaining, $value);
                }

                return $array;
            }
        }

        $array[$keys[$lastIndex]] = $value;

        return $array;
    }

    /**
     * 把下划线风格字段名转化为驼峰风格.
     *
     * @return array
     */
    public static function camelArray(array &$array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                Helper::camelArray($v);
            }

            $array[Str::camel($k)] = $v;
        }

        return $array;
    }

    /**
     * 获取文件名称.
     *
     * @param  string  $name
     * @return array|mixed
     */
    public static function basename($name)
    {
        if (! $name) {
            return $name;
        }

        $position = strrpos($name, '/');

        return $position === false
            ? $name
            : substr($name, $position + 1);
    }

    /**
     * @param  string|int  $key
     * @param  array|object  $arrayOrObject
     * @return bool
     */
    public static function keyExists($key, $arrayOrObject)
    {
        if (is_object($arrayOrObject)) {
            $arrayOrObject = static::array($arrayOrObject, false);
        }

        return array_key_exists($key, $arrayOrObject);
    }

    /**
     * 跳转.
     *
     * @param  string  $to
     * @param  Request  $request
     * @return Application|ResponseFactory|JsonResponse|RedirectResponse|Response|Redirector
     */
    public static function redirect($to, int $statusCode = 302, $request = null)
    {
        $request = $request ?: request();

        if (! URL::isValidUrl($to)) {
            $to = admin_base_path($to);
        }

        if ($request->ajax() && ! $request->pjax()) {
            return response()->json(['redirect' => $to], $statusCode);
        }

        if ($request->pjax()) {
            return response("<script>location.href = '{$to}';</script>");
        }

        $redirectCodes = [201, 301, 302, 303, 307, 308];

        return redirect($to, in_array($statusCode, $redirectCodes, true) ? $statusCode : 302);
    }
}
