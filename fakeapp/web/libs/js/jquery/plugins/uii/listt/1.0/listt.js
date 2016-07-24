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

    function selectItemExclusiveByKey(jList, k) {
        deselectAll(jList);
        getItemByKey(jList, k).addClass(listSelectedClass);
    }

    function selectItemExclusive(jList, jItem) {
        deselectAll(jList);
        unhoverAll(jList);
        jItem.addClass(listSelectedClass);
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


    function createRange(jList, jItemA, jItemB) {
        var els = getOrderedElements(jList, jItemA, jItemB);
        selectAllFromAToB(jList, els[0], els[1]);
    }


    function toggle(jItem) {
        if (jItem.hasClass(listSelectedClass)) {
            jItem.removeClass(listSelectedClass);
            jItem.addClass(listHoverClass);
        }
        else {
            jItem.addClass(listSelectedClass);
            jItem.removeClass(listHoverClass);
        }
    }


    function getKeyboardFocusedItem(jList) {
        var jRet = getFirstHoveredItem(jList);

        if (null === jRet) {
            jRet = getFirstSelectedItem(jList);
            if (null === jRet) {
                jRet = jList.find(".listt-item:first");
            }
        }
        return jRet;
    }

    function getSiblingKeyboardFocusedItem(jList, next, o) {
        var jItem;
        if (null === o._lastSelected) {
            jItem = getKeyboardFocusedItem(jList);
        }
        else {
            jItem = o.getSibling(jList, o._lastSelected, next, o);
        }
        return jItem;
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


                if (false === o.multiple) {
                    deselectAll(jList);
                    jItem.addClass(listSelectedClass);
                }
                else {
                    if (true === cmd) {
                        toggle(jItem);
                    }
                    else if (true === o._base && true === shift) {
                        createRange(jList, o._base, jItem);
                    }
                    else {
                        selectItemExclusive(jList, jItem);
                    }
                }


                if (false === shift) {
                    o._base = jItem;
                }

                // fire custom events handlers
//                o.click(jItem.attr("data-key"), jItem.attr("data-value"), jItem, jList);
            }
        });


        events.keyboardMove.add(function (e) {
            var code = e.keyCode || e.which;
            var shift = e.shiftKey;
            var cmd = e.ctrlKey || e.metaKey; // I guess ctrlKey is for windows pc, not tested.
            var jItem = null;


            switch (code) {
                case 38: // up
                case 40: // down
                    var next = false;
                    if (40 === code) {
                        next = true;
                    }
                    if (false === o.multiple) {
                        var jBase = getFirstSelectedItem(jList);
                        if (null !== jBase) {
                            // should never return null
                            jItem = o.getSibling(jList, jBase, next, o);
                        }
                        else {
                            jItem = jList.find('.listt-item:first');
                        }
                        deselectAll(jList);
                        jItem.addClass(listSelectedClass);
                        /**
                         * This would probably be useful only in the case when
                         * we dynamically switch from multiple=false mode to multiple=true mode
                         */
                        o._lastSelected = jItem;

                    }
                    else {


                        if (true === cmd) {
                            jItem = getSiblingKeyboardFocusedItem(jList, next, o);
                            unhoverAll(jList);
                            jItem.addClass(listHoverClass);
                        }
                        else if (o._base && true === shift) {
                            jItem = o.getSibling(jList, o._lastSelected, next, o);
                            createRange(jList, o._base, jItem);
                        }
                        else {
                            jItem = getSiblingKeyboardFocusedItem(jList, next, o);
                            selectItemExclusive(jList, jItem);
                            o._base = jItem;
                        }

                        o._lastSelected = jItem;
                    }

                    break;
                case 13:

                    if (true === cmd) {
                        jItem = getKeyboardFocusedItem(jList);
                        toggle(jItem);
                    }
                    else if (o._base && true === shift) {
                        jItem = getKeyboardFocusedItem(jList);
                        createRange(jList, o._base, jItem);
                    }
                    else {
                        jItem = getKeyboardFocusedItem(jList);
                        selectItemExclusive(jList, jItem);
                    }
                    if (false === shift) {
                        o._base = jItem;
                    }
                    o._lastSelected = jItem;
                    break;
                default:
                    break;
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
        else if ('selectItemExclusiveByKey' === args[0]) {
            return selectItemExclusiveByKey($(this), args[1]);
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
                    _base: null,
                    _lastSelected: null
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
            if (jItem && -1 !== ind) {
                if (false === next) {
                    var n = parseInt(ind - 1);
                    if (n < 0) {
                        if (false === o.keyboardMoveCycle) {
                            n = 0;
                        }
                    }
                    jRet = items.eq(n);
                }
                else {
                    var max = items.length;
                    var n = parseInt(ind + 1);
                    if (n >= max) {
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