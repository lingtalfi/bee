<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <script src="http://localcdn/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://localcdn/ajax/libs/pea/lib/1.0/pea-1.0.js"></script>
    <script src="/lib/js/jquery/uii/listt/_/listt.js"></script>



    <link rel="stylesheet" href="/lib/js/jquery/uii/listt/_/listt.css">
    <title>Html page</title>
</head>

<body>

<style>

    #poo {
        width: 300px;
    }

    #logbox {
        background: #ddd;
        height: 100px;
        overflow-y: scroll;
    }


</style>


<ul id="poo" class="listt"></ul>


<hr/>
<h3>Api demo</h3>

<button id="getvalues">get values (check console.log)</button>
<button id="togglemultiple">toggle multiple</button>
<button id="getselection">get selection (check console.log)</button>
<button id="setvalues">set values</button>
<button id="append">append item 13=car in root</button>
<button id="appendgroup">append item 14=bike in group europe</button>
<button id="prependgroup">prepend item 15=plane in group chine</button>
<button id="insertitembefore">insert item 16=piano before key=4 (allemagne)</button>
<button id="insertitemafterreplace">insert existing item 5=Shanghai after key=6 (Pékin)</button>
<button id="disable">disable item 3=france</button>
<button id="enable">enable item 3=france</button>
<button id="disablegroup">disable group asie</button>
<button id="enablegroup">enable group asie</button>
<button id="disableitems">disable items 1, 4, 6</button>
<button id="disableall">disable all</button>
<button id="enableall">enable all</button>
<button id="selectitems">select items 0, 3</button>
<button id="deselectall">deselect all</button>
<button id="selectall">select all</button>
<button id="selectitem">select item 1=banana</button>
<button id="deselectitem">deselect item 1=banana</button>
<div id="logbox"></div>

<script>
    (function ($) {


        var jLog = $("#logbox");
        var jTarget = $("#poo");

        function toLog(msg) {
            jLog.append(msg + '<br />');
            jLog.scrollTop(jLog[0].scrollHeight);

        }

        jTarget.listt({
            values: [
                [0, 'apple'],
                [1, 'banana'],
                ['d"oo', 'ch"erry'],
                ['europe', [
                    ['_label', "Europe"],
                    [3, "france"],
                    [4, "allemagne"]
                ]],
                ['asie', [
                    ['_label', "Asie"],
                    ['chine', [
                        ['_label', "Chine"],
                        [5, "Shanghai"],
                        [6, "Pékin"]
                    ]],
                    [7, "Japon"]
                ]]
            ],
            click: function (key, value, jItem, jList) {
                toLog("Clicked on " + key + ' (' + value + ')');
            }
        });


        $("#getvalues").on('click', function () {
            var values = jTarget.listt("getValues");
            console.log(values);
        });


        var isMultiple = false;
        $("#togglemultiple").click(function () {
            isMultiple = !isMultiple;
            var s = isMultiple ? "activated" : "deactivated";
            toLog("Multiple mode is now " + s);
            jTarget.listt("option", "multiple", isMultiple);
        });

        $("#getselection").click(function () {
            var sel = jTarget.listt("getSelection");
            console.log(sel);
        });

        $("#setvalues").click(function () {
            var values = [
                [0, 'karate'],
                [1, 'judo'],
                [2, 'kung fu'],
                ['batman', [
                    ['_label', "Batman"],
                    [3, "batmobile"],
                    [4, "batarang"]
                ]],
                ['spiderman', [
                    ['_label', "Spiderman"],
                    ['colors', [
                        ['_label', "colors"],
                        [5, "red"],
                        [6, "blue"]
                    ]],
                    [7, "spider web"]
                ]]
            ];
            jTarget.listt("setValues", values);
        });


        $("#append").click(function () {
            jTarget.listt("appendItem", 13, 'car');
        });


        $("#appendgroup").click(function () {
            jTarget.listt("appendItem", 14, 'bike', true, 'europe');
        });

        $("#prependgroup").click(function () {
            jTarget.listt("prependItem", 15, 'plane', true, 'chine');
        });

        $("#insertitembefore").click(function () {
            jTarget.listt("insertItemBefore", 16, 'piano', 4);
        });

        $("#insertitemafterreplace").click(function () {
            jTarget.listt("insertItemAfter", 5, 'Shanghai', 6);
        });

        $("#disable").click(function () {
            jTarget.listt("disableItem", 3);
        });

        $("#enable").click(function () {
            jTarget.listt("enableItem", 3);
        });

        $("#disablegroup").click(function () {
            jTarget.listt("disableGroup", 'asie');
        });

        $("#enablegroup").click(function () {
            jTarget.listt("enableGroup", 'asie');
        });

        $("#disableitems").click(function () {
            jTarget.listt("disableItems", [1, 4, 6]);
        });

        $("#disableall").click(function () {
            jTarget.listt("disableAll");
        });
        $("#enableall").click(function () {
            jTarget.listt("enableAll");
        });

        $("#selectitems").click(function () {
            jTarget.listt("selectItems", [0, 3]);
        });


        $("#selectall").click(function () {
            jTarget.listt("selectAll");
        });
        $("#deselectall").click(function () {
            jTarget.listt("deselectAll");
        });
        $("#selectitem").click(function () {
            jTarget.listt("selectItem", 1);
        });
        $("#deselectitem").click(function () {
            jTarget.listt("deselectItem", 1);
        });


    })(jQuery);
</script>

</body>
</html>