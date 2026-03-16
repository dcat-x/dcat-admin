<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ValueController
{
    /**
     * @return mixed
     */
    public function handle(Request $request)
    {
        $instance = $this->resolve($request);

        if (! $instance->passesAuthorization()) {
            return $instance->failedAuthorization();
        }

        $response = $instance->handle($request);

        if ($response) {
            return $response;
        }

        if (method_exists($instance, 'valueResult')) {
            return $instance->valueResult();
        }
    }

    /**
     * @return object
     *
     * @throws Exception
     */
    protected function resolve(Request $request)
    {
        if (! $key = $request->get('_key')) {
            throw new Exception('Invalid request.');
        }

        if (! class_exists($key)) {
            throw new Exception("Class [{$key}] does not exist.");
        }

        $instance = app($key);

        if (! method_exists($instance, 'handle') || ! method_exists($instance, 'passesAuthorization')) {
            throw new Exception("Class [{$key}] is not a valid value request handler.");
        }

        return $instance;
    }
}
