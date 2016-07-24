/**
 * Depends on:
 * - jquery
 * - beef 1.02
 * - pea
 *
 *
 */
if ('undefined' == typeof window.beefCrudWizard) {
    (function ($) {


        window.beefCrudWizard = function (params) {

            params = $.extend({
                serverId: "undefined",
                serviceId: null,
                serverUrl: 'service/ajaxservice.php'
            }, params);


            this.callAndPost = function (callParams, postParams, onSuccess, onFormReady, options) {
                var data = {
                    serverId: params.serverId,
                    id: params.serviceId,
                    params: {}
                };
                var url = params.serverUrl;


                if (pea.isArrayObject(callParams)) {
                    for (var k in callParams) {
                        data.params[k] = callParams[k];
                    }
                }

                options = $.extend({
                    callFormOptions: {}
                }, options);


                var callFormOptions = $.extend({
                    createAfter: function (jForm) {
                        if (pea.isFunction(onFormReady)) {
                            onFormReady(jForm);
                        }
                    },
                    beefOptions: {}
                }, options.callFormOptions);
                window.beef.util.callForm(data, url, function (v, nestedForm, jContent) {
                    /**
                     * When posting the form, if the server side validation fails,
                     * we inject the errors in the currently opened form
                     */
                    data.params.values = v;


                    if (pea.isArrayObject(postParams)) {
                        for (var k in postParams) {
                            data.params[k] = postParams[k];
                        }
                    }


                    ajaxTim.sendMessage(url, data, function (m) {
                        var hasError = false;
                        if (pea.isArrayObject(m) && 1 === pea.count(m) && m._errors) {
                            for (var controlName in m._errors) {
                                var errors = m._errors[controlName];
                                nestedForm.addErrorMessage(controlName, errors.join('<br/>'));
                                hasError = true;
                            }
                        }
                        if (false === hasError) {
                            /**
                             * onSuccess callback is eventually called, only if the insert was successful server side
                             */
                            if (pea.isFunction(onSuccess)) {
                                onSuccess(m);
                            }
                            jContent.dialogg("destroy");
                        }
                    });
                    return false;
                }, callFormOptions);
            };


        };
    })(jQuery);
}