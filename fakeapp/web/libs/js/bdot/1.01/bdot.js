/**
 * Bdot.
 * LingTalfi - 2015-01-14
 */

(function () {


    if ('undefined' === typeof window.bdot) {
        var beeDot = '__-BEE_DOT-__';

        function replaceAll(search, replace, subject) {
            return subject.toString().split(search).join(replace);
        }

        function isEmpty(ao) {
            for (var i in ao) {
                return false;
            }
            return true;
        }

        function isArrayOrObject(mixed) {
            var m = Object.prototype.toString.call(mixed);
            if ('[object Array]' === m || '[object Object]' === m) {
                return true;
            }
            return false;
        }

        function isArray(mixed) {
            return ('[object Array]' === Object.prototype.toString.call(mixed));
        }


        function deleteKey(key, array) {
            if (true === isArray(array)) {
                array.splice(array.indexOf(key), 1);
            }
            else {
                delete array[key];
            }
        }

        function doGetValue($path, $array, $default, $found) {
            var $value;
            if (null === $path) {
                $found.found = true;
                return $array;
            }
            $path = $path.toString();
            if (-1 === $path.indexOf('.')) {
                if ($path in $array) {
                    $value = $array[$path];
                    $found.found = true;
                }
                else {
                    $value = $default;
                }
            }
            else {
                $path = replaceAll('\\.', beeDot, $path);
                var parts = $path.split('.');
                $value = doGetDotValue(parts, $array, beeDot, $default, $found);
            }
            return $value;
        }

        function doGetDotValue($paths, $array, $beeDot, $default, $found) {
            if (false === isEmpty($paths)) {
                var $seg = $paths.shift();
                $seg = replaceAll($beeDot, '.', $seg);
                if ($seg in $array) {
                    var $value = $array[$seg];
                    if (false === isEmpty($paths)) {
                        if (isArrayOrObject($value)) {
                            $found.found = true;
                            return doGetDotValue($paths, $value, $beeDot, $default, $found);
                        }
                        else {
                            $found.found = false;
                        }
                    }
                    else {
                        $found.found = true;
                        return $value;
                    }
                }
                else {
                    $found.found = false;
                }
            }
            return $default;
        }


        function doSetValue($path, $replacement, $array) {
            $path = replaceAll('\\.', beeDot, $path);
            var $parts = $path.split('.');
            if ($parts.length > 1) {
                var el = $parts.shift();
                var $key = replaceAll(beeDot, '.', el);
                if (false === ($key in $array) ||
                    (
                    true === ($key in $array) &&
                    false === isArrayOrObject($array[$key])
                    )
                ) {
                    $array[$key] = {};
                }
                doSetValue($parts.join('.'), $replacement, $array[$key]);
            }
            else {
                var el = $parts.shift();
                var $key = replaceAll(beeDot, '.', el);
                $array[$key] = $replacement;
            }
        }


        window.bdot = {
            getDotValue: function ($path, $array, $default, $found) {
                if ('undefined' === typeof $default) {
                    $default = null;
                }
                if ('undefined' === typeof $found) {
                    $found = {
                        found: false
                    };
                }
                return doGetValue($path, $array, $default, $found);
            },
            getLastComponent: function (path) {
                if (null === path) {
                    return null;
                }
                if (-1 === path.indexOf('.')) {
                    return path;
                }
                path = replaceAll('\\.', beeDot, path);
                var parts = path.split('.');
                var lastComponent = parts.pop();
                return replaceAll(beeDot, '\\.', lastComponent);
            },
            getParentKeyPath: function (path) {
                if (null === path || 'undefined' === typeof path) {
                    return null;
                }
                if (-1 === path.indexOf('.')) {
                    return null;
                }
                path = replaceAll('\\.', beeDot, path);
                var parts = path.split('.');
                parts.pop();
                if (0 === parts.length) {
                    return null;
                }
                var parent = parts.join('.');
                return replaceAll(beeDot, '\\.', parent);
            },
            hasDotValue: function ($path, $array) {
                var found = {
                    found: false
                };
                doGetValue($path, $array, null, found);
                return found.found;
            },
            setDotValue: function ($path, $replacement, $array, allowReplace) {
                if (null === $path) {
                    if (isArrayOrObject($replacement)) {
                        $array = $replacement;
                        return;
                    }
                    else {
                        if (true === allowReplace) {
                            $array = $replacement;
                            return;
                        }
                        else {
                            throw new Error("Cannot replace the root array with a non array value without allowReplace flag");
                        }
                    }
                }
                doSetValue($path, $replacement, $array);
            },
            unsetDotValue: function ($path, $array) {

                if (-1 === $path.indexOf('.')) {
                    deleteKey($path, $array);
                }
                else {
                    $path = replaceAll('\\.', beeDot, $path);
                    var $parts = $path.split('.');
                    var $first = $parts.shift();
                    $first = replaceAll(beeDot, '.', $first);
                    if ($first in $array) {
                        if ($parts.length > 0) {
                            var $newPath = $parts.join('.');
                            window.bdot.unsetDotValue($newPath, $array[$first]);
                        }
                        else {
                            deleteKey($first, $array);
                        }
                    }

                }
            }
        };
    }
})();
