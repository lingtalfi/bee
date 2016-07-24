(function ($) {

    var listSelector = '.listt-list';
    var listSelectedClass = 'selected';
    var listDisabledClass = 'disabled';
    var listHoverClass = 'hover';


    function error(msg) {
        throw new Error("listt: " + msg);
    }

    function getContainer(jList, groupName) {
        if (groupName) {
            var jRet = jList.find('.listt-group[data-groupname=' + groupName + ']');
            if (jRet.length) {
                return jRet;
            }
        }
        return jList;
    }

    function disableItem(jList, k) {
        getItemByKey(jList, k).addClass(listDisabledClass);
    }

    function enableItem(jList, k) {
        getItemByKey(jList, k).removeClass(listDisabledClass);
    }


    function deselectItem(jList, k) {
        getItemByKey(jList, k).removeClass(listSelectedClass);
    }

    function selectItem(jList, k) {
        getItemByKey(jList, k).addClass(listSelectedClass);
    }

    function selectItemExclusive(jList, k) {
        deselectAll(jList);
        getItemByKey(jList, k).addClass(listSelectedClass);
    }


    function getFirstHoveredItem(jList) {
        var jItem = jList.find(".listt-item.hover:first");
        if (jItem.length) {
            return jItem;
        }
        return null;
    }

    function getFirstSelectedItem(jList) {
        var jItem = jList.find(".listt-item.selected:first");
        if (jItem.length) {
            return jItem;
        }
        return null;
    }


    function disableGroup(jList, groupName) {
        eachItem(getContainer(jList, groupName), function (jItem) {
            jItem.addClass(listDisabledClass);
        });
    }

    function enableGroup(jList, groupName) {
        eachItem(getContainer(jList, groupName), function (jItem) {
            jItem.removeClass(listDisabledClass);
        });
    }


    function disableItems(jList, arrayOfKeys) {
        eachItem(jList, function (jItem) {
            var key = jItem.attr("data-key");
            if (pea.inArray(key, arrayOfKeys)) {
                jItem.addClass(listDisabledClass);
            }
        });
    }

    function enableAll(jList) {
        eachItem(jList, function (jItem) {
            jItem.removeClass(listDisabledClass);
        });
    }

    function disableAll(jList) {
        eachItem(jList, function (jItem) {
            jItem.addClass(listDisabledClass);
        });
    }

    function unhoverAll(jList) {
        eachItem(jList, function (jItem) {
            jItem.removeClass(listHoverClass);
        });
    }

    function deselectAll(jList) {
        eachItem(jList, function (jItem) {
            jItem.removeClass(listSelectedClass);
        });
    }

    function selectAll(jList) {
        eachItem(jList, function (jItem) {
            jItem.addClass(listSelectedClass);
        });
    }

    function selectItems(jList, arrayOfKeys) {
        eachItem(jList, function (jItem) {
            var key = jItem.attr("data-key");
            if (pea.inArray(key, arrayOfKeys)) {
                jItem.addClass(listSelectedClass);
            }
        });
    }

    function getItemByKey(jList, k) {
        return jList.find(".listt-item[data-key=" + k + "]");
    }

    /**
     * Returns the level of an imaginary child in the given container.
     * Note: we can also pass a jItem directly.
     */
    function getContainerLevel(jContainer, jList) {
        var level = 0;
        jContainer.parentsUntil(jList, ".listt-group").addBack(".listt-group").each(function () {
            level++;
        });
        return level;
    }


    function eachItem(jList, callback) {
        jList.find(".listt-item").each(function () {
            callback($(this));
        });
    }

    function getArgValue(arg, defaultVal) {
        if ('undefined' === typeof arg) {
            arg = defaultVal;
        }
        return arg;
    }

    function removeItem(jList, k) {
        jList.find('.listt-item[data-key=' + k + ']').remove();
    }

    function buildItem(o, k, v, level) {
        var jItem = $(o.getItemHtml(v, level));
        jItem.attr('data-key', k);
        jItem.attr('data-value', v);
        return jItem;
    }

    function addItem(jList, o, mode, key, value, a3, a4) {
        switch (mode) {
            case 'append':
            case 'prepend':

                var replace = getArgValue(a3, true);
                var group = getArgValue(a4, null);
                if (true === replace) {
                    removeItem(jList, key);
                }
                var jContainer = getContainer(jList, group);
                var level = getContainerLevel(jContainer, jList);
                var jItem = buildItem(o, key, value, level);


                o.addItemToContainer(jItem, jContainer, ('append' === mode));
                break;
            case 'insertItemBefore':
            case 'insertItemAfter':

                var keyTarget = a3;
                var replace = getArgValue(a4, true);
                if (true === replace) {
                    removeItem(jList, key);
                }
                var jPivot = getItemByKey(jList, keyTarget);
                var level = getContainerLevel(jPivot, jList);
                var jItem = buildItem(o, key, value, level);
                if ('insertItemBefore' === mode) {
                    jPivot.before(jItem);
                }
                else {
                    jPivot.after(jItem);
                }

                break;
            default:
                error("Unknown mode " + mode);
                break;
        }
    }

    function getSelection(jList, o) {
        if (false === o.multiple) {
            var ret = null;
            var jItem = jList.find(".listt-item.selected:first");
            if (jItem.length) {
                ret = jItem.attr("data-key");
            }
            return ret;
        }
        else {
            var ret = [];
            jList.find(".listt-item.selected").each(function () {
                ret.push($(this).attr("data-key"));
            });
            return ret;
        }
    }


    function selectAllFromAToB(jList, jItemA, jItemB) {
        var started = false;
        var exit = false;

        deselectAll(jList);
        jList.find(".listt-item").each(function () {
            if (true === exit) {
                return;
            }
            if (false === started) {
                if (true === $(this).is(jItemA)) {
                    started = true;
                    $(this).addClass(listSelectedClass);
                    if (true === $(this).is(jItemB)) {
                        exit = true;
                    }
                }
            }
            else {
                $(this).addClass(listSelectedClass);
                if (true === $(this).is(jItemB)) {
                    exit = true;
                }
            }
        });
    }


    function getOrderedElements(jList, jItemA, jItemB) {
        var first = jItemA;
        var last = jItemB;
        var exit = false;
        jList.find(".listt-item").each(function () {
            if (true === exit) {
                return;
            }
            if (true === $(this).is(jItemA)) {
                exit = true;
            }
            else if (true === $(this).is(jItemB)) {
                first = jItemB;
                last = jItemA;
                exit = true;
            }
        });
        return [first, last];
    }

    function nativeItemSelection(jList, jItem, o, shift, cmd) {
        if (jItem && false === jItem.hasClass(listDisabledClass)) {
            if (false === o.multiple) {
                eachItem(jList, function (jIt) {
                    jIt.removeClass(listSelectedClass);
                });
                jItem.addClass(listSelectedClass);
            }
            else {
                if (true === shift && null !== o._last) {
                    var els = getOrderedElements(jList, o._last, jItem);
                    selectAllFromAToB(jList, els[0], els[1]);
                }
                else if (true === cmd) {
                    if (true === jItem.hasClass(listSelectedClass)) {
                        jItem.removeClass(listSelectedClass);
                    }
                    else {
                        jItem.addClass(listSelectedClass);
                    }
                }
                else {
                    deselectAll(jList);
                    jItem.addClass(listSelectedClass);
                }
            }
        }
    }

    function pressUpOrDown(jList, o, next, shift, cmd) {

        if (true === shift && true === o.multiple && null !== o._last) {
            var jItem;
            if (null !== o._arrow) {
                jItem = o._arrow;
            }
            else {
                if (true === next) {
                    jItem = jList.find('.listt-item.selected:last');
                }
                else {
                    jItem = jList.find('.listt-item.selected:first');
                }
            }
            jTarget = o.getSibling(jList, jItem, next, o);
            nativeItemSelection(jList, jTarget, o, shift, cmd);
        }
        else {
            var jItem;
            if (false === cmd) {
                jItem = getFirstSelectedItem(jList);
            }
            else {
                jItem = getFirstHoveredItem(jList);
            }
            var jTarget = null;
            if (jItem) {
                if (false === cmd) {

                    unhoverAll(jList);
//                if (false === shift) {
//                    jItem.removeClass(listSelectedClass);
//                }
                }
                else {
                    jItem.removeClass(listHoverClass);
                }
                jTarget = o.getSibling(jList, jItem, next, o);
                if (jTarget && false === cmd) {
                    nativeItemSelection(jList, jTarget, o, shift, cmd);
                }
            }
            else {
                if (false === o.keyboardMoveCycle) {
                    if (false === o._bottom) {
                        jTarget = jList.find('.listt-item:first');
                    }
                    else {
                        jTarget = jList.find('.listt-item:last');
                        o._bottom = false;
                    }
                }
                else {
                    if (true === next) {
                        jTarget = jList.find('.listt-item:first');
                    }
                    else {
                        jTarget = jList.find('.listt-item:last');
                    }
                }
            }
            if (null !== jTarget && jTarget.length) {
                if (false === cmd) {
                    jTarget.addClass(listSelectedClass);
                }
                else {
                    jTarget.addClass(listHoverClass);
                }
            }
            else {
                unhoverAll(jList);
            }

        }
        return jTarget;
    }

    function buildList(jList, values, o, events, level) {
        jList.empty();

        doBuildList(jList, values, o, level);


        // disable items
        disableItems(jList, o.disabledItems);
        // select items
        selectItems(jList, o.selectedItems);


        // now let's add events handlers
        events.mouseSelect.add(function (e) {
            var jTarget = $(e.target);
            var jItem = jTarget.closest(".listt-item");
            if (jItem.length) {

                var shift = e.shiftKey;
                var cmd = e.ctrlKey || e.metaKey; // I guess ctrlKey is for windows pc, not tested.

                nativeItemSelection(jList, jItem, o, shift, cmd);
                if (false === shift) { // define shift pivot point
                    o._last = jItem;
                }
                // fire custom events handlers
                o.click(jItem.attr("data-key"), jItem.attr("data-value"), jItem, jList);
            }
        });


        events.keyboardMove.add(function (e) {
            var code = e.keyCode || e.which;
            var shift = e.shiftKey;
            var cmd = e.ctrlKey || e.metaKey; // I guess ctrlKey is for windows pc, not tested.
            var jTarget = null;

            switch (code) {
                case 40: // down
                    jTarget = pressUpOrDown(jList, o, true, shift, cmd);
                    break;
                case 38: // up
                    jTarget = pressUpOrDown(jList, o, false, shift, cmd);
                    break;
                case 13:
                    var jItem = getFirstHoveredItem(jList);
                    if (jItem) {
                        nativeItemSelection(jList, jItem, o);
                    }
                    break;
                default:
                    break;
            }
            if (false === shift) { // define shift pivot point
                o._last = jTarget;
            }
            if (jTarget) { // and the arrow
                o._arrow = jTarget;
            }
            return false; // prevent text selection?
        });


        jList.on('mouseover', function (e) {
            unhoverAll(jList);
            var jTarget = $(e.target).closest('.listt-item');
            if (jTarget.length) {
                jTarget.addClass("hover");
            }
        });

        jList.on('mouseout', function (e) {
            unhoverAll(jList);
        });


    }

    function doBuildList(jList, values, o, level) {

        for (var i in values) {
            var k = values[i][0];
            var v = values[i][1];

            if ($.isArray(v)) {
                var label = '';
                if (o.groupLabelKey === v[0][0]) {
                    label = v[0][1];
                    // we do not delete the key here to not alter the original array,
                    // which could be reused by the user.
                }
                var jGroup = $(o.getGroupHtml(label, level));
                jGroup.attr("data-groupName", k);
                doBuildList(jGroup, v, o, level + 1);
                o.appendGroupToContainer(jGroup, jList, level);
            }
            else {
                if (0 === level || (0 !== level && o.groupLabelKey !== k)) {
                    var jItem = buildItem(o, k, v, level);
                    o.addItemToContainer(jItem, jList, true);
                }
            }

        }
    }


    function getValues(jList) {
        var ret = [];
        jList.find(".listt-item").each(function () {
            ret.push([$(this).attr('data-key'), $(this).attr('data-value')]);
        });
        return ret;
    }


    $.fn.listt = function () {
        var args = Array.prototype.slice.apply(arguments);
        if ('option' === args[0]) {
            var opts = $.data(this[0], 'listtOptions');
            if (undefined !== opts) {
                if (args[1]) {
                    if (undefined !== args[2]) {
                        opts[args[1]] = args[2];
                    }
                    else {
                        return opts[args[1]];
                    }
                }
            }
        }
        else if ('getSelection' === args[0]) {
            return getSelection($(this), $(this).data("listtOptions"));
        }
        else if ('appendItem' === args[0]) {
            addItem($(this), $(this).data("listtOptions"), 'append', args[1], args[2], args[3], args[4]);
        }
        else if ('prependItem' === args[0]) {
            addItem($(this), $(this).data("listtOptions"), 'prepend', args[1], args[2], args[3], args[4]);
        }
        else if ('insertItemBefore' === args[0]) {
            addItem($(this), $(this).data("listtOptions"), 'insertItemBefore', args[1], args[2], args[3], args[4]);
        }
        else if ('insertItemAfter' === args[0]) {
            addItem($(this), $(this).data("listtOptions"), 'insertItemAfter', args[1], args[2], args[3], args[4]);
        }
        else if ('getValues' === args[0]) {
            return getValues($(this));
        }
        else if ('setValues' === args[0]) {
            return buildList($(this), args[1], $(this).data("listtOptions"), $(this).data("listtEvents"), 0);
        }
        else if ('disableItem' === args[0]) {
            return disableItem($(this), args[1]);
        }
        else if ('enableItem' === args[0]) {
            return enableItem($(this), args[1]);
        }
        else if ('disableGroup' === args[0]) {
            return disableGroup($(this), args[1]);
        }
        else if ('enableGroup' === args[0]) {
            return enableGroup($(this), args[1]);
        }
        else if ('disableItems' === args[0]) {
            return disableItems($(this), args[1]);
        }
        else if ('selectItems' === args[0]) {
            return selectItems($(this), args[1]);
        }
        else if ('enableAll' === args[0]) {
            return enableAll($(this));
        }
        else if ('disableAll' === args[0]) {
            return disableAll($(this));
        }
        else if ('deselectItem' === args[0]) {
            return deselectItem($(this), args[1]);
        }
        else if ('selectItem' === args[0]) {
            return selectItem($(this), args[1]);
        }
        else if ('selectItemExclusive' === args[0]) {
            return selectItemExclusive($(this), args[1]);
        }
        else if ('deselectAll' === args[0]) {
            return deselectAll($(this));
        }
        else if ('selectAll' === args[0]) {
            return selectAll($(this));
        }
        else {
            if (pea.isArrayObject(args[0])) {
                var options = args[0];
                var o = $.extend({
                    _bottom: false,
                    _last: null,
                    _arrow: null
                }, $.fn.listt.defaults, options);


                return this.each(function () {
                    var jList = $(this);
                    var events = {
                        mouseSelect: $.Callbacks("unique"),
                        mouseMove: $.Callbacks("unique"),
                        keyboardSelect: $.Callbacks("unique"),
                        keyboardMove: $.Callbacks("unique")
                    };
                    buildList(jList, o.values, o, events, 0);
                    jList.data('listtOptions', o);
                    if (null !== o.mouseSelect) {
                        events.mouseSelect.add(o.mouseSelect);
                    }
                    if (null !== o.keyboardSelect) {
                        events.keyboardSelect.add(o.keyboardSelect);
                    }
                    if (null !== o.keyboardMove) {
                        events.keyboardMove.add(o.keyboardMove);
                    }
                    jList.data('listtEvents', events);

                    jList.off('click.listt');
                    jList.on('click.listt', function (e) {
                        events.mouseSelect.fire(e);
                    });

                    delegatedCallbacks.add('keypress', function (e) {
                        if (true === o.listenToKeyboard) {
                            events.keyboardMove.fire(e);
                        }
                    });
                });
            }
        }
    };


    $.fn.listt.defaults = {
        values: [],
        multiple: false,
        listenToKeyboard: false,
        keyboardMoveAllowBlank: true,
        keyboardMoveCycle: true,
        groupLabelKey: '_label',
        disabledItems: [],
        selectedItems: [],
        getItemHtml: function (value, level) {
            var html = pea.strRepeat('&nbsp;', level * 8) + value;
            return '<li class="listt-item">' + html + '</li>';
        },
        getGroupHtml: function (title, level) {
            var html = pea.strRepeat('&nbsp;', (level * 8) + 4) + title;
            var s = '<li class="listt-group">';
            s += '<div class="title">' + html + '</div>';
            s += '<ul></ul>';
            s += '</li>';
            return s;
        },
        addItemToContainer: function (jItem, jContainer, append) {
            var isGroup = jContainer.hasClass('listt-group');
            var jUl;
            if (false === isGroup) {
                jUl = jContainer;
            }
            else {
                jUl = jContainer.find('ul:first');
            }
            if (true === append) {
                jUl.append(jItem);
            }
            else {
                jUl.prepend(jItem);
            }
        },
        appendGroupToContainer: function (jGroup, jContainer, level) {
            if (0 === level) {
                jContainer.append(jGroup);
            }
            else {
                jContainer.find('ul:first').append(jGroup);
            }
        },
        getSibling: function (jList, jItem, next, o) {
            var jRet = null;
            var items = jList.find(".listt-item");
            var ind = items.index(jItem);
            if (-1 !== ind) {
                if (false === next) {
                    if (0 == ind && true === o.keyboardMoveAllowBlank) {

                    }
                    else {

                        var n = parseInt(ind - 1);
                        if (n < 0) {
                            if (false === o.keyboardMoveCycle) {
                                n = 0;
                            }
                        }
                        jRet = items.eq(n);
                    }
                }
                else {
                    var max = items.length;
                    var n = parseInt(ind + 1);
                    if (n >= max) {
                        if (true === o.keyboardMoveAllowBlank) {
                            if (false === o.keyboardMoveCycle) {
                                o._bottom = true;
                            }
                            return jRet;
                        }
                        if (true === o.keyboardMoveCycle) {
                            n = 0;
                        }
                        else {
                            n = max - 1;
                        }
                    }
                    jRet = items.eq(n);
                }
            }
            return jRet;
        },
        click: function (key, value, jItem, jList) {

        },
        mouseSelect: null,
        keyboardSelect: null,
        keyboardMove: null
    };


}(jQuery));