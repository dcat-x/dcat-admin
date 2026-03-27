<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Support\ClassSigner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HandleActionController
{
    /**
     * @return $this|JsonResponse
     */
    public function handle(Request $request)
    {
        $action = $this->resolveActionInstance($request);

        $action->setKey($request->get('_key'));

        if (! $action->passesAuthorization()) {
            $response = $action->failedAuthorization();
        } else {
            $response = $this->handleAction($action, $request);
        }

        return $response instanceof Response ? $response->send() : $response;
    }

    /**
     * @throws AdminException
     */
    protected function resolveActionInstance(Request $request): Action
    {
        if (! $request->has('_action')) {
            throw new AdminException('Invalid action request.');
        }

        $signed = str_replace('_', '\\', (string) $request->get('_action'));
        $actionClass = ClassSigner::verify($signed);

        if (! class_exists($actionClass)) {
            throw new AdminException("Action [{$actionClass}] does not exist.");
        }

        /** @var Action $action */
        $action = app($actionClass);

        if (! $action instanceof Action) {
            throw new AdminException("Action [{$actionClass}] must be an instance of ".Action::class.'.');
        }

        if (! method_exists($action, 'handle')) {
            throw new AdminException("Action method {$actionClass}::handle() does not exist.");
        }

        return $action;
    }

    protected function handleAction(Action $action, Request $request)
    {
        /** @phpstan-ignore method.notFound */
        return $action->handle($request);
    }
}
