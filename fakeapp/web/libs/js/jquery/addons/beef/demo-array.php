<?php


    
require_once 'alveolus/bee/boot/bam1.php';




use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use Bee\Application\Config\Util\FeeConfig;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;



AssetCalls::getInst()->callLib([
    'assetloader',
    'array2ul',
    'jquery',
    'jutil',
    'ajaxtim',
    'uii',
    'jqueryui',
    'beef',
    'pea',
    'bdot',
]);



$f = __DIR__ . '/app/config/parameters.yml';
$values = FeeConfig::readFile($f);





?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <?php echo AdrTool::getAdrMeta(); ?>
    <title>Beef Array demo</title>

    <style>
        .beef-error {
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

<h1>Beef demo</h1>

<h2>Array</h2>

<form id="zeform" action="#" method="post">
    <div>
        Name:
        <input type="text" name="name">
        <span class="tip">Default error message</span>
    </div>
    <div>
        Job:
        <input data-beef-error="myerror" type="text" name="job">
        <span class="tip">Custom error message</span>

        <div class="myerror"></div>
    </div>
    <div id="paramsmodule" data-beef-ignore="1">
        Params:
        <ul id="ttt"></ul>
    </div>


    <input class="submit" type="submit" value="Send">
</form>
<h3>What values would be posted</h3>

<div style="white-space: pre" id="zelog"></div>


<script>
    (function ($) {
        $(document).ready(function () {


            var values = <?php echo json_encode($values); ?>;

          

            var jUl = $('#ttt');
            var oArray = new window.beefSimpleArrayControl({
                container: jUl,
                isDeletable: true,
                isClosed: function (realPath, key, level) {
                    return (level > 1);
                },
                onStructureUpdatedAfter: function (v) {
                    $('#zelog').html(JSON.stringify(v));
                    $('#zelog').append(window.array2UlTool.render(v));

                }
            });
            oArray.setValue(values);

            // we always need a reference to the html form
            var jForm = $('#zeform');

            // just a visual output to see what values would be posted in a prod environment.
            var jLog = $('#zelog');


            var oForm = new window.beef.form({
                jForm: jForm,
                dynamicElements: {
                    conf: oArray
                },
                values: {
                    name: "aa", // setting the default values here
                    job: "bb",
                    conf: values
                },
                rules: {
                    conf: {
                        minCount: {
                            min: 1
                        }
                    },
                    name: {
                        minLength: {
                            min: 2
                        }
                    },
                    job: {
                        minLength: {
                            min: 2
                        }
                    }
                }
            });
            oForm.start(function (v) {
                jLog.text(JSON.stringify(v));
            });

        });
    })(jQuery);
</script>

</body>
</html>



