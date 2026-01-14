<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Traits\Macroable;

class Actions extends AbstractDisplayer
{
    use Macroable;

    /**
     * @var array
     */
    protected $appends = [];

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * @var array
     */
    protected $custom = [];

    /**
     * Default actions.
     *
     * @var array
     */
    protected $actions = [
        'view' => true,
        'edit' => true,
        'quickEdit' => false,
        'delete' => true,
    ];

    /**
     * @var string
     */
    protected $resource;

    /**
     * Action button text and icon configuration.
     */
    protected string $quickEditText = '';

    protected string $quickEditIcon = 'edit';

    protected string $editText = '';

    protected string $editIcon = 'edit-1';

    protected string $viewText = '';

    protected string $viewIcon = 'eye';

    protected string $deleteText = '';

    protected string $deleteIcon = 'trash';

    /**
     * Set quick edit button text.
     */
    public function setQuickEditText(string $val): static
    {
        $this->quickEditText = $val;

        return $this;
    }

    /**
     * Set quick edit button icon.
     */
    public function setQuickEditIcon(string $val): static
    {
        $this->quickEditIcon = $val;

        return $this;
    }

    /**
     * Set edit button text.
     */
    public function setEditText(string $val): static
    {
        $this->editText = $val;

        return $this;
    }

    /**
     * Set edit button icon.
     */
    public function setEditIcon(string $val): static
    {
        $this->editIcon = $val;

        return $this;
    }

    /**
     * Set view button text.
     */
    public function setViewText(string $val): static
    {
        $this->viewText = $val;

        return $this;
    }

    /**
     * Set view button icon.
     */
    public function setViewIcon(string $val): static
    {
        $this->viewIcon = $val;

        return $this;
    }

    /**
     * Set delete button text.
     */
    public function setDeleteText(string $val): static
    {
        $this->deleteText = $val;

        return $this;
    }

    /**
     * Set delete button icon.
     */
    public function setDeleteIcon(string $val): static
    {
        $this->deleteIcon = $val;

        return $this;
    }

    /**
     * Add a custom action.
     */
    public function add($action): static
    {
        $this->prepareAction($action);

        $this->custom[] = $action;

        return $this;
    }

    /**
     * Append a action.
     */
    public function append($action)
    {
        $this->prepareAction($action);

        array_push($this->appends, $action);

        return $this;
    }

    /**
     * Prepend a action.
     */
    public function prepend($action)
    {
        $this->prepareAction($action);

        array_unshift($this->prepends, $action);

        return $this;
    }

    protected function prepareAction(&$action)
    {
        if ($action instanceof RowAction) {
            $action->setGrid($this->grid)
                ->setColumn($this->column)
                ->setRow($this->row);
        }

        return $action;
    }

    public function view(bool $value = true)
    {
        return $this->setAction('view', $value);
    }

    /**
     * Disable view action.
     */
    public function disableView(bool $disable = true)
    {
        return $this->setAction('view', ! $disable);
    }

    public function delete(bool $value = true)
    {
        return $this->setAction('delete', $value);
    }

    /**
     * Disable delete.
     */
    public function disableDelete(bool $disable = true)
    {
        return $this->setAction('delete', ! $disable);
    }

    public function edit(bool $value = true)
    {
        return $this->setAction('edit', $value);
    }

    /**
     * Disable edit.
     */
    public function disableEdit(bool $disable = true)
    {
        return $this->setAction('edit', ! $disable);
    }

    public function quickEdit(bool $value = true)
    {
        return $this->setAction('quickEdit', $value);
    }

    /**
     * Disable quick edit.
     */
    public function disableQuickEdit(bool $disable = true)
    {
        return $this->setAction('quickEdit', ! $disable);
    }

    protected function setAction(string $key, bool $value)
    {
        $this->actions[$key] = $value;

        return $this;
    }

    /**
     * Set resource of current resource.
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource of current resource.
     */
    public function resource()
    {
        return $this->resource ?: parent::resource();
    }

    protected function resetDefaultActions(): void
    {
        $this->view($this->grid->option('view_button'));
        $this->edit($this->grid->option('edit_button'));
        $this->quickEdit($this->grid->option('quick_edit_button'));
        $this->delete($this->grid->option('delete_button'));
    }

    protected function call(array $callbacks = []): void
    {
        foreach ($callbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback->call($this->row, $this);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $callbacks = [])
    {
        $this->resetDefaultActions();

        $this->call($callbacks);

        $toString = [Helper::class, 'render'];

        $prepends = array_map($toString, $this->prepends);
        $appends = array_map($toString, $this->appends);
        $customs = array_map($toString, $this->custom);

        foreach ($this->actions as $action => $enable) {
            if ($enable) {
                $method = 'render'.ucfirst($action);
                array_push($prepends, $this->{$method}());
            }
        }

        return implode('', array_merge($prepends, $customs, $appends));
    }

    /**
     * Render view action.
     */
    protected function renderView()
    {
        $action = config('admin.grid.actions.view') ?: Show::class;
        $action = $action::make($this->getViewLabel());
        $action->addHtmlClass(['mr-10px']);

        return $this->prepareAction($action);
    }

    protected function getViewLabel(): string
    {
        $label = $this->viewText ?: trans('admin.show');

        return "<i title='{$label}' class=\"feather icon-{$this->viewIcon} grid-action-icon\"></i> {$this->viewText}";
    }

    /**
     * Render edit action.
     */
    protected function renderEdit()
    {
        $action = config('admin.grid.actions.edit') ?: Edit::class;
        $action = $action::make($this->getEditLabel());
        $action->addHtmlClass(['mr-10px']);

        return $this->prepareAction($action);
    }

    protected function getEditLabel(): string
    {
        $label = $this->editText ?: trans('admin.edit');

        return "<i title='{$label}' class=\"feather icon-{$this->editIcon} grid-action-icon\"></i> {$this->editText}";
    }

    protected function renderQuickEdit()
    {
        $action = config('admin.grid.actions.quick_edit') ?: QuickEdit::class;
        $action = $action::make($this->getQuickEditLabel());
        $action->addHtmlClass(['mr-10px']);

        return $this->prepareAction($action);
    }

    protected function getQuickEditLabel(): string
    {
        $label = $this->quickEditText ?: trans('admin.quick_edit');

        return "<i title='{$label}' class=\"feather icon-{$this->quickEditIcon} grid-action-icon\"></i> {$this->quickEditText}";
    }

    /**
     * Render delete action.
     */
    protected function renderDelete()
    {
        $action = config('admin.grid.actions.delete') ?: Delete::class;
        $action = $action::make($this->getDeleteLabel());
        $action->addHtmlClass(['mr-10px']);

        return $this->prepareAction($action);
    }

    protected function getDeleteLabel(): string
    {
        $label = $this->deleteText ?: trans('admin.delete');

        return "<i class=\"feather icon-{$this->deleteIcon} grid-action-icon\" title='{$label}'></i> {$this->deleteText}";
    }
}
