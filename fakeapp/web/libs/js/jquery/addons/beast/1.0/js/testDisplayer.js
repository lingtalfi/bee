/**
 * LingTalfi - 2015-01-26
 * Ported from Komin.Beast.TestDisplayer
 *
 *
 * @depends jquery, beelJsTable
 *
 */
if ('undefined' === typeof window.testDisplayer) {
    (function () {
        function devError(msg) {
            throw new Error("BeastTestDisplayer: " + msg);
        }

        window.testDisplayer = function ($options) {
            var options = $.extend({
                showTrace: true,
                showDebug: true
            }, $options);


            this.inject = function (beastEngine, jContainer) {
                var s = getTestResultsString(beastEngine);
                if (true === options['showDebug'] &&
                    'undefined' !== typeof beastEngine.getDebugger) {
                    s += renderDebug(beastEngine);
                }
                if (true === options['showTrace']) {
                    s += renderTrace(beastEngine);
                }
                jContainer.html(s);
            };

            //------------------------------------------------------------------------------/
            // 
            //------------------------------------------------------------------------------/
            function renderDebug(debuggableBeast) {
                var logs = debuggableBeast.getDebugger().getLogs();
                var s = '';
                for (var i in logs) {
                    s += logs[i];
                }
                return s;
            }

            function renderTrace(beastEngine) {
                var list = new window.beelJsTable(null, {
                    tableAttr: {
                        class: 'beeltable beast-tool-results-table'
                    },
                    headerColsContent: {
                        0: 'id',
                        1: 'type',
                        2: 'msg'
                    },
                    lineAttr: function (item, i) {
                        return {
                            'class': 'type' + item[1]
                        };
                    }
                });
                list.setRegularColumns([
                    '0',
                    '1',
                    '2'
                ]);
                var s = getTraceCssStyle();
                s += list.render(beastEngine.getResults());
                return s;
            }


        };

        //------------------------------------------------------------------------------/
        // 
        //------------------------------------------------------------------------------/
        function getTraceCssStyle() {
            var s = '';
            s += "<style>\
                .beast-tool-results-table{\
                border-collapse: collapse;\
                text-align: left;\
            }\
        .beast-tool-results-table,\
        .beast-tool-results-table tr,\
        .beast-tool-results-table th,\
        .beast-tool-results-table td\
            {\
                border: 1px solid black;\
                padding: 5px;\
            }\
        .beast-tool-results-table .types{\
                background: green;\
            }\
        .beast-tool-results-table .typef{\
                background: red;\
            }\
        .beast-tool-results-table .typee{\
                background: black;\
                color: yellow;\
            }\
        .beast-tool-results-table .typena{\
                background: orange;\
            }\
        .beast-tool-results-table .typesk{\
                background: white;\
            }\
            </style>";
            return s;
        }

        function getTestResultsString(beast) {
            var s = 0;
            var f = 0;
            var e = 0;
            var na = 0;
            var sk = 0;
            var r = beast.getResults();
            for (var i in r) {
                var row = r[i];
                if (1 in row) {
                    switch (row[1]) {
                        case 's':
                            s++;
                            break;
                        case 'f':
                            f++;
                            break;
                        case 'e':
                            e++;
                            break;
                        case 'na':
                            na++;
                            break;
                        case 'sk':
                            sk++;
                            break;
                        default:
                            devError("type must be one of s, f, e, na or sk");
                            break;
                    }
                }
                else {
                    devError("Invalid row, must at least contain the 1 index");
                }
            }

            var o = '';
            o += '_BEAST_TEST_RESULTS:s=' + s + ';';
            o += 'f=' + f + ';';
            o += 'e=' + e + ';';
            o += 'na=' + na + ';';
            o += 'sk=' + sk + '__';
            return o;
        }
    })();
}