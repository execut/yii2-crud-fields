(function () {
    $.widget("execut.DropDownLink", {
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.inputEl = el.parent().parent().find('select');
            t.initLink();
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.inputEl.change(function () {
                t.initLink();
            });
        },
        initLink: function () {
            var t = this,
                el = t.element,
                opts = t.options,
                val = t.inputEl.val();
            if (val) {
                el.parent().show();
                el.attr('href', opts.url.replace('%7Bid%7D', val));
            } else {
                el.parent().hide();
            }
        }
    });
}());