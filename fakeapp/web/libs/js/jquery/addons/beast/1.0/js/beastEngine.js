/**
 * LingTalfi - 2015-01-26
 * Ported from Komin.Beast.KobeeBeastEngine
 *
 *
 * @depends jQuery, debugTool, beelJsTable
 *
 */
if ('undefined' === typeof window.beastEngine) {
    (function () {
        function devError(msg) {
            throw new Error("Beast: " + msg);
        }

        window.beastEngine = function ($options) {

            var results = [];
            var options = $.extend({
                'testSuccessMessage': "ok",
                'testFailureMessage': "test failed",
                'testErrorMessage': "An error occurred while executing the test!",
                'testSkipMessage': "Test was skipped!",
                'testNotApplicable': "This test is not applicable on this machine!",
                debugger: null
            }, $options);
            var numMessage = 0;
            var oDebugger = options['debugger'];
            if (null === oDebugger) {
                oDebugger = new Debugger();
            }

            this.getDebugger = function () {
                return oDebugger;
            };

            this.test = function (f) {
                numMessage++;
                executeTest(f);
            };

            this.getResults = function () {
                return results;
            };

            this.compare = function (values, results, f, $zeoptions) {

                var zeoptions = $.extend({
                    // 0=no, 1=just the first failure message, 2=all failure messages
                    debugFailure: 1,
                    focus: null
                }, $zeoptions);

                var n = values.length;
                var n2 = results.length;

                if (n === n2) {

                    var focus = zeoptions['focus'];
                    var cmpDebug = [];
                    for (var i in values) {
                        var v = values[i];
                        var exp = results.shift();
                        numMessage++;
                        if (null === focus ||
                            (null !== focus && parseInt(focus) === numMessage)) {
                            var res = f(v);
                            executeTest(function (oMsg) {
                                // res and exp are equal when all the properties of the object are the same,
                                // but there is no need that they both share the same instance of an object to succeed
                                if (JSON.stringify(res) === JSON.stringify(exp)) {
                                    oMsg.msg = "Values res and exp are the same, with res=" + window.debugTool.miniDump(res) + " and exp=" + window.debugTool.miniDump(exp);
                                    return true;
                                }
                                else {
                                    oMsg.msg = "Values res and exp are not the same, with res=" + window.debugTool.miniDump(res) + " and exp=" + window.debugTool.miniDump(exp);
                                    if (
                                        (1 === zeoptions['debugFailure'] && 0 === cmpDebug.length) ||
                                        2 === zeoptions['debugFailure']
                                    ) {
                                        cmpDebug.push([numMessage, v, res, exp]);
                                    }
                                    return false;
                                }
                            });
                        }
                    }
                    if (cmpDebug.length) {
                        oDebugger.log(renderCompareDebug(cmpDebug));
                    }
                }
                else {
                    devError("The arrays values and results don't have the same number of entries (" + n + ", " + n2 + ")");
                }
            };


            //------------------------------------------------------------------------------/
            // PRIVATE - PROTECTED
            //------------------------------------------------------------------------------/
            function executeTest(f) {
                try {
                    var oMsg = {
                        msg: null
                    };
                    var type = null;


                    var r = f(oMsg);
                    if (true === r) {
                        if (null === oMsg.msg) {
                            oMsg.msg = options['testSuccessMessage'];
                        }
                        type = 's';
                    }
                    else if (false === r) {
                        if (null === oMsg.msg) {
                            oMsg.msg = options['testFailureMessage'];
                        }
                        type = 'f';
                    }
                    else {
                        throw new Error("A test must always return a boolean");
                    }

                }
                catch (e) {
                    if ('BeastSkipException' === e.exceptionType) {
                        oMsg.msg = e.message;
                        if (0 === oMsg.msg.length) {
                            oMsg.msg = options['testSkipMessage'];
                        }
                        oMsg.msg = 'sk';
                    }
                    else if ('BeastNotApplicableException' === e.exceptionType) {
                        oMsg.msg = e.message;
                        if (0 === oMsg.msg.length) {
                            oMsg.msg = options['testNotApplicable'];
                        }
                        type = 'na';
                    }
                    else {
                        oMsg.msg = e.message;
                        if (oMsg.msg.length > 0) {
                            oMsg.msg = formatExceptionMessage(e);
                        }
                        else {
                            oMsg.msg = options['testErrorMessage'];
                        }
                        type = 'e';
                    }
                }
                registerTestResult(type, oMsg.msg);
            }

            function registerTestResult(type, message) {
                results.push([numMessage, type, message]);
            }

        };

        //------------------------------------------------------------------------------/
        // 
        //------------------------------------------------------------------------------/
        function renderCompareDebug(cmpDebug) {
            var list = new window.beelJsTable(null, {
                tableAttr: {
                    class: 'beeltable beast-tool-debug-table'
                },
                headerColsContent: {
                    0: 'id',
                    1: 'value',
                    2: 'result',
                    3: 'expected'
                }
            });
            var dump = function (content, item) {
                return window.debugTool.dump(content);
            };
            list.setFilters({
                '1': dump,
                '2': dump,
                '3': dump
            });
            list.setRegularColumns([
                '0',
                '1',
                '2',
                '3'
            ]);
            var s = getDebugCssStyle();
            s += list.render(cmpDebug);
            return s;
        }

        function getDebugCssStyle() {
            return "<style>\
                .beast-tool-debug-table{\
                border-collapse: collapse;\
                text-align: left;\
            }\
        .beast-tool-debug-table,\
        .beast-tool-debug-table tr,\
        .beast-tool-debug-table th,\
        .beast-tool-debug-table td\
            {\
                border: 1px solid black;\
                padding: 5px;\
            }\
            </style>";
        }


        function formatExceptionMessage(e) {
            var eol = '<br>';
            var s = '';
            s += 'Message: ' + e.message + eol;
            if (e.fileName) {
                s += 'File: ' + e.fileName + eol;
            }
            if (e.lineNumber) {
                s += 'Line: ' + e.lineNumber + eol;
            }
            return s;
        }

        var Debugger = function () {
            var logs = [];
            this.log = function (msg) {
                logs.push(msg);
            };
            this.getLogs = function () {
                return logs;
            };
            this.hasLogs = function () {
                return (logs.length > 0);
            };
        };

    })();
}