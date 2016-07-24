/**
 * SimpleArray
 * LingTalfi
 * 2015-01-16
 *
 * Depends on:
 * - jquery
 * - jqueryui
 * - pea
 *
 *
 *
 *
 * Updating an array using a gui.
 *
 * The values of the array are updated using the model object.
 * The gui controls the visual part.
 *
 *
 * Since we start with a default value, it seems more convenient to start by building
 * the visual elements from the given array, and then only separate the gui from the model for clarity of code.
 *
 *
 * Nomenclature
 * --------------
 * All elements are items.
 * There are two types of items:
 *
 * - parent item    (for instance li containing other lis)
 * - child item     (for instance li not containing other lis)
 *
 * Visually, there is also a container.
 * A parent item uses a container to hold the children items (typically ul is a container).
 * The container's direct children are items (this is important because we will use this relationship to append/prepend children
 * to a container).
 *
 * There is one root item which is the topmost item of the tree.
 * This item has level 0, his children have level 1 and so on.
 *
 * Every item has a key which is unique in the context of its direct parent.
 * The key for the root item is null.
 *
 * Every item can be targeted using a path.
 * The path is created by looking down to the target item from the root item,
 * and concatenating all the keys using a dot character in between (and escaping
 * the dot in the keys with a backslash).
 *
 * For the root item, the path is null.
 *
 * What's new ?
 *
 *
 * 2015-01-29: fixing: returned value incorrect with numeric keys (added util.objectify method)
 *
 * - 1.0
 *
 *
 *
 */

if ('undefined' === typeof window.beefSimpleArrayControl) {
    (function () {

        var uniqueCpt = 0;


        window.beefSimpleArrayControl = function (params) {
            params = $.extend({
                /**
                 * The container is a jquery object.
                 * It should be either an ul or a div.
                 * If it's an ul, it will be assigned the beefsimplearray css class (see css).
                 * If it's a div the ul.beefsimplearray will be created inside.
                 *
                 */
                container: null,
                texts: {
                    addScalar: "Add a simple value",
                    addArray: "Add an array",
                    ok: "Ok",
                    cancel: "Cancel"
                },
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
                /**
                 * This method renders a parent item.
                 * It is quite complex to create your own, because you need to understand what
                 * it has to render, and under which conditions,
                 * but you can use the model below as a good starting point.
                 *
                 * Basically, parent item must contain a container.
                 *      The getContainer method is used to access the container from the parent item.
                 *
                 * It must also contain the following class:
                 *
                 * - key: the html of this element will be the key (of the array entry) visually.
                 *          For the root item, the key will be null.
                 *
                 * It might also contain the following functional classes:
                 *
                 * - toggle: will toggle between the open/close class on the item
                 * - addscalar: will append a new child item in the container
                 * - addarray: will append a new parent item in the container
                 * - count: contains the number of children
                 * - delete: clicking on this will remove this parent item
                 * - edit: clicking on this will call the editParentItem form
                 * - duplicate: clicking on this will duplicate this parent item
                 * - grab: is the element used to grab this parent item when sortable is activated
                 *
                 */
                renderParentItem: function (key, closed, count, $params, realPath, oSimpleArray) {
                    var sClose = 'open';
                    if (true === closed) {
                        sClose = 'close';
                    }
                    var s = '<li class="' + sClose + '">';
                    s += '<a class="bsaicon toggleicon toggle" href="#"></a>';
                    if (null !== key) {
                        s += ' ';
                        s += '<span class="key">' + key + '</span>';
                        s += ': ';
                    }
                    s += '<span class="actions">';
                    s += '<a class="addscalar" href="#">' + $params.texts.addScalar + '</a>';
                    s += ' | ';
                    s += '<a class="addarray" href="#">' + $params.texts.addArray + '</a>';
                    s += ' | ';
                    s += '<span class="count">' + count + '</span>';
                    if (true === oSimpleArray.isDeletable(realPath)) {
                        s += ' | ';
                        s += ' <a class="bsaicon deleteicon delete" href="#"></a>';
                    }
                    if (null !== realPath && true === oSimpleArray.isEditable(realPath)) {
                        s += ' <a class="bsaicon editicon edit" href="#"></a>';
                    }
                    if (null !== realPath && true === oSimpleArray.isDuplicable(realPath)) {
                        s += ' <a class="bsaicon duplicateicon duplicate" href="#"></a>';
                    }
                    if (true === $params.isSortable) {
                        s += ' | ';
                        s += ' <span class="bsaicon grabicon grab" href="#"></span>';
                    }
                    s += '</span>';
                    s += '<ul></ul>';
                    s += '</li>';
                    return s;
                },
                /**
                 * An item must contain the following markup:
                 * - .key: contains the key visually
                 * - .value: contains the value visually
                 *
                 * An item might also contain one or more of the following classes:
                 *
                 * - .delete: clicking on this will remove the item
                 * - .edit: clicking on this will open the edit item form
                 * - .duplicate: clicking on this will duplicate the item
                 * - .grab: is the element used to grab the item when sortable is activated
                 *
                 */
                renderItem: function (key, value, realPath, oSimpleArray) {
                    var s = '<li>';
                    s += '<span class="key">' + key + '</span>: ';
                    s += '<span class="value">' + pea.htmlSpecialChars(value) + '</span>';
                    if (true === oSimpleArray.isDeletable(realPath)) {
                        s += ' <a class="bsaicon deleteicon delete" href="#"></a>';
                    }
                    if (true === oSimpleArray.isEditable(realPath)) {
                        s += ' <a class="bsaicon editicon edit" href="#"></a>';
                    }
                    if (true === oSimpleArray.isDuplicable(realPath)) {
                        s += ' <a class="bsaicon duplicateicon duplicate" href="#"></a>';
                    }
                    if (true === params.isSortable) {
                        s += ' <span class="bsaicon grabicon grab" href="#"></span>';
                    }
                    s += '</li>';
                    return $(s);

                },
                /**
                 * @returns a jQuery set of elements
                 */
                getContainer: function (jParentItem) {
                    return jParentItem.find('>ul');
                },
                updateKey: function (key, jItem) {
                    jItem.find('.key:first').html(key);
                },
                updateValue: function (value, jItem) {
                    jItem.find('.value:first').html(pea.htmlSpecialChars(value));
                },
                isClosed: function (realPath, key, level) {
                    return false;
                },
                /**
                 * I used this method only for debug purposes
                 */
                onStructureUpdatedAfter: function (values) {

                },
                getControl: function (oSimpleArray) {
                    return oSimpleArray.getParam('container');
                },
                /**
                 * onSuccess has to be executed when the form is successfully submitted.
                 *
                 * For a scalar entry:
                 *      onSuccess ( key, value, onError, onSuccess )
                 * For an array:
                 *      onSuccess ( key, onError, onSuccess )
                 */
                callInsertForm: function (jParentItem, isScalar, oSimpleArray, onSubmit) {

                    // first close all opened forms (both update and insert forms)
                    var jRoot = oSimpleArray.getGuiApi().getRootItemByInner(jParentItem);
                    jRoot.find('.updating, .editing').each(function () {
                        $(this).next().show();
                        $(this).remove();
                    });

                    var params = oSimpleArray.getParams();
                    oSimpleArray.getGuiApi().openNodeByItem(jParentItem);
                    var defaultKey = oSimpleArray.getUtilApi().getDefaultNumericKey(jParentItem, oSimpleArray);
                    var sNone = '';
                    if (false === isScalar) {
                        sNone = 'display: none;';
                    }
                    var s = '<li class="editing"><input style="width:60px;" type="text" name="key" value="' + defaultKey + '">: ';
                    s += '<textarea style="' + sNone + 'vertical-align: top" rows="1"></textarea>';
                    s += ' <input class="ok" type="submit" value="' + pea.htmlSpecialChars(params.texts.ok) + '">';
                    s += ' <button>' + params.texts.cancel + '</button>';
                    s += '</li>';
                    var jItem = $(s);

                    params.getContainer(jParentItem).prepend(jItem);
                    var jKey = jItem.find('input:first');
                    var jValue = jItem.find('textarea');
                    var jButton = jItem.find('button');
                    jButton.click(function () {
                        jItem.remove();
                        return false;
                    });
                    jKey.select().focus();

                    function submit() {
                        var key = jKey.val();
                        var value = null;
                        if (true === isScalar) {
                            value = jValue.val();
                            onSubmit(key, value, function (err) {
                                alert(err);
                            }, function () {
                                jItem.remove();
                            });
                        }
                        else {
                            onSubmit(key, function (err) {
                                alert(err);
                            }, function () {
                                jItem.remove();
                            });
                        }
                        return false;
                    }

                    var jOk = jItem.find('.ok');
                    jOk
                        .on('click', submit)
                        .on('keypress', submit);
                },
                /**
                 * If the entry is scalar, then the value is null (and ignored)
                 *
                 * onSuccess has to be executed when the form is successfully submitted.
                 *
                 * For an array, the value will not be used, so you should pass null
                 *      onSuccess ( key, value, onError, onSuccess )    # scalar
                 *      onSuccess ( key, null, onError, onSuccess )     # array
                 */
                callUpdateForm: function (jItem, isScalar, key, value, oSimpleArray, onSubmit) {

                    // first close all opened forms (both update and insert forms)
                    var jRoot = oSimpleArray.getGuiApi().getRootItemByInner(jItem);
                    jRoot.find('.updating, .editing').each(function () {
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
                    var jForm = $(s);
                    var jKey = jForm.find('input:first');


                    var jHide = null;
                    if (true === isScalar) {
                        jHide = jItem;
                    }
                    else {
                        jHide = jItem.find('.key:first');
                    }
                    jHide.hide();
                    jHide.before(jForm);


                    var jCancel = jForm.find('button');
                    jCancel.click(function () {
                        jHide.show();
                        jForm.remove();
                        return false;
                    });
                    function submit() {
                        var jValue = jForm.find('textarea');
                        var value = null;
                        if (true === isScalar) {
                            value = jValue.val();
                        }
                        onSubmit(jKey.val(), value, function (errMsg) {
                            alert(errMsg);
                        }, function () {
                            jForm.remove();
                            jHide.show();
                        });
                        return false;
                    }

                    jKey.select().focus();
                    var jOk = jForm.find('.ok');
                    jOk
                        .on('click', submit)
                        .on('keypress', submit);

                }
            }, params);
            var sortableClass = 'beef_array_sortable_' + uniqueCpt++;
            var values = {};
            var zis = this;

            //------------------------------------------------------------------------------/
            // MAIN API 
            //------------------------------------------------------------------------------/
            this.getParam = function (key) {
                return params[key];
            };
            this.getParams = function () {
                return params;
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
            this.getSortableClass = function () {
                return sortableClass;
            };
            this.onStructureUpdatedAfter = function () {
                if (pea.isFunction(params.onStructureUpdatedAfter)) {
                    params.onStructureUpdatedAfter(values);
                }
            };
            this.getGuiApi = function () {
                return gui;
            };
            this.getUtilApi = function () {
                return util;
            };

            //------------------------------------------------------------------------------/
            // MODEL API
            //------------------------------------------------------------------------------/
            this._setValues = function ($values) {
                values = $values;
            };
            this._setValue = function (key, value) {
                window.bdot.setDotValue(key, value, values);
            };
            this._getValue = function (key) {
                return window.bdot.getDotValue(key, values);
            };
            this._unsetValue = function (path) {
                window.bdot.unsetDotValue(path, values);
            };


            //------------------------------------------------------------------------------/
            // BEEF FORM CONTROL API
            //------------------------------------------------------------------------------/
            this.setValue = function (value) {
                values = util.objectify(value);
                build.createTreeBase(params.container, values, zis);
            };
            this.getValue = function () {
                return values;
            };
            this.getControl = function () {
                if (pea.isFunction(params.getControl)) {
                    return params.getControl(zis);
                }
                return params.container;
            };
        };


        /**
         * You should not alter the bsanode class, unless you also change the css stylesheet.
         */
        var cssItem = 'bsanode';
        var cssParentItem = 'bsaparent';
        /**
         * cssRootItem is used as a limit, to dynamically get the path when searching up to the root from a node
         */
        var cssRootItem = 'bsaroot';
        var build = {
            createTreeBase: function (jContainer, values, oSimpleArray) {
                // creates the ul li(root) skeleton
                if (false === ('jquery' in jContainer)) {
                    throw new Error("Container must be a jQuery object");
                }
                jContainer.addClass("beefsimplearray");

                // creating the root item
                var n = 0;
                if (pea.isArrayOrObject(values)) {
                    n = pea.count(values);
                }

                var jRoot = $(build.createItemForArrayEntry(null, n, null, 0, oSimpleArray));
                jRoot.addClass(cssRootItem);
                jContainer.empty().append(jRoot);


                // now add children
                if (pea.isArrayOrObject(values)) {
                    var jRootContainer = oSimpleArray.getParam('getContainer')(jRoot);
                    build.createSubTree(jRootContainer, values, null, 1, oSimpleArray);
                    gui.init(jRoot, oSimpleArray);
                }

                return jRoot;
            },
            createItemForArrayEntry: function (key, count, realPath, level, oSimpleArray) {
                var params = oSimpleArray.getParams();
                var isClosed = params.isClosed(realPath, key, level);
                var jParentItem = $(params.renderParentItem(key, isClosed, count, params, realPath, oSimpleArray));
                jParentItem.addClass(cssItem + ' ' + cssParentItem);
                var jContainer = params.getContainer(jParentItem);
                jContainer.addClass(oSimpleArray.getSortableClass());
                gui.setKey(jParentItem, key);
                return jParentItem;
            },
            createItemForScalarEntry: function (key, value, realPath, oSimpleArray) {
                var params = oSimpleArray.getParams();
                var jItem = $(params.renderItem(key, value, realPath, oSimpleArray));
                jItem.addClass(cssItem);
                gui.setKey(jItem, key);
                return jItem;
            },
            createSubTree: function (jContainer, values, path, level, oSimpleArray) {
                for (var k in values) {
                    var v = values[k];
                    var realPath = util.concatenatePath(path, k);
                    if (pea.isArrayOrObject(v)) {
                        var jParentItem = build.createItemForArrayEntry(k, pea.count(v), realPath, level, oSimpleArray);
                        jContainer.append(jParentItem);
                        var jSubContainer = oSimpleArray.getParam('getContainer')(jParentItem);
                        build.createSubTree(jSubContainer, v, realPath, level + 1, oSimpleArray);
                    }
                    else {
                        var jItem = build.createItemForScalarEntry(k, v, realPath, oSimpleArray);
                        jContainer.append(jItem);
                    }
                }
            }

        };
        var util = {
            /**
             * @return string|null, null is returned if the key is null (root item)
             */
            keyToPathElement: function (key) {
                if (null === key) {
                    return null;
                }
                return key.toString().replace('.', '\\.');
            },
            getDefaultNumericKey: function (jParentItem, oSimpleArray) {
                var key = 0;
                var jContainer = oSimpleArray.getParam('getContainer')(jParentItem);
                jContainer.find('> .' + cssItem).each(function () {
                    var testKey = parseInt(gui.getKey($(this)));
                    if (testKey >= key) {
                        key = testKey + 1;
                    }
                });
                return key;
            },
            getClosestAvailableKey: function (key, jContainer) {
                var ret = key;
                if (pea.isNumeric(ret)) {
                    jContainer.find('>.' + cssItem).each(function () {
                        var testKey = parseInt($(this).attr('data-key'));
                        if (testKey >= ret) {
                            ret = testKey + 1;
                        }
                    });
                }
                else {
                    var c = 1;
                    var testKey = key + c++;
                    while (jContainer.find('>.' + cssItem + '[data-key=' + jUtil.selectorEscape(testKey) + ']').length) {
                        testKey = key + c++;
                    }
                    ret = testKey;
                }
                return ret;
            },
            countKeyByParentItem: function (key, jParentItem, oSimpleArray) {
                var n = 0;
                var jContainer = oSimpleArray.getParam('getContainer')(jParentItem);
                jContainer.find('> .' + cssItem).each(function () {
                    var testKey = gui.getKey($(this));
                    if (testKey === key) {
                        n++;
                    }
                });
                return n;
            },
            concatenatePath: function (path, key) {
                var ret = '';
                if (null !== path) {
                    ret += path + '.';
                }
                var pe = util.keyToPathElement(key);
                if (null !== pe) {
                    ret += pe;
                }
                return ret;
            },
            objectify: function (arr) {
                arr = $.extend({}, arr);
                for (var i in arr) {
                    if (pea.isArrayOrObject(arr[i])) {
                        arr[i] = util.objectify(arr[i]);
                    }
                }
                return arr;
            }
        };


        function insertNewItemAfter(jParentItem, jNewItem, oSimpleArray) {
            var jContainer = oSimpleArray.getParam('getContainer')(jParentItem);
            jContainer.append(jNewItem);
            gui.updateCounterByChildItem(jNewItem, 1);
            gui.flash(jNewItem);
            $(document).scrollTop(jNewItem.offset().top);
        }

        var gui = {
            assignSortable: function (jEl, oSimpleArray) {

                // reset?
                jEl.sortable();
                jEl.sortable("destroy");


                jEl.sortable({
                    items: ">." + cssItem,
                    connectWith: '.' + oSimpleArray.getSortableClass(),
                    handle: ".grab",
                    revert: false,
                    start: function (e, ui) {
                        var path = gui.getPathByItem(ui.item);
                        var value = oSimpleArray._getValue(path);
                        ui.item.data('sortPath', path);
                        ui.item.data('sortValue', value);
                        ui.item.data('sortItem', gui.getParentItem(ui.item));
                    },
                    stop: function (e, ui) {
                        var oldPath = ui.item.data('sortPath');
                        var oldValue = ui.item.data('sortValue');
                        var jOldParentItem = ui.item.data('sortItem');


                        var jItem = ui.item;
                        var jContainer = ui.item.parent();
                        var key = gui.getKey(ui.item);

                        var jEl = jContainer.find('>.' + cssItem + '[data-key=' + jUtil.selectorEscape(key) + ']');
                        var isValid = (jEl.length <= 1);

                        if (true === isValid) {

                            var jParentItem = gui.getParentItem(jItem);
                            var parentPath = gui.getPathByItem(jParentItem);
                            var newPath = util.concatenatePath(parentPath, key);


                            gui.updateCounterByChildItem(ui.item, 1);
                            gui.updateCounterByItem(jOldParentItem, -1);


                            /**
                             * I had a weird problem with cycling (infinite loop) when using the oldValue directly.
                             * So the easy workaround below simply use a copy of the value.
                             */
                            var oldValCpy = $.extend(true, {}, oldValue);
                            oSimpleArray._setValue(newPath, oldValCpy);
                            oSimpleArray._unsetValue(oldPath);
                            oSimpleArray.onStructureUpdatedAfter();

                        }
                        else {
                            ui.item.effect("pulsate", {});
                            return false;
                        }

                    }
                });

            },
            flash: function (jItem) {
                jItem.effect("highlight", {}, 3000);
            },
            getItemByInner: function (jInner) {
                return jInner.closest('.' + cssItem);
            },
            getPathByItem: function (jItem) {
                if (gui.isRoot(jItem)) {
                    return null;
                }
                var key = gui.getKey(jItem);
                var keys = [];
                jItem.parentsUntil('.' + cssRootItem, '.' + cssItem).each(function () {
                    keys.push(gui.getKey($(this)));
                });
                keys = keys.reverse();
                keys.push(key);
                var components = keys.map(function (v) {
                    return util.keyToPathElement(v);
                });
                return components.join('.');
            },
            isParentItem: function (jItem) {
                return jItem.hasClass(cssParentItem);
            },
            getKey: function (jItem) {
                if (gui.isRoot(jItem)) {
                    return null;
                }
                return jItem.attr('data-key');
            },
            setKey: function (jItem, key) {
                jItem.attr('data-key', key);
            },
            closeNodeByItem: function (jItem) {
                jItem.removeClass('open').addClass('close');
            },
            openNodeByItem: function (jItem) {
                jItem.removeClass('close').addClass('open');
            },
            isRoot: function (jItem) {
                return jItem.hasClass(cssRootItem);
            },
            getRootItemByInner: function (jInner) {
                return jInner.closest('.' + cssRootItem);
            },
            getParentItem: function (jChildItem) {
                return jChildItem.parents('.' + cssItem + ':first');
            },
            getCountElement: function (jItem) {
                return jItem.find('.count:first');
            },
            getLevelByItem: function (jItem) {
                var p = jItem.parentsUntil('.' + cssRootItem, '.' + cssItem);
                return p.length;
            },
            updateCounterByItem: function (jItem, number, absolute) {
                var jCount = gui.getCountElement(jItem);
                var count = 0;
                if ('undefined' === typeof absolute) {
                    count = parseInt(jCount.html());
                    count += number;
                    if (count < 0) {
                        count = 0;
                    }
                }
                else {
                    count = absolute;
                }
                jCount.html(count);
            },
            updateCounterByChildItem: function (jChildItem, number, absolute) {
                var jItem = gui.getParentItem(jChildItem);
                return gui.updateCounterByItem(jItem, number, absolute);
            },
            init: function (jRoot, oSimpleArray) {
                var params = oSimpleArray.getParams();
                if (true === params.isSortable) {
                    var jContainer = params.getContainer(jRoot);
                    jContainer.addClass(oSimpleArray.getSortableClass());
                    gui.assignSortable($("." + oSimpleArray.getSortableClass()), oSimpleArray);
                }


                jRoot.on('click.simplearray', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass('toggle')) {
                        var jItem = gui.getItemByInner(jTarget);
                        if (jItem.hasClass('close')) {
                            gui.openNodeByItem(jItem);
                        }
                        else if (jItem.hasClass('open')) {
                            gui.closeNodeByItem(jItem);
                        }
                        return false;
                    }
                    else if (jTarget.hasClass('delete')) {
                        var jItem = gui.getItemByInner(jTarget);
                        if (true === gui.isRoot(jItem)) {
                            gui.updateCounterByChildItem(jItem, 0, true);
                            var jContainer = params.getContainer(jItem);
                            jContainer.empty();
                            oSimpleArray._setValues({});
                        }
                        else {
                            var path = gui.getPathByItem(jItem);
                            gui.updateCounterByChildItem(jItem, -1);
                            jItem.remove();
                            oSimpleArray._unsetValue(path);
                        }
                        oSimpleArray.onStructureUpdatedAfter();
                        return false;
                    }
                    else if (jTarget.hasClass('addscalar')) {
                        var jItem = gui.getItemByInner(jTarget);
                        params.callInsertForm(jItem, true, oSimpleArray, function (key, value, onError, onSuccess) {
                            var path = gui.getPathByItem(jItem);
                            path = util.concatenatePath(path, key);
                            var n = util.countKeyByParentItem(key, jItem, oSimpleArray);
                            if (0 === n) {
                                var jNewItem = build.createItemForScalarEntry(key, value, path, oSimpleArray);
                                insertNewItemAfter(jItem, jNewItem, oSimpleArray);
                                oSimpleArray._setValue(path, value);
                                oSimpleArray.onStructureUpdatedAfter();
                                onSuccess();
                            }
                            else {
                                onError("An item with key " + key + " already exists");
                            }
                        });
                        return false;
                    }
                    else if (jTarget.hasClass('addarray')) {
                        var jItem = gui.getItemByInner(jTarget);
                        params.callInsertForm(jItem, false, oSimpleArray, function (key, onError, onSuccess) {
                            var path = gui.getPathByItem(jItem);
                            path = util.concatenatePath(path, key);
                            var n = util.countKeyByParentItem(key, jItem, oSimpleArray);
                            if (0 === n) {
                                var level = gui.getLevelByItem(jItem) + 1;
                                var value = {};
                                var count = 0;
                                var jNewItem = build.createItemForArrayEntry(key, count, path, level, oSimpleArray);
                                insertNewItemAfter(jItem, jNewItem, oSimpleArray);
                                oSimpleArray._setValue(path, value);

                                if (true === params.isSortable) {
                                    gui.assignSortable(jNewItem.find('.' + oSimpleArray.getSortableClass()), oSimpleArray);
                                }

                                oSimpleArray.onStructureUpdatedAfter();
                                onSuccess();
                            }
                            else {
                                onError("An item with key " + key + " already exists");
                            }
                        });
                        return false;
                    }
                    else if (jTarget.hasClass('edit')) {
                        var jItem = gui.getItemByInner(jTarget);
                        if (false === gui.isRoot(jItem)) {
                            var key = gui.getKey(jItem);
                            var path = gui.getPathByItem(jItem);
                            var value = oSimpleArray._getValue(path);
                            var isScalar = (false === gui.isParentItem(jItem));
                            params.callUpdateForm(jItem, isScalar, key, value, oSimpleArray, function (newKey, newVal, onError, onSuccess) {
                                var jParentItem = gui.getParentItem(jItem);
                                var parentPath = gui.getPathByItem(jParentItem);
                                var newPath = util.concatenatePath(parentPath, newKey);

                                var expectedNb = 0;
                                if (newPath === path) {
                                    expectedNb = 1;
                                }
                                var n = util.countKeyByParentItem(newKey, jParentItem, oSimpleArray);
                                if (n === expectedNb) {
                                    gui.setKey(jItem, newKey);

                                    var params = oSimpleArray.getParams();
                                    params.updateKey(newKey, jItem);
                                    if (false === isScalar) {
                                        newVal = value;
                                    }
                                    else {
                                        params.updateValue(newVal, jItem);
                                    }

                                    oSimpleArray._setValue(newPath, newVal);
                                    if (newPath !== path) {
                                        oSimpleArray._unsetValue(path);
                                    }
                                    oSimpleArray.onStructureUpdatedAfter();
                                    onSuccess();
                                }
                                else {
                                    onError("An item with key " + newKey + " already exists");
                                }
                            }, params);
                        }

                        return false;
                    }
                    else if (jTarget.hasClass('duplicate')) {
                        var jItem = gui.getItemByInner(jTarget);
                        if (false === gui.isRoot(jItem)) {

                            var key = gui.getKey(jItem);
                            var path = gui.getPathByItem(jItem);
                            var value = oSimpleArray._getValue(path);

                            var jParentItem = gui.getParentItem(jItem);
                            var jContainer = params.getContainer(jParentItem);
                            var newKey = util.getClosestAvailableKey(key, jContainer);
                            var parentPath = gui.getPathByItem(jParentItem);
                            var newPath = util.concatenatePath(parentPath, newKey);
                            var isScalar = !gui.isParentItem(jItem);


                            var jCopy = jItem.clone();
                            gui.setKey(jCopy, newKey);
                            params.updateKey(newKey, jCopy);
                            if (true === isScalar) {
                                params.updateValue(value, jCopy);
                            }
                            else {
                                // copy the value to avoid problems with cycling that I had personally (not sure why)
                                value = $.extend(true, {}, value);
                            }
                            jItem.after(jCopy);

                            oSimpleArray._setValue(newPath, value);

                            gui.updateCounterByChildItem(jItem, 1);


                            if (true === params.isSortable) {
                                gui.assignSortable(jCopy.find('.' + oSimpleArray.getSortableClass()), oSimpleArray);
                            }
                            oSimpleArray.onStructureUpdatedAfter();
                        }

                        return false;
                    }
                });
            }
        };

    })();
}
