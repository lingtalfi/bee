/**
 * Depends on:
 *
 * - jquery
 * - ?positionn
 * - pea 1.01
 * - shortcutMatch 1.0
 *
 */
(function ($) {

    if ('undefined' === typeof window.macGuiMenu) {


        /**
         * We store ids of opened panels so that we only create the behaviour once,
         * this lazy approach which can save lot of time if there are many panels on the page.
         * By preparation, I mean create the behaviour that opens children panel if any,
         *      and maybe more?
         */
        window._macGuiMenuPreparedIds = [];
        /**
         * This is a helper to hide sticking panels.
         * parentUid -> [jChildPanel, jParentItem]
         *
         */
        window._macGuiMenuStickers = {};


        function devError(msg) {
            console.log("MacGuiMenu error: " + msg);
        }

        var _topMenuCpt = 0;
        var _topMenuModeIns = {}; // id (cpt) => (bool) modeIn

        /**
         *
         * - (_shortcuts):
         * ----- contextId:
         * --------- zones: [jZone, ...]  (array)
         * --------- shortcuts: (shortcut 2 itemUid)
         * ------------- ctrl+a: uid
         *
         */
        var _shortcuts = {};
        var _standardShortcutsTable = {
            cmd: '&#8984;',
            ctrl: '&#8963;',
            alt: '&#8997;',
            shift: '&#8679;',
            backspace: '&#9003;',
            suppr: '&#8998;',
            esc: '&#9099;',
            right: '&#8594;',
            left: '&#8592;',
            up: '&#8593;',
            down: '&#8595;',
            pageUp: '&#8670;',
            pageDown: '&#8671;',
            home: '&#8598;',
            end: '&#8600;',
            tab: '&#8677;'
        };
        var _shortcutMapCpt = 0;
        var _activeContext = null; // there can only be one active context at the time

        /**
         * This array is useful for the case when you dynamically add a shortcut to an item.
         * With this array, you can get the panel from the item, then you can get the item's context.
         */
        var _panUid2ContextId = {};


        /**
         * Left click btn active/inactive decorator are both callbacks with the following signature:
         *
         *  callback ( jZone )
         *
         * They purpose is to update the css of the btn, so that it represents the current state:
         * active or inactive.
         *
         *
         */
        var _lcActiveDecorator = function (jZone) {
            jZone.addClass('active');
        };
        var _lcInactiveDecorator = function (jZone) {
            jZone.removeClass('active');
        };

        /**
         * Keeping track of all left btn, so that we can deactivate them at once
         */
        var _jButtons = [];
        var _jActiveButton = false;


        //------------------------------------------------------------------------------/
        // DEFAULT CODE
        //------------------------------------------------------------------------------/
        window.macGuiMenu = {
            /**
             * This special function MUST be defined by the user.
             * Since all items are unique on a page (that's how this script is conceived),
             * this function is a unique per page
             */
            executeItem: function (jItem, uid) {
            },
            //------------------------------------------------------------------------------/
            //
            //------------------------------------------------------------------------------/
            getItem: function (uid) {
                return getItem(uid);
            },
            removeItem: function (uid) {
                var jItem = getItem(uid);
                if (false !== jItem) {
                    jItem.remove();
                    return true;
                }
                return false;
            },
            getPanel: function (uid) {
                return getPanel(uid);
            },
            destroyPanel: function (uid) {
                var jPanel = getPanel(uid);
                if (false !== jPanel) {
                    jPanel.remove();
                }
                if (true === (uid in window._macGuiMenuPreparedIds)) {
                    delete window._macGuiMenuPreparedIds[uid];
                }
            },
            hideAllPanels: function () {
                hideAllPanels();
            },
            //------------------------------------------------------------------------------/
            // SHORTCUTS
            //------------------------------------------------------------------------------/
            setStandardShortcutsTable: function (shortcutsTable) {
                _standardShortcutsTable = shortcutsTable;
            },
            setStandardShortcutsTableEntry: function (k, v) {
                _standardShortcutsTable[k] = v;
            },
            setShortcuts: function (shortcuts) {
                _shortcuts = shortcuts;
            },
            addShortcutZone: function (contextId, jZone) {
                contextId = createShortcutContext(contextId);
                _shortcuts[contextId].zones.push(jZone);
            },
            listenToShortcuts: function () {

                // first create mouse reactive zones
                for (var contextId in _shortcuts) {
                    var jZones = _shortcuts[contextId].zones;
                    (function (cId) {
                        for (var i in jZones) {
                            var jZone = jZones[i];
                            jZone
                                .off('mouseover.macguiMenuShortcuts')
                                .off('mouseout.macguiMenuShortcuts')
                                .on('mouseover.macguiMenuShortcuts', function () {
                                    /**
                                     * Why a delay?
                                     * When we switch from a contiguous zone of to another from the
                                     * same context, if it mouses out (from the old zone) at the same time
                                     * or after it mouses in the new zone, the activeContext might be null.
                                     */
                                    setTimeout(function () {
                                        updateActiveContext(cId);
                                    }, 10);
                                })
                                .on('mouseout.macguiMenuShortcuts', function () {
                                    updateActiveContext(null);
                                });
                        }
                    })(contextId);
                }

                // then listen to the keypress (keydown?) events
                $(document)
                    .off('keydown.macGuiMenuShortcuts')
                    .on('keydown.macGuiMenuShortcuts', null, null, function (e) {
                        if (null !== _activeContext && _activeContext in _shortcuts) {

                            var shortcuts = _shortcuts[_activeContext].shortcuts;

                            /**
                             *
                             * If item A (cmd+z) is before item B (cmd+shift+z)
                             * and we do cmd+shift+z, it should execute item B.
                             * However, without the following len var, it does execute item A.
                             */
                            var len = 0;
                            var uid = null;
                            for (var shortcut in shortcuts) {
                                var sLen = shortcut.length;
                                if (true === shortcutMatch.match(e, shortcut) && sLen > len) {
                                    uid = shortcuts[shortcut];
                                    len = sLen;
                                }
                            }
                            if (null !== uid) {
                                if (true === macGuiMenu.isActiveStandardItem(getItem(uid))) {
                                    macGuiMenu.executeItem(getItem(uid), uid);
                                    return false; // preventing browser to execute their native shortcuts....
                                }
                            }

                        }
                    });
            },
            //------------------------------------------------------------------------------/
            //
            //------------------------------------------------------------------------------/
            createRightClickZone: function (jZone, panel) {
                var jPanel = null;
                if ('string' === typeof panel) {
                    jPanel = getPanel(panel);
                }
                else {
                    jPanel = panel;
                }

                if ('jquery' in jPanel) {
                    // this we need to prevent the default browser right click on a specific zone
                    // you might comment the line below if you want the browser's dialog to popup as well
                    jZone.attr("oncontextmenu", "return false;");
                    jZone.on('mousedown.macGuiMenuRightClick', function (e) {
                        if (3 == e.which) {
                            return openPanelAtMouse(e, jPanel);
                        }
                    });
                }
                else {
                    devError("createRightClickZone: panel not found with given panel argument");
                }
            },
            createLeftClickZone: function (jZone, panel, options) {

                _jButtons.push(jZone);

                options = $.extend({
                    /**
                     * Either use position, or define the position manually, using
                     * the my and at properties.
                     */
                    position: null,
                    my: 'left top+2',
                    at: 'left bottom'
                }, options);


                if ('right' === options.position) {
                    options.my = 'left top-4';
                    options.at = 'right top';
                }
                else if ('left' === options.position) {
                    options.my = 'right top-4';
                    options.at = 'left top';
                }


                var jPanel = null;
                if ('string' === typeof panel) {
                    jPanel = getPanel(panel);
                }
                else {
                    jPanel = panel;
                }

                if ('jquery' in jPanel) {

                    jZone.on('mousedown.macGuiMenuLeftClick', function (e) {
                        if (1 == e.which) {
                            if (false === _jActiveButton) {
                                activateZone(jZone);
                                var opt = {
                                    position: {
                                        my: options.my,
                                        at: options.at,
                                        of: jZone
                                    }
                                };
                                return openPanelAtMouse(e, jPanel, opt);
                            }
                        }
                        if (true === _jActiveButton) {
                            hideAllPanels();
                            deactivateAllButtons();
                        }
                    });
                }
                else {
                    devError("createRightClickZone: panel not found with given panel argument");
                }
            },
            setLeftClickBtnActiveDecorator: function (callback) {
                _lcActiveDecorator = callback;
            },
            setLeftClickBtnInactiveDecorator: function (callback) {
                _lcInactiveDecorator = callback;
            },
            createStandardPanel: function (panel) {
                return createStandardPanel(panel);
            },
            closeOnWindowClickBehaviour: function () {
                closeOnWindowClick();
            },
            preparePanelBehaviour: function (jPanel) {
                preparePanelBehaviour(jPanel);
            },
            preparePanelItemBehaviour: function (jPanel, jItem) {
                preparePanelItemBehaviour(jPanel, jItem, panelUid(jPanel));
            },
            //------------------------------------------------------------------------------/
            // MACGUI TOPMENU
            //------------------------------------------------------------------------------/
            createTopMenu: function (items) {
                var s = '<ul data-macgui-topmenu-id="' + _topMenuCpt + '" class="macgui-topmenu">';
                _topMenuCpt++;

                if (items.length > 0) {
                    s += '';
                    for (var i in items) {
                        var item = items[i];
                        s += getTopMenuItemHtml(item);
                    }
                    s += '</ul>';
                }
                if (s) {
                    return $(s);
                }
                return false;
            },
            initTopMenu: function (jTopMenu) {

                var tmId = getTopMenuId(jTopMenu);
                _topMenuModeIns[tmId] = false;


                jTopMenu.on("click", function (e) {
                    var jTarget = $(e.target);

                    // if the click is inside a menu topitem, we switch to modeIn
                    var jItem = jTarget.closest(".topitem");
                    if (jItem.length > 0) {
                        _topMenuModeIns[tmId] = true;
                        activateTopMenuItem(jItem);


                        $(window)
                            .off("mousedown.macGuiTopMenuTmp")
                            .on("mousedown.macGuiTopMenuTmp", function (e) {
                                clearTopMenu();
                                _topMenuModeIns[tmId] = false;
                            });
                    }
                    else {
                        _topMenuModeIns[tmId] = false;
                    }
                });


                jTopMenu.find(".topitem").each(function () {
                    initTopMenuItem($(this), tmId);
                });
            },
            addTopMenuItem: function (item, jTopMenu) {
                var jItem = $(getTopMenuItemHtml(item));
                var tmId = jTopMenu.attr("data-macgui-topmenu-id");
                jTopMenu.append(jItem);
                initTopMenuItem(jItem, tmId);
            },
            //------------------------------------------------------------------------------/
            // STANDARD ITEM
            //------------------------------------------------------------------------------/
            getStandardItemProp: function (uid, prop) {
                var jItem = getItem(uid);
                if (false !== jItem) {

                    switch (prop) {
                        case 'checked':
                            return macGuiMenu.isCheckedStandardItem(jItem);
                            break;
                        case 'active':
                            return macGuiMenu.isActiveStandardItem(jItem);
                            break;
                        case 'icon':
                            var jIcon = jItem.find(".lefticon");
                            if (jIcon) {
                                return jIcon.attr('src');
                            }
                            return false;
                            break;
                        case 'labelText':
                            var jLabel = jItem.find(".labelcontainer");
                            if (jLabel) {
                                return jLabel.text();
                            }
                            return false;
                            break;
                        case 'labelHtml':
                        case 'label':
                            var jLabel = jItem.find(".labelcontainer");
                            if (jLabel) {
                                return jLabel.html();
                            }
                            return false;
                            break;
                        case 'dialog':
                            var jLabel = jItem.find(".labelcontainer");
                            if (jLabel) {
                                var text = jLabel.text();
                                if ('...' === pea.substr(text, -3)) {
                                    return true;
                                }
                            }
                            return false;
                            break;
                        case 'child':
                            var childId = jItem.attr('data-parentof');
                            if (childId) {
                                return getPanel(childId);
                            }
                            return false;
                            break;
                        case 'shortcut':
                            var jRightBox = jItem.find(".rightbox");
                            if (jRightBox.length > 0) {
                                return jRightBox.html();
                            }
                            return false;
                            break;
                        default:
                            return null;
                            break;
                    }
                }
            },
            /**
             uid: null,
             checked: false,
             icon: null,
             label: null,
             dialog: false,
             child: null, // panel uid
             shortcut: null,
             active: true
             */
            updateStandardItem: function (uid, props) {
                var jItem = getItem(uid);
                if (false !== jItem) {
                    if ('checked' in props) {
                        if (true === props.checked) {
                            macGuiMenu.tickStandardItemCheckmark(jItem);
                        }
                        else if (false === props.checked) {
                            macGuiMenu.untickStandardItemCheckmark(jItem);
                        }
                        else if ('toggle' === props.checked) {
                            macGuiMenu.toggleStandardItemCheckmark(jItem);
                        }
                    }
                    if ('active' in props) {
                        if (true === props.active) {
                            macGuiMenu.activateStandardItem(jItem);
                        }
                        else if (false === props.active) {
                            macGuiMenu.deactivateStandardItem(jItem);
                        }
                        else if ('toggle' === props.active) {
                            macGuiMenu.toggleStandardItemActive(jItem);
                        }
                    }
                    if ('icon' in props) {
                        var jLabel = jItem.find(".labelcontainer");
                        if (jLabel.length) {
                            var jIcon = jLabel.find(".lefticon");
                            if (jIcon.length) {
                                jIcon.attr('src', props.icon);
                            }
                            else {
                                jLabel.prepend('<img class="lefticon" src="' + pea.htmlSpecialChars(props.icon) + '"/>');
                            }
                        }
                    }
                    if (
                        ('label' in props) || ('labelHtml' in props)
                    ) {
                        var jLabel = jItem.find(".labelcontainer");
                        if (jLabel.length) {
                            jLabel.html(props.label);
                        }
                    }
                    if ('labelText' in props) {
                        var jLabel = jItem.find(".labelcontainer");
                        if (jLabel.length) {
                            jLabel.text(props.label);
                        }
                    }
                    if ('dialog' in props) {
                        var jLabel = jItem.find(".labelcontainer");
                        if (jLabel.length) {
                            var text = jLabel.text();
                            if ('...' === pea.substr(text, -3)) {
                                if (false === props.dialog) {
                                    text = pea.substr(text, 0, -3);
                                    jLabel.text(text);
                                }
                            }
                            else {
                                if (true === props.dialog) {
                                    text += '...';
                                    jLabel.text(text);
                                }
                            }
                        }
                    }
                    if ('child' in props) {
                        var childId = props.child;
                        if (null === childId) {
                            jItem.find(".rightbox").remove();
                            jItem.removeAttr('data-parentof');
                            jItem.unbind('mouseenter mouseleave');
                            jItem.removeClass('hover');
                            var jPanel = getPanelByItem(jItem);
                            macGuiMenu.preparePanelItemBehaviour(jPanel, jItem);
                        }
                        else {
                            var jPanel = getPanel(childId);
                            if (false !== jPanel) {
                                jItem.attr("data-parentof", childId);
                                var jRightBox = jItem.find(".rightbox");
                                if (0 === jRightBox.length) {
                                    jRightBox = $('<span class="rightbox"></span>');
                                    jItem.append(jRightBox);
                                }
                                jRightBox.html("&#x25BA;");
                                macGuiMenu.preparePanelItemBehaviour(jPanel, jItem);
                            }
                        }
                    }
                    if ('shortcut' in props) {
                        if (false === props.shortcut) {
                            props.shortcut = '';
                        }

                        if (true === itemHasShortcutContext(uid)) {
                            updateShortcut(uid, props.shortcut);
                        }
                        else {
                            var contextId = getItemShortcutContext(uid);
                            setShortcut(contextId, props.shortcut, uid);
                        }


                        var jRightBox = jItem.find(".rightbox");
                        if (0 === jRightBox.length) {
                            jRightBox = $('<span class="rightbox"></span>');
                            jItem.append(jRightBox);
                        }

                        var html = shortcutNotationToHtml(props.shortcut);
                        jRightBox.html(html);
                    }
                }
            },
            createStandardItem: function (item, panelUid) {
                return $(prepareStandardItemHtml(item, panelUid));
//                return $(getStandardItemHtml(item, panelUid));
            },
            createAndPrepareStandardItem: function (item, panel) {
                var jItem = macGuiMenu.createStandardItem(item, panelUid);
                var jPanel = null;
                if ('string' === typeof panel) {
                    jPanel = getPanel(panel);
                }
                else {
                    jPanel = panel;
                }


                if (null !== jPanel) {
                    macGuiMenu.preparePanelItemBehaviour(jPanel, jItem);
                    return jItem;
                }
                return false;
            },
            // CHECKMARK
            //------------------------------------------------------------------------------/
            toggleStandardItemCheckmark: function (jItem) {
                if (true === macGuiMenu.isCheckedStandardItem(jItem)) {
                    macGuiMenu.untickStandardItemCheckmark(jItem);
                }
                else {
                    macGuiMenu.tickStandardItemCheckmark(jItem);
                }
            },
            tickStandardItemCheckmark: function (jItem) {
                jItem.find(".leftcolumn:first").html("&#x2713;");
            },
            untickStandardItemCheckmark: function (jItem) {
                jItem.find(".leftcolumn:first").html("");
            },
            isCheckedStandardItem: function (jItem) {
                var html = jItem.find(".leftcolumn:first").html();
                return (html.length > 0);
            },
            // ACTIVE/INACTIVE
            //------------------------------------------------------------------------------/
            toggleStandardItemActive: function (jItem) {
                if (true === macGuiMenu.isActiveStandardItem(jItem)) {
                    macGuiMenu.deactivateStandardItem(jItem);
                }
                else {
                    macGuiMenu.activateStandardItem(jItem);
                }
            },
            activateStandardItem: function (jItem) {
                jItem.removeClass("inactive");
            },
            deactivateStandardItem: function (jItem) {
                jItem.addClass("inactive");
            },
            isActiveStandardItem: function (jItem) {
                return itemActive(jItem);
            }
        };


        //------------------------------------------------------------------------------/
        // CONSTRUCTOR API
        //------------------------------------------------------------------------------/


        function panelUid($jPanel) {
            return $jPanel.attr("data-macgui-menu-panel-id");
        }

        function getPanel(uid) {
            if (null !== uid) {
                var jPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(uid) + "]");
                if (jPanel.length > 0) {
                    return jPanel;
                }
            }
            return false;
        }

        function getPanelByItem(jItem) {
            return jItem.closest(".macgui-menu-panel");
        }

        function itemActive(jItem) {
            return (false === jItem.hasClass("inactive"));
        }

        function itemUid($jItem) {
            return $jItem.attr("data-macgui-menu-item-id");
        }

        function preparePanelBehaviour($jPanel) {

            var panUid = panelUid($jPanel);
            if (false === (panUid in window._macGuiMenuPreparedIds)) {
                window._macGuiMenuPreparedIds[panUid] = true;
                window._macGuiMenuStickers[panUid] = false;
                $jPanel.find(".item").each(function () {
                    preparePanelItemBehaviour($jPanel, $(this), panUid);
                });
            }
        }


        function preparePanelItemBehaviour($jPanel, jItem, panUid) {

            var uid = jItem.attr("data-macgui-menu-item-id");
            if (uid) {
                var childPanelId = jItem.attr('data-parentof');
                if (childPanelId) {

                    var jChildPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(childPanelId) + "]");
                    if (jChildPanel.length > 0) {
                        jItem.unbind('mouseenter mouseleave');
                        jItem.hover(function () {
                            if (true === itemActive($(this))) {
                                if (true === window._macGuiMenuStickers[panUid]) {
                                    removeStickersOnHover($jPanel);
                                    window._macGuiMenuStickers[panUid] = false;
                                }

                                $(this).addClass('hover');
                                jChildPanel
                                    .off("mouseover.macguiStickyDetection")
                                    .on("mouseover.macguiStickyDetection", function () {
                                        window._macGuiMenuStickers[panUid] = true;
                                        jChildPanel.off("mouseover.macguiStickyDetection");
                                    });
                                openSubPanel($(this), jChildPanel);
                            }

                        }, function () {

                            if (true === itemActive($(this))) {
                                var zis = $(this);
                                setTimeout(function () {
                                    if (false === window._macGuiMenuStickers[panUid]) {
                                        zis.removeClass('hover');
                                        jChildPanel.hide();
                                    }
                                }, 1);
                            }

                        });
                    }
                    else {
                        devError("child panel not found with id: " + childPanelId);
                    }

                }
                else {
                    jItem.unbind('mouseenter mouseleave');
                    jItem.hover(function () {
                        if (true === itemActive($(this))) {
                            $(this).addClass('hover');
                            if (true === window._macGuiMenuStickers[panUid]) {
                                removeStickersOnHover($jPanel);
                                window._macGuiMenuStickers[panUid] = false;
                            }
                        }
                    }, function () {
                        if (true === itemActive($(this))) {
                            $(this).removeClass('hover');
                        }
                    });
                }
            }
            else {
                devError("uid not found for the current item");
            }
        }

        function removeStickersOnHover(jPanel, nested) {
            jPanel.find(".item[data-parentof]").each(function () {
                $(this).removeClass("hover");
                var childPanelId = $(this).attr("data-parentof");
                var jChildPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(childPanelId) + "]");
                if (jChildPanel.length > 0) {
                    removeStickersOnHover(jChildPanel, true);
                }
            });
            if (true === nested) {
                jPanel.hide();
            }
        }


        function openSubPanel($jParentItem, $jPanel) {
            preparePanelBehaviour($jPanel);
            $jPanel.show();
            $jPanel.positionn({
                my: "left top",
                of: $jParentItem,
                at: "right top-7"
            });
        }


        function hideAllPanels() {
            $(".macgui-menu-panel").hide();
        }

        function openPanelAtMouse(event, $jPanel, options) {


            options = $.extend({
                position: {
                    my: "left top",
                    of: event
                }
            }, options);


            /**
             * Chrome was too fast, and it triggered its native right mouse click event
             * BEFORE the main content of the openPanelAtMouse could be executed.
             * So I used the setTimeout trick to return false AS SOON AS possible.
             *
             */
            setTimeout(function () {
                hideAllPanels();
                preparePanelBehaviour($jPanel);

                $jPanel.show();
                $jPanel.positionn(options.position);

                event.preventDefault();
                event.stopImmediatePropagation();
                if (false === options.leftClick) {
                    closeOnWindowClick();
                }
                else {
                    closeAndDeactivateButtonsOnWindowClick();
                }
            }, 1);

            return false; // for chrome
        }


        function createStandardPanel(panel) {

            var p = $.extend({
                uid: null,
                contextId: 'all',
                items: []
            }, panel);


            if (null !== p.uid) {


                _panUid2ContextId[p.uid] = p.contextId;

                var s = '<div class="macgui-menu-panel" data-macgui-menu-panel-id="' + pea.htmlSpecialChars(p.uid) + '">';
                for (i in p.items) {
                    var item = p.items[i];
                    s += prepareStandardItemHtml(item, p.uid, p.contextId);
                }
                s += '</div>';

            }
            else {
                devError("Panel uid must be set");
            }

            var jPanel = $(s);
            $("body").append(jPanel);
            return jPanel;
        }

        function prepareStandardItemHtml(item, panelUid, contextId) {
            if (pea.isArrayObject(item)) { // some items are just hr
                createShortcutContext(contextId);
                if ('shortcut' in item) {
                    if ('uid' in item) {
                        setShortcut(contextId, item.shortcut, item.uid);
                    }
                    else {
                        devError("item does not have an uid!");
                    }
                }
            }
            return getStandardItemHtml(item, panelUid);
        }


        function getStandardItemHtml(item, panelUid) {
            var s = '';
            if ('hr' === item) {
                s += '<div class="hr"></div>' + "\n";
            }
            else {
                var o = $.extend({
                    uid: null,
                    checked: false,
                    icon: null,
                    label: null,
                    dialog: false,
                    child: null, // panel uid
                    shortcut: null,
                    active: true
                }, item);

                if (null !== o.uid) {
                    s += '<div class="item';
                    if (false === o.active) {
                        s += ' inactive';
                    }
                    s += '" data-macgui-menu-item-id="' + pea.htmlSpecialChars(o.uid) + '"';
                    if (null !== o.child) {
                        s += ' data-parentof="' + pea.htmlSpecialChars(o.child) + '"';
                    }
                    s += '>';
                    s += '<span class="leftcolumn">';
                    if (true === o.checked) {
                        s += '&#x2713;';
                    }
                    s += '</span>' +
                    '<span class="labelcontainer">';
                    if (null !== o.icon) {
                        s += '<img class="lefticon" src="' + pea.htmlSpecialChars(o.icon) + '" alt="icon" /> ';
                    }
                    s += o.label;
                    if (o.dialog) {
                        s += '...';
                    }
                    s += '</span>';
                    if (o.shortcut) {
                        var html = shortcutNotationToHtml(o.shortcut);
                        s += '<span class="rightbox">' + html + '</span>';
                    }
                    else if (null !== o.child) {
                        s += '<span class="rightbox">&#x25BA;</span>';
                    }
                    s += '</div>';
                }
                else {
                    // note: for dynamically set items, panelUid will probably be undefined
                    devError("item uid must be set (panel=" + panelUid + ")");
                }
            }
            return s;
        }


        //------------------------------------------------------------------------------/
        // ACTION API
        //------------------------------------------------------------------------------/
        function getItem(uid) {
            var jItem = $(".macgui-menu-panel [data-macgui-menu-item-id=" + selectorEscape(uid) + "]:first");
            if (jItem.length) {
                return jItem;
            }
            return false;
        }

        $(window).on('mousedown.macGuiItemAction', function (e) {
            var jTarget = $(e.target);
            var jItem = jTarget.closest(".item");
            if (jItem.length > 0 && jTarget.closest(".macgui-menu-panel").length > 0) {
                macGuiMenu.executeItem(jItem, itemUid(jItem));
            }
        });


        //------------------------------------------------------------------------------/
        // MACGUI TOPMENU API
        //------------------------------------------------------------------------------/
        function clearTopMenu() {
            $(".macgui-topmenu .topitem").removeClass("hover");
        }

        function placePanelBelowItem(jPanel, jItem) {

            hideAllPanels();
            preparePanelBehaviour(jPanel);

            jPanel.show();
            jPanel.positionn({
                my: 'left top',
                at: 'left bottom',
                of: jItem
            });

            closeOnWindowClick();
        }

        function initTopMenuItem(jTopMenuItem, tmId) {
            jTopMenuItem.hover(function () {
                if (true === _topMenuModeIns[tmId]) {
                    activateTopMenuItem(jTopMenuItem);
                }
            }, function () {
                if (true === _topMenuModeIns[tmId]) {
                    var zis = jTopMenuItem;
                    setTimeout(function () {
                        $(window)
                            .off("mousemove.macGuiTopMenuTmp2")
                            .on("mousemove.macGuiTopMenuTmp2", function (e) {
                                $(window).off("mousemove.macGuiTopMenuTmp2");
                                var jTarget = $(e.target);
                                if (0 === jTarget.closest(".topitem").length) {
                                    if (0 === jTarget.closest(".macgui-menu-panel").length) {
                                        hideAllPanels();
                                    }
                                    if (jTarget.closest(".macgui-topmenu").length > 0) {
                                        zis.removeClass("hover");
                                    }
                                }
                                else {
                                    if (0 === jTarget.closest(".macgui-menu-panel").length) {
                                        zis.removeClass("hover");
                                    }
                                }
                            });
                    }, 1);
                }
            });
        }

        function getTopMenuId(jTopMenu) {
            return jTopMenu.attr('data-macgui-topmenu-id');
        }

        function getTopMenuItemHtml(item) {
            var s = '';
            if ('pid' in item) {
                if ('label' in item) {
                    s += '<li class="topitem" data-parentof="' + selectorEscape(item.pid) + '">' + item.label + '</li>';
                }
                else {
                    devError("createTopMenu: Missing label key in given items");
                }
            }
            else {
                devError("createTopMenu: Missing pid key in given items");
            }
            return s;
        }


        function closeAndDeactivateButtonsOnWindowClick() {
            $(window)
                .off("mousedown.macGuiLeftClickClose")
                .on("mousedown.macGuiLeftClickClose", function (e) {

                    deactivateAllButtons(true);
                    hideAllPanels();
                    _jActiveButton = false;
                    $(window).off("mousedown.macGuiLeftClickClose");

                });
        }

        function closeOnWindowClick(callback) {
            $(window)
                .off("mousedown.macGuiTopMenuPanel")
                .on("mousedown.macGuiTopMenuPanel", function (e) {
                    hideAllPanels();
                    $(window).off("mousedown.macGuiTopMenuPanel");
                });
        }

        function activateTopMenuItem(jItem) {
            jItem.addClass("hover");
            var childId = jItem.attr("data-parentof");
            if (childId) {
                var jPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(childId) + "]");
                if (jPanel.length > 0) {
                    placePanelBelowItem(jPanel, jItem);
                }
            }
            jItem.closest(".macgui-topmenu").find(".topitem").each(function () {
                if ($(this)[0] !== jItem[0]) {
                    $(this).removeClass("hover");
                }
            });
        }

        function selectorEscape(sExpression) {
            return sExpression.replace(/[!"#$%&'()*+,.\/:;<=>?@\[\\\]^`{|}~]/g, '\\$&');
        }

        //------------------------------------------------------------------------------/
        // SHORTCUTS
        //------------------------------------------------------------------------------/
        function shortcutNotationToHtml(sNotation) {
            var p = sNotation.split("+");
            var s = '';
            for (var i in p) {
                var symbol = p[i];
                var symbolLow = symbol.toLowerCase();
                if (symbolLow in _standardShortcutsTable) {
                    s += _standardShortcutsTable[symbolLow];
                }
                else {
                    if (1 === symbol.length) {
                        symbol = symbol.toUpperCase();
                    }
                    s += symbol;
                }
            }
            return s;
        }

        function shortcutHtmlToNotation(html) {
            var s = html;
            for (var symbol in _standardShortcutsTable) {
                s = s.replace(_standardShortcutsTable[symbol], symbol);
            }
            return s;
        }

        function createShortcutContext(contextId) {
            contextId = getRealShortcutContext(contextId);
            if (false === (contextId in _shortcuts)) {
                _shortcuts[contextId] = {
                    zones: [],
                    shortcuts: {}
                };
            }
            return contextId;
        }

        function getRealShortcutContext(contextId) {
            if ('undefined' === typeof contextId || null === contextId) {
                contextId = 'all';
            }
            return contextId;
        }


        /**
         * If shortcut is null, it will just remove any shortcut matching the given uid.
         */
        function updateShortcut(uid, shortcut) {
            for (var contextId in _shortcuts) {
                var shortcuts = _shortcuts[contextId].shortcuts;
                for (var sh in shortcuts) {
                    if (uid === shortcuts[sh]) {
                        delete _shortcuts[contextId].shortcuts[sh];
                        if (null !== shortcut) {
                            _shortcuts[contextId].shortcuts[shortcut] = uid;
                        }
                    }
                }
            }
        }


        function itemHasShortcutContext(uid) {
            for (var contextId in _shortcuts) {
                var shortcuts = _shortcuts[contextId].shortcuts;
                for (var sh in shortcuts) {
                    if (uid === shortcuts[sh]) {
                        return true;
                    }
                }
            }
            return false;
        }

        function getItemShortcutContext(uid) {
            var jItem = getItem(uid);
            if (false !== jItem) {
                var jPanel = getPanelByItem(jItem);
                if (jPanel.length) {
                    var puid = panelUid(jPanel);
                    if (puid in _panUid2ContextId) {
                        return _panUid2ContextId[puid];
                    }
                }
            }
            return false;
        }

        function setShortcut(contextId, shortcut, uid) {
            contextId = getRealShortcutContext(contextId);
            _shortcuts[contextId].shortcuts[shortcut] = uid;
        }

        function updateActiveContext(context) {
            _activeContext = context;
        }


        //------------------------------------------------------------------------------/
        // LEFT CLICK ZONE
        //------------------------------------------------------------------------------/
        function deactivateZone(jZone) {
            _lcInactiveDecorator(jZone);
        }

        function activateZone(jZone) {
            _lcActiveDecorator(jZone);
            _jActiveButton = true;
        }

        function deactivateAllButtons() {
            for (var i in _jButtons) {
                deactivateZone(_jButtons[i]);
            }
            _jActiveButton = false;
        }


    }

})(jQuery);