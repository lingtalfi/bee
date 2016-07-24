<?php



//------------------------------------------------------------------------------/
// MACGUI - MENU
//------------------------------------------------------------------------------/
/**
 * LingTalfi -- 2014-11-20
 */



?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script src="../../../js/jquery-1.11.1.min.js"></script>
    <script src="../../../js/positionn.min.js"></script>
    <script src="../../../js/pea-1.0.min.js"></script>
    <script src="../../../js/jquery.hotkeys.js"></script>
    <script src="http://approot0/web/libs/js/jquery/addons/shortcutMatch/1.0/shortcutMatch-1.0.js"></script>

    <?php if (false && file_exists(__DIR__ . '/../../../sell/macgui-menu-1.0.min.js')): ?>
        <script src="../../../sell/macgui-menu-1.0.min.js"></script>
    <?php elseif (file_exists(__DIR__ . '/sources/macgui-menu-1.0.js')): ?>
        <script src="../../../sources/macgui-menu-1.0.js"></script>
    <?php endif; ?>

    <link rel="stylesheet" href="../../../css/macgui-menu.css">

    <title>MacGuiMenu: tuto rightclick menu</title>
    <style>


        #imac {
            width: 613px;
            height: 507px;
            background: url(../../../img/imac.png) no-repeat center center;
            position: relative;
        }

        .mousezone {
            height: 335px;
            left: 15px;
            position: absolute;
            top: 15px;
            width: 583px;
            background: orange;
        }

        .mousezone2 {
            height: 40px;
            left: 470px;
            position: absolute;
            top: 150px;
            width: 60px;
            background: red;
            cursor: pointer;
        }

        .userzonetitle {
            margin-top: 20px;
            margin-bottom: 0px;
        }

        .userzone {
            background: #6c8061;
            padding: 20px;
        }

        .actiondemo {
            font-family: Verdana, sans-serif;
            float: left;
            width: 30%;
            padding-right: 10px;
            padding-left: 10px;
            padding-bottom: 20px;
            border-right: 1px solid #89a279;
            height: 200px;
        }

        .actiondemo h3 {
            font-size: 14px;
            font-weight: normal;
            text-decoration: underline;
        }

        .actiondemo ul {
            list-style-type: none;
        }

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

        .red {
            color: red;
        }

        .blue {
            color: blue;
        }

        .green {
            color: green;
        }

        .hover .red,
        .hover .blue,
        .hover .green {
            color: white;
        }

    </style>
</head>

<body>

<div id="imac">
    <div id="mousezoneone" class="mousezone">
        <div id="mousezonetwo" class="mousezone2"></div>
    </div>
    <p id="logcontainer">
        <span>Executing: </span><span id="log"></span>
    </p>
</div>




<script>
(function ($) {

    /**
     * In this implementation, we assume that:
     * - the panel is static: items cannot be added dynamically
     */

    $(document).ready(function () {


        var jZone1 = $("#mousezoneone");


        //------------------------------------------------------------------------------/
        // CREATING PANELS USING THE API
        //------------------------------------------------------------------------------/
        var jBody = $("body");
        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'desktop',
            items: [
                {
                    uid: "desktop.one",
                    icon: 'img/icon.home.png',
                    label: "About This Mac"

                },
                {
                    uid: "desktop.two",
                    label: "Software Update",
                    dialog: true
                },
                'hr',
                {
                    uid: "desktop.three",
                    label: "Do something",
                    shortcut: 'ctrl+a'
                }
            ]
        }));

        var jLog = $("#log");
        macGuiMenu.executeItem = function (jItem, uid) {
            jLog.html(uid);
        };

        macGuiMenu.createRightClickZone(jZone1, "desktop");

        macGuiMenu.addShortcutZone('all', jZone1);
        macGuiMenu.listenToShortcuts();



    });
})(jQuery);
</script>

</body>
</html>



