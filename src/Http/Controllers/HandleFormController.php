<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Embeds;
use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Form\Field\HasMany;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Support\ClassSigner;
use Dcat\Admin\Traits\HasUploadedFile;
use Dcat\Admin\Widgets\Form;
use Illuminate\Http\Request;

class HandleFormController
{
    use HasUploadedFile;

    public function handle(Request $request)
    {
        $form = $this->resolveForm($request);

        if (! $form->passesAuthorization()) {
            return $form->failedAuthorization();
        }

        $this->buildForm($form);

        if ($errors = $form->validate($request)) {
            return $form->validationErrorsResponse($errors);
        }

        $input = $form->sanitize($request->all());

        return $this->sendResponse($this->handleForm($form, $input));
    }

    public function uploadFile(Request $request)
    {
        $form = $this->resolveForm($request);

        $this->buildForm($form);

        $field = $this->getField($request, $form);

        if (! $field) {
            return $this->responseErrorMessage('Field not found.');
        }

        /** @var File $field */
        return $field->upload($this->file());
    }

    /**
     * @return Field|null
     */
    protected function getField(Request $request, $form)
    {
        $column = $this->uploader()->upload_column ?: $request->get('_column');

        if (! $relation = $request->get('_relation')) {
            return $form->field($column);
        }

        $relation = is_array($relation) ? current($relation) : $relation;

        $relationField = $form->field($relation);

        if (! $relationField) {
            return null;
        }

        if ($relationField instanceof HasMany) {
            return $relationField->buildNestedForm()->field($column);
        }
        if ($relationField instanceof Embeds) {
            return $relationField->field($column);
        }
    }

    public function destroyFile(Request $request)
    {
        $form = $this->resolveForm($request);

        $this->buildForm($form);

        $field = $this->getField($request, $form);

        if (! $field) {
            return $this->responseErrorMessage('Field not found.');
        }

        /** @var File $field */
        $field->deleteFile($request->key);

        return $this->responseDeleted();
    }

    /**
     * @return Form
     *
     * @throws AdminException
     */
    protected function resolveForm(Request $request)
    {
        if (! $request->has(Form::REQUEST_NAME)) {
            throw new AdminException('Invalid form request.');
        }

        $formClass = ClassSigner::verify(
            (string) $request->get(Form::REQUEST_NAME)
        );

        if (! class_exists($formClass)) {
            throw new AdminException("Form [{$formClass}] does not exist.");
        }

        /** @var Form $form */
        $form = app($formClass);

        if (! $form instanceof Form) {
            throw new AdminException("Form [{$formClass}] must be an instance of ".Form::class.'.');
        }

        if (! method_exists($form, 'handle')) {
            throw new AdminException("Form method {$formClass}::handle() does not exist.");
        }

        return $form;
    }

    protected function sendResponse($response)
    {
        if ($response instanceof JsonResponse) {
            return $response->send();
        }

        return $response;
    }

    protected function buildForm(Form $form): void
    {
        call_user_func([$form, 'form']);
    }

    protected function handleForm(Form $form, array $input)
    {
        return call_user_func([$form, 'handle'], $input);
    }
}
