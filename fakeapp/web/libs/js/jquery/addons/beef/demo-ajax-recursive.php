<?php


require_once 'alveolus/bee/boot/bam1.php';




use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;

use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;




AssetCalls::getInst()->callLib([
    'beef',
//    'array2ul',
]);




?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Beef Demo</title>
    <?php echo AdrTool::getAdrMeta(); ?>


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

<h1>Beef demo</h1>

<h2>Recursive ajax calls</h2>

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
    <div id="idealthing" data-beef-ignore="1">
        Your ideal thing:
        <a class="configure" href="#">Click here to configure your ideal thing</a>
        <span class="tip">
            Dynamic element calling a remote form.
        </span>
        <!--  The field below is a helper and is not processed,
         since the parent has the data-beef-ignore attribute to 1 (this is a dynamic element)-->
        <input type="hidden" name="xx" value="irrelevant">

        <div class="thelog"></div>
    </div>
    <input class="submit" type="submit" value="Send">
</form>
<h3>What values would be posted</h3>

<div id="zelog"></div>


<script>
    (function ($) {
        $(document).ready(function () {


            // we always need a reference to the html form
            var jForm = $('#zeform');

            // just a visual output to see what values would be posted in a prod environment.
            var jLog = $('#zelog');


            window.IdealThing = function (params) {
                var jEl = params.element;
                var jLoge = jEl.find('.thelog');
                var jLink = jEl.find('.configure');
                var zis = this;

                var identifier = params.id;
                if (params.identifier) {
                    identifier = params.identifier;
                }

                this.getControl = function () {
                };

                jLink.on('click', function (e) {
                    var data = {
                        id: identifier,
                        values: zis.getValue()
                    };
                    var url = 'service/demo/ajax-recursive/server.php';
                    var options = {};
                    window.beef.util.callForm(data, url, function (values) {
                        zis.setValue(values);
                        jLoge.text(JSON.stringify(values));
                    }, options);
                    return false;
                });
            };

            var oIdealThing = new IdealThing({
                element: $('#idealthing')
            });


            var oForm = new window.beef.form({
                jForm: jForm,
                dynamicElements: {
                    idealPartner: oIdealThing
                },
                values: {
                    name: "aa", // setting the default values here
                    job: "bb"
                },
                rules: {
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



