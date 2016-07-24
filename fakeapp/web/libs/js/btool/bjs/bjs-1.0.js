/**
 * LingTalfi - 2015-01-22
 *
 *
 */
(function () {
    if ('undefined' === typeof window.bJsTool) {

        window.bJsTool = {
            getNextNaturalKey: function ($array) {
                var ret = 0;
                for (var i in $array) {
                    if (parseInt(i) >= ret) {
                        ret = parseInt(i) + 1;
                    }
                }
                return ret.toString();
            }
        };


    }
})();
 