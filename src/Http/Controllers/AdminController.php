<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * @method mixed grid()
 * @method mixed detail($id)
 * @method mixed form()
 */
class AdminController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    /**
     * Set description for following 4 action pages.
     *
     * @var array
     */
    protected $description = [
        //        'index'  => 'Index',
        //        'show'   => 'Show',
        //        'edit'   => 'Edit',
        //        'create' => 'Create',
    ];

    /**
     * Set translation path.
     *
     * @var string
     */
    protected $translation;

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title ?: admin_trans_label();
    }

    /**
     * Get description for following 4 action pages.
     *
     * @return array
     */
    protected function description()
    {
        return $this->description;
    }

    /**
     * Get translation path.
     *
     * @return string
     */
    protected function translation()
    {
        return $this->translation;
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body(
                method_exists($this, 'customIndex')
                    ? $this->customIndex()
                    : $this->grid()
            );
    }

    /**
     * Show interface.
     *
     * @param  mixed  $id
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['show'] ?? trans('admin.show'))
            ->body(
                method_exists($this, 'customShow')
                    ? $this->customShow($id)
                    : $this->detail($id)
            );
    }

    /**
     * Edit interface.
     *
     * @param  mixed  $id
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['edit'] ?? trans('admin.edit'))
            ->body(
                method_exists($this, 'customEdit')
                    ? $this->customEdit($id)
                    : $this->form()->edit($id)
            );
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->translation($this->translation())
            ->title($this->title())
            ->description($this->description()['create'] ?? trans('admin.create'))
            ->body(
                method_exists($this, 'customCreate')
                    ? $this->customCreate()
                    : $this->form()
            );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if (method_exists($this, 'customUpdate')) {
            return $this->customUpdate($id);
        }

        return $this->form()->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        if (method_exists($this, 'customStore')) {
            return $this->customStore();
        }

        return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (method_exists($this, 'customDestroy')) {
            return $this->customDestroy($id);
        }

        return $this->form()->destroy($id);
    }
}
