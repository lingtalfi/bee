/**
 * Depends on:
 *
 * - jquery
 * - ?positionn
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




        //------------------------------------------------------------------------------/
        // DEFAULT CODE
        //------------------------------------------------------------------------------/
        window.macGuiMenu = {
            executeItem: function (jItem) {
            },
            getItem: function (uid) {
                return getItem(uid);
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
            //
            //------------------------------------------------------------------------------/
            createRightClickZone: function (jZone, panel) {
                var jPanel = null;
                if ('string' === typeof panel) {
                    jPanel = $(window).find(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(panel) + "]:first");
                }
                else {
                    jPanel = panel;
                }

                if ('jquery' in jPanel) {
                    // this we need to prevent the default browser right clic on a specific zone
                    // you might comment the line below if you want the browser's dialog to popup as well
                    jZone.attr("oncontextmenu", "return false;");
                    jZone.on('mousedown', function (e) {
                        if (3 == e.which) {
                            openPanelAtMouse(e, jPanel);
                        }
                    });
                }
                else {
                    devError("createRightClickZone: panel not found with given panel argument");
                }
            },
            createStandardPanel: function (panel) {
                return createStandardPanel(panel);
            },
            preparePanelBehaviour: function (jPanel) {
                preparePanelBehaviour(jPanel);
            },
            //------------------------------------------------------------------------------/
            // MACGUI TOPMENU
            //------------------------------------------------------------------------------/
            createTopMenu: function (items) {
                var s = '<ul class="macgui-topmenu">';
                if (items.length > 0) {
                    s += '';
                    for (var i in items) {
                        var item = items[i];
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
                    }
                    s += '</ul>';
                }
                if (s) {
                    return $(s);
                }
                return false;
            },
            initTopMenu: function (jTopMenu) {

                var modeIn = false;


                jTopMenu.on("click", function (e) {
                    var jTarget = $(e.target);

                    // if the click is inside a menu topitem, we switch to modeIn
                    var jItem = jTarget.closest(".topitem");
                    if (jItem.length > 0) {
                        modeIn = true;
                        activateTopMenuItem(jItem);


                        $(window)
                            .off("mousedown.macGuiTopMenuTmp")
                            .on("mousedown.macGuiTopMenuTmp", function (e) {
                                clearTopMenu();
                                modeIn = false;
                            });
                    }
                    else {
                        modeIn = false;
                    }
                });


                jTopMenu.find(".topitem").each(function () {
                    $(this).hover(function () {
                        if (true === modeIn) {
                            activateTopMenuItem($(this));
                        }
                    }, function () {
                        if (true === modeIn) {
                            var zis = $(this);
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
                });
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
            var jPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(uid) + "]");
            if (jPanel.length > 0) {
                return jPanel;
            }
            return false;
        };
        function itemActive(jItem) {
            return (false === jItem.hasClass("inactive"));
        }

        function itemUid($jItem) {
            return $jItem.attr("data-macgui-menu-item-id");
        }

        //------------------------------------------------------------------------------/
        // SELL PROTECTION
        //------------------------------------------------------------------------------/
        function getDamen() {
            return 'ame';
        }

        function m(i) {
            return String.fromCharCode(i);
        }

        function n(i) {
            return String.fromCharCode(i + 5);
        }

        function reverse(s) {
            return s.split("").reverse().join("");
        }

        var d = 104 + 7;
        var res = m(104) + String.fromCharCode(d);
        var b = 'stn';
        var domain = window.location[res + b + getDamen()];
        var gnu = 'http://';
        var zeb = n(103); // l
        var a = m(105); // i
        var a12 = m(110); // n
        var b = m(103); // g
        var v = m(116); // t
        var a3 = 'al';
        var p = 1 + 1;
        var gen = zeb + a + a12 + b + v + a3 + reverse('if');
        if (
            (gen + parseInt(1 + 1)) !== domain &&
            (gnu + gen + '.' + reverse('moc')) !== domain
        ) {
            return false;
        }

        function preparePanelBehaviour($jPanel) {

            var panUid = panelUid($jPanel);
            if (false === (panUid in window._macGuiMenuPreparedIds)) {
                window._macGuiMenuPreparedIds[panUid] = true;
                var sticky = false;
                $jPanel.find(".item").each(function () {
                    var uid = $(this).attr("data-macgui-menu-item-id");
                    if (uid) {
                        var childPanelId = $(this).attr('data-parentof');
                        if (childPanelId) {

                            var jChildPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + selectorEscape(childPanelId) + "]");
                            if (jChildPanel.length > 0) {
                                $(this).hover(function () {
                                    if (true === itemActive($(this))) {
                                        if (true === sticky) {
                                            removeStickersOnHover($jPanel);
                                            sticky = false;
                                        }

                                        $(this).addClass('hover');
                                        jChildPanel
                                            .off("mouseover.macguiStickyDetection")
                                            .on("mouseover.macguiStickyDetection", function () {
                                                sticky = true;
                                                jChildPanel.off("mouseover.macguiStickyDetection");
                                            });
                                        openSubPanel($(this), jChildPanel);
                                    }

                                }, function () {

                                    if (true === itemActive($(this))) {
                                        var zis = $(this);
                                        setTimeout(function () {
                                            if (false === sticky) {
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
                            $(this).hover(function () {
                                if (true === itemActive($(this))) {
                                    $(this).addClass('hover');
                                    if (true === sticky) {
                                        removeStickersOnHover($jPanel);
                                        sticky = false;
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
                });
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

        function openPanelAtMouse(event, $jPanel) {
            hideAllPanels();
            preparePanelBehaviour($jPanel);

            $jPanel.show();
            $jPanel.positionn({
                my: "left top",
                of: event
            });

            event.stopImmediatePropagation();
            $(window)
                .off("mousedown.tmpPanel")
                .on("mousedown.tmpPanel", function (e) {
                    hideAllPanels();
                });
        }


        function createStandardPanel(panel) {

            var p = $.extend({
                uid: null,
                items: []
            }, panel);


            if (null !== p.uid) {

                var s = '<div class="macgui-menu-panel" data-macgui-menu-panel-id="' + p.uid + '">';
                for (i in p.items) {
                    var item = p.items[i];
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
                            s += '" data-macgui-menu-item-id="' + o.uid + '"';
                            if (null !== o.child) {
                                s += ' data-parentof="' + o.child + '"';
                            }
                            s += '>';
                            s += '<span class="leftcolumn">';
                            if (true === o.checked) {
                                s += '&#x2713;';
                            }
                            s += '</span>' +
                            '<span class="labelcontainer">';
                            if (null !== o.icon) {
                                s += '<img src="' + o.icon + '" alt="icon" /> ';
                            }
                            s += o.label;
                            if (o.dialog) {
                                s += '...';
                            }
                            s += '</span>';
                            if (o.shortcut) {
                                s += '<span class="rightbox">' + o.shortcut + '</span>';
                            }
                            else if (null !== o.child) {
                                s += '<span class="rightbox">&#x25BA;</span>';
                            }
                            s += '</div>';
                        }
                        else {
                            devError("item uid must be set (panel=" + p.uid + ")");
                        }
                    }
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
                macGuiMenu.executeItem(jItem);
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

            $(window)
                .off("mousedown.macGuiTopMenuPanel")
                .on("mousedown.macGuiTopMenuPanel", function (e) {
                    hideAllPanels();
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

    }

})(jQuery);