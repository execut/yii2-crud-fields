$.widget('execut.Select2Execut', {
    _containerEl: null,
    _create: function () {
        var t = this;
        t._initElements();
        t._initEvents();
    },
    _initElements: function () {
        var t = this;
        t._select2El = t.element.find('select');
        t._containerEl = t.element.find('.select2-container .select2-selection--single');
    },
    _initEvents: function () {
        var t = this,
            selectStarted = false,
            selectionEndTimeout = false;
        $(document).on('selectionchange', function () {

            // wait 500 ms after the last selection change event
            if (selectionEndTimeout) {
                clearTimeout(selectionEndTimeout);
            }

            selectionEndTimeout = setTimeout(function () {
                selectStarted = false;
            }, 500);
        });
        t._containerEl.unbind('mousedown')
            .on('selectstart', function () {
                selectStarted = true;
            })
            .click(function () {
                if (!selectStarted) {
                    if (t._containerEl.parent().parent().hasClass('select2-container--open')) {
                        t._select2El.select2('close');
                    } else {
                        t._select2El.select2('open');
                    }
                }
            });
    },
});