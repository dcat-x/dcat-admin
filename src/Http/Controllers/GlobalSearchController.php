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
        $keyword = $request->input('q', '');
        $limit = (int) $request->input('limit', 5);

        if (strlen($keyword) < 1) {
            return response()->json(['groups' => []]);
        }

        $globalSearch = Admin::globalSearch();
        $groups = [];

        foreach ($globalSearch->getProviders() as $provider) {
            $results = $provider->search($keyword, $limit);
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
