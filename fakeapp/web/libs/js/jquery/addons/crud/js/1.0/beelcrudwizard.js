/**
 * Depends on:
 *
 * - jquery
 * - pea
 *
 *
 * What's new:
 *
 * 2015-02-05: reorganizing code to separate instance related code from utility code.
 *
 */
(function () {


    if ('undefined' === typeof window.beelCrudWizard) {
        /**
         * In this wizard, we use the following html:
         *
         * (container)
         *
         * ----- .tableholder   (we will inject the admin table  in this element)
         *              The generated table should be generated with CrudAdminTableListRenderer,
         *              and therefore its markup would be:
         *
         *              - .beeltable
         *              ----- thead
         *              --------- tr
         *              ------------- .sorter
         *              ----- tbody
         *
         *
         *
         * Note: widgets are handled externally, and should interact with this api.
         *
         */
        window.beelCrudWizard = function (params) {

            params = $.extend({
                container: null,
                url: 'service/ajaxservice.php',
                serverId: 'pragmatikCrudServer',
                serviceId: 'crud',
                crudParamsForRead: {},
                crudParamsForDelete: {},
                /**
                 * array of $cssClass => callback( jTarget, cssClass )
                 */
                clickActions: {},
            }, params);

            var jContainer = params.container;
            if (null === params.container) {
                throw new Error("container cannot be null");
            }

            var jTableHolder = $('.tableholder', jContainer);
            var zis = this;

            /**
             * Those two vars are initialized after the first refresh (rowsOnly request)
             * and might be bound to this wizard instance until the end of the script
             */
            var jHead = null;
            var jBody = null;
            var colNames = null;

            // cache js side
            var searchHeadersValues = {};
            var keyupTimeout = null;
            var keyupTimeoutDelay = 280;
            var likeMode = 'default'; // mysql|default
            var nbItemsMax = 20;
            var numPage = 1;
            var onRefreshAfter = function () {
            };
            var jFirstCheckBox;
            var tableValues = [];


            jContainer.on('click.beelCrudWizard', function (e) {
                var jTarget = $(e.target);
                for (var cssClass in params.clickActions) {
                    if (jTarget.hasClass(cssClass)) {
                        var handler = params.clickActions[cssClass];
                        var ret = handler(jTarget, cssClass);
                        if (false === ret) {
                            return ret;
                        }
                    }
                }
            });

            //------------------------------------------------------------------------------/
            // TABLE GUI SCRIPT
            //------------------------------------------------------------------------------/


            function cleanSearchHeaders() {
                searchHeadersValues = {};
                jHead.find('tr.columnsearch input').each(function (i) {
                    $(this).val('');
                });
                refreshView(true);
            }


            function setSearchHeaders() {
                searchHeadersValues = {};
                jHead.find('tr.columnsearch input').each(function (i) {
                    var colName = colNames[i];
                    var val = $(this).val();
                    if (val.length > 0) {
                        searchHeadersValues[colName] = $(this).val();
                    }
                });

            }


            function initTable(jTable) {
                addSearchHeader(jHead, colNames, searchHeadersValues);
                jTable.find('tr.columnsearch input').on('keyup', function (e) {
                    var code = e.which;
                    if (code > 40 || code < 37) { // excluding arrow keys
                        clearTimeout(keyupTimeout);
                        keyupTimeout = setTimeout(function () {
                            refreshView(true);
                        }, keyupTimeoutDelay);
                    }
                });
                jTableHolder.on('click', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass('sorter')) {
                        setNextSorterClass(jTarget);
                        refreshView(true);
                    }
                    else if (jTarget.hasClass('clearsearches')) {
                        cleanSearchHeaders();
                    }
                });
            }


            //------------------------------------------------------------------------------/
            // CRUD COMMUNICATION
            //------------------------------------------------------------------------------/
            var url = params.url;
            var data = {
                id: params.serviceId,
                serverId: params.serverId
            };
            data.params = {};


            function refreshView(rowsOnly) {
                if (true === rowsOnly) {
                    var sorts = getSorts(jHead, colNames);
                    setSearchHeaders();
                    data.params = {
                        sorts: sorts,
                        rowsOnly: true,
                        searches: searchHeadersValues
                    };
                }
                else {
                    data.params = {
                        rowsOnly: false
                    };
                }
                for (var k in params.crudParamsForRead) {
                    data.params[k] = params.crudParamsForRead[k];
                }


                if (null !== likeMode) {
                    data.params.likeMode = likeMode;
                }
                if (null !== nbItemsMax) {
                    data.params.maxItems = nbItemsMax;
                }
                if (null !== numPage) {
                    data.params.numPage = numPage;
                }

                window.ajaxTim.sendMessage(url, data, function (m) {

                    if (false === rowsOnly) {
                        jTableHolder.html(m.html);
                        var jTable = jTableHolder.find('.beeltable:first');
                        jHead = jTable.find('thead:first');
                        jBody = jTable.find('tbody:first');
                        colNames = m.colNames;
                        initTable(jTable);
                        jFirstCheckBox = jTableHolder.find('tr:first input:first');
                        jFirstCheckBox.on('change', function () {
                            if (true === $(this).prop('checked')) {
                                selectCheckboxes(jTableHolder);
                            }
                            else {
                                unselectCheckboxes(jTableHolder);
                            }
                        });
                    }
                    else {
                        jBody.html(m.html);
                    }
                    tableValues = m.values;
                    onRefreshAfter(m);
                });

            }


            //------------------------------------------------------------------------------/
            // PUBLIC METHODS
            //------------------------------------------------------------------------------/
            this.start = function () {
                refreshView(false);
            };
            this.setLikeMode = function (mode) {
                likeMode = mode;
            };

            this.setNbItemsMax = function (max) {
                nbItemsMax = max;
            };
            this.refresh = function () {
                refreshView(true);
            };
            this.setClickAction = function (cssClass, callback) {
                params.clickActions[cssClass] = callback;
            };

            this.setOnRefreshAfter = function (callback) {
                onRefreshAfter = callback;
            };

            this.setCurrentPage = function (currentPage) {
                numPage = currentPage;
            };

            this.getSelectedRowsValues = function () {
                var ret = [];
                jTableHolder.find('tbody tr td:first-of-type input').each(function (i) {
                    if (true === $(this).prop('checked')) {
                        ret.push(tableValues[i]);
                    }
                });
                return ret;
            };
            this.getRowValuesByInner = function (jInner) {
                var ret = [];
                var jTr = jInner.closest('tr');
                return tableValues[jTr.index()];
            };

            //this.deleteSelectedRows = function (completed) {
            //    data.params = {
            //        rowsRiv: zis.getSelectedRowsValues()
            //    };
            //    for (var k in params.crudParamsForDelete) {
            //        data.params[k] = params.crudParamsForDelete[k];
            //    }
            //    window.ajaxTim.sendMessage(url, data, function (nbDeleted) {
            //        completed(nbDeleted);
            //    });
            //};
        };

        //------------------------------------------------------------------------------/
        // 
        //------------------------------------------------------------------------------/
        function setNextSorterClass(jTarget) {
            var cssClass = '';
            if (jTarget.hasClass('double')) {
                cssClass = 'asc';
            }
            else if (jTarget.hasClass('asc')) {
                cssClass = 'desc';
            }
            else if (jTarget.hasClass('desc')) {
                cssClass = 'double';
            }
            jTarget.removeClass('sorter double asc desc').addClass('sorter ' + cssClass);
        }

        function addSearchHeader(jHead, colNames, searchHeadersValues) {
            var s = '';
            var ind = 0;
            jHead.find('tr:first th').each(function (i) {
                var jSort = $(this).find('.sorter');

                if (0 === i) {
                    s += '<td class="clearsearches">';
                }
                else {
                    s += '<td>';
                }
                if (jSort.length) {
                    var colName = colNames[ind];
                    var value = '';
                    if (colName in searchHeadersValues) {
                        value = searchHeadersValues[colName];
                    }
                    s += '<input type="text" value="' + pea.htmlSpecialChars(value) + '">';
                    ind++;
                }
                else {
                    s += '';
                }
                s += '</td>';
            });
            jHead.append('<tr class="columnsearch">' + s + '</tr>');
        }


        function selectCheckboxes(jTableHolder) {
            jTableHolder.find('tbody tr td:first-of-type input').each(function () {
                $(this).prop('checked', true);
            });
        }

        function unselectCheckboxes(jTableHolder) {
            jTableHolder.find('tbody tr td:first-of-type input').each(function () {
                $(this).prop('checked', false);
            });
        }


        function getSorts(jHead, colNames) {
            var ret = [];
            // note: .sorter is only affected to regular columns, not special columns
            jHead.find('tr:first .sorter').each(function (i, v) {
                if (
                    $(this).hasClass('asc') ||
                    $(this).hasClass('desc')
                ) {
                    var dir = 'asc';
                    if ($(this).hasClass('desc')) {
                        dir = 'desc';
                    }
                    var colName = colNames[i];
                    ret.push([colName, dir]);
                }
            });
            return ret;
        }

    }
})();