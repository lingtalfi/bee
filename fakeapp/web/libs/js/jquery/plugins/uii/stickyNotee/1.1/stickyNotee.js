/**
 * Depends on:
 *
 * - jquery
 * - uii.positionn (1.0)
 *
 */

(function ($) {


    function destroyNote(jTarget) {
        var jNext = jEl.next('.stickyNotee-overlay');
        if (jNext.length) {
            jNext.remove();
        }
        jEl.stickyNotee("option", "popOut")();
        jEl.remove();
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
        jNote.positionn({
            my: options.my,
            at: options.at,
            collision: options.collision,
            of: jTarget
        });


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
        my: 'center bottom-10',
        at: 'center top',
        collision: 'none',
        text: 'Sticky note, default text',
        destroy: function (jTarget) {
        },
        open: function (jTarget) {
        }
    };

    $.fn.stickyNotee.baseZIndex = 100;

}(jQuery));