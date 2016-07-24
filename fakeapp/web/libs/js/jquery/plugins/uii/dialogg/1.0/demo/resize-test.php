<!DOCTYPE html>
<html>
<head>
    <title>Html page</title>
    <meta charset="utf-8"/>

    <base href="http://approot0/web/libs/">


    <script src="http://localcdn/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://localcdn/ajax/libs/pea/lib/1.0/pea-1.0.js"></script>
    <script src="http://localcdn/ajax/libs/jutil/lib/1.0/jutil-1.0.js"></script>


    <script src="js/jquery/plugins/uii/dragg/1.03/dragg.js"></script>
    <script src="js/jquery/plugins/uii/dialogg/1.0/dialogg.js"></script>
    <script src="js/jquery/plugins/uii/resizz/1.0/resizz.js"></script>


    <link rel="stylesheet" href="js/jquery/plugins/uii/resizz/1.0/resizz.css">
    <link rel="stylesheet" href="js/jquery/plugins/uii/dialogg/1.0/dialogg.css">

    <style>
        .dialogcontent {
            white-space: nowrap;
            padding: 20px;
        }
    </style>
</head>

<body>
Hello

<button id="action">click</button>

<script>

    (function ($) {
        $(document).ready(function () {

            //------------------------------------------------------------------------------/
            // BEEF VIA CRUD ROUTINE
            //------------------------------------------------------------------------------/
            var jContent = $('<div class="dialogcontent" style="height:100%"></div>');
            $("body").append(jContent);


            jContent.dialogg({
                title: "Boo",
                modal: false,
                buttons: [
                    {
                        'text': "Send",
                        'click': function (e) {


                            $("#mycontent").dialogg("destroy");

                        }
                    }
                ]
            });
            var i = 0;
            $("#action").click(function () {


                if (0 === i % 2) {
                    jContent.html('<p  style="width:50px;white-space: nowrap">' +
                        'Lorem ipsum dolor sit amet' +
                        '</p>');
                }
                else {
                    jContent.html(
                        '<p style="white-space: nowrap">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ex labore. Dolorem doloribus excepturi maiores non voluptatem</p>' +
                            '<p style="white-space: nowrap">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ex labore. Dolorem doloribus excepturi maiores non voluptatem</p>' +
                            '<p style="white-space: nowrap">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ex labore. Dolorem doloribus excepturi maiores non voluptatem</p>' +
                            '<p style="white-space: nowrap">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ex labore. Dolorem doloribus excepturi maiores non voluptatem</p>' +
                            '<p style="white-space: nowrap">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate, ex labore. Dolorem doloribus excepturi maiores non voluptatem</p>'
                    );
                }
                jContent.dialogg('autoSize');

                i++;
                return false;
            });


        });
    })(jQuery);

</script>


</body>
</html>