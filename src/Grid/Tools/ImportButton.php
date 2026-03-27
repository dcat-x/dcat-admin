<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\ImportController;
use Illuminate\Contracts\Support\Renderable;

class ImportButton implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    protected function setUpScripts()
    {
        $url = admin_base_path('dcat-api/import');
        $gridName = $this->grid->getName();
        $importConfig = $this->buildEncodedConfig();

        $script = <<<JS
(function () {
    var importBtn = $('.grid-import-btn-{$gridName}');

    importBtn.on('click', function (e) {
        e.preventDefault();

        var idx = layer.open({
            type: 1,
            title: importBtn.data('title'),
            area: ['600px', '400px'],
            shade: [0.3, '#000'],
            content: $('#grid-import-modal-{$gridName}'),
            btn: [Dcat.lang['submit'], Dcat.lang['cancel'] || 'Cancel'],
            yes: function (index) {
                var form = $('#grid-import-form-{$gridName}');
                var fileInput = form.find('input[type="file"]')[0];

                if (!fileInput || !fileInput.files.length) {
                    Dcat.warning('{$this->fileRequiredMessage()}');
                    return;
                }

                var formData = new FormData(form[0]);
                formData.append('_token', Dcat.token);
                formData.append('_grid', '{$gridName}');
                formData.append('_import_config', '{$importConfig}');

                var btn = layer.getChildFrame ? null : true;
                layer.msg('{$this->importingMessage()}', {icon: 16, shade: [0.3, '#000'], time: 0});

                $.ajax({
                    url: '{$url}/execute',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        layer.closeAll('loading');
                        layer.close(idx);
                        if (response.status) {
                            Dcat.success(response.message || 'Import completed');
                            Dcat.reload();
                        } else {
                            Dcat.error(response.message || 'Import failed');
                        }
                    },
                    error: function (xhr) {
                        layer.closeAll('loading');
                        var msg = xhr.responseJSON ? (xhr.responseJSON.message || 'Import failed') : 'Import failed';
                        Dcat.error(msg);
                    }
                });
            },
            end: function () {
                // Reset form when modal closes
                $('#grid-import-form-{$gridName}')[0] && $('#grid-import-form-{$gridName}')[0].reset();
            }
        });
    });
})();
JS;

        Admin::script($script);
    }

    protected function fileRequiredMessage(): string
    {
        return trans('admin.import_file_required', [], 'en') !== 'admin.import_file_required'
            ? trans('admin.import_file_required')
            : 'Please select a file';
    }

    protected function importingMessage(): string
    {
        return trans('admin.importing', [], 'en') !== 'admin.importing'
            ? trans('admin.importing')
            : 'Importing...';
    }

    protected function buildEncodedConfig(): string
    {
        $config = $this->grid->buildImporterConfig();

        return ImportController::encodeConfig($config);
    }

    public function render()
    {
        $this->setUpScripts();

        $import = trans('admin.import');
        $encodedConfig = $this->buildEncodedConfig();
        $templateUrl = admin_base_path('dcat-api/import/template').'?_grid='.$this->grid->getName().'&_import_config='.urlencode($encodedConfig);
        $templateText = trans('admin.import_template', [], 'en') !== 'admin.import_template'
            ? trans('admin.import_template')
            : 'Download Template';
        $selectFile = trans('admin.import_select_file', [], 'en') !== 'admin.import_select_file'
            ? trans('admin.import_select_file')
            : 'Select File';
        $gridName = $this->grid->getName();

        $modal = <<<HTML
<div id="grid-import-modal-{$gridName}" style="display:none;padding:20px;">
    <form id="grid-import-form-{$gridName}" method="POST" enctype="multipart/form-data">
        <div style="margin-bottom:15px;">
            <a href="{$templateUrl}" class="btn btn-sm btn-outline-primary">
                <i class="feather icon-download"></i> {$templateText}
            </a>
        </div>
        <div style="margin-bottom:15px;">
            <label>{$selectFile}</label>
            <input type="file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv" />
        </div>
    </form>
</div>
HTML;

        $button = $this->grid->tools()->format(
            <<<EOT
<button type="button" class="btn btn-primary grid-import-btn-{$gridName}" data-title="{$import}" style="margin-right:3px">
    <i class="feather icon-upload"></i>
    <span class="d-none d-sm-inline">&nbsp;{$import}&nbsp;</span>
</button>
EOT
        );

        return $button.$modal;
    }
}
