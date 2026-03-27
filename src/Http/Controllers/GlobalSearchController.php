<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GlobalSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->input('q', ''));
        $limit = min(max((int) $request->input('limit', 5), 1), 50);

        if (strlen($keyword) < 2) {
            return response()->json(['groups' => []]);
        }

        $globalSearch = Admin::globalSearch();
        $groups = [];

        foreach ($globalSearch->getProviders() as $provider) {
            try {
                $results = $provider->search($keyword, $limit);
            } catch (\Throwable $e) {
                report($e);

                continue;
            }

            if (! empty($results)) {
                $groups[] = [
                    'title' => $provider->title(),
                    'items' => $results,
                ];
            }
        }

        return response()->json(['groups' => $groups]);
    }
}
