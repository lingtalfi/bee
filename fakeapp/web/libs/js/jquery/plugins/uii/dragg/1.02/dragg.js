(function ($) {


    var draggableEls = [];


    var current = {
        startX: 0,
        startY: 0,
        offsetX: 0,
        offsetY: 0,
        oldZIndex: 0,
        oldPosition: null,
        dragElement: null,
        options: {}
    };


    $(document).on('mousedown.dragg', function (e) {
        if (1 === e.which) {
            var jTarget = $(e.target);
            for (var i in draggableEls) {

                var options = draggableEls[i][1];

                // let's see if the click was inside the handle
                var handle = (options['handle']) ? options['handle'] : draggableEls[i][0];

                // be sure that this handle is not forbidden
                if (!jTarget.is(options['cancel'])) {

                    var jHandle = jTarget.closest(handle);
                    if (jHandle.length) {

                        var dDrag = draggableEls[i][0];
                        var jDrag = $(dDrag);


//                        var uid = jutil.uniqueId(jDrag, 'dragg-uid-');


                        current.oldPosition = jDrag.css('position');
                        if ('static' === current.oldPosition) {
                            jDrag.css('position', 'relative');
                        }

                        current.startX = e.clientX;
                        current.startY = e.clientY;
                        var pos = jDrag.position();

                        current.offsetX = toNumber(pos.left);
                        current.offsetY = toNumber(pos.top);


                        // bring the clicked element to the front while it is being dragged
                        if (0 !== parseInt(options['zIndexMode'])) {
                            current.oldZIndex = dDrag.style.zIndex;
                            if ($.isFunction(options['indexMax'])) {
                                dDrag.style.zIndex = options['indexMax']();
                            }
                            else {
                                dDrag.style.zIndex = options['indexMax'];
                            }
                        }


                        // store the currently dragged element
                        current.dragElement = dDrag;
                        current.options = options;
                        options['start'](e, current);

                        $(document).on('mousemove.dragg', function (e) {
                            current.options['nativeDrag'](e, current, current.options['drag']);
                        });


                        // cancel out any text selections
                        document.body.focus();

                        // prevent text selection in IE
                        document.onselectstart = function () {
                            return false;
                        };
                        // prevent IE from trying to drag an image
                        e.target.ondragstart = function () {
                            return false;
                        };

                        // prevent text selection (except IE)
                        return false;

                    }
                }
            }
        }
    });


    $(document).on('mouseup.dragg', function (e) {
        if (current.dragElement != null) {
            if (2 === parseInt(current.options['zIndexMode'])) {
                current.dragElement.style.zIndex = current.oldZIndex;
            }


            // we're done with these events until the next OnMouseDown
            $(document).off('mousemove.dragg');
            document.onselectstart = null;
            current.dragElement.ondragstart = null;

            current.options['stop'](e, current);

            // this is how we know we're not dragging
            current.dragElement = null;


        }
    });


    function toNumber(value) {
        var n = parseInt(value);
        return n == null || isNaN(n) ? 0 : n;
    }


    $.fn.dragg = function () {

        var args = Array.prototype.slice.apply(arguments);
        if ('option' === args[0]) {
            var opts = $.data(this[0], 'draggOptions');
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
        else {
            var options = args[0];
            var o = $.extend({}, $.fn.dragg.defaults, options);
            return this.each(function () {
                $.data(this, 'draggOptions', o);
                draggableEls.push([this, o]);
            });
        }
    };

    $.fn.dragg.animateCurrentToOriginalPosition = function () {
        if (current.dragElement != null) {
            $(current.dragElement).animate({
                top: current.offsetY + 'px',
                left: current.offsetX + 'px'
            }, 400);
        }
    };

    $.fn.dragg.isVisible = function (jEl) {
        // is accessible
//        var h = $(document).height();
//        var w = $(document).width();

        // is visible
        var h = $(window).height();
        var w = $(window).width();

        var width = jEl.width();
        var height = jEl.height();

        var offset = jEl.offset();
        if (
            offset.top + height <= 0 ||
                offset.left + width <= 0 ||
                offset.left >= w ||
                offset.top >= h
            ) {
            return false;
        }

        return true;
    };


    $.fn.dragg.defaults = {
        // int|function
        indexMax: function () {
            if ('undefined' === typeof window._draggAutoInc) {
                window._draggAutoInc = 10000;
            }
            return window._draggAutoInc++;
        },
        /**
         * int: 0|1|2
         *  - 0: no index variation
         *  - 1: index climbs up (or down?) to the indexMax value upon the drag start
         *  - 2: like mode 1, but returns to its original position when the drag ends
         *
         */
        zIndexMode: 1,
        handle: null, // jquery selector | object
        axis: null,
        cancel: 'input,textarea,button,select,option',
        // EVENTS
        start: function (e, current) {
        },
        stop: function (e, current) {
            if (false === $.fn.dragg.isVisible($(current.dragElement))) {
                $.fn.dragg.animateCurrentToOriginalPosition();
            }
        },
        nativeDrag: function (e, current, fnDrag) {
            var posX = current.offsetX + e.clientX - current.startX;
            var posY = current.offsetY + e.clientY - current.startY;
            if ('y' !== current.options.axis) {
                current.dragElement.style.left = posX + 'px';
            }
            if ('x' !== current.options.axis) {
                current.dragElement.style.top = posY + 'px';
            }

            fnDrag(posX, posY, e, current);
        },
        drag: function (posX, posY, e, current) {

        }
    };
}(jQuery));