/**
 * jquery util.
 * 2014-02-05 -> 2014-03-02
 *
 * Some utils that I use while developing with jquery.
 *
 * @depends pea, jquery, bee
 *
 */

if ('undefined' === typeof window.jUtil) {
    window.jUtil = {
        cloneObject: function (object, mode) {
            /**
             * http://stackoverflow.com/questions/122102/most-efficient-way-to-clone-an-object
             * I want to note that the .clone() method in jQuery only clones DOM elements.
             * In order to clone JavaScript objects, you would do:
             */
            if ('shallow' === mode) {
                // Shallow copy
                return jQuery.extend({}, object);
            }
            // Deep copy by default
            return jQuery.extend(true, {}, object);
        },
        getBodyEndDiv: function (cssClass, visible) {
            var jDiv = $("." + cssClass + ':first');
            if (!jDiv.length) {
                visible = pea.boolVal(visible);
                var sDisplay = (true === visible) ? 'block' : 'none';
                var sAttr = '';
                if (pea.isSet(cssClass)) {
                    sAttr += ' class="' + cssClass + '"';
                }
                jDiv = $('<div' + sAttr + ' style="display:' + sDisplay + '"></div>');
                $('body').append(jDiv);
            }
            return jDiv;
        },
        getElementUniqueId: function (jItem) {
            var uid = jItem.attr("id");
            if (uid) {
                return uid;
            }
            var uid = bee.getUniqueCssId();
            jItem.attr("id", uid);
            return uid;
        },
        getTemplateCopy: function (jOriginalTpl, tags) {

            var jCopy = jOriginalTpl.clone();
            var html = $('<div>').append(jOriginalTpl.clone()).html();
            if (pea.isArrayObject(tags)) {
                for (var i in tags) {
                    tags['[' + i + ']'] = tags[i];
                    bee.unsetKeys(i, tags);
                }
                html = pea.strReplace(pea.arrayKeys(tags), pea.arrayValues(tags), html);
                jCopy = $(html);
            }
            return jCopy;
        },
        listenToElementsWithClass: function (e, classes, callback) {
            var jTarget = $(e.target);
            var jRealTarget = null;
            var matchedClass = null;
            for (var i in classes) {
                var curClass = classes[i];
                var jTar = jTarget.closest("." + curClass);
                if (jTar.length) {
                    jRealTarget = jTar;
                    matchedClass = curClass;
                    break;
                }
            }
            if (null !== jRealTarget) {
                return callback(matchedClass, jRealTarget);
            }
        },

        // @link http://api.jquery.com/category/selectors/
        selectorEscape: function (sExpression) {
            return sExpression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        },
        uniqueId: function (jItem, prefix) {
            var uid = jItem.attr("id");
            if (uid) {
                return uid;
            }
            if ('undefined' === typeof prefix) {
                prefix = 'uid-';
            }
            var uid = prefix + pea.rand(1, 1000000000);
            jItem.attr("id", uid);
            return uid;
        }
    };
}
