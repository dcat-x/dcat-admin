<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ConfigHealthInspector
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function inspect(): array
    {
        return $this->inspectByScope('all');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inspectByScope(string $scope, bool $forceRefresh = false): array
    {
        $scope = strtolower(trim($scope));
        if (! in_array($scope, ['all', 'menu', 'permission'], true)) {
            $scope = 'all';
        }

        $ttl = (int) config('admin.health_check.cache_ttl', 0);
        if (! $forceRefresh && $ttl > 0) {
            $cacheKey = sprintf(
                'admin:health-check:%s:%s',
                $scope,
                md5((string) config('admin.database.connection').':'.(string) config('admin.route.prefix'))
            );

            return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($scope) {
                return $this->inspectByScope($scope, true);
            });
        }

        if ($scope === 'menu') {
            return $this->inspectMenuConfig();
        }

        if ($scope === 'permission') {
            return $this->inspectPermissionConfig();
        }

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
                    'severity' => 'warning',
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
                'severity' => 'warning',
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
                        'severity' => 'warning',
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
                            'severity' => 'error',
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
                'severity' => 'error',
                'message' => "Duplicate permission slug detected: {$slug}",
                'ids' => $ids,
                'value' => $slug,
            ];
        }

        return $issues;
    }
}
