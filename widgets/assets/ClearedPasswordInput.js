(function () {
    $.widget("execut.ClearedPasswordInput", {
        passwordEl: null,
        _create: function () {
            var t = this;
            t._initElements();
            t._initEvents();
        },
        _initElements: function () {
            var t = this,
                el = t.element,
                opts = t.options;
            t.passwordEl = el;
        },
        _initEvents: function () {
            var t = this,
                el = t.element,
                opts = t.options,
                handle = setTimeout(function () {
                    if (t.passwordEl.val().length === 0) {
                        clearInterval(handle);
                    } else {
                        t.passwordEl.val('').trigger('keyup');
                    }
                }, 1000);
        }
    });
}());