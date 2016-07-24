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
        arrayKeyExists: function (key, search) {
            if (!search || (search.constructor !== Array && search.constructor !== Object)) {
                return false;
            }

            return key in search;
        },
        arrayKeys: function (input, search_value, argStrict) {
            var search = typeof search_value !== 'undefined',
                tmp_arr = [],
                strict = !!argStrict,
                include = true,
                key = '';

            if (input && typeof input === 'object' && input.change_key_case) { // Duck-type check for our own array()-created PHPJS_Array
                return input.keys(search_value, argStrict);
            }

            for (key in input) {
                if (input.hasOwnProperty(key)) {
                    include = true;
                    if (search) {
                        if (strict && input[key] !== search_value) {
                            include = false;
                        } else if (input[key] != search_value) {
                            include = false;
                        }
                    }

                    if (include) {
                        tmp_arr[tmp_arr.length] = key;
                    }
                }
            }

            return tmp_arr;
        },
        arrayMerge: function () {
            var args = Array.prototype.slice.call(arguments),
                argl = args.length,
                arg,
                retObj = {},
                k = '',
                argil = 0,
                j = 0,
                i = 0,
                ct = 0,
                toStr = Object.prototype.toString,
                retArr = true;

            for (i = 0; i < argl; i++) {
                if (toStr.call(args[i]) !== '[object Array]') {
                    retArr = false;
                    break;
                }
            }

            if (retArr) {
                retArr = [];
                for (i = 0; i < argl; i++) {
                    retArr = retArr.concat(args[i]);
                }
                return retArr;
            }

            for (i = 0, ct = 0; i < argl; i++) {
                arg = args[i];
                if (toStr.call(arg) === '[object Array]') {
                    for (j = 0, argil = arg.length; j < argil; j++) {
                        retObj[ct++] = arg[j];
                    }
                }
                else {
                    for (k in arg) {
                        if (arg.hasOwnProperty(k)) {
                            if (parseInt(k, 10) + '' === k) {
                                retObj[ct++] = arg[k];
                            }
                            else {
                                retObj[k] = arg[k];
                            }
                        }
                    }
                }
            }
            return retObj;
        },
        arrayReplace: function (arr) {
            var retObj = {},
                i = 0,
                p = '',
                argl = arguments.length;

            if (argl < 2) {
                throw new Error('There should be at least 2 arguments passed to array_replace()');
            }

            // Although docs state that the arguments are passed in by reference, it seems they are not altered, but rather the copy that is returned (just guessing), so we make a copy here, instead of acting on arr itself
            for (p in arr) {
                retObj[p] = arr[p];
            }

            for (i = 1; i < argl; i++) {
                for (p in arguments[i]) {
                    retObj[p] = arguments[i][p];
                }
            }
            return retObj;
        },
        arrayReverse: function (array, preserve_keys) {
            var isArray = Object.prototype.toString.call(array) === "[object Array]",
                tmp_arr = preserve_keys ? {} : [],
                key;

            if (isArray && !preserve_keys) {
                return array.slice(0).reverse();
            }

            if (preserve_keys) {
                var keys = [];
                for (key in array) {
                    // if (array.hasOwnProperty(key)) {
                    keys.push(key);
                    // }
                }

                var i = keys.length;
                while (i--) {
                    key = keys[i];
                    // FIXME: don't rely on browsers keeping keys in insertion order
                    // it's implementation specific
                    // eg. the result will differ from expected in Google Chrome
                    tmp_arr[key] = array[key];
                }
            } else {
                for (key in array) {
                    // if (array.hasOwnProperty(key)) {
                    tmp_arr.unshift(array[key]);
                    // }
                }
            }

            return tmp_arr;
        },
        arraySearch: function (needle, haystack, strict) {
            strict = pea.boolVal(strict);
            if (true === strict) {
                for (var i in haystack) {
                    if (haystack[i] === needle) {
                        return i;
                    }
                }
            }
            else {
                for (var i in haystack) {
                    if (haystack[i] == needle) {
                        return i;
                    }
                }
            }
            return false;
        },
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
        arrayWalk: function (array, funcname, userdata) {
            //  discuss at: http://phpjs.org/functions/array_walk/
            // original by: Johnny Mast (http://www.phpvrouwen.nl)
            // bugfixed by: David
            // improved by: Brett Zamir (http://brett-zamir.me)
            //  depends on: array
            //        note: Using ini_set('phpjs.no-eval', true) will only work with
            //        note: user-defined string functions, not built-in functions like void()
            //        test: skip
            //   example 1: array_walk ({'a':'b'}, 'void', 'userdata');
            //   returns 1: true
            //   example 2: array_walk ('a', 'void', 'userdata');
            //   returns 2: false
            //   example 3: array_walk ([3, 4], function () {}, 'userdata');
            //   returns 3: true
            //   example 4: array_walk ({40: 'My age', 50: 'My IQ'}, [window, 'prompt']);
            //   returns 4: true
            //   example 5: ini_set('phpjs.return_phpjs_arrays', 'on');
            //   example 5: var arr = array({40: 'My age'}, {50: 'My IQ'});
            //   example 5: array_walk(arr, [window, 'prompt']);
            //   returns 5: '[object Object]'

            var key, value, ini;

            if (!array || typeof array !== 'object') {
                return false;
            }
            if (typeof array === 'object' && array.change_key_case) { // Duck-type check for our own array()-created PHPJS_Array
                if (arguments.length > 2) {
                    return array.walk(funcname, userdata);
                } else {
                    return array.walk(funcname);
                }
            }

            try {
                if (typeof funcname === 'function') {
                    for (key in array) {
                        if (arguments.length > 2) {
                            funcname(array[key], key, userdata);
                        } else {
                            funcname(array[key], key);
                        }
                    }
                } else if (typeof funcname === 'string') {
                    this.php_js = this.php_js || {};
                    this.php_js.ini = this.php_js.ini || {};
                    ini = this.php_js.ini['phpjs.no-eval'];
                    if (ini && (
                        parseInt(ini.local_value, 10) !== 0 && (!ini.local_value.toLowerCase || ini.local_value.toLowerCase() !==
                            'off')
                        )) {
                        if (arguments.length > 2) {
                            for (key in array) {
                                this.window[funcname](array[key], key, userdata);
                            }
                        } else {
                            for (key in array) {
                                this.window[funcname](array[key], key);
                            }
                        }
                    } else {
                        if (arguments.length > 2) {
                            for (key in array) {
                                eval(funcname + '(array[key], key, userdata)');
                            }
                        } else {
                            for (key in array) {
                                eval(funcname + '(array[key], key)');
                            }
                        }
                    }
                } else if (funcname && typeof funcname === 'object' && funcname.length === 2) {
                    var obj = funcname[0],
                        func = funcname[1];
                    if (arguments.length > 2) {
                        for (key in array) {
                            obj[func](array[key], key, userdata);
                        }
                    } else {
                        for (key in array) {
                            obj[func](array[key], key);
                        }
                    }
                } else {
                    return false;
                }
            } catch (e) {
                return false;
            }

            return true;
        },
        boolVal: function (mValue) {
            return Boolean(mValue);
        },
        callUserFunc: function (cb) {
            //  discuss at: http://phpjs.org/functions/call_user_func/
            // original by: Brett Zamir (http://brett-zamir.me)
            // improved by: Diplom@t (http://difane.com/)
            // improved by: Brett Zamir (http://brett-zamir.me)
            //   example 1: call_user_func('isNaN', 'a');
            //   returns 1: true

            var func;

            if (typeof cb === 'string') {
                func = (typeof this[cb] === 'function') ? this[cb] : func = (new Function(null, 'return ' + cb))();
            } else if (Object.prototype.toString.call(cb) === '[object Array]') {
                func = (typeof cb[0] === 'string') ? eval(cb[0] + "['" + cb[1] + "']") : func = cb[0][cb[1]];
            } else if (typeof cb === 'function') {
                func = cb;
            }

            if (typeof func !== 'function') {
                throw new Error(func + ' is not a valid function');
            }

            var parameters = Array.prototype.slice.call(arguments, 1);
            return (typeof cb[0] === 'string') ? func.apply(eval(cb[0]), parameters) : (typeof cb[0] !== 'object') ? func.apply(
                null, parameters) : func.apply(cb[0], parameters);
        },
        callUserFuncArray: function (cb, parameters) {
            //  discuss at: http://phpjs.org/functions/call_user_func_array/
            // original by: Thiago Mata (http://thiagomata.blog.com)
            //  revised by: Jon Hohle
            // improved by: Brett Zamir (http://brett-zamir.me)
            // improved by: Diplom@t (http://difane.com/)
            // improved by: Brett Zamir (http://brett-zamir.me)
            //   example 1: call_user_func_array('isNaN', ['a']);
            //   returns 1: true
            //   example 2: call_user_func_array('isNaN', [1]);
            //   returns 2: false

            var func;

            if (typeof cb === 'string') {
                func = (typeof this[cb] === 'function') ? this[cb] : func = (new Function(null, 'return ' + cb))();
            } else if (Object.prototype.toString.call(cb) === '[object Array]') {
                func = (typeof cb[0] === 'string') ? eval(cb[0] + "['" + cb[1] + "']") : func = cb[0][cb[1]];
            } else if (typeof cb === 'function') {
                func = cb;
            }

            if (typeof func !== 'function') {
                throw new Error(func + ' is not a valid function');
            }

            return (typeof cb[0] === 'string') ? func.apply(eval(cb[0]), parameters) : (typeof cb[0] !== 'object') ? func.apply(
                null, parameters) : func.apply(cb[0], parameters);
        },
        count: function (mixed_var, mode) {
            var key, cnt = 0;

            if (mixed_var === null || typeof mixed_var === 'undefined') {
                return 0;
            } else if (mixed_var.constructor !== Array && mixed_var.constructor !== Object) {
                return 1;
            }

            if (mode === 'COUNT_RECURSIVE') {
                mode = 1;
            }
            if (mode != 1) {
                mode = 0;
            }

            for (key in mixed_var) {
                if (mixed_var.hasOwnProperty(key)) {
                    cnt++;
                    if (mode == 1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object)) {
                        cnt += this.count(mixed_var[key], 1);
                    }
                }
            }

            return cnt;
        },
        echo: function () {
            //  discuss at: http://phpjs.org/functions/echo/
            // original by: Philip Peterson
            // improved by: echo is bad
            // improved by: Nate
            // improved by: Brett Zamir (http://brett-zamir.me)
            // improved by: Brett Zamir (http://brett-zamir.me)
            // improved by: Brett Zamir (http://brett-zamir.me)
            //  revised by: Der Simon (http://innerdom.sourceforge.net/)
            // bugfixed by: Eugene Bulkin (http://doubleaw.com/)
            // bugfixed by: Brett Zamir (http://brett-zamir.me)
            // bugfixed by: Brett Zamir (http://brett-zamir.me)
            // bugfixed by: EdorFaus
            //    input by: JB
            //        note: If browsers start to support DOM Level 3 Load and Save (parsing/serializing),
            //        note: we wouldn't need any such long code (even most of the code below). See
            //        note: link below for a cross-browser implementation in JavaScript. HTML5 might
            //        note: possibly support DOMParser, but that is not presently a standard.
            //        note: Although innerHTML is widely used and may become standard as of HTML5, it is also not ideal for
            //        note: use with a temporary holder before appending to the DOM (as is our last resort below),
            //        note: since it may not work in an XML context
            //        note: Using innerHTML to directly add to the BODY is very dangerous because it will
            //        note: break all pre-existing references to HTMLElements.
            //   example 1: echo('<div><p>abc</p><p>abc</p></div>');
            //   returns 1: undefined

            var isNode = typeof module !== 'undefined' && module.exports && typeof global !== "undefined" && {}.toString.call(
                global) == '[object global]';
            if (isNode) {
                var args = Array.prototype.slice.call(arguments);
                return console.log(args.join(' '));
            }

            var arg = '';
            var argc = arguments.length;
            var argv = arguments;
            var i = 0;
            var holder, win = zis.window;

            var d = win.document;
            var ns_xhtml = 'http://www.w3.org/1999/xhtml';
            // If we're in a XUL context
            var ns_xul = 'http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul';

            var stringToDOM = function (str, parent, ns, container) {
                var extraNSs = '';
                if (ns === ns_xul) {
                    extraNSs = ' xmlns:html="' + ns_xhtml + '"';
                }
                var stringContainer = '<' + container + ' xmlns="' + ns + '"' + extraNSs + '>' + str + '</' + container + '>';
                var dils = win.DOMImplementationLS;
                var dp = win.DOMParser;
                var ax = win.ActiveXObject;
                if (dils && dils.createLSInput && dils.createLSParser) {
                    // Follows the DOM 3 Load and Save standard, but not
                    // implemented in browsers at present; HTML5 is to standardize on innerHTML, but not for XML (though
                    // possibly will also standardize with DOMParser); in the meantime, to ensure fullest browser support, could
                    // attach http://svn2.assembla.com/svn/brettz9/DOMToString/DOM3.js (see http://svn2.assembla.com/svn/brettz9/DOMToString/DOM3.xhtml for a simple test file)
                    var lsInput = dils.createLSInput();
                    // If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
                    lsInput.stringData = stringContainer;
                    // synchronous, no schema type
                    var lsParser = dils.createLSParser(1, null);
                    return lsParser.parse(lsInput)
                        .firstChild;
                } else if (dp) {
                    // If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
                    try {
                        var fc = new dp()
                            .parseFromString(stringContainer, 'text/xml');
                        if (fc && fc.documentElement && fc.documentElement.localName !== 'parsererror' && fc.documentElement.namespaceURI !==
                            'http://www.mozilla.org/newlayout/xml/parsererror.xml') {
                            return fc.documentElement.firstChild;
                        }
                        // If there's a parsing error, we just continue on
                    } catch (e) {
                        // If there's a parsing error, we just continue on
                    }
                } else if (ax) {
                    // We don't bother with a holder in Explorer as it doesn't support namespaces
                    var axo = new ax('MSXML2.DOMDocument');
                    axo.loadXML(str);
                    return axo.documentElement;
                }
                /*else if (win.XMLHttpRequest) {
                 // Supposed to work in older Safari
                 var req = new win.XMLHttpRequest;
                 req.open('GET', 'data:application/xml;charset=utf-8,'+encodeURIComponent(str), false);
                 if (req.overrideMimeType) {
                 req.overrideMimeType('application/xml');
                 }
                 req.send(null);
                 return req.responseXML;
                 }*/
                // Document fragment did not work with innerHTML, so we create a temporary element holder
                // If we're in XHTML, we'll try to allow the XHTML namespace to be available by default
                //if (d.createElementNS && (d.contentType && d.contentType !== 'text/html')) {
                // Don't create namespaced elements if we're being served as HTML (currently only Mozilla supports this detection in true XHTML-supporting browsers, but Safari and Opera should work with the above DOMParser anyways, and IE doesn't support createElementNS anyways)
                if (d.createElementNS && // Browser supports the method
                    (d.documentElement.namespaceURI || // We can use if the document is using a namespace
                        d.documentElement.nodeName.toLowerCase() !== 'html' || // We know it's not HTML4 or less, if the tag is not HTML (even if the root namespace is null)
                        (d.contentType && d.contentType !== 'text/html') // We know it's not regular HTML4 or less if this is Mozilla (only browser supporting the attribute) and the content type is something other than text/html; other HTML5 roots (like svg) still have a namespace
                        )) {
                    // Don't create namespaced elements if we're being served as HTML (currently only Mozilla supports this detection in true XHTML-supporting browsers, but Safari and Opera should work with the above DOMParser anyways, and IE doesn't support createElementNS anyways); last test is for the sake of being in a pure XML document
                    holder = d.createElementNS(ns, container);
                } else {
                    // Document fragment did not work with innerHTML
                    holder = d.createElement(container);
                }
                holder.innerHTML = str;
                while (holder.firstChild) {
                    parent.appendChild(holder.firstChild);
                }
                return false;
                // throw 'Your browser does not support DOM parsing as required by echo()';
            };

            var ieFix = function (node) {
                if (node.nodeType === 1) {
                    var newNode = d.createElement(node.nodeName);
                    var i, len;
                    if (node.attributes && node.attributes.length > 0) {
                        for (i = 0, len = node.attributes.length; i < len; i++) {
                            newNode.setAttribute(node.attributes[i].nodeName, node.getAttribute(node.attributes[i].nodeName));
                        }
                    }
                    if (node.childNodes && node.childNodes.length > 0) {
                        for (i = 0, len = node.childNodes.length; i < len; i++) {
                            newNode.appendChild(ieFix(node.childNodes[i]));
                        }
                    }
                    return newNode;
                } else {
                    return d.createTextNode(node.nodeValue);
                }
            };

            var replacer = function (s, m1, m2) {
                // We assume for now that embedded variables do not have dollar sign; to add a dollar sign, you currently must use {$$var} (We might change this, however.)
                // Doesn't cover all cases yet: see http://php.net/manual/en/language.types.string.php#language.types.string.syntax.double
                if (m1 !== '\\') {
                    return m1 + eval(m2);
                } else {
                    return s;
                }
            };

            this.php_js = this.php_js || {};
            var phpjs = this.php_js;
            var ini = phpjs.ini;
            var obs = phpjs.obs;
            for (i = 0; i < argc; i++) {
                arg = argv[i];
                if (ini && ini['phpjs.echo_embedded_vars']) {
                    arg = arg.replace(/(.?)\{?\$(\w*?\}|\w*)/g, replacer);
                }

                if (!phpjs.flushing && obs && obs.length) {
                    // If flushing we output, but otherwise presence of a buffer means caching output
                    obs[obs.length - 1].buffer += arg;
                    continue;
                }

                if (d.appendChild) {
                    if (d.body) {
                        if (win.navigator.appName === 'Microsoft Internet Explorer') {
                            // We unfortunately cannot use feature detection, since this is an IE bug with cloneNode nodes being appended
                            d.body.appendChild(stringToDOM(ieFix(arg)));
                        } else {
                            var unappendedLeft = stringToDOM(arg, d.body, ns_xhtml, 'div')
                                .cloneNode(true); // We will not actually append the div tag (just using for providing XHTML namespace by default)
                            if (unappendedLeft) {
                                d.body.appendChild(unappendedLeft);
                            }
                        }
                    } else {
                        // We will not actually append the description tag (just using for providing XUL namespace by default)
                        d.documentElement.appendChild(stringToDOM(arg, d.documentElement, ns_xul, 'description'));
                    }
                } else if (d.write) {
                    d.write(arg);
                } else {
                    console.log(arg);
                }
            }
        },
        empty: function (mixed_var) {
            var undef, key, i, len;
            var emptyValues = [undef, null, false, 0, "", "0"];

            for (i = 0, len = emptyValues.length; i < len; i++) {
                if (mixed_var === emptyValues[i]) {
                    return true;
                }
            }

            if (typeof mixed_var === "object") {
                for (key in mixed_var) {
                    // TODO: should we check for own properties only?
                    //if (mixed_var.hasOwnProperty(key)) {
                    return false;
                    //}
                }
                return true;
            }

            return false;
        },
        end: function (arr) {
            this.php_js = this.php_js || {};
            this.php_js.pointers = this.php_js.pointers || [];
            var indexOf = function (value) {
                for (var i = 0, length = this.length; i < length; i++) {
                    if (this[i] === value) {
                        return i;
                    }
                }
                return -1;
            };
            // END REDUNDANT
            var pointers = this.php_js.pointers;
            if (!pointers.indexOf) {
                pointers.indexOf = indexOf;
            }
            if (pointers.indexOf(arr) === -1) {
                pointers.push(arr, 0);
            }
            var arrpos = pointers.indexOf(arr);
            if (Object.prototype.toString.call(arr) !== '[object Array]') {
                var ct = 0;
                var val;
                for (var k in arr) {
                    ct++;
                    val = arr[k];
                }
                if (ct === 0) {
                    return false; // Empty
                }
                pointers[arrpos + 1] = ct - 1;
                return val;
            }
            if (arr.length === 0) {
                return false;
            }
            pointers[arrpos + 1] = arr.length - 1;
            return arr[pointers[arrpos + 1]];
        },
        file: function (url) {
            var req = this.window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
            if (!req) {
                throw new Error('XMLHttpRequest not supported');
            }

            req.open("GET", url, false);
            req.send(null);

            return req.responseText.split('\n');
        },
        floatVal: function (mixed_var) {
            return (parseFloat(mixed_var) || 0);
        },
        getType: function (mixed_var) {
            var s = typeof mixed_var,
                name;
            var getFuncName = function (fn) {
                var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
                if (!name) {
                    return '(Anonymous)';
                }
                return name[1];
            };
            if (s === 'object') {
                if (mixed_var !== null) { // From: http://javascript.crockford.com/remedial.html
                    if (typeof mixed_var.length === 'number' && !(mixed_var.propertyIsEnumerable('length')) && typeof mixed_var.splice === 'function') {
                        s = 'array';
                    } else if (mixed_var.constructor && getFuncName(mixed_var.constructor)) {
                        name = getFuncName(mixed_var.constructor);
                        if (name === 'Date') {
                            s = 'date'; // not in PHP
                        } else if (name === 'RegExp') {
                            s = 'regexp'; // not in PHP
                        } else if (name === 'PHPJS_Resource') { // Check against our own resource constructor
                            s = 'resource';
                        }
                    }
                } else {
                    s = 'null';
                }
            } else if (s === 'number') {
                s = this.isFloat(mixed_var) ? 'double' : 'integer';
            }
            return s;
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
        intVal: function (mixed_var, base) {
            var tmp;

            var type = typeof mixed_var;

            if (type === 'boolean') {
                return +mixed_var;
            } else if (type === 'string') {
                tmp = parseInt(mixed_var, base || 10);
                return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
            } else if (type === 'number' && isFinite(mixed_var)) {
                return mixed_var | 0;
            } else {
                return 0;
            }
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
        isCallable: function (v, syntax_only, callable_name) {
            var name = '',
                obj = {},
                method = '';
            var getFuncName = function (fn) {
                var name = (/\W*function\s+([\w\$]+)\s*\(/)
                    .exec(fn);
                if (!name) {
                    return '(Anonymous)';
                }
                return name[1];
            };
            if (typeof v === 'string') {
                obj = this.window;
                method = v;
                name = v;
            } else if (typeof v === 'function') {
                return true;
            } else if (Object.prototype.toString.call(v) === '[object Array]' &&
                v.length === 2 && typeof v[0] === 'object' && typeof v[1] === 'string') {
                obj = v[0];
                method = v[1];
                name = (obj.constructor && getFuncName(obj.constructor)) + '::' + method;
            } else {
                return false;
            }
            if (syntax_only || typeof obj[method] === 'function') {
                if (callable_name) {
                    this.window[callable_name] = name;
                }
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
        isSet: function () {
            var a = arguments,
                l = a.length,
                i = 0,
                undef;

            if (l === 0) {
                throw new Error('Empty isset');
            }

            while (i !== l) {
                if (a[i] === undef || a[i] === null) {
                    return false;
                }
                i++;
            }
            return true;
        },
        isString: function (mixed) {
            return ('string' === typeof mixed);
        },
        key: function (arr) {
            // BEGIN REDUNDANT
            this.php_js = this.php_js || {};
            this.php_js.pointers = this.php_js.pointers || [];
            var indexOf = function (value) {
                for (var i = 0, length = this.length; i < length; i++) {
                    if (this[i] === value) {
                        return i;
                    }
                }
                return -1;
            };
            // END REDUNDANT
            var pointers = this.php_js.pointers;
            if (!pointers.indexOf) {
                pointers.indexOf = indexOf;
            }

            if (pointers.indexOf(arr) === -1) {
                pointers.push(arr, 0);
            }
            var cursor = pointers[pointers.indexOf(arr) + 1];
            if (Object.prototype.toString.call(arr) !== '[object Array]') {
                var ct = 0;
                for (var k in arr) {
                    if (ct === cursor) {
                        return k;
                    }
                    ct++;
                }
                return false; // Empty
            }
            if (arr.length === 0) {
                return false;
            }
            return cursor;
        },
        obGetClean: function () {
            // http://kevin.vanzonneveld.net
            // +   original by: Brett Zamir (http://brett-zamir.me)
            // *     example 1: ob_get_clean();
            // *     returns 1: 'some buffer contents'

            var PHP_OUTPUT_HANDLER_START = 1,
                PHP_OUTPUT_HANDLER_END = 4;

            this.php_js = this.php_js || {};
            var phpjs = this.php_js,
                obs = phpjs.obs;
            if (!obs || !obs.length) {
                return false;
            }
            var flags = 0,
                ob = obs[obs.length - 1],
                buffer = ob.buffer;
            if (ob.callback) {
                if (!ob.status) {
                    flags |= PHP_OUTPUT_HANDLER_START;
                }
                flags |= PHP_OUTPUT_HANDLER_END;
                ob.status = 2;
                buffer = ob.callback(buffer, flags);
            }
            obs.pop();
            return buffer;
        },
        obStart: function (output_callback, chunk_size, erase) {
            // http://kevin.vanzonneveld.net
            // +   original by: Brett Zamir (http://brett-zamir.me)
            // %        note 1: chunk_size and erase arguments are not presently supported
            // *     example 1: ob_start('someCallback', 4096, true);
            // *     returns 1: true

            var bufferObj = {},
                internalType = false,
                extra = false;
            erase = !(erase === false); // true is default
            chunk_size = chunk_size === 1 ? 4096 : (chunk_size || 0);

            this.php_js = this.php_js || {};
            this.php_js.obs = this.php_js.obs || []; // Array for nestable buffers
            var phpjs = this.php_js,
                ini = phpjs.ini,
                obs = phpjs.obs;

            if (!obs && (ini && ini.output_buffering && (typeof ini.output_buffering.local_value !== 'string' || ini.output_buffering.local_value.toLowerCase() !== 'off'))) {
                extra = true; // We'll run another ob_start() below (recursion prevented)
            }

            if (typeof output_callback === 'string') {
                if (output_callback === 'URL-Rewriter') { // Any others?
                    internalType = true;
                    output_callback = function URLRewriter() {
                    }; // No callbacks?
                }
                if (typeof this.window[output_callback] === 'function') {
                    output_callback = this.window[output_callback]; // callback expressed as a string (PHP-style)
                } else {
                    return false;
                }
            }
            bufferObj = {
                erase: erase,
                chunk_size: chunk_size,
                callback: output_callback,
                type: 1,
                status: 0,
                buffer: ''
            };

            // Fix: When else do type and status vary (see also below for non-full-status)
            // type: PHP_OUTPUT_HANDLER_INTERNAL (0) or PHP_OUTPUT_HANDLER_USER (1)
            // status: PHP_OUTPUT_HANDLER_START (0), PHP_OUTPUT_HANDLER_CONT (1) or PHP_OUTPUT_HANDLER_END (2)
            // Fix: Need to add the following (for ob_get_status)?:   size: 40960, block_size:10240; how to set size/block_size?
            if (internalType) {
                bufferObj.type = 0;
            }

            obs.push(bufferObj);

            if (extra) {
                return this.ob_start(); // We have to start buffering ourselves if the preference is set (and no buffering on yet)
            }

            return true;
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
        round: function (value, precision, mode) {
            var m, f, isHalf, sgn; // helper variables
            precision |= 0; // making sure precision is integer
            m = Math.pow(10, precision);
            value *= m;
            sgn = (value > 0) | -(value < 0); // sign of the number
            isHalf = value % 1 === 0.5 * sgn;
            f = Math.floor(value);

            if (isHalf) {
                switch (mode) {
                    case 'PHP_ROUND_HALF_DOWN':
                        value = f + (sgn < 0); // rounds .5 toward zero
                        break;
                    case 'PHP_ROUND_HALF_EVEN':
                        value = f + (f % 2 * sgn); // rouds .5 towards the next even integer
                        break;
                    case 'PHP_ROUND_HALF_ODD':
                        value = f + !(f % 2); // rounds .5 towards the next odd integer
                        break;
                    default:
                        value = f + (sgn > 0); // rounds .5 away from zero
                }
            }

            return (isHalf ? value : Math.round(value)) / m;
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
                        number = Math.round(number - number % 1); // Plain Math.round doesn't just truncate
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
        strReplace: function (search, replace, subject, count) {
            var i = 0,
                j = 0,
                temp = '',
                repl = '',
                sl = 0,
                fl = 0,
                f = [].concat(search),
                r = [].concat(replace),
                s = subject,
                ra = Object.prototype.toString.call(r) === '[object Array]',
                sa = Object.prototype.toString.call(s) === '[object Array]';
            s = [].concat(s);
            if (count) {
                this.window[count] = 0;
            }

            for (i = 0, sl = s.length; i < sl; i++) {
                if (s[i] === '') {
                    continue;
                }
                for (j = 0, fl = f.length; j < fl; j++) {
                    temp = s[i] + '';
                    repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                    s[i] = (temp).split(f[j]).join(repl);
                    if (count && s[i] !== temp) {
                        this.window[count] += (temp.length - s[i].length) / f[j].length;
                    }
                }
            }
            return sa ? s : s[0];
        },
        strRepeat: function (input, multiplier) {
            //  discuss at: http://phpjs.org/functions/str_repeat/
            // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // improved by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
            // improved by: Ian Carter (http://euona.com/)
            //   example 1: str_repeat('-=', 10);
            //   returns 1: '-=-=-=-=-=-=-=-=-=-='

            var y = '';
            while (true) {
                if (multiplier & 1) {
                    y += input;
                }
                multiplier >>= 1;
                if (multiplier) {
                    input += input;
                } else {
                    break;
                }
            }
            return y;
        },
        strval: function (str) {
            var type = '';

            if (str === null) {
                return '';
            }
            return str.toString();
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
        trim: function (str, charlist) {
            var whitespace, l = 0,
                i = 0;
            str += '';

            if (!charlist) {
                // default list
                whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
            } else {
                // preg_quote custom list
                charlist += '';
                whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
            }

            l = str.length;
            for (i = 0; i < l; i++) {
                if (whitespace.indexOf(str.charAt(i)) === -1) {
                    str = str.substring(i);
                    break;
                }
            }

            l = str.length;
            for (i = l - 1; i >= 0; i--) {
                if (whitespace.indexOf(str.charAt(i)) === -1) {
                    str = str.substring(0, i + 1);
                    break;
                }
            }

            return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
        },
        unsetKey: function (sKey, aArray) {
            aArray.splice(sKey, 1);
        },
        varDump: function () {

            //  discuss at: http://phpjs.org/functions/var_dump/
            // original by: Brett Zamir (http://brett-zamir.me)
            // improved by: Zahlii
            // improved by: Brett Zamir (http://brett-zamir.me)
            //  depends on: echo
            //        note: For returning a string, use var_export() with the second argument set to true
            //        test: skip
            //   example 1: var_dump(1);
            //   returns 1: 'int(1)'

            var output = '',
                pad_char = ' ',
                pad_val = 4,
                lgth = 0,
                i = 0;

            var _getFuncName = function (fn) {
                var name = (/\W*function\s+([\w\$]+)\s*\(/)
                    .exec(fn);
                if (!name) {
                    return '(Anonymous)';
                }
                return name[1];
            };

            var _repeat_char = function (len, pad_char) {
                var str = '';
                for (var i = 0; i < len; i++) {
                    str += pad_char;
                }
                return str;
            };
            var _getInnerVal = function (val, thick_pad) {
                var ret = '';
                if (val === null) {
                    ret = 'NULL';
                } else if (typeof val === 'boolean') {
                    ret = 'bool(' + val + ')';
                } else if (typeof val === 'string') {
                    ret = 'string(' + val.length + ') "' + val + '"';
                } else if (typeof val === 'number') {
                    if (parseFloat(val) == parseInt(val, 10)) {
                        ret = 'int(' + val + ')';
                    } else {
                        ret = 'float(' + val + ')';
                    }
                }
                // The remaining are not PHP behavior because these values only exist in this exact form in JavaScript
                else if (typeof val === 'undefined') {
                    ret = 'undefined';
                } else if (typeof val === 'function') {
                    var funcLines = val.toString()
                        .split('\n');
                    ret = '';
                    for (var i = 0, fll = funcLines.length; i < fll; i++) {
                        ret += (i !== 0 ? '\n' + thick_pad : '') + funcLines[i];
                    }
                } else if (val instanceof Date) {
                    ret = 'Date(' + val + ')';
                } else if (val instanceof RegExp) {
                    ret = 'RegExp(' + val + ')';
                } else if (val.nodeName) {
                    // Different than PHP's DOMElement
                    switch (val.nodeType) {
                        case 1:
                            if (typeof val.namespaceURI === 'undefined' || val.namespaceURI === 'http://www.w3.org/1999/xhtml') {
                                // Undefined namespace could be plain XML, but namespaceURI not widely supported
                                ret = 'HTMLElement("' + val.nodeName + '")';
                            } else {
                                ret = 'XML Element("' + val.nodeName + '")';
                            }
                            break;
                        case 2:
                            ret = 'ATTRIBUTE_NODE(' + val.nodeName + ')';
                            break;
                        case 3:
                            ret = 'TEXT_NODE(' + val.nodeValue + ')';
                            break;
                        case 4:
                            ret = 'CDATA_SECTION_NODE(' + val.nodeValue + ')';
                            break;
                        case 5:
                            ret = 'ENTITY_REFERENCE_NODE';
                            break;
                        case 6:
                            ret = 'ENTITY_NODE';
                            break;
                        case 7:
                            ret = 'PROCESSING_INSTRUCTION_NODE(' + val.nodeName + ':' + val.nodeValue + ')';
                            break;
                        case 8:
                            ret = 'COMMENT_NODE(' + val.nodeValue + ')';
                            break;
                        case 9:
                            ret = 'DOCUMENT_NODE';
                            break;
                        case 10:
                            ret = 'DOCUMENT_TYPE_NODE';
                            break;
                        case 11:
                            ret = 'DOCUMENT_FRAGMENT_NODE';
                            break;
                        case 12:
                            ret = 'NOTATION_NODE';
                            break;
                    }
                }
                return ret;
            };

            var _formatArray = function (obj, cur_depth, pad_val, pad_char) {
                var someProp = '';
                if (cur_depth > 0) {
                    cur_depth++;
                }

                var base_pad = _repeat_char(pad_val * (cur_depth - 1), pad_char);
                var thick_pad = _repeat_char(pad_val * (cur_depth + 1), pad_char);
                var str = '';
                var val = '';

                if (typeof obj === 'object' && obj !== null) {
                    if (obj.constructor && _getFuncName(obj.constructor) === 'PHPJS_Resource') {
                        return obj.var_dump();
                    }
                    lgth = 0;
                    for (someProp in obj) {
                        lgth++;
                    }
                    str += 'array(' + lgth + ') {\n';
                    for (var key in obj) {
                        var objVal = obj[key];
                        if (typeof objVal === 'object' && objVal !== null && !(objVal instanceof Date) && !(objVal instanceof RegExp) && !
                            objVal.nodeName) {
                            str += thick_pad + '[' + key + '] =>\n' + thick_pad + _formatArray(objVal, cur_depth + 1, pad_val,
                                pad_char);
                        } else {
                            val = _getInnerVal(objVal, thick_pad);
                            str += thick_pad + '[' + key + '] =>\n' + thick_pad + val + '\n';
                        }
                    }
                    str += base_pad + '}\n';
                } else {
                    str = _getInnerVal(obj, thick_pad);
                }
                return str;
            };

            output = _formatArray(arguments[0], 0, pad_val, pad_char);
            for (i = 1; i < arguments.length; i++) {
                output += '\n' + _formatArray(arguments[i], 0, pad_val, pad_char);
            }

            this.echo(output);

        },
        vsprintf: function (format, args) {
            return this.sprintf.apply(this, [format].concat(args));
        }
    };
}
