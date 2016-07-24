(function ($) {


    function error(msg, errMode) {
        msg = "easyDownloadCounter error: " + msg;
        if ('alert' === errMode) {
            alert(msg);
        }
        else {
            console.log(msg);
        }
    }


    $.fn.easyDownloadCounter = function () {
        var args = Array.prototype.slice.apply(arguments);

        var errorMode = $.fn.easyDownloadCounter.settings.errorMode;
        var options = args[0];
        var o = $.extend({}, $.fn.easyDownloadCounter.defaults, options);

        if (null === o.serviceUrl) {
            error("Please defined the serviceUrl option first", errorMode);
            return;
        }


        return this.each(function () {
            /**
             * I ran into this issue that when firing click without preventing the default behaviour,
             * the service would fail returning a response,
             * so I use two events.
             */

            $(this).on('mousedown', function (e) {
                var id = $(this).attr("data-edc-id");
                if (undefined === id) {
                    id = o.identifier;
                }
                if (null !== id) {
                    $.post($.fn.easyDownloadCounter.settings.serviceUrl, {
                        edcId: id
                    }, function (m) {
                        if ("ok" === m) {
                            // ok
                        }
                        else {
                            error(m);
                        }

                    });
                }
                else {
                    error("no identifier found for the clicked element", errorMode);
                }

                // now update a counter with same id if any
                var jCounter = $("[data-edc-counter=" + id + "]");
                if (jCounter.length) {
                    var n = jCounter.html();
                    n = parseInt(n);
                    n++;
                    jCounter.html(n);
                }


            });
        });

    };


    $.fn.easyDownloadCounter.defaults = {};

    $.fn.easyDownloadCounter.settings = {
        serviceUrl: '/service/edc-service.php',
        errorMode: 'alert' // alert|silent
    };


    // feed all counter boxes available on the page
    // we do it all at once to save some http requests
    $(document).ready(function () {
        var url = $.fn.easyDownloadCounter.settings.serviceUrl;
        var id2JElement = {};
        var ids = [];
        $("[data-edc-counter]").each(function () {
            var id = $(this).attr("data-edc-counter");
            id2JElement[id] = $(this);
            ids.push(id);
        });


        $.post(url, {
            edcCptIds: ids
        }, function (id2Total) {
            for (var i in id2Total) {
                var n = id2Total[i];
                if (null === n) {
                    n = 0; // that's an error, shouldn't happen
                }
                id2JElement[i].html(n);
            }

        }, 'json');


    });



}(jQuery));