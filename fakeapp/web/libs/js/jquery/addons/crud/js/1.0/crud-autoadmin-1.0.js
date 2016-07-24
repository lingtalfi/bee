/**
 * This is an implementation
 * for Pragmatik.Crud plugin's crud autoAdmin implementation.
 *
 *
 * Depends on:
 * - jquery
 * - beef 1.02
 * - beel
 * - pea
 *
 *
 */
if ('undefined' == typeof window.crudAutoAdmin) {
    (function ($) {


        window.crudAutoAdmin = {
            wizard: function (params) {

                params = $.extend({
                    container: null,
                    serverUrl: 'service/ajaxservice.php',
                    serverId: "undefined",
                    serviceId: 'crud',
                    beefWizard: null,
                    beelWizard: null,
                    /**
                     * Table is only necessary if
                     * beefWizard and beelWizard instances
                     * are not defined (null).
                     */
                    crudId: null,
                    /**
                     * @param type: multiple|single
                     */
                    actionName2CssClass: function (actionName, type) {
                        if ('multiple' === type) {
                            return 'multiple-action-' + actionName;
                        }
                        return 'action-' + actionName;
                    }
                }, params);

                var zis = this;
                var beefWizard = params.beefWizard;
                var beelWizard = params.beelWizard;
                var jContainer = params.container;
                var clickActions = {
                    edit: function (jTarget, oWizard) {

                    }
                };


                if (null === beefWizard) {
                    beefWizard = new window.beefCrudWizard({
                        serverUrl: params.serverUrl,
                        serverId: params.serverId,
                        serviceId: params.serviceId
                    });
                }
                if (null === beelWizard) {
                    //------------------------------------------------------------------------------/
                    // BEEL
                    //------------------------------------------------------------------------------/
                    var callAndPostOptions = {
                        callFormOptions: {
                            beefOptions: {
                                getErrorControl: function (jReferentControl) {
                                    return jReferentControl.closest('tr').next('.beef-error-control');
                                },
                                getErrorMessageHolder: function (jErrorControl) {
                                    return jErrorControl.find('td:first');
                                }
                                // want to turn default beef error system to a js popup?
                                // just uncomment the validationHandler property below!
                                //validationHandler: function (controlErrors, oBeef) {
                                //    var s = '';
                                //    var n = "\n";
                                //    var t = "\t";
                                //    s += "The form contains the following errors:" + n;
                                //    for (var controlName in controlErrors) {
                                //        var errors = controlErrors[controlName];
                                //        if (errors.length) {
                                //            s += "- Errors for control " + controlName + ':' + n;
                                //            for (var i in errors) {
                                //                s += t + errors[i] + n;
                                //            }
                                //        }
                                //    }
                                //    alert(s);
                                //}
                            }
                        }
                    };
                    var actions = {
                        newrecord: function (jTarget) {
                            var jLoader = jTarget.next('.ajaxloader:first');
                            jLoader.removeClass('hidden');

                            beefWizard.callAndPost({
                                mode: 'auto',
                                type: 'formInsert',
                                crudId: params.crudId
                            }, {
                                mode: 'auto',
                                type: 'insert',
                                crudId: params.crudId
                            }, function (v) {
                                //console.log('posted successfully', v);
                                beelWizard.refresh();
                            }, function (jForm) {
                                jLoader.addClass('hidden');
                            }, callAndPostOptions);
                            return false;
                        }
                    };
                    beelWizard = new window.beelCrudWizard({
                        container: jContainer,
                        url: params.serverUrl,
                        serverId: params.serverId,
                        serviceId: params.serviceId,
                        crudParamsForRead: {
                            mode: 'auto',
                            type: 'list',
                            crudId: params.crudId
                        },
                        crudParamsForDelete: {
                            mode: 'auto',
                            type: 'delete',
                            crudId: params.crudId
                        },
                        clickActions: actions
                    });


                    var widgets = new window.beelCrudWizardWidgets({
                        container: jContainer,
                        nbItemsPerPage: 10
                    });

                    // before we start the wizard, let's take the widgets into accounts
                    widgets.bind(beelWizard);
                }
                beelWizard.start();


                this.getBeefWizard = function () {
                    return beefWizard;
                };
                this.getBeelWizard = function () {
                    return beelWizard;
                };
                this.setClickAction = function (actionName, callback, type) {
                    if ('undefined' === typeof type) {
                        type = 'single';
                    }
                    var cssClass = params.actionName2CssClass(actionName, type);
                    beelWizard.setClickAction(cssClass, function (jTarget) {
                        if ('multiple' === type) {
                            var rows = beelWizard.getSelectedRowsValues();
                            return callback(jTarget, zis, rows);
                        }
                        else {
                            var rowValues = beelWizard.getRowValuesByInner(jTarget);
                            return callback(jTarget, zis, rowValues);
                        }
                    });
                };

                this.setRowClickAction = function (actionName, callback) {
                    zis.setClickAction(actionName, function (jTarget, oWizard, rowValues) {

                        var jLoader = jTarget.next();
                        if (0 === jLoader.length) {
                            jLoader = $('<span class="ajaxloader"></span>');
                            jTarget.after(jLoader);
                        }
                        oWizard.callRowAction(actionName, {
                            rowValues: rowValues
                        }, function (r) {
                            callback(r, jTarget, oWizard, rowValues);
                            jLoader.remove();
                        }, 'rowAction');
                        return false;
                    }, 'single');
                };

                this.setMultipleRowsClickAction = function (actionName, callback) {
                    zis.setClickAction(actionName, function (jTarget, oWizard, rows) {
                        if (rows.length) {
                            var jLoader = jTarget.next();
                            if (0 === jLoader.length) {
                                jLoader = $('<span class="ajaxloader"></span>');
                                jTarget.after(jLoader);
                            }
                            oWizard.callRowAction(actionName, {
                                rows: rows
                            }, function (r) {
                                callback(r, jTarget, oWizard, rows);
                                jLoader.remove();
                            }, 'multipleRowsAction');
                        }
                        return false;

                    }, 'multiple');
                };


                this.callRowAction = function (actionName, extraData, callback, type) {
                    if ('undefined' === typeof type) {
                        type = 'rowAction';
                    }
                    var url = params.serverUrl;
                    var extra = {
                        actionName: actionName
                    };
                    if (pea.isArrayObject(extraData)) {
                        for (var i in extraData) {
                            extra[i] = extraData[i];
                        }
                    }
                    var data = getServerBasePayload(type, extra);
                    ajaxTim.sendMessage(url, data, function (m) {
                        if (pea.isFunction(callback)) {
                            callback(m);
                        }
                    });
                };

                this.refreshList = function () {
                    beelWizard.refresh();
                };


                function getServerBasePayload(type, extraParams) {
                    var $params = {
                        mode: 'auto',
                        crudId: params.crudId,
                        type: type
                    };
                    if (pea.isArrayObject(extraParams)) {
                        for (var i in extraParams) {
                            $params[i] = extraParams[i];
                        }
                    }
                    return {
                        serverId: params.serverId,
                        id: params.serviceId,
                        params: $params
                    };
                }

            }
        };

      


    })(jQuery);
}