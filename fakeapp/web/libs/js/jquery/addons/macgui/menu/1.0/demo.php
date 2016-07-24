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

    <title>MacGui menu demo</title>
    <style>


        #imac {
            width: 613px;
            height: 507px;
            background: url(img/imac.png) no-repeat center center;
            position: relative;
        }

        .mousezone {
            height: 335px;
            left: 15px;
            position: absolute;
            top: 15px;
            width: 583px;
            background: url(img/sleepingcat.jpg) no-repeat left top;
        }

        .mousezone2 {
            height: 40px;
            left: 470px;
            position: absolute;
            top: 150px;
            width: 60px;
            background: url(img/folder.png) no-repeat left top;
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
<div class="info">
    <p>
        &#8592; Demo 1: click a top menu item, right click inside the screen, and/or right click inside the folder.
        <br>
        <a href="#">Try demo 2 (left click)</a>
    </p>
    <hr>
    <p>
        For the documentation, please visit <a target="_blank" href="#">LingTalfi.com</a>
    </p>
    <hr>
    <p>
        For vidéos, click the link below
    </p>
    <ul>
        <li><a href="#">Presentation</a></li>
        <li><a href="#">Topmenu tutorial</a></li>
        <li><a href="#">Right click tutorial</a></li>
    </ul>
    <hr>
    <p>
        Note: this module is developed for firefox & chrome only, it might/might not work in other browsers.
        <a href="#">learn more</a>
    </p>

</div>

<h4 class="userzonetitle">Action panel</h4>

<p>
    The first item is the topmost item in the apple menu.
    Most of the actions below apply to the first item.
</p>

<div class="userzone">


    <div class="actiondemo">
        <h3>Dynamically Change Structure</h3>
        <ul>
            <li>
                <button id="action-create-topmenu-item">create top menu item</button>
            </li>
            <li>
                <button id="action-create-panel">create arbitrary panel</button>
            </li>
            <li>
                <button id="action-insert-after-item">Insert item after the first item</button>
            </li>
            <li>
                <button id="action-remove-item">Remove first item</button>
            </li>
            <li>
                <button id="action-append-item">Recreate first item</button>
            </li>
            <li>
                <button id="action-bind-child">Bind child to the first item</button>
            </li>
            <li>
                <button id="action-unbind-child">Unbind child from the first item</button>
            </li>
        </ul>
    </div>

    <div class="actiondemo">
        <h3>Update Standard Item</h3>
        <ul>
            <li>
                <button id="action-toggle-checkmark">Toggle Checkmark</button>
            </li>
            <li>
                <button id="action-toggle-active">Toggle Active</button>
            </li>
            <li>
                <button id="action-toggle-label">Toggle Label</button>
            </li>
            <li>
                <button id="action-toggle-icon">Toggle Icon</button>
            </li>
            <li>
                <button id="action-toggle-shortcut">Toggle Shortcut</button>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
</div>


<script>
(function ($) {


    $(document).ready(function () {


        var jZone1 = $("#mousezoneone");
        var jMenuTop = macGuiMenu.createTopMenu([
            {
                pid: 'home',
                label: '&#63743;'
            },
            {
                pid: 'phpStorm',
                label: 'PhpStorm'
            },
            {
                pid: 'file',
                label: 'File'
            },
            {
                pid: "edit",
                label: 'Edit'
            }
        ]);
        jZone1.append(jMenuTop);


        //------------------------------------------------------------------------------/
        // CREATING PANELS USING THE API
        //------------------------------------------------------------------------------/
        var jBody = $("body");
        var firstItem = {
            uid: "home.one",
            icon: 'img/icon.home.png',
            label: "About This Mac",
            active: false

        };

        var jFirstPanel = macGuiMenu.createStandardPanel({
            uid: 'home',
            items: [
                firstItem,
                {
                    uid: "home.two",
                    label: "Software Update",
                    dialog: true
                },
                {
                    uid: "home.three",
                    label: "Mac OS X Software",
                    dialog: true
                },
                'hr',
                {
                    uid: "home.four",
                    label: "System Preferences",
                    dialog: true
                },
                {
                    uid: "home.five",
                    label: "Dock",
                    child: 'dock'
                },
                {
                    uid: "home.six",
                    label: "Location",
                    child: 'location'
                },
                'hr',
                {
                    uid: "home.seven",
                    label: "Recent items"
                },
                'hr',
                {
                    uid: "home.eight",
                    label: "Force Quit Finder",
                    shortcut: "alt+cmd+esc"
                },
                {
                    uid: "home.nine",
                    label: "Sleep"
                },
                {
                    uid: "home.ten",
                    label: "Restart"
                },
                {
                    uid: "home.eleven",
                    label: "Shutdown"
                },
                {
                    uid: "home.twelve",
                    label: "Close Session",
                    shortcut: "shift+cmd+q"
                }
            ]
        });
        jBody.append(jFirstPanel);

        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'dock',
            items: [
                {
                    uid: "dock.one",
                    label: "Do something"
                },
                {
                    uid: "dock.two",
                    label: "Do something else"
                },
                'hr',
                {
                    uid: "dock.three",
                    label: "Position to A",
                    checked: true
                },
                {
                    uid: "dock.four",
                    label: "Position to B"
                },
                {
                    uid: "dock.five",
                    label: "Position to C"
                }
            ]
        }));
        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'location',
            items: [
                {
                    uid: "location.one",
                    label: "Do something"
                },
                {
                    uid: "location.two",
                    label: "Do something else"
                },
                {
                    uid: "location.three",
                    label: "Testing more recursion",
                    child: "category"
                }
            ]
        }));

        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'category',
            items: [
                {
                    uid: "category.1",
                    label: "Apple",
                    icon: "img/icon.apple.png"
                },
                {
                    uid: "category.2",
                    label: "Banana",
                    icon: "img/icon.banana.png"
                },
                {
                    uid: "category.3",
                    label: "Cherry",
                    icon: "img/icon.cherry.png"
                },
                'hr',
                {
                    uid: "category.f.1",
                    label: "Karate",
                    icon: "img/icon.karate.png"
                },
                {
                    uid: "category.f.2",
                    label: "Kung fu",
                    icon: "img/icon.tao.png"
                },
                {
                    uid: "category.f.3",
                    label: "Judo",
                    icon: "img/icon.judo.png"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'phpStorm',
            items: [
                {
                    uid: "phpStorm.one",
                    label: "About PhpStorm"
                },
                {
                    uid: "phpStorm.two",
                    label: "Check for Updates",
                    dialog: true
                },
                'hr',
                {
                    uid: "phpStorm.three",
                    label: "Preferences",
                    dialog: true,
                    shortcut: "cmd+,"
                },
                'hr',
                {
                    uid: "phpStorm.four",
                    label: "Services",
                    child: 'phpStormServices'
                },
                'hr',
                {
                    uid: "phpStorm.five",
                    label: "Hide PhpStorm",
                    shortcut: "cmd+H"
                },
                {
                    uid: "phpStorm.six",
                    label: "Hide Others",
                    shortcut: "cmd+alt+H"
                },
                {
                    uid: "phpStorm.seven",
                    label: "Show All"
                },
                'hr',
                {
                    uid: "phpStorm.eight",
                    label: "Quit PhpStorm",
                    shortcut: "cmd+Q"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'phpStormServices',
            items: [
                {
                    uid: "phpStormServices.one",
                    label: "No Services Apply",
                    active: false
                },
                {
                    uid: "phpStormServices.two",
                    label: "Services Preferences",
                    icon: "img/icon.terminal.png",
                    dialog: true
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'file',
            items: [
                {
                    uid: "file.one",
                    label: "New Project",
                    dialog: true
                },
                {
                    uid: "file.two",
                    label: "New",
                    shortcut: "cmd+N"
                },
                {
                    uid: "file.three",
                    label: "Open Directory",
                    dialog: true
                },
                {
                    uid: "file.four",
                    label: "Open",
                    icon: 'img/icon.folder.png',
                    dialog: true
                },
                {
                    uid: "file.five",
                    label: "Open URL",
                    dialog: true
                },
                {
                    uid: "file.six",
                    label: "New Project from Existing Files",
                    dialog: true
                },
                {
                    uid: "file.seven",
                    label: "Save As",
                    dialog: true,
                    shortcut: "shift+cmd+S"
                },
                {
                    uid: "file.eight",
                    label: "Open Recent",
                    child: 'openRecent'
                },
                {
                    uid: "file.nine",
                    label: "Close Project"
                },
                {
                    uid: "file.ten",
                    label: "Rename Project",
                    dialog: true
                }
            ]
        }));
        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'openRecent',
            items: [
                {
                    uid: "openRecent.one",
                    label: "MacGui Menu"
                },
                'hr',
                {
                    uid: "openRecent.two",
                    label: "Clear List"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'edit',
            items: [
                {
                    uid: "edit.one",
                    label: "Undo Typing",
                    shortcut: "cmd+Z",
                    icon: "img/icon.undo.png"
                },
                {
                    uid: "edit.two",
                    label: "Redo",
                    shortcut: "shift+cmd+Z",
                    icon: "img/icon.redo.png",
                    active: false
                },
                'hr',
                {
                    uid: "edit.cut",
                    label: "Cut",
                    shortcut: "cmd+X",
                    icon: "img/icon.cut.png"
                },
                {
                    uid: "edit.copy",
                    label: "Copy",
                    shortcut: "cmd+C",
                    icon: "img/icon.copy.png"
                },
                {
                    uid: "edit.copyPath",
                    label: "Copy Path",
                    shortcut: "shift+cmd+C"
                },
                {
                    uid: "edit.copyReference",
                    label: "Copy Reference",
                    shortcut: "alt+shift+cmd+C"
                },
                {
                    uid: "edit.paste",
                    label: "Paste",
                    shortcut: "cmd+V",
                    icon: "img/icon.paste.png"
                },
                {
                    uid: "edit.pasteFromHistory",
                    label: "Paste from History",
                    shortcut: "shift+cmd+V"
                },
                {
                    uid: "edit.pasteSimple",
                    label: "Paste Simple",
                    shortcut: "alt+shift+cmd+V"
                },
                {
                    uid: "edit.delete",
                    label: "Paste Delete",
                    shortcut: "suppr"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'rcDesktop',
            items: [
                {
                    uid: "rcDesktop.one",
                    label: "New Folder"
                },
                'hr',
                {
                    uid: "rcDesktop.two",
                    label: "Get Info",
                    shortcut: 'cmd+i'
                },
                'hr',
                {
                    uid: "rcDesktop.three",
                    label: "Change Desktop Background",
                    dialog: true
                },
                {
                    uid: "rcDesktop.four",
                    label: "Clean Up"
                },
                {
                    uid: "rcDesktop.four",
                    label: "Clean Up By",
                    child: "cleanUp"

                },
                {
                    uid: "rcDesktop.five",
                    label: "Sort Up By",
                    child: "sortBy"
                },
                {
                    uid: "rcDesktop.six",
                    label: "Show View Options"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'cleanUp',
            items: [
                {
                    uid: "cleanUp.one",
                    label: "Name"
                },
                {
                    uid: "cleanUp.two",
                    label: "Date Modified"
                },
                {
                    uid: "cleanUp.three",
                    label: "Date Created"
                },
                {
                    uid: "cleanUp.four",
                    label: "Size"
                },
                {
                    uid: "cleanUp.five",
                    label: "Kind"
                },
                {
                    uid: "cleanUp.six",
                    label: "Label"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'sortBy',
            items: [
                {
                    uid: "sortBy.none",
                    label: "None",
                    checked: true
                },
                'hr',
                {
                    uid: "sortBy.alignGrid",
                    label: "Align on grid"
                },
                'hr',
                {
                    uid: "sortBy.one",
                    label: "Name"
                },
                {
                    uid: "sortBy.two",
                    label: "Type"
                },
                {
                    uid: "sortBy.three",
                    label: "Date Created"
                },
                {
                    uid: "sortBy.four",
                    label: "Size"
                },
                {
                    uid: "sortBy.six",
                    label: "Label"
                }
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'rcFolder',
            items: [
                {
                    uid: "rcFolder.open",
                    label: "Open"
                },
                'hr',
                {
                    uid: "rcFolder.moveToTrash",
                    label: "Move to Trash",
                    shortcut: 'cmd+t'
                },
                'hr',
                {
                    uid: "rcFolder.getInfo",
                    label: "Get Info"
                },
                {
                    uid: "rcFolder.compress",
                    label: 'Compress "Red Folder"'
                },
                {
                    uid: "rcFolder.duplicate",
                    label: "Duplicate"
                },
                {
                    uid: "rcFolder.makeAlias",
                    label: "Make Alias"
                },
                'hr',
                {
                    uid: "rcFolder.copy",
                    label: 'Copy "Red Folder"'
                },
                'hr',
                {
                    uid: "rcFolder.cleanUpSelection",
                    label: 'Clean Up Selection'
                },
                'hr',
                {
                    uid: "rcFolder.configure",
                    label: 'Configure Folder Actions',
                    dialog: true
                },
            ]
        }));


        jBody.append(macGuiMenu.createStandardPanel({
            uid: 'colors',
            items: [
                {
                    uid: "colors.red",
                    label: '<span class="red">Red</span>'
                },
                {
                    uid: "colors.blue",
                    label: '<span class="blue">Blue</span>'
                },
                {
                    uid: "colors.green",
                    label: '<span class="green">Green</span>'
                }
            ]
        }));

        //------------------------------------------------------------------------------/
        // SCRIPT
        //------------------------------------------------------------------------------/
        var tmIdCpt = 0;
        var itemIdCpt = 0;
        var jLog = $("#log");
        macGuiMenu.executeItem = function (jItem, uid) {
            jLog.html(uid);
        };


        macGuiMenu.createRightClickZone(jZone1, "rcDesktop");
        macGuiMenu.createRightClickZone($("#mousezonetwo"), 'rcFolder');
        macGuiMenu.initTopMenu(jMenuTop);
        macGuiMenu.addShortcutZone('all', jZone1);
        macGuiMenu.listenToShortcuts();

        //------------------------------------------------------------------------------/
        // DYNAMICALLY CHANGE STRUCTURE
        //------------------------------------------------------------------------------/

        function createNewTopMenuItemPanel(uid) {
            jBody.append(macGuiMenu.createStandardPanel({
                uid: uid,
                items: [
                    {
                        uid: uid + ".one",
                        label: "Bim",
                        shortcut: 'cmd+b'
                    },
                    {
                        uid: uid + ".two",
                        label: "Bam"
                    },
                    {
                        uid: uid + ".three",
                        label: "Boom",
                        child: "category"
                    }
                ]
            }));
        }

        $("#action-create-topmenu-item").click(function () {
            var mtId = 'tmid' + tmIdCpt;
            var label = 'Item ' + tmIdCpt;
            tmIdCpt++;
            createNewTopMenuItemPanel(mtId);
            macGuiMenu.addTopMenuItem({
                pid: mtId,
                label: label
            }, jMenuTop);
        });


        $("#action-create-panel").click(function () {

            macGuiMenu.destroyPanel("onthefly");
            var jPanel = macGuiMenu.createStandardPanel({
                uid: 'onthefly',
                items: [
                    {
                        uid: "any",
                        label: "Hello, it is possible to reuse any panel/subpanel...",
                        dialog: true,
                        child: "home"
                    },
                    'hr',
                    {
                        uid: "any2",
                        checked: true,
                        label: "Hello there",
                        shortcut: "alt+p"
                    }
                ]
            });
            macGuiMenu.preparePanelBehaviour(jPanel);
            jPanel.show();
            macGuiMenu.closeOnWindowClickBehaviour();
        });


        var once = false;
        $("#action-insert-after-item").click(function () {
            var jItem = macGuiMenu.getItem("home.one");
            if (false !== jItem) {
                var uid = 'panelItem' + itemIdCpt;
                var label = 'Chting ' + itemIdCpt;
                itemIdCpt++;

                var item = {
                    uid: uid,
                    label: label
                };

                if (false === once) {
                    once = true;
                    item.shortcut = 'alt+x';
                }


                var jChild = macGuiMenu.createAndPrepareStandardItem(item, jFirstPanel);
                jItem.after(jChild);
            }
            return false;
        });

        $("#action-remove-item").click(function () {
            macGuiMenu.removeItem("home.one");
        });

        $("#action-append-item").click(function () {
            var jItem = macGuiMenu.getItem('home.one');
            if (false === jItem) {
                jItem = macGuiMenu.createAndPrepareStandardItem(firstItem, jFirstPanel);
                jFirstPanel.prepend(jItem);
            }
        });


        $("#action-bind-child").click(function () {
            macGuiMenu.updateStandardItem("home.one", {
                child: "colors"
            });
        });

        $("#action-unbind-child").click(function () {
            macGuiMenu.updateStandardItem("home.one", {
                child: null
            });
        });


        $("#action-toggle-checkmark").click(function () {
            macGuiMenu.updateStandardItem("home.one", {
                checked: 'toggle'
            });
        });

        $("#action-toggle-active").click(function () {
            macGuiMenu.updateStandardItem("home.one", {
                active: 'toggle'
            });
        });

        var labels = [macGuiMenu.getStandardItemProp("home.one", 'label'), "Toggled"];
        var labelsCpt = 0;
        $("#action-toggle-label").click(function () {
            labelsCpt++;
            var lblIndex = labelsCpt % 2;
            macGuiMenu.updateStandardItem("home.one", {
                label: labels[lblIndex]
            });
        });

        var icons = [macGuiMenu.getStandardItemProp("home.one", 'icon'), "img/icon.home.toggled.png"];
        var iconsCpt = 0;
        $("#action-toggle-icon").click(function () {
            iconsCpt++;
            var iconIndex = iconsCpt % 2;
            macGuiMenu.updateStandardItem("home.one", {
                icon: icons[iconIndex]
            });
        });


        var shortcuts = [macGuiMenu.getStandardItemProp("home.one", 'shortcut'), "A"];
        var shortcutsCpt = 0;
        $("#action-toggle-shortcut").click(function () {
            shortcutsCpt++;
            var shortcutIndex = shortcutsCpt % 2;
            macGuiMenu.updateStandardItem("home.one", {
                shortcut: shortcuts[shortcutIndex]
            });
        });


    });
})(jQuery);
</script>

</body>
</html>



