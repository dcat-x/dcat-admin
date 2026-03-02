<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
use Illuminate\Support\Str;

class BatchInput extends Presenter
{
    public static $css = [
        '@select2',
    ];

    protected string $id;

    protected string $placeholder = '';

    protected string $lookupUrl;

    protected string $batchTitle = '';

    protected string $batchDescription = '';

    protected string $batchIcon = 'feather icon-list';

    protected string $batchButtonText = '';

    protected int $batchMax = 100;

    protected string $validationPattern = '';

    protected string $validationMessage = '';

    protected string $itemLabel = '';

    protected string $batchPlaceholder = '';

    protected string $queryField = 'keywords';

    protected ?string $model = null;

    protected string $modelKey = 'id';

    protected string $modelText = 'name';

    public function __construct(string $lookupUrl)
    {
        $this->lookupUrl = $lookupUrl;
        $this->id = 'batch-input-'.Str::random(8);
    }

    /**
     * @return $this
     */
    public function placeholder(string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return $this
     */
    public function batchTitle(string $title)
    {
        $this->batchTitle = $title;

        return $this;
    }

    /**
     * @return $this
     */
    public function batchDescription(string $description)
    {
        $this->batchDescription = $description;

        return $this;
    }

    /**
     * @return $this
     */
    public function batchIcon(string $icon)
    {
        $this->batchIcon = $icon;

        return $this;
    }

    /**
     * @return $this
     */
    public function batchButtonText(string $text)
    {
        $this->batchButtonText = $text;

        return $this;
    }

    /**
     * @return $this
     */
    public function batchMax(int $max)
    {
        $this->batchMax = $max;

        return $this;
    }

    /**
     * 设置验证正则 (JS 正则, 不含分隔符).
     *
     * @return $this
     */
    public function validationPattern(string $pattern, string $message = '')
    {
        $this->validationPattern = $pattern;
        $this->validationMessage = $message;

        return $this;
    }

    /**
     * 设置项目标签 (用于提示文案, 如 "邮箱"、"手机号").
     *
     * @return $this
     */
    public function itemLabel(string $label)
    {
        $this->itemLabel = $label;

        return $this;
    }

    /**
     * 设置批量弹窗 textarea 的 placeholder.
     *
     * @return $this
     */
    public function batchPlaceholder(string $placeholder)
    {
        $this->batchPlaceholder = $placeholder;

        return $this;
    }

    /**
     * 设置 AJAX 提交的字段名.
     *
     * @return $this
     */
    public function queryField(string $field)
    {
        $this->queryField = $field;

        return $this;
    }

    /**
     * 设置用于回显标签的模型.
     *
     * @return $this
     */
    public function model(string $model, string $key = 'id', string $text = 'name')
    {
        $this->model = $model;
        $this->modelKey = $key;
        $this->modelText = $text;

        return $this;
    }

    public function defaultVariables(): array
    {
        $this->addScript();

        return [
            'id' => $this->id,
            'placeholder' => $this->placeholder ?: trans('admin.batch_input.placeholder'),
            'batchButtonText' => $this->batchButtonText ?: trans('admin.batch_input.batch'),
        ];
    }

    /**
     * 回显：从 URL 参数中恢复已选中的标签.
     */
    protected function resolveDisplayItems(): array
    {
        $value = $this->value();
        if (empty($value)) {
            return [];
        }

        $ids = is_array($value) ? $value : explode(',', (string) $value);
        $ids = array_filter($ids);

        if (empty($ids) || empty($this->model)) {
            return [];
        }

        $items = [];
        $results = $this->model::query()
            ->whereIn($this->modelKey, $ids)
            ->pluck($this->modelText, $this->modelKey);

        foreach ($results as $id => $label) {
            $items[] = ['id' => $id, 'label' => $label];
        }

        return $items;
    }

    protected function addScript(): void
    {
        $config = json_encode([
            'id' => $this->id,
            'lookupUrl' => $this->lookupUrl,
            'batchMax' => $this->batchMax,
            'batchTitle' => $this->batchTitle ?: trans('admin.batch_input.title'),
            'batchDescription' => $this->batchDescription ?: trans('admin.batch_input.description'),
            'batchIcon' => $this->batchIcon,
            'validationPattern' => $this->validationPattern,
            'validationMessage' => $this->validationMessage,
            'itemLabel' => $this->itemLabel ?: trans('admin.batch_input.item'),
            'batchPlaceholder' => $this->batchPlaceholder ?: trans('admin.batch_input.example'),
            'queryField' => $this->queryField,
            'initItems' => $this->resolveDisplayItems(),
            'lang' => [
                'noMatch' => trans('admin.batch_input.no_match'),
                'matched' => trans('admin.batch_input.matched'),
                'pleaseInput' => trans('admin.batch_input.please_input'),
                'networkError' => trans('admin.batch_input.network_error'),
                'exceedMax' => trans('admin.batch_input.exceed_max'),
                'noValid' => trans('admin.batch_input.no_valid'),
                'identified' => trans('admin.batch_input.identified'),
                'invalidCount' => trans('admin.batch_input.invalid_count'),
                'searching' => trans('admin.batch_input.searching'),
                'clear' => trans('admin.batch_input.clear'),
                'submit' => trans('admin.confirm'),
                'cancel' => trans('admin.cancel'),
            ],
        ]);

        Admin::script(
            <<<JS
(function() {
    var cfg = {$config};
    var id = cfg.id;
    var hiddenInput = document.getElementById('hidden-' + id);

    // ===== 单个输入回车搜索 =====
    var textInput = document.getElementById('input-' + id);
    if (textInput) {
        textInput.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            var keyword = this.value.trim();
            if (!keyword) return;

            var postData = { _token: Dcat.token };
            postData[cfg.queryField] = [keyword];

            \$.ajax({
                url: cfg.lookupUrl,
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(res) {
                    if (res.status && res.data.matched_count > 0) {
                        var matched = res.data.matched;
                        var selected = [];
                        for (var uid in matched) {
                            selected.push({ id: uid, label: matched[uid] });
                        }
                        renderTags(selected);
                        textInput.value = '';
                    } else {
                        Dcat.warning(cfg.lang.noMatch);
                    }
                }
            });
        });
    }

    // ===== 批量按钮 =====
    \$('#batch-btn-' + id).off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var content = '<div style="padding: 20px 20px 0;">' +
            '<div style="display:flex;align-items:center;margin-bottom:12px;color:#666;font-size:13px;">' +
                '<i class="' + cfg.batchIcon + '" style="margin-right:6px;color:#999;"></i>' +
                cfg.batchDescription +
            '</div>' +
            '<textarea id="batch-textarea-' + id + '" style="width:100%;height:200px;border:1px solid #dcdfe6;border-radius:4px;padding:12px;font-size:13px;line-height:1.8;resize:vertical;color:#333;" placeholder="' + cfg.batchPlaceholder.replace(/"/g, '&quot;') + '"></textarea>' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-bottom:16px;border-bottom:1px solid #f0f0f0;">' +
                '<span id="batch-stats-' + id + '" style="font-size:13px;color:#999;">' + cfg.lang.identified.replace(':count', '0') + '</span>' +
                '<a href="javascript:void(0)" id="batch-clear-' + id + '" style="font-size:13px;color:#ff4d4f;text-decoration:none;">' + cfg.lang.clear + '</a>' +
            '</div>' +
            '<div style="display:flex;justify-content:flex-end;padding:14px 0;">' +
                '<button type="button" id="batch-cancel-' + id + '" class="btn btn-sm btn-default" style="min-width:70px;margin-right:10px;">' + cfg.lang.cancel + '</button>' +
                '<button type="button" id="batch-submit-' + id + '" class="btn btn-sm btn-primary" style="min-width:70px;">' + cfg.lang.submit + '</button>' +
            '</div>' +
            '</div>';

        var layerIdx = layer.open({
            type: 1,
            title: '<b>' + cfg.batchTitle + '</b>',
            area: ['500px', 'auto'],
            offset: '100px',
            content: content,
            shade: [0.3, '#000'],
            btn: false,
            shadeClose: true,
            success: function(layero) {
                var textarea = layero.find('#batch-textarea-' + id);
                var statsEl = layero.find('#batch-stats-' + id);

                textarea.on('input paste', function() {
                    setTimeout(function() {
                        var raw = textarea.val().trim();
                        if (!raw) {
                            statsEl.html(cfg.lang.identified.replace(':count', '0'));
                            return;
                        }
                        var items = parseBatchItems(raw);
                        var vc = items.filter(function(e){ return e.valid; }).length;
                        var ic = items.length - vc;
                        var s = cfg.lang.identified.replace(':count', String(vc));
                        if (ic > 0) s += '，<span style="color:#ff4d4f;">' + cfg.lang.invalidCount.replace(':count', String(ic)) + '</span>';
                        if (items.length > cfg.batchMax) s += '，<span style="color:#ff4d4f;">' + cfg.lang.exceedMax.replace(':max', String(cfg.batchMax)) + '</span>';
                        statsEl.html(s);
                    }, 50);
                });

                layero.find('#batch-clear-' + id).on('click', function() {
                    textarea.val('').trigger('input');
                });

                layero.find('#batch-cancel-' + id).on('click', function() {
                    layer.close(layerIdx);
                });

                layero.find('#batch-submit-' + id).on('click', function() {
                    var raw = textarea.val().trim();
                    if (!raw) { Dcat.warning(cfg.lang.pleaseInput); return; }

                    var items = parseBatchItems(raw);
                    var validItems = items.filter(function(e) { return e.valid; }).map(function(e) { return e.value; });
                    if (!validItems.length) { Dcat.warning(cfg.lang.noValid); return; }
                    if (validItems.length > cfg.batchMax) { Dcat.warning(cfg.lang.exceedMax.replace(':max', String(cfg.batchMax))); return; }

                    var submitBtn = \$(this);
                    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + cfg.lang.searching);

                    var postData = { _token: Dcat.token };
                    postData[cfg.queryField] = validItems;

                    \$.ajax({
                        url: cfg.lookupUrl,
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(res) {
                            submitBtn.prop('disabled', false).html(cfg.lang.submit);
                            if (res.status && res.data.matched_count > 0) {
                                var matched = res.data.matched;
                                var selected = [];
                                for (var uid in matched) {
                                    selected.push({ id: uid, label: matched[uid] });
                                }
                                renderTags(selected);
                                Dcat.success(cfg.lang.matched.replace(':matched', String(res.data.matched_count)).replace(':total', String(validItems.length)));
                                layer.close(layerIdx);
                            } else {
                                Dcat.warning(cfg.lang.noMatch);
                            }
                        },
                        error: function() {
                            submitBtn.prop('disabled', false).html(cfg.lang.submit);
                            Dcat.error(cfg.lang.networkError);
                        }
                    });
                });
            }
        });
    });

    // ===== 渲染 select2 风格的标签 =====
    function renderTags(selected) {
        var box = \$('#' + id);
        var inputWrap = box.find('.batch-input-wrap');
        var tagWrap = box.find('.batch-input-tag-wrap');

        inputWrap.addClass('d-none');
        tagWrap.removeClass('d-none');

        if (!box.hasClass('select2')) {
            box.addClass('select2 select2-container select2-container--default select2-container--below');
        }
        box.removeClass('form-control');

        var ids = selected.map(function(s) { return s.id; });
        \$(hiddenInput).val(ids.join(',')).trigger('change');

        var tagItems = ['<span class="select2-selection__clear remove-all">\u00d7</span>'];
        for (var i = 0; i < selected.length; i++) {
            tagItems.push(
                '<li class="select2-selection__choice">' +
                escapeHtml(selected[i].label) +
                ' <span data-id="' + selected[i].id + '" class="select2-selection__choice__remove remove" role="presentation"> \u00d7</span>' +
                '</li>'
            );
        }

        var tagHtml = '<span class="select2-selection select2-selection--multiple">' +
            '<ul class="select2-selection__rendered">' + tagItems.join('') + '</ul></span>';

        var tagEl = \$(tagHtml);
        tagWrap.html(tagEl);

        tagEl.find('.remove').on('click', function() {
            var removeId = \$(this).data('id');
            \$(this).parent().remove();
            var currentIds = \$(hiddenInput).val().split(',').filter(function(v) { return v != removeId; });
            \$(hiddenInput).val(currentIds.join(',')).trigger('change');
            if (!currentIds.length) { resetToInput(); }
        });

        tagEl.find('.remove-all').on('click', function() { resetToInput(); });
    }

    // ===== 重置为输入状态 =====
    function resetToInput() {
        var box = \$('#' + id);
        box.find('.batch-input-wrap').removeClass('d-none');
        box.find('.batch-input-tag-wrap').addClass('d-none').html('');
        box.addClass('form-control').removeClass('select2 select2-container select2-container--default select2-container--below');
        \$(hiddenInput).val('').trigger('change');
    }

    function parseBatchItems(raw) {
        var items = raw.split(/[\\n,;\\s]+/).filter(function(s) { return s.trim() !== ''; });
        var seen = {}, result = [];
        items.forEach(function(item) {
            item = item.trim().toLowerCase();
            if (seen[item]) return;
            seen[item] = true;
            var valid = true;
            if (cfg.validationPattern) {
                valid = new RegExp(cfg.validationPattern).test(item);
            }
            result.push({ value: item, valid: valid });
        });
        return result;
    }

    function escapeHtml(text) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(text));
        return d.innerHTML;
    }

    // ===== 初始化：如果有回显数据则渲染标签 =====
    if (cfg.initItems && cfg.initItems.length) {
        renderTags(cfg.initItems);
    }
})();
JS
        );
    }
}
