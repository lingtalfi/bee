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
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/positionn.min.js"></script>
    <script src="js/pea-1.0.min.js"></script>
    <script src="js/jquery.hotkeys.js"></script>
    <script src="http://approot0/web/libs/js/jquery/addons/shortcutMatch/1.0/shortcutMatch-1.0.js"></script>

    <?php if (false && file_exists(__DIR__ . '/sell/macgui-menu-1.0.min.js')): ?>
        <script src="sell/macgui-menu-1.0.min.js"></script>
    <?php elseif (file_exists(__DIR__ . '/sources/macgui-menu-1.0.js')): ?>
        <script src="sources/macgui-menu-1.0.js"></script>
    <?php endif; ?>

    <link rel="stylesheet" href="css/macgui-menu.css">

    <title>MacGui menu demo 2</title>
    <style>

        .clear {
            clear: both;
        }

        .info {
            position: absolute;
            top: 30px;
            right: 5px;
            background: white;
            opacity: 0.6;
            border: 2px solid #ddd;
            border-radius: 10px;
            width: 300px;
            padding: 20px;

        }

        #logcontainer {
            left: 340px;
            position: absolute;
            top: 368px;
        }

        .toolbar {
            margin-left: 150px;
        }

        .toolbar .tool {
            float: left;
            margin-left: 50px;
            cursor: default;
        }

        .toolbar .tool {
            border-top: 1px solid #7f7f7f;
            border-right: 1px solid #7a7a7a;
            border-bottom: 1px solid #747474;
            border-left: 1px solid #7a7a7a;
            border-radius: 5px;
            width: 40px;
            height: 22px;

            box-shadow: 0px 1px 1px 1px #d4d4d4;

            background: #f6f6f6; /* Old browsers */
            background: -moz-linear-gradient(top, #f6f6f6 0%, #eaeaea 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f6f6f6), color-stop(100%, #eaeaea)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, #f6f6f6 0%, #eaeaea 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, #f6f6f6 0%, #eaeaea 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, #f6f6f6 0%, #eaeaea 100%); /* IE10+ */
            background: linear-gradient(to bottom, #f6f6f6 0%, #eaeaea 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f6f6f6', endColorstr='#eaeaea', GradientType=0); /* IE6-9 */

        }

        .toolbar .tool.active {
            background: #adadad; /* Old browsers */
            background: -moz-linear-gradient(top, #adadad 0%, #d5d5d5 53%, #d5d5d5 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #adadad), color-stop(53%, #d5d5d5), color-stop(100%, #d5d5d5)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, #adadad 0%, #d5d5d5 53%, #d5d5d5 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, #adadad 0%, #d5d5d5 53%, #d5d5d5 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, #adadad 0%, #d5d5d5 53%, #d5d5d5 100%); /* IE10+ */
            background: linear-gradient(to bottom, #adadad 0%, #d5d5d5 53%, #d5d5d5 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#adadad', endColorstr='#d5d5d5', GradientType=0); /* IE6-9 */

        }

        .toolbar .tool .icon {
            padding-top: 5px;
            padding-left: 6px;

        }

        .toolbar .tool .icon.leftarrow {
            padding-top: 4px;
            padding-left: 4px;
        }

        .toolbar .tool .icon.rightarrow {
            padding-top: 4px;
            padding-left: 17px;
        }

        .toolbar .tool .arrow {
            font-size: 9px;
            padding-top: 8px;
            padding-left: 7px;
            padding-right: 5px;
        }

        .toolbar .tool .icon,
        .toolbar .tool .arrow {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>

<body>


<div class="toolbar">
    <div id="leftbutton" class="tool tool-sensor"><img class="icon tool-sensor leftarrow" src="img/double-arrow-left.png"></div>
    <div id="middlebutton" class="tool tool-sensor"><img class="icon tool-sensor" src="img/bticon.list.png"><span
            class="arrow tool-sensor">&#x25bc;</span></div>
    <div id="rightbutton" class="tool tool-sensor"><img class="icon tool-sensor rightarrow" src="img/double-arrow-right.png"></div>
    <div class="clear"></div>
</div>


<p id="logcontainer">
    <span>Executing: </span><span id="log"></span>
</p>

<div class="info">
    <p>
        &#8592; Demo 2: left click the icons.
        <br>
        You can also right click the middle icon.
        <br>
        <a href="#">Try demo 1 (top menu and right click)</a>
    </p>
    <hr>
    <p>
        For the documentation, please visit <a target="_blank" href="#">LingTalfi.com</a>
    </p>
    <hr>
    <p>
        Note: this module is developed for firefox & chrome only, it might/might not work in other browsers.
        <a href="#">learn more</a>
    </p>

</div>


<script>
    (function ($) {


        $(document).ready(function () {




            //------------------------------------------------------------------------------/
            // SCRIPT
            //------------------------------------------------------------------------------/

            var jLog = $("#log");
            macGuiMenu.executeItem = function (jItem, uid) {
                jLog.html(uid);
            };

            var jBody = $("body");
            jBody.append(macGuiMenu.createStandardPanel({
                uid: 'btn1',
                items: [
                    {
                        uid: "btn1.doSomething",
                        label: "Do something"
                    },
                    {
                        uid: "btn1.doSomethingElse",
                        label: "Do something else"
                    },
                    'hr',
                    {
                        uid: "btn1.positionToA",
                        label: "Position to A",
                        checked: true
                    },
                    {
                        uid: "btn1.positionToB",
                        label: "Position to B"
                    }
                ]
            }));


            jBody.append(macGuiMenu.createStandardPanel({
                uid: 'btnLeft',
                items: [
                    {
                        uid: "btnLeft.mixing",
                        label: "Mixing"
                    },
                    {
                        uid: "btnLeft.wrapping",
                        label: "Wrapping"
                    }
                ]
            }));

            jBody.append(macGuiMenu.createStandardPanel({
                uid: 'btnRight',
                items: [
                    {
                        uid: "btnRight.bind",
                        label: "Bind",
                        shortcut: "ctrl+b"
                    },
                    {
                        uid: "btnRight.unbind",
                        label: "Unbind",
                        shortcut: "ctrl+u"
                    }
                ]
            }));

            jBody.append(macGuiMenu.createStandardPanel({
                uid: 'contextual',
                items: [
                    {
                        uid: "contextual.shoo",
                        label: "Shoo"
                    },
                    {
                        uid: "contextual.shaa",
                        label: "Shaa"
                    }
                ]
            }));


            var jMiddle = $("#middlebutton");

            macGuiMenu.createRightClickZone(jMiddle, "contextual");
            macGuiMenu.createLeftClickZone(jMiddle, "btn1");
            macGuiMenu.createLeftClickZone($("#leftbutton"), "btnLeft", {position: 'left'});
            macGuiMenu.createLeftClickZone($("#rightbutton"), "btnRight", {position: 'right'});
            macGuiMenu.addShortcutZone('all', $(document));
            macGuiMenu.listenToShortcuts();

        });
    })(jQuery);
</script>

</body>
</html>



