(function ($) {


    function getDialog(jContent) {
        return jContent.closest(".dialogg");
    }

    function closeDialog(jEl) {
        var jNext = jEl.next('.dialogg-overlay');
        if (jNext.length) {
            jNext.hide();
        }
        jEl.hide();
        jEl.dialogg("option", "close")(jEl);
    }

    function destroyDialog(jEl) {
        var jNext = jEl.next('.dialogg-overlay');
        if (jNext.length) {
            jNext.remove();
        }
        jEl.dialogg("option", "destroy")(jEl);
        jEl.remove();
    }

    function openDialog(jEl) {
        var jNext = jEl.next('.dialogg-overlay');
        if (jNext.length) {
            jNext.show();
        }
        jEl.show();
        jEl.dialogg("option", "popIn")(jEl);
    }


    function resize(jDialog, dimensions) {

        var width = null; // auto|int
        var height = null; // auto|int
        var contentHeight = null; // int

        if ('width' in dimensions) {
            width = dimensions.width;
        }
        if ('height' in dimensions) {
            height = dimensions.height;
        }

        var jContent = jDialog.find(">.content");
        var props = {};


        if (null !== width) {

            if ('auto' === width) {
                /**
                 * If we resize a dialog having a big content to a dialog with smaller content,
                 * the scrollWidth property represents the visible content area, which is the width
                 * of the big content, and not the small one.
                 * In order to get the scrollWidth property return the value of the small content,
                 * we temporarily set the width to 1.
                 */
//                jContent.css('width', 1);
//                width = parseInt(jContent.get(0).scrollWidth) + 20; // 20=approximate scroll bar width
//                jContent.css('width', width);

                width = jContent.outerWidth();

            }
            props.width = width;
            var delta = jDialog.width() - props.width;
            if (delta >= 0) {
                props.left = jDialog.offset().left + Math.abs(delta / 2);
            }
            else {
                props.left = jDialog.offset().left - Math.abs(delta / 2);
            }
        }

        if (null !== height) {

            if ('auto' === height) {
                /**
                 * same note as for auto width
                 */
                var curHeight = jContent.height();
                jContent.css('height', 1);
                contentHeight = jContent.get(0).scrollHeight + 20; // 20=approximate scroll bar height
                jContent.css('height', curHeight);
            }
            else {
                props.height = height;
                contentHeight = jContent.height() + (height - jDialog.height());
            }
            jContent.animate({
                height: contentHeight
            });
        }

        jDialog.animate(props, 400);
    }


    function startDialogg(jContentContent, options) {



        // create the dialogg
        var jDialog = $(options.buildDialogg(options));
        var jTitleBar = jDialog.find('>.titlebar');
        var jTitle = jTitleBar.find('>.title');
        var jClose = jTitleBar.find('>.close');
        var jContent = jDialog.find('>.content');
        var jButtonsBar = jDialog.find('>.buttonsbar');

        jContent.append(jContentContent);
        jContentContent.show();

        jTitle.html(options.title);


        var props = {};
        props.width = options.width;
        props.height = options.height;
        if (false === options.show) {
            props.display = 'none';
        }

//        if (false === options.resizable &&
//            false === options.draggable &&
//            'static' === jDialog.css('position')
//            ) {
//        }
        props.position = 'absolute';

        jDialog.css(props);


        if (options.buttons.length > 0) {
            for (var i in options.buttons) {
                (function (i) {
                    var button = options.buttons[i];
                    if ('text' in button) {
                        var jButton = $(options.buildButton(button.text));
                        jButtonsBar.append(jButton);
                    }
                    else {
                        throw new Error("missing text property for button");
                    }
                    if ('click' in button) {
                        jButton.on('click', function (e) {
                            button.click(e);
                        });
                    }
                })(i);
            }
            jButtonsBar.show();
        }
        else {
            jButtonsBar.hide();
        }


        jClose.on('click', function (e) {
            closeDialog(jDialog);
        });


        $('body').append(jDialog);
        if (true === options.draggable && ('dragg' in $.fn)) {
            jDialog.dragg({
                handle: jTitleBar
            });
        }

        if (true === options.resizable && ('resizz' in $.fn)) {
            var offsetY2 = jDialog.height() - jContent.height();
            jDialog.resizz({
                resize: function (props, width, height, offsetX, offsetY) {
                    jContent.css({
                        width: '100%',
                        height: props.height - offsetY2
                    });
                },
                minWidth: options.minWidth,
                minHeight: options.minHeight,
                maxWidth: options.maxWidth,
                maxHeight: options.maxHeight,
                handles: options.handles,
                createHandle: options.createHandle
            });
        }


        // z-index
        var dialogIndex = null;
        if (true === options.modal) {
            var jOverlay = $('<div class="dialogg-overlay"></div>');
            if (false === options.show) {
                jOverlay.hide();
            }
            jDialog.after(jOverlay);
            if (null !== options.zIndex) {
                jOverlay.css('zIndex', options.zIndex);
                dialogIndex = options.zIndex + 1;
            }
            else {
                // get highest z-index amongst dialoggs instances
                var highestIndex = 0;
                $(".dialogg").not(jDialog).each(function () {
                    var index_current = parseInt($(this).css("zIndex"), 10);
                    if (index_current > highestIndex) {
                        highestIndex = index_current;
                    }
                });
                if (0 === highestIndex) {
                    highestIndex = $.fn.dialogg.baseZIndex;
                }
                else {
                    // eventually I prefer to be sure that new overlay index is MORE THAN (and not EQUAL TO)
                    // the highest dialogg's instance's index
                    highestIndex++;
                }
                jOverlay.css('zIndex', highestIndex);
                dialogIndex = highestIndex + 1;
            }
        }
        else {
            if (null !== options.zIndex) {
                dialogIndex = options.zIndex;
            }
            // else use baseZIndex?
        }
        if (null !== dialogIndex) {
            jDialog.css("zIndex", dialogIndex);
        }


        // esc should be an equivalent to close button
        $(document).on('keydown.dialoggClose', function (event) {
            if (event.which == 27) {
                closeDialog(jDialog);
            }
        });


        options.positionDialogg(jDialog);

        options.create(jDialog, options);
        options.popIn(jDialog, options);

        return jDialog;
    }


    $.fn.dialogg = function () {
        var args = Array.prototype.slice.apply(arguments);
        if ('option' === args[0]) {
            var opts = $.data(this[0], 'dialoggOptions');
            if (undefined !== opts) {
                if (args[1]) {
                    if (undefined !== args[2]) {
                        opts[args[1]] = args[2];
                    }
                    else {
                        return opts[args[1]];
                    }
                }
            }
        }
        else if ('close' === args[0]) {
            closeDialog(getDialog(this));
        }
        else if ('open' === args[0]) {
            openDialog(getDialog(this));
        }
        else if ('destroy' === args[0]) {
            destroyDialog(getDialog(this));
        }
        else if ('autoSize' === args[0]) {
            var dimensions = {
                width: 'auto',
                height: 'auto'
            };
            if (args[1]) {
                if ('width' in args[1]) {
                    dimensions.width = args[1].width;
                }
                if ('height' in args[1]) {
                    dimensions.height = args[1].height;
                }
            }
            resize(getDialog(this), dimensions);
        }
        else if ('content' === args[0]) {
            if (args[1]) {
                var opts = $.data(this[0], 'dialoggOptions');
                var jDialog = getDialog(this);
                if (jDialog.length) {

                    var title = '';
                    if (args[2]) {
                        title = args[2];
                    }

                    var width = null;
                    var height = null;
                    if (args[3]) {
                        if ('width' in args[3]) {
                            width = args[3].width;
                        }
                        if ('height' in args[3]) {
                            height = args[3].height;
                        }
                    }
                    var jTitle = jDialog.find(">.titlebar >.title");
                    var jContent = jDialog.find(">.content");
                    jContent.html(args[1]);
                    jTitle.html(title);

                    resize(jDialog, {
                        width: width,
                        height: height
                    });
                }
            }
        }
        else {
            var options = args[0];
            var o = $.extend({}, $.fn.dialogg.defaults, options);
            return this.each(function () {
                var jDialog = startDialogg($(this), o);
                // note: it didn't work with $.data(jDialog, 'dialoggOptions', o);
                // but it works with the following line...
                jDialog.data('dialoggOptions', o);
            });
        }
    };


    $.fn.dialogg.defaults = {

        /**
         * Returns the dialogg html, without an empty buttons bar
         */
        buildDialogg: function (options) {
            var zeclass = '';
            if (options.class) {
                zeclass = options.class;
            }
            var s = '';
            s += '<div class="dialogg';
            if (zeclass.length) {
                s += ' ' + zeclass;
            }
            s += '">' +
                '<div class="titlebar">' +
                '<span class="title"></span>' +
                '<button class="close">X</button>' +
                '</div>' +
                '<div class="content"></div>' +
                '<div class="buttonsbar"></div>' +
                '</div>';
            return s;
        },
        buildButton: function (text) {
            return '<button class="button">' + text + '</button>';
        },
        title: '',
        positionDialogg: function (jDialog) {
            var w = $(document).width();
            var h = $(document).height();
            var width = jDialog.width();
            var height = jDialog.height();
            var offset = jDialog.offset();

            var left = (w / 2) - (width / 2);
            var top = (h / 2) - (height / 2);

            jDialog.css({
                left: left,
                top: top
            });


        },
        modal: false,
        show: true,
        width: '300', // css value|int (px)
        height: 'auto', // css value|int (px)
        draggable: true,
        zIndex: null, // null (default)|int, null means auto
        /**
         * Array of buttonObject.
         * - buttonObject:
         * ----- text: the button text
         * ----- ?click: click callback
         *
         */
        buttons: [],
        //------------------------------------------------------------------------------/
        // RESIZZ OPTIONS
        //------------------------------------------------------------------------------/
        resizable: true,
        minWidth: 250,
        minHeight: 150,
        maxWidth: null,
        maxHeight: null,
        // a csv of handles to create, possible values are n, ne, e, se, s, sw, w, nw
        handles: 'se',
        createHandle: function (jEl, handleName) {
            var jHandle = $('<div class="resizz-handle resizz-handle-' + handleName + '"></div>');
            jEl.append(jHandle);
            return jHandle;
        },
        //------------------------------------------------------------------------------/
        // DIALOGG EVENTS
        //------------------------------------------------------------------------------/
        popIn: function (jDialog) {

        },
        close: function (jDialog) {

        },
        destroy: function (jDialog) {

        },
        create: function (jDialog, options) {

        }
    };

    $.fn.dialogg.baseZIndex = 100;

}(jQuery));