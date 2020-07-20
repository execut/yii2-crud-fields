(function(){
    $.widget("execut.RadioListWithSubform", {
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
            t.initRelatedElements();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.relatedElements = $(opts.relatedSelector)
            t.inputs = el.find('input');
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.inputs.change(function () {
                t.initRelatedElements();
            });

            if (typeof opts.value !== 'undefined' && opts.value) {
                t.inputs.filter('[value=' + opts.value + ']').prop('checked', true).change();
            }
        },
        initRelatedElements: function () {
            var t = this,
                el = t.element,
                opts = t.options,
                checkedEls = t.inputs.filter(':checked');
            if (!checkedEls.length || checkedEls.val()) {
                t.relatedElements.hide().find('input').attr('disabled', true);
            } else {
                t.relatedElements.show().find('input').attr('disabled', false);
            }
        }
    });
}());