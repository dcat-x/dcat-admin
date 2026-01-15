<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Http\Repositories\Department;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Models\Department as DepartmentModel;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Form as WidgetForm;

class DepartmentController extends AdminController
{
    public function title()
    {
        return trans('admin.departments');
    }

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $row->column(7, $this->treeView()->render());

                $row->column(5, function ($column) {
                    $form = new WidgetForm;
                    $form->action(admin_url('auth/departments'));

                    $departmentModel = config('admin.database.departments_model', DepartmentModel::class);
                    $roleModel = config('admin.database.roles_model');

                    $form->select('parent_id', trans('admin.parent_id'))
                        ->options($departmentModel::selectOptions());
                    $form->text('name', trans('admin.name'))->required();
                    $form->text('code', trans('admin.department_code'))->required();
                    $form->switch('status', trans('admin.status'))->default(1);

                    if (config('admin.department.inherit_department_roles', true)) {
                        $form->multipleSelect('roles', trans('admin.roles'))
                            ->options($roleModel::all()->pluck('name', 'id'));
                    }

                    $form->width(9, 2);

                    $column->append(Box::make(trans('admin.new'), $form));
                });
            });
    }

    protected function treeView()
    {
        $departmentModel = config('admin.database.departments_model', DepartmentModel::class);

        return new Tree(new $departmentModel, function (Tree $tree) {
            $tree->disableCreateButton();
            $tree->disableQuickCreateButton();
            $tree->disableEditButton();
            $tree->maxDepth(5);

            $tree->branch(function ($branch) {
                $status = $branch['status'] ? '' : ' <span class="text-muted">(disabled)</span>';
                $payload = "<strong>{$branch['name']}</strong>&nbsp;&nbsp;";
                $payload .= "<span class='text-primary'>[{$branch['code']}]</span>";
                $payload .= $status;

                return $payload;
            });
        });
    }

    public function form()
    {
        $departmentModel = config('admin.database.departments_model', DepartmentModel::class);

        $with = config('admin.department.inherit_department_roles', true) ? ['roles'] : [];

        return Form::make(Department::with($with), function (Form $form) use ($departmentModel) {
            $departmentsTable = config('admin.database.departments_table', 'admin_departments');
            $connection = config('admin.database.connection');

            $id = $form->getKey();

            $form->display('id', 'ID');

            $form->select('parent_id', trans('admin.parent_id'))
                ->options(function () use ($departmentModel, $id) {
                    return $departmentModel::selectOptions(function ($query) use ($id) {
                        if ($id) {
                            return $query->where('id', '!=', $id);
                        }

                        return $query;
                    });
                })
                ->saving(function ($v) {
                    return (int) $v;
                });

            $form->text('name', trans('admin.name'))->required();

            $form->text('code', trans('admin.department_code'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$departmentsTable}"])
                ->updateRules(['required', "unique:{$connection}.{$departmentsTable},code,$id"]);

            $form->switch('status', trans('admin.status'))->default(1);

            if (config('admin.department.inherit_department_roles', true)) {
                $form->multipleSelect('roles', trans('admin.roles'))
                    ->options(function () {
                        $roleModel = config('admin.database.roles_model');

                        return $roleModel::all()->pluck('name', 'id');
                    })
                    ->customFormat(function ($v) {
                        return array_column($v, 'id');
                    });
            }

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }
}
