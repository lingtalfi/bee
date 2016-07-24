<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script src="http://approot0/web/libs/js/jquery/lib/1.10.2/jquery.min.js"></script>
    <script src="assetloader.js"></script>
    <title>My html page</title>
</head>

<body>


<a id="start" href="#">Load all dependencies</a>

<script>

    function say(msg) {
        console.log(msg);
    }


    (function ($) {
        $(document).ready(function () {

            $('#start').on('click', function () {

                var dependencies = [
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/pou.js',
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/sou.js',
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/pou.js',
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/pou.js',
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/pou.css',
                    'http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assets/dou.js',
                ];

                window.assetLoader.loadDependencies(dependencies, function () {
                    say("Now all dependencies are loaded");
                });
            });


        });
    })(jQuery);
</script>

</body>
</html>



