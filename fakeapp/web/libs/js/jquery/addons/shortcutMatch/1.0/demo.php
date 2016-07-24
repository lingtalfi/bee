<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script src="libs/jquery-1.10.2.min.js"></script>
    <script src="shortcutMatch-1.0.js"></script>
    <title>ShortcutMatch demo</title>
    <style>
        .box {
            border: 1px solid black;
            padding: 5px;
            width: 200px;
            height: 50px;
            text-align: center;
        }

        .boom {
            width: 16px;
            height: 16px;
            border-radius: 5px;
        }

        .red {
            background: red;
        }

        .green {
            background: green;
        }

    </style>
</head>

<body>

<h1>Mac shortcut key demo</h1>

<p>
    Type a key to see what shortcutMatch really sees.<br>
    shortcutMatch only cares about the following chars:
</p>

<ul>
    <li>alphabetic chars</li>
    <li>numeric chars on both the keyboard and the numpad</li>
    <li>the function keysv
    <li>some special keys like escape, enter, space</li>
    <li>the arrows</li>
</ul>
<p>
    Note: in order to fully test this page,
    the usual browser's shortcuts are disabled.
    So, to refresh the page, use the page refresh icon (the cmd+r shortcut won't work).
</p>

<div class="box" id="box"></div>
<div class="box" id="dbox"></div>


<p>
    Now in the following test, write a shortcut in the input,
    then click outside the input (otherwise the shortcut won't be detected),
    and type a shortcut: the lamp should turn green if it matches, or red if it doesn't.
</p>

<input id="matchtest" type="text">

<div id="boom" class="boom red"></div>


<script>
    (function ($) {
        $(document).ready(function () {

            var jBox = $("#box");
            var jDebug = $("#dbox");
            var jInput = $("#matchtest");
            var jBoom = $("#boom");


            $(document).on('keydown.pou', null, null, function (e) {
                jBox.html(shortcutMatch.getLiteralKeys(e));
                jDebug.html(e.which);

                var shortcut = jInput.val();
                if (true === shortcutMatch.match(e, shortcut)) {
                    jBoom.removeClass("red").addClass('green');
                }
                else {
                    jBoom.removeClass("green").addClass('red');
                }

                if ('INPUT' === e.target.tagName) {

                }
                else {
                    return false;
                }
            });


        });
    })(jQuery);
</script>


</body>
</html>