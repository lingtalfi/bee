/**
 * jquery util.
 * 2014-02-05 -> 2014-10-23
 *
 * Some utils that I use while developing with jquery.
 *
 * Depends on: jquery
 *
 */

if ('undefined' === typeof window.jUtil) {
    window.jUtil = {
        // @link http://api.jquery.com/category/selectors/
        selectorEscape: function (sExpression) {
            return sExpression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        },
        uniqueId: function (jItem) {


            var uid = jItem.attr("id");
            if (uid) {
                return uid;
            }

            function getUniqueCssId() {
                return 'uid-' + Bee.rand(1, 1000000000);
            }

            uid = getUniqueCssId();
            while ($('#' + uid).length) {
                uid = getUniqueCssId();
            }
            jItem.attr("id", uid);
            return uid;
        }
    };
}
