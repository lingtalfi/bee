/**
 * pea.
 *
 * Why the name pea?
 * Because p like in php.
 *
 * 2014-02-05 - 2014-11-11
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
        sprintf: function () {
            var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
            var a = arguments;
            var i = 0;
            var format = a[i++];

            // pad()
            var pad = function (str, len, chr, leftJustify) {
                if (!chr) {
                    chr = ' ';
                }
                var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0)
                    .join(chr);
                return leftJustify ? str + padding : padding + str;
            };

            // justify()
            var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
                var diff = minWidth - value.length;
                if (diff > 0) {
                    if (leftJustify || !zeroPad) {
                        value = pad(value, minWidth, customPadChar, leftJustify);
                    } else {
                        value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
                    }
                }
                return value;
            };

            // formatBaseX()
            var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
                // Note: casts negative numbers to positive ones
                var number = value >>> 0;
                prefix = prefix && number && {
                    '2': '0b',
                    '8': '0',
                    '16': '0x'
                }[base] || '';
                value = prefix + pad(number.toString(base), precision || 0, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            };

            // formatString()
            var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
                if (precision != null) {
                    value = value.slice(0, precision);
                }
                return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
            };

            // doFormat()
            var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
                var number, prefix, method, textTransform, value;

                if (substring === '%%') {
                    return '%';
                }

                // parse flags
                var leftJustify = false;
                var positivePrefix = '';
                var zeroPad = false;
                var prefixBaseX = false;
                var customPadChar = ' ';
                var flagsl = flags.length;
                for (var j = 0; flags && j < flagsl; j++) {
                    switch (flags.charAt(j)) {
                        case ' ':
                            positivePrefix = ' ';
                            break;
                        case '+':
                            positivePrefix = '+';
                            break;
                        case '-':
                            leftJustify = true;
                            break;
                        case "'":
                            customPadChar = flags.charAt(j + 1);
                            break;
                        case '0':
                            zeroPad = true;
                            customPadChar = '0';
                            break;
                        case '#':
                            prefixBaseX = true;
                            break;
                    }
                }

                // parameters may be null, undefined, empty-string or real valued
                // we want to ignore null, undefined and empty-string values
                if (!minWidth) {
                    minWidth = 0;
                } else if (minWidth === '*') {
                    minWidth = +a[i++];
                } else if (minWidth.charAt(0) == '*') {
                    minWidth = +a[minWidth.slice(1, -1)];
                } else {
                    minWidth = +minWidth;
                }

                // Note: undocumented perl feature:
                if (minWidth < 0) {
                    minWidth = -minWidth;
                    leftJustify = true;
                }

                if (!isFinite(minWidth)) {
                    throw new Error('sprintf: (minimum-)width must be finite');
                }

                if (!precision) {
                    precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type === 'd') ? 0 : undefined;
                } else if (precision === '*') {
                    precision = +a[i++];
                } else if (precision.charAt(0) == '*') {
                    precision = +a[precision.slice(1, -1)];
                } else {
                    precision = +precision;
                }

                // grab value using valueIndex if required?
                value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

                switch (type) {
                    case 's':
                        return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
                    case 'c':
                        return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
                    case 'b':
                        return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'o':
                        return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'x':
                        return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'X':
                        return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad)
                            .toUpperCase();
                    case 'u':
                        return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
                    case 'i':
                    case 'd':
                        number = +value || 0;
                        // Plain Math.round doesn't just truncate
                        number = Math.round(number - number % 1);
                        prefix = number < 0 ? '-' : positivePrefix;
                        value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad);
                    case 'e':
                    case 'E':
                    case 'f': // Should handle locales (as per setlocale)
                    case 'F':
                    case 'g':
                    case 'G':
                        number = +value;
                        prefix = number < 0 ? '-' : positivePrefix;
                        method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                        textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                        value = prefix + Math.abs(number)[method](precision);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
                    default:
                        return substring;
                }
            };

            return format.replace(regex, doFormat);
        },
        substr: function (str, start, len) {
            var i = 0,
                allBMP = true,
                es = 0,
                el = 0,
                se = 0,
                ret = '';
            str += '';
            var end = str.length;

            // BEGIN REDUNDANT
            this.php_js = this.php_js || {};
            this.php_js.ini = this.php_js.ini || {};
            // END REDUNDANT
            switch ((this.php_js.ini['unicode.semantics'] && this.php_js.ini['unicode.semantics'].local_value.toLowerCase())) {
                case 'on':
                    // Full-blown Unicode including non-Basic-Multilingual-Plane characters
                    // strlen()
                    for (i = 0; i < str.length; i++) {
                        if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
                            allBMP = false;
                            break;
                        }
                    }

                    if (!allBMP) {
                        if (start < 0) {
                            for (i = end - 1, es = (start += end); i >= es; i--) {
                                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                                    start--;
                                    es--;
                                }
                            }
                        } else {
                            var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
                            while ((surrogatePairs.exec(str)) != null) {
                                var li = surrogatePairs.lastIndex;
                                if (li - 2 < start) {
                                    start++;
                                } else {
                                    break;
                                }
                            }
                        }

                        if (start >= end || start < 0) {
                            return false;
                        }
                        if (len < 0) {
                            for (i = end - 1, el = (end += len); i >= el; i--) {
                                if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
                                    end--;
                                    el--;
                                }
                            }
                            if (start > end) {
                                return false;
                            }
                            return str.slice(start, end);
                        } else {
                            se = start + len;
                            for (i = start; i < se; i++) {
                                ret += str.charAt(i);
                                if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
                                    se++; // Go one further, since one of the "characters" is part of a surrogate pair
                                }
                            }
                            return ret;
                        }
                        break;
                    }
                // Fall-through
                case 'off':
                // assumes there are no non-BMP characters;
                //    if there may be such characters, then it is best to turn it on (critical in true XHTML/XML)
                default:
                    if (start < 0) {
                        start += end;
                    }
                    end = typeof len === 'undefined' ? end : (len < 0 ? len + end : len + start);
                    // PHP returns false if start does not fall within the string.
                    // PHP returns false if the calculated end comes before the calculated start.
                    // PHP returns an empty string if start and end are the same.
                    // Otherwise, PHP returns the portion of the string from start to end.
                    return start >= str.length || start < 0 || start > end ? !1 : str.slice(start, end);
            }
            return undefined; // Please Netbeans
        },
        unsetKey: function (sKey, aArray) {
            aArray.splice(sKey, 1);
        }
    };
}
