/**
 * LingTalfi - 2015-01-15
 *
 * Depends on:
 *
 * - jquery
 * - pea
 *
 *
 *
 */
(function () {
    if ('undefined' === typeof window.array2Ul) {

        window.array2UlTool = {
            render: function (values) {
                var o = new window.array2Ul();
                return o.render(values);
            }
        };

        window.array2Ul = function (options) {

            options = $.extend(options, {
                arrayItemFormat: '<li>%s: <ul>%s</ul></li>',
                regularItemFormat: '<li>%s: %s</li>',
                displayEmptyArrays: true
            });

            this.render = function (values) {
                var s = '';
                for (var k in values) {
                    s += renderItem(k, values[k], k.replace('.', '\\.'));
                }
                if (true === shouldWrapItems(s)) {
                    s = wrapItems(s);
                }
                return s;
            };

            //------------------------------------------------------------------------------/
            // 
            //------------------------------------------------------------------------------/
            function renderItem(key, value, path) {
                var s = '';
                if (pea.isArrayOrObject(value)) {
                    if (true === shouldDisplayArray(value)) {
                        var c = '';
                        for (var k in value) {
                            c += renderItem(k, value[k], path + '.' + k.replace('.', '\\.'));
                        }
                        s += pea.sprintf(getArrayItemFormat(key, value, path), key, c);
                    }
                }
                else {
                    s += pea.sprintf(getRegularItemFormat(key, value, path), key, valueToString(value));
                }
                return s;
            }

            function wrapItems(items) {
                return '<ul>' + items + '</ul>';
            }

            function valueToString(value) {
                if (null === value) {
                    value = '';
                }
                return pea.htmlSpecialChars(value.toString());
            }

            function shouldDisplayArray(value) {
                if (true === options.displayEmptyArrays) {
                    return true;
                }
                return (pea.count(value) > 0);
            }

            function shouldWrapItems(items) {
                return (items.length > 0);
            }

            function getArrayItemFormat(key, value, path) {
                var c = options.arrayItemFormat;
                if (pea.isFunction(c)) {
                    return c.call(null, key, value, path);
                }
                return c;
            }

            function getRegularItemFormat(key, value, path) {
                var c = options.regularItemFormat;
                if (pea.isFunction(c)) {
                    return c.call(null, key, value, path);
                }
                return c;
            }
        };


    }
})();
 