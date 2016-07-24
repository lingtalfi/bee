(function ($) {


    var draggableEls = [];


    var current = {
        oldPosition: null,
        dragElement: null,
        options: {}
    };


    function resize(dEl, options) {

        var jEl = $(dEl);
        var pos = jEl.css('position');
        if ('static' === pos) {
            jEl.css('position', 'relative');
        }

        var oldState = {};
        // creating the handles
        var handles = options.handles.split(',');
        for (var i in handles) {
            (function (i) {
                var handleName = $.trim(handles[i]);
                var jHandle = options.createHandle(jEl, handleName);
                var width = 0;
                var height = 0;
                var offsetX = parseInt(jEl.css('left'));
                var offsetY = parseInt(jEl.css('top'));
                /**
                 * The maximum css left value that a left handle (nw, w, sw) can take,
                 * in regards to minWidth
                 */
                var maxLeft = null;
                var maxTop = null;
                var minLeft = null;
                var minTop = null;

                var callback = null;
                // maybe it would make sense to let the user change the following callbacks?
                switch (handleName) {
                    case 'se':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.width = width + (e.clientX - current.startX);
                            props.height = height + (e.clientY - current.startY);
                        };
                        break;
                    case 'nw':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.left = offsetX + (e.clientX - current.startX);
                            jHandle.css('left', current.offsetX);
                            props.width = (width - (e.clientX - current.startX));

                            props.top = offsetY + (e.clientY - current.startY);
                            jHandle.css('top', current.offsetY);
                            props.height = (height - (e.clientY - current.startY));
                        };
                        break;
                    case 'n':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {

                            props.top = offsetY + (e.clientY - current.startY);
                            jHandle.css('top', current.offsetY);
                            props.height = (height - (e.clientY - current.startY));
                        };
                        break;
                    case 'ne':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.width = width + (e.clientX - current.startX);

                            props.top = offsetY + (e.clientY - current.startY);
                            jHandle.css('top', current.offsetY);
                            props.height = (height - (e.clientY - current.startY));
                        };
                        break;
                    case 'e':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.width = width + (e.clientX - current.startX);
                        };
                        break;
                    case 's':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.height = height + (e.clientY - current.startY);
                        };
                        break;
                    case 'sw':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.left = offsetX + (e.clientX - current.startX);
                            jHandle.css('left', current.offsetX);
                            props.width = (width - (e.clientX - current.startX));
                            props.height = height + (e.clientY - current.startY);
                        };
                        break;
                    case 'w':
                        callback = function (props, posX, posY, width, height, e, current, jHandle) {
                            props.left = offsetX + (e.clientX - current.startX);
                            jHandle.css('left', current.offsetX);
                            props.width = (width - (e.clientX - current.startX));
                        };
                        break;
                    default:
                        throw new Error('Unknown callback for handleName ' + handleName);
                        break;
                }

                var axis = null;
                if ('n' === handleName || 's' === handleName) {
                    axis = 'y';
                }
                else if ('w' === handleName || 'e' === handleName) {
                    axis = 'x';
                }
                jHandle.dragg({
                    axis: axis,
                    start: function (e, current) {
                        width = jEl.width();
                        height = jEl.height();
                        offsetX = parseInt(jEl.css('left'));
                        offsetY = parseInt(jEl.css('top'));
                        if (null !== options.minWidth) {
                            if(options.minWidth > width){
                                jEl.width(options.minWidth);
                            }
                            maxLeft = offsetX + width - options.minWidth;
                        }
                        if (null !== options.minHeight) {
                            if(options.minHeight> height){
                                jEl.height(options.minHeight);
                            }
                            maxTop = offsetY + height - options.minHeight;
                        }
                        if (null !== options.maxWidth) {
                            if(options.maxWidth < width){
                                jEl.width(options.maxWidth);
                            }
                            minLeft = offsetX - (options.maxWidth - width);
                        }
                        if (null !== options.maxHeight) {
                            if(options.maxHeight < height){
                                jEl.height(options.maxHeight);
                            }
                            minTop = offsetY - (options.maxHeight - height);
                        }

                        // save the current state so that when we destroy resizz, we can revert back to the old state
                        oldState = {
                            position: pos,
                            offset: jEl.offset()

                        };
                    },
                    drag: function (posX, posY, e, current) {
                        var props = {};
                        callback(props, posX, posY, width, height, e, current, jHandle);


                        // handle min and max dims
                        if (null !== options.minWidth && props.width < options.minWidth) {
                            if (null !== maxLeft && 'left' in props && props.left > maxLeft) {
                                props.left = maxLeft;
                            }
                            props.width = options.minWidth;
                            jHandle.css('left', '');
                        }
                        if (null !== options.minHeight && props.height < options.minHeight) {
                            if (null !== maxTop && 'top' in props && props.top > maxTop) {
                                props.top = maxTop;
                            }
                            props.height = options.minHeight;
                            jHandle.css('top', '');
                        }
                        if (null !== options.maxWidth && props.width > options.maxWidth) {
                            if (null !== minLeft && 'left' in props && props.left < minLeft) {
                                props.left = minLeft;
                            }
                            props.width = options.maxWidth;
                            jHandle.css('left', '');
                        }
                        if (null !== options.maxHeight && props.height > options.maxHeight) {
                            if (null !== minTop && 'top' in props && props.top < minTop) {
                                props.top = minTop;
                            }
                            props.height = options.maxHeight;
                            jHandle.css('top', '');
                        }

                        jEl.css(props);
                        options.resize(props, width, height, offsetX, offsetY);
                    },
                    stop: function (e, current) {
                        if (false === $.fn.dragg.isVisible($(current.dragElement))) {
                            jEl.animate({
                                top: offsetY + 'px',
                                left: offsetX + 'px',
                                width: width + 'px',
                                height: height + 'px'
                            }, 400);
                        }

                        // let's remove the updated position
                        // I guess it reverts back to the (previously tmp overridden) css defined positions.
                        jHandle.css({
                            top: '',
                            left: '',
                            zIndex: current.oldZIndex
                        });

                    }
                });
            })(i);
        }

    }


    $.fn.resizz = function () {
        var args = Array.prototype.slice.apply(arguments);
        if ('option' === args[0]) {
            var opts = $.data(this[0], 'resizzOptions');
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
            var o = $.extend({}, $.fn.resizz.defaults, options);
            return this.each(function () {
                $.data(this, 'resizzOptions', o);
                resize(this, o);
            });
        }
    };


    $.fn.resizz.defaults = {
        // a csv of handles to create, possible values are n, ne, e, se, s, sw, w, nw
        handles: 'nw, n, ne, e, se, s, sw, w',
        createHandle: function (jEl, handleName) {
            var jHandle = $('<div class="resizz-handle resizz-handle-' + handleName + '"></div>');
            jEl.append(jHandle);
            return jHandle;
        },
        /**
         * - props: contains updated values
         * (which values are updated depends on the handle being dragged)
         * ----- ?width
         * ----- ?height
         * ----- ?left
         * ----- ?top
         *
         * the following values represent the values of the element before the drag started
         * - width
         * - height
         * - offsetX  (css left)
         * - offsetY  (css top)
         *
         */
        resize: function(props, width, height, offsetX, offsetY){

        },
        minWidth: 10,
        minHeight: 10,
        maxWidth: null,
        maxHeight: null
    };
}(jQuery));