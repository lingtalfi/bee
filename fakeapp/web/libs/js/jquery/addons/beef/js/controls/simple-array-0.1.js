/**
 * Depends on:
 *
 * - jquery
 * - jquery ui - sortable - effect
 * - pea
 * - beef
 * - jutil
 * - bdot
 *
 *
 *
 * This is a simple array for beef.
 * Simple means that the html for the whole tree is directly accessible.
 * In other words, we cannot use this object to request some parts of the tree via ajax.
 *
 * The main benefit that we get from this simple conception is that we can simply visually update the tree,
 * and just parse the entries when done.
 * In other words, we don't need to keep the values synced while updating the tree, we just scan the tree on demand.
 *
 *
 * functional class structure
 *
 * - .node(.open|.close)
 * ----- .actions
 * --------- .delete
 * --------- .grab
 * --------- .count
 * --------- .addscalar
 * --------- .addarray
 *
 *
 * - li
 * ----- .key
 * ----- .value
 *          (
 *              since the display of a value might be slightly different from the real value (image conversion,
 *              other things...),
 *              we cannot rely exclusively on the html to get the values of the array.
 *              We rather use the real values in memory.
 *           )
 *
 *
 * Level
 * ------
 * Level 0 elements are those whose parent is the root li.
 * Special level -1 is only for root li.
 *
 *
 * One event on global container
 * -------------------------------
 * The philosophy here is to put one event on the global container.
 * You should be aware, when assign events to specific nodes of the tree, that when duplicated,
 * the events are NOT duplicated.
 *
 *
 */


if ('undefined' === typeof window.beefSimpleArrayControl) {
    var uniqueCpt = 0;
    var unguessable = '__::beeUnguessable::__';

    // constants
    var cssClass = 'beefsimplearray';
    var cssNode = 'node';
    var cssRootLi = 'root';

    window.beefSimpleArrayControl = function (params) {


        params = $.extend({
            container: null,
            isSortable: true,
            /**
             * All boolean below can be customized on a per item basis.
             * bool|bool function (realPath)
             *      Realpath is null for root li
             *
             */
            isEditable: true,
            isDuplicable: true,
            isDeletable: true,
            // 
            sortableClass: 'beef_array_sortable_' + uniqueCpt++,
            /**
             * onSuccess has to be executed when the form is successfully submitted.
             *
             * For a scalar entry:
             *      onSuccess ( key, value, onLiInsertedAfter )
             * For an array:
             *      onSuccess ( key, onLiInsertedAfter )
             */
            callInsertForm: function (jNodeLi, isScalar, oArrayControl, onSuccess, $params) {

                oArrayControl.openNode(jNodeLi);
                var defaultKey = oArrayControl.getDefaultNumericKey(jNodeLi);
                var sNone = '';
                if (false === isScalar) {
                    sNone = 'display: none;';
                }
                var s = '<li><input style="width:60px;" type="text" name="key" value="' + defaultKey + '">: ';
                s += '<textarea style="' + sNone + 'vertical-align: top" rows="1"></textarea>';
                s += ' <input class="ok" type="submit" value="Ok">';
                s += ' <button>Cancel</button>';
                s += '</li>';
                var jLi = $(s);
                jNodeLi.find(">ul:first").prepend(jLi);
                var jKey = jLi.find('input:first');
                var jValue = jLi.find('textarea');
                var jButton = jLi.find('button');
                jButton.click(function () {
                    jLi.remove();
                    return false;
                });
                jKey.select().focus();

                function submit() {
                    var key = jKey.val();
                    if (true === isScalar) {
                        var value = jValue.val();
                        onSuccess(key, value, function () {
                            jLi.remove();
                        });
                    }
                    else {
                        onSuccess(key, function () {
                            jLi.remove();
                        });
                    }
                    return false;
                }

                var jOk = jLi.find('.ok');
                jOk
                    .on('click', submit)
                    .on('keypress', submit);
            },
            /**
             * If the entry is scalar, then the value is null (and ignored)
             */
            callUpdateForm: function (jLi, isScalar, key, value, oArrayControl, onSuccess, $params) {


                // first close all opened forms (both update and insert forms)
                var jUl = oArrayControl.getContainerByInner(jLi);
                jUl.find('.updating').each(function () {
                    $(this).next().show();
                    $(this).remove();
                });

                // now create the update form
                var sNone = '';
                var sTag = 'li';
                if (false === isScalar) {
                    sNone = 'display: none;';
                    sTag = 'span';
                }

                var s = '<' + sTag + ' class="updating"><input style="width:60px;" type="text" name="key" value="' + key + '">: ';
                s += '<textarea style="' + sNone + 'vertical-align: top" rows="1">' + value + '</textarea>';
                s += ' <input class="ok" type="submit" value="Ok">';
                s += ' <button>Cancel</button>';
                s += '</' + sTag + '>';
                var jFormLi = $(s);
                var jKey = jFormLi.find('input:first');


                var jHide = null;
                if (true === isScalar) {
                    jHide = jLi;
                }
                else {
                    jHide = jLi.find('.key:first');
                }
                jHide.hide();
                jHide.before(jFormLi);


                var jCancel = jFormLi.find('button');
                jCancel.click(function () {
                    jHide.show();
                    jFormLi.remove();
                    return false;
                });
                function submit() {
                    jFormLi.find('.error').remove();
                    var jValue = jFormLi.find('textarea');
                    onSuccess(jKey.val(), jValue.val(), function () {
                        jFormLi.remove();
                        jHide.show();
                    }, function (errMsg) {
                        jFormLi.prepend('<div class="error">' + errMsg + '</div>');
                    });
                    return false;
                }

                jKey.select().focus();
                var jOk = jFormLi.find('.ok');
                jOk
                    .on('click', submit)
                    .on('keypress', submit);

            },
            /**
             * If realPath is null, the node is rootLi
             */
            arrayEntryFmt: function (k, closed, count, $params, realPath, oArrayControl) {
                var sClose = 'open';
                if (true === closed) {
                    sClose = 'close';
                }
                var s = '<li class="' + cssNode + ' ' + sClose + '">';
                s += '<a class="bsaicon toggleicon" href="#"></a>';
                if (null !== k) {
                    s += ' ';
                    s += '<span class="key">' + k + '</span>';
                    s += ': ';
                }
                s += '<span class="actions">';
                s += '<a class="addscalar" href="#">' + $params.texts.addScalar + '</a>';
                s += ' | ';
                s += '<a class="addarray" href="#">' + $params.texts.addArray + '</a>';
                s += ' | ';
                s += '<span class="count">' + count + '</span>';
                if (true === oArrayControl.isDeletable(realPath)) {
                    s += ' | ';
                    s += ' <a class="bsaicon deleteicon delete" href="#"></a>';
                }
                if (null !== realPath && true === zis.isEditable(realPath)) {
                    s += ' <a class="bsaicon editicon edit" href="#"></a>';
                }
                if (null !== realPath && true === zis.isDuplicable(realPath)) {
                    s += ' <a class="bsaicon duplicateicon duplicate" href="#"></a>';
                }
                if (true === $params.isSortable) {
                    s += ' | ';
                    s += ' <span class="bsaicon grabicon grab" href="#"></span>';
                }
                s += '</span>';
                s += '</li>';
                return s;
            },
            scalarEntryFmt: '<span class="key">$k</span>: <span class="value">$v</span>',
            isClosed: function (realPath, key, level) {
                return false;
            },
            texts: {
                addScalar: "Add a simple value",
                addArray: "Add an array"
            },
            values: {}
        }, params);

        if (null === params.container) {
            throw new Error("container property cannot be null");
        }

        var jContainer = params.container;
        var fmt = params.scalarEntryFmt;
        var zis = this;
        var values = params.values;


        function getLiForScalarEntry(k, v, realPath) {
            var s = fmt.replace('$k', k).replace('$v', pea.htmlSpecialChars(v));
            var a = '';
            if (true === zis.isDeletable(realPath)) {
                a += ' <a class="bsaicon deleteicon delete" href="#"></a>';
            }
            if (true === zis.isEditable(realPath)) {
                a += ' <a class="bsaicon editicon edit" href="#"></a>';
            }
            if (true === zis.isDuplicable(realPath)) {
                a += ' <a class="bsaicon duplicateicon duplicate" href="#"></a>';
            }
            if (true === params.isSortable) {
                a += ' <span class="bsaicon grabicon grab" href="#"></span>';
            }
            var jLi = $('<li>' + s + a + '</li>');
            jLi.attr('data-path', realPath);
            jLi.attr('data-key', k);
            return jLi;
        }

        function getLiForArrayEntry(k, v, realPath, level) {
            var isClosed = params.isClosed(realPath, k, level);
            var n = pea.count(v);
            var jLi = $(params.arrayEntryFmt(k, isClosed, n, params, realPath, zis));
            jLi.append('<ul class="' + params.sortableClass + '"></ul>');
            jLi.attr('data-path', realPath);
            jLi.attr('data-key', k);
            return jLi;
        }

        function addTree(jUl, values, path, level) {
            for (var k in values) {
                var v = values[k];
                var realPath = keyToPath(k);
                if (null !== path) {
                    realPath = path + '.' + realPath;
                }

                if (pea.isArrayOrObject(v)) {
                    var jLi = getLiForArrayEntry(k, v, realPath, level);
                    jUl.append(jLi);
                    var jChildUl = jLi.find('>ul');
                    addTree(jChildUl, v, realPath, level + 1);
                }
                else {
                    var jLi = getLiForScalarEntry(k, v, realPath);
                    jUl.append(jLi);
                }
            }
        }

        function getRootLi() {
            return jContainer.find('>li.' + cssRootLi);
        }


        function onStructureUpdatedAfter() {
            //$('#zelog').html(JSON.stringify(values));
            //$('#zelog').append(window.array2UlTool.render(values));
        }

        //------------------------------------------------------------------------------/
        // API
        //------------------------------------------------------------------------------/
        this.recreate = function (values) {
            var n = pea.count(values);
            /**
             * Creating a first li to hold the buttons
             */
            jContainer.empty();
            var key = null;
            var isClosed = params.isClosed(null, key, 0);
            var jRootLi = $(params.arrayEntryFmt(key, isClosed, n, params, null, zis));
            jRootLi.addClass(cssRootLi);
            jContainer.append(jRootLi);
            var jChildUl = $('<ul class="' + params.sortableClass + '"></ul>');
            jRootLi.append(jChildUl);

            if (pea.isArrayOrObject(values)) {
                addTree(jChildUl, values, null, 0);
            }


        };

        /**
         * get node or itself (with path=null)
         */
        this.getNode = function (path) {
            if (null === path) {
                return getRootLi();
            }
            var s = 'li[data-path=' + jUtil.selectorEscape(path) + ']';
            var jLi = jContainer.find(s);
            if (jLi.length) {
                return jLi;
            }
            return false;
        };

        this.closeNode = function (path) {
            var jLi = this.getNode(path);
            if (false !== jLi && jLi.hasClass('node')) {
                jLi.removeClass('open').addClass('close');
            }
        };

        this.openNode = function (pathOrNode) {
            var jLi;
            if ('string' === typeof pathOrNode) {
                jLi = this.getNode(pathOrNode);
            }
            else {
                jLi = pathOrNode;
            }
            if (null === jLi) {
                jLi = getRootLi();
            }
            if (false !== jLi && jLi.hasClass('node')) {
                jLi.removeClass('close').addClass('open');
            }
        };

        /**
         * string|null, null for the root element, and a string (bdot) for any other node.
         */
        this.getPathByElement = function (jLi) {
            var ret = jLi.attr('data-path');
            if ('undefined' === typeof ret) {
                ret = null;
            }
            return ret;
        };

        this.getKeyByElement = function (jLi) {
            var ret = jLi.attr('data-key');
            if ('undefined' === typeof ret) {
                ret = null;
            }
            return ret;
        };

        this.getValueByElement = function (jLi) {
            var path = zis.getPathByElement(jLi);
            return window.bdot.getDotValue(path, values, null);
        };

        this.getDefaultNumericKey = function (jNodeLi) {
            var key = 0;
            jNodeLi.find('>ul >li').each(function () {
                var testKey = parseInt($(this).attr('data-key'));
                if (testKey >= key) {
                    key = testKey + 1;
                }
            });
            return key;
        };


        this.getContainerByInner = function (jInner) {
            return jInner.closest('.' + cssClass);
        };


        this.flash = function (jLi, error) {
            jLi.effect("highlight", {}, 3000);
        };


        this.isEditable = function (realPath) {
            if (pea.isFunction(params.isEditable)) {
                return params.isEditable(realPath);
            }
            return !!params.isEditable;
        };

        this.isDuplicable = function (realPath) {
            if (pea.isFunction(params.isDuplicable)) {
                return params.isDuplicable(realPath);
            }
            return !!params.isDuplicable;
        };

        this.isDeletable = function (realPath) {
            if (pea.isFunction(params.isDeletable)) {
                return params.isDeletable(realPath);
            }
            return !!params.isDeletable;
        };


        //------------------------------------------------------------------------------/
        // DATA MODEL
        //------------------------------------------------------------------------------/
        this.setData = function (key, value) {
            window.bdot.setDotValue(key, value, values);
        };

        //------------------------------------------------------------------------------/
        // BEEF CONTROL "INTERFACE"
        //------------------------------------------------------------------------------/
        this.getValue = function () {
            return values;
        };


        //------------------------------------------------------------------------------/
        // PRIVATE
        //------------------------------------------------------------------------------/
        function updateCounterByInner(jInner, number) {
            var jLi = jInner.parents('li.node:first');
            var jCount = jLi.find('.count:first');
            var count = parseInt(jCount.html());
            count += number;
            if (count < 0) {
                count = 0;
            }
            jCount.html(count);
        }


        function getLevelByParentLi(jNodeLi) {
            var p = jNodeLi.parentsUntil('.' + cssClass, '.' + cssNode);
            return p.length - 1;
        }

        function isKeyValid(oldKey, newKey, jUl) {
            if (newKey === oldKey) {
                return true;
            }
            var jEl = jUl.find('>li[data-key=' + jUtil.selectorEscape(newKey) + ']');
            return (0 === jEl.length);
        }


        function updateElementKeyAndValue(jLi, key, value) {
            var path = zis.getPathByElement(jLi);
            var parentPath = window.bdot.getParentKeyPath(path);
            var realPath = concatenatePath(parentPath, key);

            // first update the values if necessary
            window.bdot.setDotValue(realPath, value, values);
            if (path !== realPath) {
                window.bdot.unsetDotValue(path, values);
            }

            // then update the dom
            jLi.attr('data-path', realPath);
            jLi.attr('data-key', key);

            if (jLi.hasClass('node')) {
                jLi.find('.key:first').html(key);
            }
            else {
                jLi.find('.key:first').html(key);
                jLi.find('.value:first').html(pea.htmlSpecialChars(value));
            }

        }

        function _insertNewLiAfter(jLi, jNewLi, onLiInsertedAfter) {
            jLi.find('>ul').append(jNewLi);
            updateCounterByInner(jNewLi, 1);
            zis.flash(jNewLi);
            $(document).scrollTop(jNewLi.offset().top);
            if (pea.isFunction(onLiInsertedAfter)) {
                onLiInsertedAfter();
            }
        }

        function getItemContainer(jParentItem) {
            var jUl = jParentItem.find('>ul');
            if (jUl.length) {
                return jUl;
            }
            throw new Error("Invalid markup: a parent item must have an item container");
        }

        function getClosestAvailableKey(key, jUl) {
            var ret = key;
            if (pea.isNumeric(ret)) {
                jUl.find('>li').each(function () {
                    var testKey = parseInt($(this).attr('data-key'));
                    if (testKey >= ret) {
                        ret = testKey + 1;
                    }
                });
            }
            else {
                var c = 1;
                var testKey = key + c++;
                while (jUl.find('>li[data-key=' + jUtil.selectorEscape(testKey) + ']').length) {
                    testKey = key + c++;
                }
                ret = testKey;
            }
            return ret;
        }

        function isParentItem(jItem) {
            return jItem.hasClass('node');
        }

        function keyToPath(key) {
            return key.toString().replace('.', '\\.');
        }


        /**
         * basePath might be null if it is the root element.
         * key should be an int or a string.
         */
        function concatenatePath(basePath, key) {
            var path = basePath;
            if (null === basePath) {
                path = keyToPath(key);
            }
            else {
                path = basePath + '.' + keyToPath(key);
            }
            return path;
        }

        /**
         * Assuming that all keys are correct, reassign the paths.
         * This happens when you duplicate a part of the tree that contains other nodes.
         */
        function resetPaths(jParentItem) {
            var basePath = zis.getPathByElement(jParentItem);
            jParentItem.find('>ul >li').each(function () {
                var key = zis.getKeyByElement($(this));
                var path = concatenatePath(basePath, key);
                $(this).attr('data-path', path);
                if (isParentItem($(this))) {
                    resetPaths($(this));
                }
            });
        }


        function getParentItem(jItem) {
            return jItem.parents('li:first');
        }


        var sortedElParent = null;
        var sortedOldPath = null;
        var sortedOldValue = null;

        function assignSortable(jEl) {

            // reset?
            jEl.sortable();
            jEl.sortable("destroy");


            jEl.sortable({
                items: "> li",
                connectWith: '.' + params.sortableClass,
                handle: ".grab",
                revert: false,
                start: function (e, ui) {
                    sortedElParent = ui.item.parent();
                    sortedOldPath = zis.getPathByElement(ui.item);
                    sortedOldValue = zis.getValueByElement(ui.item);
                },
                stop: function (e, ui) {
                    var jUl = ui.item.parent();
                    if (sortedElParent[0] !== jUl[0]) {
                        var key = zis.getKeyByElement(ui.item);

                        var jEl = jUl.find('>li[data-key=' + jUtil.selectorEscape(key) + ']');
                        var isValid = (jEl.length <= 1);

                        if (true === isValid) {
                            var jParent = getParentItem(jUl);
                            var parentPath = zis.getPathByElement(jParent);
                            var newPath = concatenatePath(parentPath, key);
                            console.log(sortedOldValue, sortedOldPath);
                            ui.item.attr('data-path', newPath);
                            updateCounterByInner(ui.item, 1);
                            updateCounterByInner(sortedElParent, -1);
                            if (isParentItem(ui.item)) {
                                resetPaths(ui.item);
                            }

                            window.bdot.setDotValue(newPath, sortedOldValue, values);
                            window.bdot.unsetDotValue(sortedOldPath, values);

                            onStructureUpdatedAfter();

                        }
                        else {
                            ui.item.effect("pulsate", {});
                            return false;
                        }
                    }
                }
            });

        }

        //------------------------------------------------------------------------------/
        // INIT
        //------------------------------------------------------------------------------/
        jContainer.addClass(cssClass);
        this.recreate(params.values);
        if (true === params.isSortable) {
            jContainer.addClass(params.sortableClass);
            assignSortable($("." + params.sortableClass));
        }
        jContainer.on('click.simplearray', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass('toggleicon')) {
                var jLi = jTarget.closest('li.node');
                if (jLi.hasClass('close')) {
                    var path = zis.getPathByElement(jLi);
                    zis.openNode(path);
                }
                else if (jLi.hasClass('open')) {
                    var path = zis.getPathByElement(jLi);
                    zis.closeNode(path);
                }
                return false;
            }
            else if (jTarget.hasClass('delete')) {
                var jLi = jTarget.closest('li');
                if (jLi.hasClass('root')) {
                    updateCounterByInner(jLi, 0, true);
                    jLi.find('.count').html(0);
                    jLi.find('ul').empty();
                    values = {};
                }
                else {
                    updateCounterByInner(jLi, -1);
                    jLi.remove();
                    var path = zis.getPathByElement(jLi);
                    window.bdot.unsetDotValue(path, values);
                }
                onStructureUpdatedAfter();
                return false;
            }
            else if (jTarget.hasClass('addscalar')) {
                var jLi = jTarget.closest('li.node');
                params.callInsertForm(jLi, true, zis, function (key, value, onLiInsertedAfter) {
                    var realPath = zis.getPathByElement(jLi);
                    if (null === realPath) { // root node
                        realPath = key;
                    }
                    else {
                        realPath += '.' + key;
                    }

                    var jNewLi = getLiForScalarEntry(key, value, realPath);
                    _insertNewLiAfter(jLi, jNewLi, onLiInsertedAfter);
                    zis.setData(realPath, value, values);
                    onStructureUpdatedAfter();

                }, params);
                return false;
            }
            else if (jTarget.hasClass('addarray')) {
                var jLi = jTarget.closest('li.node');
                params.callInsertForm(jLi, false, zis, function (key, onLiInsertedAfter) {
                    var realPath = zis.getPathByElement(jLi);
                    if (null === realPath) { // root node
                        realPath = key;
                    }
                    else {
                        realPath += '.' + key;
                    }
                    var level = getLevelByParentLi(jLi) + 1;
                    var jNewLi = getLiForArrayEntry(key, {}, realPath, level);
                    _insertNewLiAfter(jLi, jNewLi, onLiInsertedAfter);
                    zis.setData(realPath, {}, values);
                    if (true === params.isSortable) {
                        assignSortable(jLi.find('.' + params.sortableClass));
                    }
                    onStructureUpdatedAfter();
                }, params);
                return false;
            }
            else if (jTarget.hasClass('edit')) {
                var jLi = jTarget.closest('li');
                if (false === jLi.hasClass(cssRootLi)) {
                    var key = zis.getKeyByElement(jLi);
                    var value = null;
                    var isScalar = (false === jLi.hasClass('node'));
                    value = zis.getValueByElement(jLi);
                    params.callUpdateForm(jLi, isScalar, key, value, zis, function (newKey, newVal, onSuccess, onError) {

                        var jUl = jLi.closest('ul');
                        if (isKeyValid(key, newKey, jUl)) {
                            if (false === isScalar) {
                                newVal = zis.getValueByElement(jLi);
                            }
                            updateElementKeyAndValue(jLi, newKey, newVal, isScalar);
                            onStructureUpdatedAfter();
                            onSuccess();
                        }
                        else {
                            onError("Key already exists: " + newKey);
                        }
                    }, params);
                }

                return false;
            }
            else if (jTarget.hasClass('duplicate')) {
                var jLi = jTarget.closest('li');
                if (false === jLi.hasClass(cssRootLi)) {

                    var path = zis.getPathByElement(jLi);
                    var key = zis.getKeyByElement(jLi);
                    var value = zis.getValueByElement(jLi);


                    var isParent = isParentItem(jLi);
                    var jUl = jLi.closest('ul');


                    var newKey = getClosestAvailableKey(key, jUl);
                    var newPath = window.bdot.getParentKeyPath(path);
                    if (null === newPath) {
                        newPath = keyToPath(newKey);
                    }
                    else {
                        newPath = newPath + '.' + keyToPath(newKey.toString());
                    }

                    var jCopy = jLi.clone();


                    jCopy.attr('data-key', newKey);
                    jCopy.attr('data-path', newPath);
                    jCopy.find('.key:first').html(newKey);
                    if (false === isParent) {
                        jCopy.find('.value:first').html(pea.htmlSpecialChars(value));
                    }
                    jLi.after(jCopy);
                    window.bdot.setDotValue(newPath, value, values);

                    // now update paths for all children
                    if (true === isParent) {
                        resetPaths(jCopy);
                    }
                    updateCounterByInner(jLi, 1);
                    if (true === params.isSortable) {
                        assignSortable(jCopy.find('.' + params.sortableClass));
                    }
                    onStructureUpdatedAfter();
                }

                return false;
            }
        });


    };


}
