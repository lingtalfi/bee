/**
 * @dependencies:
 * - jquery
 * - ajaxtim-1.0
 *
 *
 */
(function ($) {
    if ('undefined' === typeof window.babyPushPoster) {


        function error(msg) {
            alert("babyPushPoster: " + msg);
        }

        function refresh(serviceUrl, fileId, processData, oPoster) {

            if (false === oPoster.aborted) {
                oPoster.onWatchProgressBefore();

                ajaxTim.sendMessage(serviceUrl, {
                    action: 'watchProgress',
                    fileId: fileId
                }, function (msg) {
                    if ('data' in msg && 'isEnd' in msg) {
                        processData(msg.data);
                        if (false === msg.isEnd) {
                            setTimeout(function () {
                                refresh(serviceUrl, fileId, processData, oPoster);
                            }, oPoster.refreshTime);
                        }
                        else {
                            oPoster.onEnd();
                        }
                    }
                    else {
                        oPoster.aborted = true;
                        oPoster.onEnd();
                        error("Implementation error: the BabyPushServer doesn't respond with the expected data (data and isEnd keys were expected)");
                    }
                }, {
                    onErrorBefore: function () {
                        oPoster.aborted = true;
                        oPoster.onEnd();
                    }
                });
            }
        }


        window.babyPushPoster = function () {

            this.refreshTime = 1000;
            this.aborted = false;

            /**
             * The two functions below were created to allow implementation of an ajax loader
             * which would appear at the beginning, and be removed at the very end, or when something goes wrong.
             * So that's why the onEnd function is called whenever an error occur, or when the END flag is detected.
             *
             * The onWatchProgressBefore callback is called each time before a monitoring call is made.
             */
            this.onWatchProgressBefore = function () {
            };
            this.onEnd = function () {
            };


            var zis = this;
            this.sendMessage = function (serviceUrl, taskParams, processData, options) {

                /**
                 * Design note: I set all the properties that may be updated dynamically
                 * during the refresh method in the options array below,
                 * and I passed the instance to the refresh method, so that it can access those properties
                 * in live.
                 */

                options = $.extend({
                    refreshRate: 1000,
                    onWatchProgressBefore: function () {
                    },
                    onEnd: function () {

                    }
                }, options);

                zis.refreshTime = options.refreshRate;
                zis.onWatchProgressBefore = options.onWatchProgressBefore;
                zis.onEnd = options.onEnd;


                // request a fileId to monitor the progress of the task
                ajaxTim.sendMessage(serviceUrl, {
                    action: 'getFileId',
                    taskParams: taskParams
                }, function (fileId) {
                    // ask the server to execute the task:
                    // we don't expect any return from that call,
                    // and actually the server will probably hang up in a while loop or something...
                    ajaxTim.sendMessage(serviceUrl, {
                        action: 'executeTask',
                        fileId: fileId,
                        taskParams: taskParams
                    }, function () {
                    }, {
                        onErrorBefore: function (msg) {
                            zis.aborted = true;
                            options.onEnd();
                        }
                    });
                    refresh(serviceUrl, fileId, processData, zis);
                });
            };


            this.setRefreshTime = function ($resfreshTime) {
                zis.refreshTime = $resfreshTime;
            };
            this.setOnWatchProgressBefore = function ($onWatchProgressBefore) {
                zis.onWatchProgressBefore = $onWatchProgressBefore;
            };
            this.setOnEnd = function ($onEnd) {
                zis.onEnd = $onEnd;
            };
        };


    }
})(jQuery);


