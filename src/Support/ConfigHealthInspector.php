<?php

namespace Dcat\Admin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConfigHealthInspector
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function inspect(): array
    {
        return array_merge(
            $this->inspectMenuConfig(),
            $this->inspectPermissionConfig()
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inspectMenuConfig(): array
    {
        $menuModel = config('admin.database.menu_model');
        if (! $menuModel || ! class_exists($menuModel)) {
            return [];
        }

        /** @var Collection<int, Model> $menus */
        $menus = $menuModel::query()
            ->select(['id', 'title', 'uri'])
            ->get();

        $issues = [];
        $groupedByUri = [];

        foreach ($menus as $menu) {
            $uri = trim((string) ($menu->uri ?? ''));
            if ($uri === '') {
                continue;
            }

            $groupedByUri[$uri][] = (int) $menu->id;

            if (preg_match('/\s/', $uri)) {
                $issues[] = [
                    'type' => 'menu.uri.whitespace',
                    'message' => "Menu URI contains whitespace: {$uri}",
                    'ids' => [(int) $menu->id],
                    'value' => $uri,
                ];
            }
        }

        foreach ($groupedByUri as $uri => $ids) {
            if (count($ids) <= 1) {
                continue;
            }

            $issues[] = [
                'type' => 'menu.uri.duplicate',
                'message' => "Duplicate menu URI detected: {$uri}",
                'ids' => $ids,
                'value' => $uri,
            ];
        }

        return $issues;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inspectPermissionConfig(): array
    {
        $permissionModel = config('admin.database.permissions_model');
        if (! $permissionModel || ! class_exists($permissionModel)) {
            return [];
        }

        /** @var Collection<int, Model> $permissions */
        $permissions = $permissionModel::query()
            ->select(['id', 'slug', 'http_path'])
            ->get();

        $issues = [];
        $groupedBySlug = [];
        $allowedMethods = property_exists($permissionModel, 'httpMethods')
            ? (array) $permissionModel::$httpMethods
            : [];

        foreach ($permissions as $permission) {
            $slug = trim((string) ($permission->slug ?? ''));
            if ($slug !== '') {
                $groupedBySlug[$slug][] = (int) $permission->id;
            }

            $paths = Helper::array($permission->http_path ?? [], false);
            foreach ($paths as $path) {
                $path = (string) $path;
                if ($path === '') {
                    continue;
                }

                if (preg_match('/\s/', $path)) {
                    $issues[] = [
                        'type' => 'permission.path.whitespace',
                        'message' => "Permission path contains whitespace: {$path}",
                        'ids' => [(int) $permission->id],
                        'value' => $path,
                    ];
                }

                if (Str::contains($path, ':')) {
                    [$methods] = explode(':', $path, 2);
                    $methodItems = array_filter(explode(',', strtoupper($methods)));
                    $invalidMethods = $allowedMethods
                        ? array_diff($methodItems, $allowedMethods)
                        : [];

                    if ($invalidMethods) {
                        $issues[] = [
                            'type' => 'permission.path.invalid_method',
                            'message' => 'Permission path contains invalid HTTP method prefix',
                            'ids' => [(int) $permission->id],
                            'value' => $path,
                            'invalid' => array_values($invalidMethods),
                        ];
                    }
                }
            }
        }

        foreach ($groupedBySlug as $slug => $ids) {
            if (count($ids) <= 1) {
                continue;
            }

            $issues[] = [
                'type' => 'permission.slug.duplicate',
                'message' => "Duplicate permission slug detected: {$slug}",
                'ids' => $ids,
                'value' => $slug,
            ];
        }

        return $issues;
    }
}
