<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Repositories\DataRule;
use Dcat\Admin\Models\DataRule as DataRuleModel;
use Dcat\Admin\Show;

class DataRuleController extends AdminController
{
    protected function title()
    {
        return trans('admin.data_rule.title');
    }

    protected function grid()
    {
        return Grid::make(DataRule::with(['menu']), function (Grid $grid) {
            $grid->column('id', 'ID')->sortable();
            $grid->column('menu.title', trans('admin.data_rule.menu'))->label();
            $grid->column('name', trans('admin.name'));
            $grid->column('field', trans('admin.data_rule.field'));
            $grid->column('condition', trans('admin.data_rule.condition'))
                ->using(DataRuleModel::getConditionOptions());
            $grid->column('value', trans('admin.data_rule.value'));
            $grid->column('value_type', trans('admin.data_rule.value_type'))
                ->using(DataRuleModel::getValueTypeOptions())
                ->label();
            $grid->column('scope', trans('admin.data_rule.scope'))
                ->using(DataRuleModel::getScopeOptions())
                ->label('primary');
            $grid->column('status', trans('admin.status'))
                ->switch();
            $grid->column('order', trans('admin.order'))
                ->editable();
            $grid->column('created_at', trans('admin.created_at'));

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', trans('admin.name'));

                $menuModel = config('admin.database.menu_model');
                $filter->equal('menu_id', trans('admin.data_rule.menu'))
                    ->select($menuModel::selectOptions());

                $filter->equal('scope', trans('admin.data_rule.scope'))
                    ->select(DataRuleModel::getScopeOptions());

                $filter->equal('status', trans('admin.status'))
                    ->select([
                        1 => trans('admin.enable'),
                        0 => trans('admin.disable'),
                    ]);
            });

            $grid->quickSearch(['name', 'field']);

            $grid->enableDialogCreate();
            $grid->showQuickEditButton();
            $grid->disableViewButton();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, DataRule::with(['menu']), function (Show $show) {
            $show->field('id', 'ID');
            $show->field('menu.title', trans('admin.data_rule.menu'));
            $show->field('name', trans('admin.name'));
            $show->field('field', trans('admin.data_rule.field'));
            $show->field('condition', trans('admin.data_rule.condition'))
                ->using(DataRuleModel::getConditionOptions());
            $show->field('value', trans('admin.data_rule.value'));
            $show->field('value_type', trans('admin.data_rule.value_type'))
                ->using(DataRuleModel::getValueTypeOptions());
            $show->field('scope', trans('admin.data_rule.scope'))
                ->using(DataRuleModel::getScopeOptions());
            $show->field('status', trans('admin.status'))
                ->using([1 => trans('admin.enable'), 0 => trans('admin.disable')]);
            $show->field('order', trans('admin.order'));
            $show->field('created_at', trans('admin.created_at'));
            $show->field('updated_at', trans('admin.updated_at'));
        });
    }

    public function form()
    {
        return Form::make(DataRule::with(['menu']), function (Form $form) {
            $form->display('id', 'ID');

            $menuModel = config('admin.database.menu_model');

            $form->select('menu_id', trans('admin.data_rule.menu'))
                ->options($menuModel::selectOptions())
                ->required();

            $form->text('name', trans('admin.name'))
                ->required();

            $form->text('field', trans('admin.data_rule.field'))
                ->required()
                ->help(trans('admin.data_rule.field_help'));

            $form->select('condition', trans('admin.data_rule.condition'))
                ->options(DataRuleModel::getConditionOptions())
                ->default(DataRuleModel::CONDITION_EQUAL)
                ->required();

            $form->text('value', trans('admin.data_rule.value'))
                ->help(trans('admin.data_rule.value_help'));

            $form->radio('value_type', trans('admin.data_rule.value_type'))
                ->options(DataRuleModel::getValueTypeOptions())
                ->default(DataRuleModel::VALUE_TYPE_FIXED)
                ->when(DataRuleModel::VALUE_TYPE_VARIABLE, function (Form $form) {
                    $form->html($this->getVariablesHtml(), trans('admin.data_rule.system_variables'));
                });

            $form->radio('scope', trans('admin.data_rule.scope'))
                ->options(DataRuleModel::getScopeOptions())
                ->default(DataRuleModel::SCOPE_ROW)
                ->required();

            $form->switch('status', trans('admin.status'))
                ->default(1);

            $form->number('order', trans('admin.order'))
                ->default(0);

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    /**
     * 获取系统变量说明HTML
     */
    protected function getVariablesHtml(): string
    {
        $variables = DataRuleModel::getSystemVariables();
        $html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
        $html .= '<thead><tr><th>' . trans('admin.data_rule.variable') . '</th><th>' . trans('admin.description') . '</th></tr></thead><tbody>';

        foreach ($variables as $var => $desc) {
            $html .= "<tr><td><code>{$var}</code></td><td>{$desc}</td></tr>";
        }

        $html .= '</tbody></table></div>';

        return $html;
    }
}
