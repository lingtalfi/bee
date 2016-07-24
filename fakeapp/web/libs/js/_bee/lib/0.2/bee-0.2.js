/**
 * bee.
 *
 * Some nifty functions I found on the net, or created myself.
 *
 * depends on pea.
 *
 *
 * 2014-02-26
 *
 */


if ('undefined' === typeof window.bee) {
    (function () {
        var sessionCpt = 0;
        window.bee = {
            createUrl: function (params, pathName) {
                var ret = '';
                if (!pea.isSet(pathName)) {
                    pathName = document.location.pathname;
                }

                ret += pathName;
                if (pea.isArrayObject(params) && pea.count(params) > 0) {
                    ret += '?';
                    var c = true;
                    for (var key in params) {
                        if (false === c) {
                            ret += '&';
                        }
                        ret += key + '=' + params[key];
                        c = false;
                    }
                }
                return ret;

            },
            getFileExtension: function (url) {
                var pos = url.lastIndexOf('.');
                if (-1 !== pos) {
                    return pea.substr(url, pos + 1);
                }
                return null;
            },
            getUniqueId: function (prefix) {
                var d = new Date().getTime();
                d += (parseInt(Math.random() * 100)).toString();
                if (undefined === prefix) {
                    prefix = 'bee-';
                }
                d = prefix + d + sessionCpt++;
                return d;
            },
            getUrlParams: function () {
                var searchString = window.location.search.substring(1);
                var ret = [];
                if (searchString.length > 1) {
                    ret = searchString.split('&');
                }
                return ret;
            },
            hasKeys: function (keys, array) {
                for (var i in keys) {
                    if (false === pea.arrayKeyExists(keys[i], array)) {
                        return false;
                    }
                }
                return true;
            },
            /**
             * Might not work in ie8 and under.
             * http://stackoverflow.com/questions/19023633/json-object-undefined-in-ie-compatibility-mode
             */
            isEqualArray: function (a1, a2) {
                return JSON.stringify(a1) == JSON.stringify(a2);
            },
            map: function (arr, callback) {
                if (arr.map) {
                    return arr.map(callback);
                }

                var r = [],
                    i;
                for (i = 0; i < arr.length; i++) {
                    r.push(callback(arr[i]));
                }

                return r;
            },
            stripScripts: function (s) {
                var div = document.createElement('div');
                div.innerHTML = s;
                var scripts = div.getElementsByTagName('script');
                var i = scripts.length;
                while (i--) {
                    scripts[i].parentNode.removeChild(scripts[i]);
                }
                return div.innerHTML;
            },
            /**
             * Inspired from
             * @pattern [callable-representation-mee™]
             */
            toCallableRepresentation: function ($callable) {
                if (pea.isCallable($callable)) {
                    if (pea.isString($callable)) {
                        return pea.sprintf('[callable function %s]', $callable);
                    }
                    else {
                        return '[callable closure]';
                    }
                }
                return '[Not a callable]';
            },
            /**
             * Inspired from
             * @pattern [symbolic-literals-mee™]
             */
            toSymbolicLiteral: function ($mixed, $verbosity, $symbols) {
                if (!$verbosity) {
                    $verbosity = 0;
                }
                if (!pea.isArrayObject($symbols)) {
                    $symbols = {};
                }
                $symbols = pea.arrayMerge({
                    'true': '[true]',
                    'false': '[false]',
                    'null': '[null]',
                    'array': '[array]',
                    'callable': '[callable]',
                    'object': '[object]',
                    'resource': '[resource]',
                    'unknown': '[unknown]'
                }, $symbols);

                if (pea.isString($mixed) || pea.isNumeric($mixed)) {
                    return $mixed;
                }
                if (true === $mixed) {
                    return $symbols['true'];
                }
                else if (false === $mixed) {
                    return $symbols['false'];
                }
                else if (null === $mixed) {
                    return $symbols['null'];
                }
                else if (pea.isCallable($mixed)) {
                    if (0 === $verbosity) {
                        return $symbols['callable'];
                    }
                    return bee.toCallableRepresentation($mixed);
                }
                else if (pea.isArray($mixed)) {
                    return $symbols['array'];
                }
                else if (pea.isObject($mixed)) {
                    if (0 === $verbosity) {
                        return $symbols['object'];
                    }
                    return pea.sprintf('[object: %s]', $mixed.constructor);

                }
                return $symbols['unknown'];
            },
            toUl: function ($array, $parentCallback, $childCallback, $wrapOutCallback, $options) {
                $options = pea.arrayReplace({
                    allowHtml: false,
                    displayNumericKeys: true,
                    symbolicVerbosity: 0,
                    symbols: []
                }, $options);

                if (!$parentCallback) {
                    $parentCallback = function ($key, $list) {
                        var $sKey = (false === $options.allowHtml) ? pea.htmlSpecialChars($key) : $key;
                        if (false === $options.displayNumericKeys) {
                            if (pea.isNumeric($sKey)) {
                                return pea.sprintf('<li><ul>%s</ul></li>', $list);
                            }
                        }
                        return pea.sprintf('<li><span>%s</span><ul>%s</ul></li>', $sKey, $list);
                    };
                }
                if (!$childCallback) {
                    $childCallback = function ($key, $value, $allowHtml, $displayNumericKeys) {
                        $sKey = (false === $allowHtml) ? pea.htmlSpecialChars($key) : $key;
                        $value = bee.toSymbolicLiteral($value, $options.symbolicVerbosity, $options.symbols);
                        $sValue = (false === $allowHtml) ? pea.htmlSpecialChars($value) : $value;
                        if (false === $displayNumericKeys) {
                            if (pea.isNumeric($sKey)) {
                                return pea.sprintf('<li><span>%s</span></li>', $sValue);
                            }
                        }
                        return pea.sprintf('<li><span>%s: %s</span></li>', $sKey, $sValue);
                    };
                }
                if (!$wrapOutCallback) {
                    $wrapOutCallback = function ($out) {
                        return pea.sprintf('<ul>%s</ul>', $out);
                    };
                }
                var $out = bee._doToUl($array, $parentCallback, $childCallback, $options.allowHtml, $options.displayNumericKeys);
                $out = $wrapOutCallback($out);
                return $out;
            },
            _doToUl: function ($array, $parentCallback, $childCallback, $allowHtml, $displayNumericKeys) {
                var $out = "";
                for (var $key in $array) {
                    var $elem = $array[$key];
                    if (!pea.isArrayOrObject($elem)) {
                        $out += $childCallback($key, $elem), $allowHtml, $displayNumericKeys;
                    } else {
                        $out += $parentCallback($key, bee._doToUl($elem, $parentCallback, $childCallback, $allowHtml, $displayNumericKeys));
                    }
                }
                return $out;
            },
            unsetKeys: function (keys, arrayOrObject) {
                /**
                 * http://www.dakindesign.com/blog/deleting-multiple-items-from-a-javascript-array/
                 */
                if (!pea.isArray(keys)) {
                    keys = [keys];
                }

                if (pea.isArray(arrayOrObject)) {
                    keys.sort(function (a, b) {
                        return a - b
                    });
                }

                for (var i = 0; i < keys.length; i++) {
                    var key = keys[i];
                    if (pea.isArray(arrayOrObject)) {
                        arrayOrObject.splice(key - i, 1);
                    }
                    else {
                        delete arrayOrObject[key];
                    }
                }
            }
        };
    })();


    //------------------------------------------------------------------------------/
    // JSON 2 ADDITION
    //------------------------------------------------------------------------------/
    // https://raw.github.com/douglascrockford/JSON-js/master/json2.js
    "object" != typeof JSON && (JSON = {}), function () {
        "use strict";
        function f(t) {
            return 10 > t ? "0" + t : t
        }

        function quote(t) {
            return escapable.lastIndex = 0, escapable.test(t) ? '"' + t.replace(escapable, function (t) {
                var e = meta[t];
                return"string" == typeof e ? e : "\\u" + ("0000" + t.charCodeAt(0).toString(16)).slice(-4)
            }) + '"' : '"' + t + '"'
        }

        function str(t, e) {
            var n, r, o, f, u, i = gap, p = e[t];
            switch (p && "object" == typeof p && "function" == typeof p.toJSON && (p = p.toJSON(t)), "function" == typeof rep && (p = rep.call(e, t, p)), typeof p) {
                case"string":
                    return quote(p);
                case"number":
                    return isFinite(p) ? String(p) : "null";
                case"boolean":
                case"null":
                    return String(p);
                case"object":
                    if (!p)return"null";
                    if (gap += indent, u = [], "[object Array]" === Object.prototype.toString.apply(p)) {
                        for (f = p.length, n = 0; f > n; n += 1)u[n] = str(n, p) || "null";
                        return o = 0 === u.length ? "[]" : gap ? "[\n" + gap + u.join(",\n" + gap) + "\n" + i + "]" : "[" + u.join(",") + "]", gap = i, o
                    }
                    if (rep && "object" == typeof rep)for (f = rep.length, n = 0; f > n; n += 1)"string" == typeof rep[n] && (r = rep[n], o = str(r, p), o && u.push(quote(r) + (gap ? ": " : ":") + o)); else for (r in p)Object.prototype.hasOwnProperty.call(p, r) && (o = str(r, p), o && u.push(quote(r) + (gap ? ": " : ":") + o));
                    return o = 0 === u.length ? "{}" : gap ? "{\n" + gap + u.join(",\n" + gap) + "\n" + i + "}" : "{" + u.join(",") + "}", gap = i, o
            }
        }

        "function" != typeof Date.prototype.toJSON && (Date.prototype.toJSON = function () {
            return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z" : null
        }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function () {
            return this.valueOf()
        });
        var cx, escapable, gap, indent, meta, rep;
        "function" != typeof JSON.stringify && (escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, meta = {"\b": "\\b", "	": "\\t", "\n": "\\n", "\f": "\\f", "\r": "\\r", '"': '\\"', "\\": "\\\\"}, JSON.stringify = function (t, e, n) {
            var r;
            if (gap = "", indent = "", "number" == typeof n)for (r = 0; n > r; r += 1)indent += " "; else"string" == typeof n && (indent = n);
            if (rep = e, e && "function" != typeof e && ("object" != typeof e || "number" != typeof e.length))throw new Error("JSON.stringify");
            return str("", {"": t})
        }), "function" != typeof JSON.parse && (cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, JSON.parse = function (text, reviver) {
            function walk(t, e) {
                var n, r, o = t[e];
                if (o && "object" == typeof o)for (n in o)Object.prototype.hasOwnProperty.call(o, n) && (r = walk(o, n), void 0 !== r ? o[n] = r : delete o[n]);
                return reviver.call(t, e, o)
            }

            var j;
            if (text = String(text), cx.lastIndex = 0, cx.test(text) && (text = text.replace(cx, function (t) {
                return"\\u" + ("0000" + t.charCodeAt(0).toString(16)).slice(-4)
            })), /^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, "")))return j = eval("(" + text + ")"), "function" == typeof reviver ? walk({"": j}, "") : j;
            throw new SyntaxError("JSON.parse")
        })
    }();
}
