(function ($) {

    if ('undefined' === typeof window.shortcutMatch) {

        /**
         * This map contains the codes that this object knows how to interpret.
         * It does only contain the alphanumeric codes, the arrows, and some special
         * keys such as enter, backspace, capsLock, the function keys, ...,
         * and the cmd key for mac fans, although I do not recommend to use it.
         * NumPad keys are prefixed with n.
         *
         * Also note that this is intended to be use with the keydown event, and not the keypress event
         * (keypress event is case sensitive, so it adds unnecessary complexity for the handling of codes).
         * (http://howtodoinjava.com/2013/12/20/jquery-keyup-function-demo/)
         *
         *
         * It does not contain the punctuation keys because I noticed that their
         * code depends on something (language, keyboard layout, os?).
         *
         * For instance, here is the difference between an (assumed)
         * american keyboard on pc, and my french keyboard on mac (in parenthesis)
         *
         * source for american keyboard: http://css-tricks.com/snippets/javascript/javascript-keycodes/
         *
         *          semi-colon: 186     (59)
         *          equal: 187          (61)
         *          comma: 188          (188)     (actually, since comma matches, and it is sometimes used in shortcuts, like "cmd+,", it is part of the codes map)
         *          dash: 189           (193)
         *          period: 190         (59)
         *
         *
         *
         */
        var codes = {
            'a': 65,
            'b': 66,
            'c': 67,
            'd': 68,
            'e': 69,
            'f': 70,
            'g': 71,
            'h': 72,
            'i': 73,
            'j': 74,
            'k': 75,
            'l': 76,
            'm': 77,
            'n': 78,
            'o': 79,
            'p': 80,
            'q': 81,
            'r': 82,
            's': 83,
            't': 84,
            'u': 85,
            'v': 86,
            'w': 87,
            'x': 88,
            'y': 89,
            'z': 90,
            // numpad
            'n0': 96,
            'n1': 97,
            'n2': 98,
            'n3': 99,
            'n4': 100,
            'n5': 101,
            'n6': 102,
            'n7': 103,
            'n8': 104,
            'n9': 105,
            // numbers on the keyboard
            '0': 48,
            '1': 49,
            '2': 50,
            '3': 51,
            '4': 52,
            '5': 53,
            '6': 54,
            '7': 55,
            '8': 56,
            '9': 57,
            // function keys
            'f1': 112,
            'f2': 113,
            'f3': 114,
            'f4': 115,
            'f5': 116,
            'f6': 117,
            'f7': 118,
            'f8': 119,
            'f9': 120,
            'f10': 121,
            'f11': 122,
            'f12': 123,

            // specials
            'backspace': 8,
            'tab': 9,
            'shift': 16,
            'ctrl': 17,
            'alt': 18,
            'cmd': 224,
            'capsLock': 20,
            'escape': 27,
            'space': 32,
            'enter': 13,
            'return': 13,
            'pageUp': 33,
            'pageDown': 34,
            'home': 36,
            'end': 35,
            'left': 37,
            'up': 38,
            'right': 39,
            'down': 40,
            'delete': 46,
            'suppr': 46,
            // puncutation
            ',': 188
        };


        window.shortcutMatch = {

            /**
             * This is just a test method that shouldn't be used in production,
             * to check if keys are recognized as expected
             *
             */
            getLiteralKeys: function (e) {
                var code = e.which;
                var a = [];
                if (true === e.ctrlKey) {
                    a.push('ctrl');
                }
                if (true === e.altKey) {
                    a.push('alt');
                }
                if (true === e.shiftKey) {
                    a.push('shift');
                }
                if (true === e.metaKey) {
                    a.push('cmd');
                }
                var modifiers = ['ctrl', 'alt', 'shift', 'cmd'];
                for (var i in codes) {
                    // modifiers are already added
                    if ($.inArray(i, modifiers) > -1) {
                        continue;
                    }
                    if (code === codes[i]) {
                        a.push(i);
                        break;
                    }
                }
                return a.join('+');
            },

            /**
             * Will match if one or more of the given shortcuts match the event.
             *
             *
             * - e: jquery event
             * - shortcut: string. A shortcut is a plus separated string,
             *          each component representing a known code (see the codes map).
             *          For instance: ctrl+a
             *
             */
            match: function (e, shortcut) {
                shortcut = shortcut.toLowerCase();
                var r;
                var modifiers = ['ctrl', 'alt', 'shift', 'cmd'];
                var p = shortcut.split('+');
                r = true;
                for (var j in p) {
                    if ('ctrl' === p[j] && false === e.ctrlKey) {
                        r = false;
                    }
                    if ('alt' === p[j] && false === e.altKey) {
                        r = false;
                    }
                    if ('shift' === p[j] && false === e.shiftKey) {
                        r = false;
                    }
                    if ('cmd' === p[j] && false === e.metaKey) {
                        r = false;
                    }
                    if ($.inArray(p[j], modifiers) === -1) {
                        if (false === (p[j] in codes) || e.which !== codes[p[j]]) {
                            r = false;
                        }
                    }
                }
                if (true === r) {
                    return true;
                }
                return false;
            }
        };
    }
})
    (jQuery);
