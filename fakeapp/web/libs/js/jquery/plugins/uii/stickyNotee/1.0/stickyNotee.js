(function ($) {


    function destroyNote(jTarget) {
        var jNext = jEl.next('.stickyNotee-overlay');
        if (jNext.length) {
            jNext.remove();
        }
        jEl.stickyNotee("option", "popOut")();
        jEl.remove();
    }


    function getNoteOffset(position, targetOffset, jTarget, noteWidth, noteHeight) {
        var targetWidth = jTarget.width();
        var targetHeight = jTarget.height();
        var p = position.split('.', 2);
        var notePos = p[0];
        var targetPos = p[1];
        var left = targetOffset.left;
        var top = targetOffset.top;


//        left = 0;
//        top = 0;

        switch (targetPos) {
            case 'c':
                top += targetHeight / 2;
                left += targetWidth / 2;
                break;
            case 'n':
                left += targetWidth / 2;
                break;
            case 'ne':
                left += targetWidth;
                break;
            case 'e':
                left += targetWidth;
                top += targetHeight / 2;
                break;
            case 'se':
                left += targetWidth;
                top += targetHeight;
                break;
            case 's':
                left += targetWidth / 2;
                top += targetHeight;
                break;
            case 'sw':
                top += targetHeight;
                break;
            case 'w':
                top += targetHeight / 2;
                break;
            case 'nw':
                break;
            default:
                throw new Error("Unknown position for target: " + targetPos);
                break;
        }

        switch (notePos) {
            case 'c':
                top -= noteHeight / 2;
                left -= noteWidth / 2;
                break;
            case 'n':
                left -= noteWidth / 2;
                break;
            case 'ne':
                left -= noteWidth;
                break;
            case 'e':
                left -= noteWidth;
                top -= noteHeight / 2;
                break;
            case 'se':
                left -= noteWidth;
                top -= noteHeight;
                break;
            case 's':
                left -= noteWidth / 2;
                top -= noteHeight;
                break;
            case 'sw':
                top -= noteHeight;
                break;
            case 'w':
                top -= noteHeight / 2;
                break;
            case 'nw':
                break;
            default:
                throw new Error("Unknown position for note: " + notePos);
                break;
        }
        return {
            top: top,
            left: left
        };
    }

    function startNote(jTarget, options) {


        // creating the note
        var jNote = $(options.createNote());




        var props = {
            width: options.width,
            height: options.height,
            position: "absolute"
        };

        jNote.css(props);
        jNote.append(options.text);

        // displaying note
        jTarget.after(jNote);

        // positionning the note, we can only do this once the note is displayed
        var noteWidth = jNote.outerWidth();
        var noteHeight = jNote.outerHeight();
        var offset = jTarget.offset();
        var noteOffset = getNoteOffset(options.position, offset, jTarget, noteWidth, noteHeight);

        props = {
            left: noteOffset.left + options.offset[1],
            top: noteOffset.top + options.offset[0]
        };
        if (false === options.show) {
            props.display = 'none';
        }
        jNote.css(props);


        // gives an opportunity to destroy the note, or to dynamically style the note
        options.open(jTarget, jNote);


        return jNote;
    }


    $.fn.stickyNotee = function () {
        var args = Array.prototype.slice.apply(arguments);
        if ('option' === args[0]) {
            var opts = $.data(this[0], 'stickyNoteeOptions');
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
        else {
            var options = args[0];
            var o = $.extend({}, $.fn.stickyNotee.defaults, options);
            return this.each(function () {
                var jNote = startNote($(this), o);
                jNote.data('stickyNoteeOptions', o);
            });
        }
    };


    $.fn.stickyNotee.defaults = {
        createNote: function () {
            return '<div class="stickynotee"></div>';
        },
        show: true,
        width: 200, // css value|int (px)
        height: 'auto', // css value|int (px)
        /**
         * offset of the note, applied to the note's initial position
         * (defined by the position option).
         */
        offset: [0, 0],
        /**
         * Two positions separated by a dot.
         * The first position represents a note position,
         * and the second position represents a target position.
         * Both positions can take one of the
         * following values: n, ne, e, se, s, sw, w, nw, c.
         * c being the center.
         * Default is s.n
         *
         */
        position: 's.n',
        text: 'Sticky note, default text',
        destroy: function (jTarget) {
        },
        open: function (jTarget) {
        }
    };

    $.fn.stickyNotee.baseZIndex = 100;

}(jQuery));