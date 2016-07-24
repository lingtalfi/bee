/**
 * Depends on
 * - jquery
 * - ?dialogg: if dialogg is found, the error messages will take advantage of it,
 *                  otherwise, either an alert or the console.log (depending on errorMode)
 *                  will be used.
 */
(function ($) {

    if ('undefined' === typeof window.ajaxTim) {


        function error(msg, options) {

            var errorMode = options.errorMode;

            // this was primarily created to allow user to remove a started loader when something goes wrong.
            var onErrorBefore = options.onErrorBefore;
            if ('function' === typeof onErrorBefore) {
                onErrorBefore(msg);
            }

            if ('dialog' === errorMode && 'dialogg' in $.fn) {
                var content = $('<div>' + msg + '</div>');
                $("body").append(content);
                content.dialogg({
                    title: "Error",
                    modal: true,
                    close: function (jEl) {
                        content.dialogg("destroy");
                    }
                });
            }
            else if (
                ('dialog' === errorMode && false === ('dialogg' in $.fn)) ||
                    'alert' === errorMode
                ) {
                alert(msg);
            }
            else if ('exception' === errorMode) {
                throw new Error(msg);
            }
            else if ('log' === errorMode) {
                console.log(msg);
            }
            else {
                throw new Error("Unknown errorMode: " + errorMode);
            }
        }


        function onSuccessHandler(r, onSuccess, options) {
            if (r) {
                if ('t' in r) {
                    if ('m' in r) {
                        if ('s' === r.t) {
                            onSuccess(r.m);
                        }
                        else {
                            error(r.m, options);
                        }
                    }
                    else {
                        error("Invalid ajaxTim service: variable m not found in the response", options);
                    }
                }
                else {
                    error("Invalid ajaxTim service: variable t not found in the response", options);
                }
            }
            else {
                error("The service response is empty", options);
            }
        }


        window.ajaxTim = function () {
        };
        ajaxTim.sendMessage = function (url, data, onSuccess, options) {

            options = $.extend({
                errorMode: 'dialog',
                onErrorBefore: function (msg) {
                },
                method: 'post'
            }, options);


            if ('post' === options.method) {
                $.post(url, data, function (r) {
                    onSuccessHandler(r, onSuccess, options);
                }, 'json')
                    .fail(function () {
                        error("The ajax request failed", options);
                    });
            }
            else if ('get' === options.method) {
                $.get(url, data, function (r) {
                    onSuccessHandler(r, onSuccess, options);
                }, 'json')
                    .fail(function () {
                        error("The ajax request failed", options);
                    });
            }
            else {
                throw new Error("Unknown method: " + options.method);
            }
        };
    }
})(jQuery);