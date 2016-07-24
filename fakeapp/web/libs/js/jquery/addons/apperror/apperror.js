/**
 * Depends on:
 *
 * - jquery
 * - ?dialogg: takes advantage of it if it exists
 *
 */
(function ($) {

    if ('undefined' === typeof window.appError) {


        function error(msg, errorMode) {
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


        window.appError = function (options) {
            options = $.extend({
                prefix: "App error: "
            }, options);


            this.notice = function (msg) {
                error(msg, 'log');
            };
            this.userError = function (msg) {
                error(msg, 'dialog');
            };
            this.devError = function (msg) {
                error(msg, 'dialog');
            };
            this.error = function (msg) {
                error(msg, 'dialog');
            };
            this.exception = function (msg) {
                error(msg, 'exception');
            };

        };
    }

})(jQuery);