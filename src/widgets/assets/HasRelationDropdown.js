(function () {
    $.widget("execut.HasRelationDropdown", {
        selectEl: null,
        parentEl: null,
        parentElHidden: null,
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.selectEl = el.find('select');
            t.parentEl = $(opts.parentSelector);
            // t.parentElHidden = t.parentEl.parent().find('input[type=hidden]');
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            // setTimeout(function () {
            //     t.selectEl.unbind('change');
            //     t.selectEl.change(function () {
            //         t._initParent();
            //     });
            // }, 100);
            // t._initParent();
            // t.parentElHidden.change(function () {
            //     if (!t.innerEvent) {
            //         t._initParent();
            //     }
            // });
        },
        innerEvent: false,
        _initParent: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.innerEvent = true;
            if (t.selectEl.val() !== '') {
                t.parentEl.prop('disabled', true);
                t.parentElHidden.attr('disabled', true);
            } else {
                t.parentEl.prop('disabled', false);
                t.parentElHidden.attr('disabled', false);
            }

            t.innerEvent = false;
        }
    });
}());