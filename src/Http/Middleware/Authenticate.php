<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            ! config('admin.auth.enable', true)
            || ! Admin::guard()->guest()
            || $this->shouldPassThrough($request)
        ) {
            return $next($request);
        }

        return admin_redirect('auth/login', 401);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function shouldPassThrough($request)
    {
        $excepts = array_merge(
            (array) config('admin.auth.except', []),
            Admin::context()->getArray('auth.except')
        );
        $handled = [];

        foreach ($excepts as $except) {
            if ($except === null || $except === '') {
                continue;
            }

            $except = (string) $except;

            if (isset($handled[$except])) {
                continue;
            }
            $handled[$except] = true;

            if ($request->routeIs($except)) {
                return true;
            }

            $adminRoute = admin_route_name($except);
            if ($adminRoute !== $except && $request->routeIs($adminRoute)) {
                return true;
            }

            $pathExcept = admin_base_path($except);

            if ($pathExcept !== '/') {
                $pathExcept = trim($pathExcept, '/');
            }

            if (Helper::matchRequestPath($pathExcept)) {
                return true;
            }
        }

        return false;
    }
}
