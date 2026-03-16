
class FormAutoSave {
    constructor(options) {
        let _this = this;

        _this.options = $.extend({
            form: null,
            interval: 30,
        }, options);

        _this.$form = $(_this.options.form).first();

        if (! _this.$form.length) {
            return;
        }

        _this.storageKey = _this._buildStorageKey();
        _this._timer = null;

        _this.init();
    }

    _buildStorageKey() {
        let formId = this.$form.attr('id') || 'default';

        return 'dcat_autosave_' + formId + '_' + window.location.pathname;
    }

    init() {
        let _this = this;

        _this._checkDraft();
        _this._startAutoSave();
        _this._bindClearOnSubmit();
    }

    _getFields() {
        return this.$form.find(':input')
            .not('[type=file],.file-input,[name=_token],[name=_method]');
    }

    _serialize() {
        let data = {};

        this._getFields().each(function () {
            let $el = $(this),
                name = $el.attr('name');

            if (! name) {
                return;
            }

            let type = $el.attr('type');

            if (type === 'checkbox' || type === 'radio') {
                if ($el.is(':checked')) {
                    if (data[name] !== undefined) {
                        if (! Array.isArray(data[name])) {
                            data[name] = [data[name]];
                        }
                        data[name].push($el.val());
                    } else {
                        data[name] = $el.val();
                    }
                }
            } else {
                data[name] = $el.val();
            }
        });

        return data;
    }

    _restore(draft) {
        let _this = this;

        _this._getFields().each(function () {
            let $el = $(this),
                name = $el.attr('name');

            if (! name || ! draft.hasOwnProperty(name)) {
                return;
            }

            let type = $el.attr('type'),
                val = draft[name];

            if (type === 'checkbox' || type === 'radio') {
                let values = Array.isArray(val) ? val : [val];
                $el.prop('checked', values.indexOf($el.val()) !== -1);
            } else {
                $el.val(val);
            }

            $el.trigger('change');
        });
    }

    _checkDraft() {
        let _this = this,
            raw = localStorage.getItem(_this.storageKey);

        if (! raw) {
            return;
        }

        let draft;
        try {
            draft = JSON.parse(raw);
        } catch (e) {
            localStorage.removeItem(_this.storageKey);
            return;
        }

        Dcat.info(
            '<div style="margin-bottom:8px">' +
            Dcat.lang.auto_save_tip +
            '</div>' +
            '<button class="btn btn-sm btn-primary mr-2 autosave-restore">' +
            Dcat.lang.auto_save_restore +
            '</button>' +
            '<button class="btn btn-sm btn-default autosave-discard">' +
            Dcat.lang.auto_save_discard +
            '</button>',
            {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true,
                onShown: function () {
                    $('.autosave-restore').off('click').on('click', function () {
                        _this._restore(draft);
                        Dcat.success(Dcat.lang.auto_save_restored);
                        toastr.clear();
                    });

                    $('.autosave-discard').off('click').on('click', function () {
                        _this.clear();
                        toastr.clear();
                    });
                }
            }
        );
    }

    _startAutoSave() {
        let _this = this,
            intervalMs = _this.options.interval * 1000;

        _this._timer = setInterval(function () {
            let data = _this._serialize();

            if (Object.keys(data).length > 0) {
                try {
                    localStorage.setItem(_this.storageKey, JSON.stringify(data));
                } catch (e) {
                    // localStorage full or unavailable
                }
            }
        }, intervalMs);
    }

    _bindClearOnSubmit() {
        let _this = this;

        Dcat.Form.submitted(function (response) {
            if (response && response.status !== false) {
                _this.clear();
            }
        });
    }

    clear() {
        if (this._timer) {
            clearInterval(this._timer);
        }
        localStorage.removeItem(this.storageKey);
    }
}

export default FormAutoSave
