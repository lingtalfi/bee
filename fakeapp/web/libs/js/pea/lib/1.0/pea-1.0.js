/**
 * pea.
 *
 * Why the name pea?
 * Because p like in php.
 *
 * 2014-02-05
 *
 * php like functions in js.
 * Most functions come from the phpjs library.
 *
 */

if ('undefined' === typeof window.pea) {
    var zis = this;

    window.pea = {
        arrayValues: function (input) {
            var tmp_arr = [],
                key = '';

            if (input && typeof input === 'object' && input.change_key_case) { // Duck-type check for our own array()-created PHPJS_Array
                return input.values();
            }

            for (key in input) {
                tmp_arr[tmp_arr.length] = input[key];
            }

            return tmp_arr;
        },
        count: function (arrayObject) {
            var r = 0;
            for (var i in arrayObject) {
                r++;
            }
            return r;
        },
        htmlSpecialChars: function (string, quote_style, charset, double_encode) {
            var optTemp = 0,
                i = 0,
                noquotes = false;
            if (typeof quote_style === 'undefined' || quote_style === null) {
                quote_style = 2;
            }
            string = string.toString();
            if (double_encode !== false) { // Put this first to avoid double-encoding
                string = string.replace(/&/g, '&amp;');
            }
            string = string.replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            var OPTS = {
                'ENT_NOQUOTES': 0,
                'ENT_HTML_QUOTE_SINGLE': 1,
                'ENT_HTML_QUOTE_DOUBLE': 2,
                'ENT_COMPAT': 2,
                'ENT_QUOTES': 3,
                'ENT_IGNORE': 4
            };
            if (quote_style === 0) {
                noquotes = true;
            }
            if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
                quote_style = [].concat(quote_style);
                for (i = 0; i < quote_style.length; i++) {
                    // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
                    if (OPTS[quote_style[i]] === 0) {
                        noquotes = true;
                    } else if (OPTS[quote_style[i]]) {
                        optTemp = optTemp | OPTS[quote_style[i]];
                    }
                }
                quote_style = optTemp;
            }
            if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
                string = string.replace(/'/g, '&#039;');
            }
            if (!noquotes) {
                string = string.replace(/"/g, '&quot;');
            }

            return string;
        },
        inArray: function (needle, haystack, argStrict) {
            var key = '',
                strict = !!argStrict;

            if (strict) {
                for (key in haystack) {
                    if (haystack[key] === needle) {
                        return true;
                    }
                }
            } else {
                for (key in haystack) {
                    if (haystack[key] == needle) {
                        return true;
                    }
                }
            }
            return false;
        },
        isArray: function (mixed) {
            if (Object.prototype.toString.call(mixed) === '[object Array]') {
                return true;
            }
            return false;
        },
        isArrayObject: function (mixed) {
            if (pea.inArray(Object.prototype.toString.call(mixed), ['[object Object]'])) {
                return true;
            }
            return false;
        },
        isArrayOrObject: function (mixed) {
            if (pea.inArray(Object.prototype.toString.call(mixed), ['[object Array]', '[object Object]'])) {
                return true;
            }
            return false;
        },
        isFloat: function (mixed_var) {
            return +mixed_var === mixed_var && (!isFinite(mixed_var) || !!(mixed_var % 1));
        },
        isFunction: function (mixed) {
            if (typeof mixed == 'function') {
                return true;
            }
            return false;
        },
        isInt: function (mixed_var) {
            return mixed_var === +mixed_var && isFinite(mixed_var) && !(mixed_var % 1);
        },
        isNumeric: function (mixed_var) {
            return (typeof mixed_var === 'number' || typeof mixed_var === 'string') && mixed_var !== '' && !isNaN(mixed_var);
        },
        isObject: function (mixed_var) {
            if (Object.prototype.toString.call(mixed_var) === '[object Array]') {
                return false;
            }
            return mixed_var !== null && typeof mixed_var === 'object';
        },
        isString: function (mixed) {
            return ('string' === typeof mixed);
        },
        rand: function (min, max) {
            var argc = arguments.length;
            if (argc === 0) {
                min = 0;
                max = 2147483647;
            } else if (argc === 1) {
                throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');
            }
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },
        unsetKey: function (sKey, aArray) {
            aArray.splice(sKey, 1);
        }
    };
}
