<?php



//------------------------------------------------------------------------------/
// MACGUI - MENU
//------------------------------------------------------------------------------/
/**
 * LingTalfi -- 2014-11-09
 */



?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <!--<script src="http://localcdn/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="http://approot0/web/libs/js/jquery/addons/fileSelect/1.0/css/fileselect.style.css">


    <script src="http://approot0/web/libs/js/jquery/plugins/uii/positionn/1.0/positionn.min.js"></script>


    <title>Html page</title>
    <style>


        .mousezone {
            width: 400px;
            height: 300px;
            background: orange;
        }

        .mousezone.right {
            position: fixed;
            right: 100px;
        }

        .mousezone.red {
            background: red;
            position: absolute;
            top: 0;
            right: 0;
        }

        .macgui-menu-panel {
            position: absolute;
            top: 50px;
            left: 300px;

            border-radius: 3px;
            border-top: 1px solid #dee1e4;
            border-right: 1px solid #bbbdc0;
            border-left: 1px solid #bbbdc0;
            border-bottom: 1px solid #979797;

            box-shadow: 0 4px 10px 6px rgba(0, 0, 0, 0.04);

            padding: 4px 0px;
            font-family: arial;
            font-size: 15px;
            background: white;
            /*opacity: 0.95;*/

            display: none;

        }

        .macgui-menu-panel .hr {
            height: 1px;
            background: #e4e5e5;
            margin: 5px 1px;
        }

        /*******************************
        STANDARD ITEM
        *******************************/
        .macgui-menu-panel .item {
            height: 19px;
            line-height: 19px;
            margin-top: 1px;
            padding-left: 5px;
            padding-right: 10px;
        }

        .macgui-menu-panel .item.inactive {
            color: #969899;
        }

        .macgui-menu-panel .item .leftcolumn {
            width: 16px;
            display: inline-block;
        }

        .macgui-menu-panel .item.hover {
            background: url(item-hover-bg.png) repeat center center;
            color: white;
            cursor: default;
        }

        .macgui-menu-panel .item .labelcontainer .lefticon {
            margin-right: 4px;
            vertical-align: text-top;
        }

        .macgui-menu-panel .item .rightbox {
            float: right;
            text-align: right;
            margin-left: 21px;

        }

        /*******************************
        MACGUI TOPMENU
        *******************************/
        .macgui-topmenu {
            width: 100%;
            height: 21px;
            background: white;
            margin: 0;
            padding: 0;
            opacity: 0.95;
            cursor: default;
            box-shadow: 0px 5px 10px 1px rgba(0, 0, 0, 0.5);
        }

        .macgui-topmenu li {
            float: left;
            height: 21px;
            line-height: 21px;
            list-style-type: none;
            font-size: 16px;
            padding-left: 10px;
            padding-right: 10px;
            position: relative;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .macgui-topmenu li.hover {
            background: white url(item-hover-bg.png) repeat center center;
            color: white;
        }


    </style>
</head>

<body>

<div class="mousezone left">
    <ul class="macgui-topmenu">
        <li class="topitem" data-parentof="1">&#63743;</li>
        <li class="topitem" data-parentof="2">PhpStorm</li>
        <li class="topitem" data-parentof="3">File</li>
        <li class="topitem" data-parentof="4">Edit</li>
    </ul>

</div>
<div>
    <h4>Current action</h4>

    <p id="log">

    </p>
</div>
<div class="mousezone red right">

</div>
<div class="actiondemo">
    <button id="action-toggle-checkmark">Toggle checkmark</button>
    <button id="action-toggle-active">Toggle active</button>
    <button id="action-tmp">create panel</button>
</div>


<div class="macgui-menu-panel" data-macgui-menu-panel-id="1">
    <div class="item inactive" data-macgui-menu-item-id="1">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Précédent
        zoeir ozer oziehr oih doijsoijsdos d  jii
        </span>
        <span class="rightbox">&#8984;(</span>
    </div>
    <div class="item" data-macgui-menu-item-id="2" data-parentof="2">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">
            <img class="lefticon" src="icon.home.png"/>Suivant
        </span>
        <span class="rightbox">&#x25BA;</span>
    </div>
    <div class="hr"></div>
    <div class="item" data-macgui-menu-item-id="13">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Lire les informations</span>
    </div>
    <div class="item" data-macgui-menu-item-id="15">
        <span class="leftcolumn">&#x2713;</span>
        <span class="labelcontainer">Icône et texte</span>
    </div>
    <div class="hr"></div>
    <div class="item" data-macgui-menu-item-id="14" data-parentof="4">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Présentation</span>
        <span class="rightbox">&#x25BA;</span>
    </div>
</div>


<div class="macgui-menu-panel" data-macgui-menu-panel-id="2">
    <div class="item" data-macgui-menu-item-id="3">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Précédent
        zoeir ozer oziehr oih doijsoijsdos d  jii
        </span>
        <span class="rightbox">&#8984;(</span>
    </div>
    <div class="item" data-macgui-menu-item-id="4" data-parentof="3">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">
            <img class="lefticon" src="icon.home.png"/>

            Suivant
        </span>
        <span class="rightbox">&#x25BA;</span>
    </div>
</div>


<div class="macgui-menu-panel" data-macgui-menu-panel-id="3">
    <div class="item" data-macgui-menu-item-id="5">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Précédent
        zoeir ozer oziehr oih doijsoijsdos d  jii
        </span>
        <span class="rightbox">&#8984;(</span>
    </div>
    <div class="item" data-macgui-menu-item-id="6">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">
            <img class="lefticon" src="icon.home.png"/>

            Suivant
        </span>
        <span class="rightbox">&#8984;)</span>
    </div>
</div>
<div class="macgui-menu-panel" data-macgui-menu-panel-id="4">
    <div class="item" data-macgui-menu-item-id="56">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">Précédent
        zoeir ozer oziehr oih doijsoijsdos d  jii
        </span>
        <span class="rightbox">&#8984;(</span>
    </div>
    <div class="item" data-macgui-menu-item-id="66">
        <span class="leftcolumn"></span>
        <span class="labelcontainer">
            <img class="lefticon" src="icon.home.png"/>

            Suivant
        </span>
        <span class="rightbox">&#8984;)</span>
    </div>
</div>


<script>
(function ($) {

    /**
     * In this implementation, we assume that:
     * - the panel is static: items cannot be added dynamically
     */

    $(document).ready(function () {



        //------------------------------------------------------------------------------/
        // CONSTRUCTOR API
        //------------------------------------------------------------------------------/
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


        var jPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=1]");


        function devError(msg) {
            console.log("dev error: " + msg);
        }

        function panelUid($jPanel) {
            return $jPanel.attr("data-macgui-menu-panel-id");
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
                var sticky = false;
                $jPanel.find(".item").each(function () {
                    var uid = $(this).attr("data-macgui-menu-item-id");
                    if (uid) {
                        var childPanelId = $(this).attr('data-parentof');
                        if (childPanelId) {

                            var jChildPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + childPanelId + "]");
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
                var jChildPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + childPanelId + "]");
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
                    var jPan = $(e.target).closest(".macgui-menu-panel");
                    if (false === (jPan.length > 0)) {
                        hideAllPanels();
                    }
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
            var jItem = $(".macgui-menu-panel [data-macgui-menu-item-id=" + uid + "]:first");
            if (jItem.length) {
                return jItem;
            }
            return false;
        }

        // CHECKMARK
        //------------------------------------------------------------------------------/
        function toggleStandardItemCheckmark(jItem) {
            if (true === isCheckedStandardItem(jItem)) {
                untickStandardItemCheckmark(jItem);
            }
            else {
                tickStandardItemCheckmark(jItem);
            }
        }

        function tickStandardItemCheckmark(jItem) {
            jItem.find(".leftcolumn:first").html("&#x2713;");
        }

        function untickStandardItemCheckmark(jItem) {
            jItem.find(".leftcolumn:first").html("");
        }

        function isCheckedStandardItem(jItem) {
            var html = jItem.find(".leftcolumn:first").html();
            return (html.length > 0);
        }

        // ACTIVE/INACTIVE
        //------------------------------------------------------------------------------/
        function toggleStandardItemActive(jItem) {
            if (true === isActiveStandardItem(jItem)) {
                deactivateStandardItem(jItem);
            }
            else {
                activateStandardItem(jItem);
            }
        }

        function activateStandardItem(jItem) {
            jItem.removeClass("inactive");
        }

        function deactivateStandardItem(jItem) {
            jItem.addClass("inactive");
        }

        function isActiveStandardItem(jItem) {
            return itemActive(jItem);
        }


        //------------------------------------------------------------------------------/
        // SCRIPT
        //------------------------------------------------------------------------------/
        var jZone = $(".mousezone");

        // this we need to prevent the default browser right clic on a specific zone
        // you might comment the line below if you want the browser's dialog to popup as well
        jZone.attr("oncontextmenu", "return false;");
        jZone.on('mousedown', function (e) {
            if (3 == e.which) {
                openPanelAtMouse(e, jPanel);
            }
        });


        $("#action-toggle-checkmark").click(function () {
            var jItem = getItem("15");
            if (false !== jItem) {
                toggleStandardItemCheckmark(jItem);
            }
            return false;
        });


        $("#action-toggle-active").click(function () {
            var jItem = getItem("1");
            if (false !== jItem) {
                toggleStandardItemActive(jItem);
            }
            return false;
        });


        $("#action-tmp").click(function () {
            var jPanel = createStandardPanel({
                uid: 'olicheat',
                items: [
                    {
                        uid: "abo",
                        checked: false,
                        icon: null,
                        label: "Hello",
                        dialog: true,
                        child: "1", // panel uid
                        shortcut: null,
                        active: true
                    },
                    'hr',
                    {
                        uid: "abo2",
                        checked: true,
                        icon: null,
                        label: "Hello there",
                        dialog: false,
                        child: null, // panel uid
                        shortcut: "Ax",
                        active: true
                    }
                ]
            });
            preparePanelBehaviour(jPanel);
            jPanel.show();
        });


        var jLog = $("#log")

        function executeItem(jItem) {
            if (true === isActiveStandardItem(jItem)) {
                jLog.html("Clicked on item: " + jItem.attr("data-macgui-menu-item-id"));
            }
        }

        $(window).on('mousedown.macGuiItemAction', function (e) {
            var jTarget = $(e.target);
            var jItem = jTarget.closest(".item");
            if (jItem.length > 0 && jTarget.closest(".macgui-menu-panel").length > 0) {
                executeItem(jItem);
            }
        });


        //------------------------------------------------------------------------------/
        // MACGUI TOPMENU
        //------------------------------------------------------------------------------/
        var modeIn = false;

        /**
         * Text selection problems...
         */

        function clearTopMenu() {
            $(".macgui-topmenu .topitem").removeClass("hover");
            modeIn = false;
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
                var jPanel = $(".macgui-menu-panel[data-macgui-menu-panel-id=" + childId + "]");
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


        $(".macgui-topmenu").on("click", function (e) {
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
                    });
            }
            else {
                modeIn = false;
            }
        });


        $(".macgui-topmenu").find(".topitem").each(function () {
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


    });
})(jQuery);
</script>

</body>
</html>



