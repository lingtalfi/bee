/**
 * LingTalfi - 2015-01-26
 * This is a port of Komin > Beel > TableListRenderer.php
 * The doc is in php, not here.
 *
 *
 *
 * @depends jquery, pea, htmlTool
 *
 */
if ('undefined' === typeof window.beelJsTable) {


    window.beelJsTable = function ($itemRenderer, $options) {

        var itemRenderer = $itemRenderer;
        var columns = {};
        var filters = {};
        var options = $.extend({
            useHeader: true,
            tableAttr: {
                class: 'beeltable'
            },
            headerRowAttr: {},
            headerColsTag: 'th',
            headerColsAttr: {},
            headerColsContent: {},
            lineAttr: {},
            colAttr: {},
            useOddEven: true
        }, $options);
        var orderedCols = null;

        if (!itemRenderer) {
            itemRenderer = renderItem;
        }


        this.render = function (items) {
            orderedCols = null;
            var s = '';
            s += '<table' + window.htmlTool.toAttributesString(options['tableAttr']) + '>';
            if (true === options['useHeader'] && null !== columns) {
                s += getHeader();
            }
            s += getBody(items);
            s += '</table>';
            return s;
        };


        this.setRegularColumns = function ($columns) {
            for (var i in $columns) {
                var name = $columns[i];
                columns[name] = {};
            }
        };

        this.setSpecialColumn = function (name, pos, content) {
            if ('undefined' === typeof pos) {
                pos = 'last';
                columns[name] = {
                    pos: pos,
                    content: content
                };
            }
        };


        this.setOption = function (k, v) {
            options[k] = v;
        };
        this.setFilters = function ($filters) {
            filters = $filters;
        };

        this.setFilter = function (k, v) {
            filters[k] = v;
        };


        //------------------------------------------------------------------------------/
        // 
        //------------------------------------------------------------------------------/
        function renderItem(item, i) {
            var trAttr = options['lineAttr'];
            if (pea.isFunction(trAttr)) {
                trAttr = trAttr(item, i);
            }

            if (true === options['useOddEven']) {
                var $class = (0 === i % 2) ? 'even' : 'odd';
                if ('class' in trAttr) {
                    trAttr['class'] += ' ' + $class;
                }
                else {
                    trAttr['class'] = $class;
                }
            }

            var s = '';
            s += '<tr' + window.htmlTool.toAttributesString(trAttr) + '>';
            var c = getLineOpening();
            if (null !== c) {
                s += c;
            }

            // preparing positioned columns
            var posCols = getOrderedCols();
            for (var i in posCols) {
                var info = posCols[i];
                var colValue = null;
                var colName = info['name'];

                // col tag attr
                if (false === info['special']) {
                    colValue = item[colName];
                }
                else {
                    colValue = info['content'];
                    if (pea.isFunction(colValue)) {
                        colValue = colValue(item);
                    }
                }

                var colAttr = getColAttr(item, colName, colValue);
                var tag = '<td' + window.htmlTool.toAttributesString(colAttr) + '>';
                s += tag + filterCol(colValue, colName, item) + '</td>';
            }
            s += '</tr>';
            return s;

        }


        function getLineOpening() {
            return '';
        }

        function getOrderedCols() {
            if (null === orderedCols) {
                orderedCols = [];

                // first adding regular columns
                for (var n in columns) {
                    var info = columns[n];
                    if (false === ('pos' in info)) {
                        info['name'] = n;
                        info['special'] = false;
                        orderedCols.push(info);
                    }
                }


                // now adding special columns
                for (var n in columns) {
                    var info = columns[n];
                    if ('pos' in info) {
                        info['name'] = n;
                        info['special'] = true;
                        if ('last' === info['pos']) {
                            orderedCols.push(info);
                        }
                        else {
                            orderedCols.splice(info['pos'], 0, [info]);
                        }
                    }
                }

            }
            return orderedCols;
        }

        function getColAttr(item, colName, colValue) {
            if ('undefined' === typeof colValue) {
                colValue = null;
            }
            var colAttr = options['colAttr'];
            if (pea.isFunction(colAttr)) {
                colAttr = colAttr(item, colName, colValue);
            }
            return colAttr;
        }


        function filterCol(content, colName, item) {
            if (colName in filters) {
                var $filters = filters[colName];
                if (false === pea.isArrayOrObject($filters)) {
                    $filters = [$filters];
                }
                for (var i in $filters) {
                    content = $filters[i](content, item);
                }
            }
            return content;
        }


        function getHeaderLineOpening() {
            return '';
        }

        function getHeader() {
            var s = '';
            s += '<tr' + window.htmlTool.toAttributesString(options['headerRowAttr']) + '>';
            s += getHeaderLineOpening();

            var th = options['headerColsTag'];
            columns = getOrderedCols();
            for (var i in columns) {
                var info = columns[i];
                var name = info['name'];
                var special = info['special'];
                var attr = '';
                if (name in options['headerColsAttr']) {
                    if (pea.isFunction(options['headerColsAttr'][name])) {
                        attr = options['headerColsAttr'][name](name);
                    }
                }
                if (pea.isArrayOrObject(attr)) {
                    attr = window.htmlTool.toAttributesString(attr);
                }

                var content = name;
                if (name in options['headerColsContent']) {
                    if (pea.isFunction(options['headerColsContent'][name])) {
                        content = options['headerColsContent'][name](content);
                    }
                    else {
                        content = options['headerColsContent'][name];
                    }
                }
                content = filterHeaderContent(content, name, special);

                s += '<' + th + attr + '>' + content + '</' + th + '>';
            }
            s += '</tr>';
            return s;
        }


        function filterHeaderContent($content, $colName, $isSpecial) {
            return $content;
        }

        function getBody(items) {
            var s = '';
            for (var i in items) {
                var item = items[i];
                s += itemRenderer(item, i);
            }
            return s;
        }
    };
}


