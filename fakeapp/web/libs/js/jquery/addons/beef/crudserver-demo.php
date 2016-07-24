<?php


$_beeApplicationRoot = __DIR__;
require_once 'alveolus/bee/boot/booted-chopin.php';


//use WebModule\Komin\Beef\Server\HtmlFactory\HtmlFactory;
//$f = "/Volumes/Macintosh HD/Users/pierrelafitte/Desktop/mondossier/web/Komin>/service création/projets/bee/developer/bee/approot0/web/libs/js/jquery/addons/beef/app/cache/control/webtv-emissions.txt";
//$fac = new HtmlFactory();
//    $controls = \Bee\Notation\File\BabyYaml\Tool\BabyYamlTool::parseFile($f);
//    a($controls);
//$a = $fac->getHtml($controls);
//az($a);





\WebModule\Komin\User\UserToken\UserTokenTool::connect([
    'login' => 'ling',
    'pass' => 'ling',
]);


?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script src="http://approot0/web/libs/js/jquery/addons/assetloader/1.0/assetloader.js"></script>
    <script src="http://approot0/web/libs/js/jquery/lib/1.10.2/jquery.min.js"></script>
    <script src="http://approot0/web/libs/js/jquery/addons/jutil/1.0/jutil.js"></script>
    <script src="http://approot0/web/libs/js/jquery/addons/ajaxtim/ajaxtim-1.0.js"></script>
    <script src="http://approot0/web/libs/js/jquery/plugins/uii/dialogg/1.0/dialogg.js"></script>
    <script src="http://approot0/web/libs/js/jquery/plugins/uii/dragg/1.03/dragg.min.js"></script>
    <script src="http://approot0/web/libs/js/jquery/plugins/uii/resizz/1.0/resizz.min.js"></script>
    <script src="http://approot0/web/libs/js/jquery/plugins/uii/positionn/1.0/positionn.min.js"></script>
    <script src="http://approot0/web/libs/js/pea/lib/1.01/pea-1.01.js"></script>
    

    <script src="js/beef-1.02.js"></script>
    <title>Beef Demo</title>
    <link rel="stylesheet" href="css/beef.css">
    <link rel="stylesheet" href="http://approot0/web/libs/js/jquery/plugins/uii/dialogg/1.0/dialogg.css">
    <link rel="stylesheet" href="http://approot0/web/libs/js/jquery/plugins/uii/resizz/1.0/resizz.css">

    <style>
        .error {
            color: red;
        }

        .myerror {
            color: orange;
        }

        .tip {
            font-size: 10px;
            font-family: verdana;
        }
    </style>
</head>
<body>
<script>
    (function ($) {
        $(document).ready(function () {


//            var data = {
//                serviceId: "pragmatikCrudServer",
//                id: "c.webtv.emissions",
//                params: {
////                    values: {
////                        titre: "p"
////                    }
//                }
//            };

            var data = {
                serviceId: "pragmatikCrudServer",
                id: "u.webtv.emissions",
                params: {
                    riv: {
                        id: 6
                    }
//                    values: {
//                        id: 6,
//                        titre: "hdello"
//                    }
                }
            };

            var url = 'service/ajaxservice.php';
            var options = {};
            window.beef.util.callForm(data, url, function (v, nestedForm, jContent) {
                data.params.values = v;
                ajaxTim.sendMessage(url, data, function (m) {
                    var hasError = false;
                    if (pea.isArrayObject(m) && 1 === pea.count(m) && m._errors) {
                        for (var controlName in m._errors) {
                            var errors = m._errors[controlName];
                            nestedForm.addErrorMessage(controlName, errors.join('<br/>'));
                            hasError = true;
                        }
                    }
                    if (false === hasError) {
                        console.log("yiiha: closing dialog");
                        jContent.dialogg("destroy");
                    }
                });
                return false;
            }, options);


        });
    })(jQuery);
</script>
</body>
</html>