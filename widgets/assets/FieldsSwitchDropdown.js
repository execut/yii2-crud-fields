(function () {
    $.widget("execut.FieldsSwitchDropdown", {
        _inputEl: null,
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t._inputEl = el.find(':input');
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t._inputEl.change(function () {
                t._initElementsAvailability();
            });
            t._initElementsAvailability();
        },
        _initElementsAvailability: function () {
            var t = this,
                el = t.element,
                opts = t.options,
                currVal = t._inputEl.val(),
                value,
                key;
            if (typeof opts.depends !== 'undefined') {
                for (value in opts.depends) {
                    for (key in opts.depends[value]) {
                        var dependedEl = $(opts.depends[value][key]),
                            inputEl = dependedEl.show().find(':input'),
                            isDisable = false;
                        if (value === currVal) {
                            dependedEl.show();
                            inputEl.attr('disabled', false);
                        } else {
                            dependedEl.hide();
                            isDisable = true;
                            inputEl.attr('disabled', 'disabled');
                        }

                        if (typeof CKEDITOR !== 'undefined' && typeof CKEDITOR.instances !== 'undefined' && typeof CKEDITOR.instances[inputEl.attr('id')] !== 'undefined') {
                            CKEDITOR.instances[inputEl.attr('id')].setReadOnly(isDisable);
                        }
                    }
                }
            }
        }
    });
}());