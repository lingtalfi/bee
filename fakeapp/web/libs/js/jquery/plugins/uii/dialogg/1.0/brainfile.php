<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Resizz</title>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="/lib/js/jquery/uii/dragg/1.03/dragg.js"></script>
    <script src="/lib/js/jquery/uii/dialogg/dialogg.js"></script>
    <script src="/lib/js/jquery/uii/resizz/resizz.js"></script>
    <link rel="stylesheet" href="/lib/js/jquery/uii/resizz/resizz.css">
    <link rel="stylesheet" href="/lib/js/jquery/uii/dialogg/dialogg.css">

</head>


<div id="mycontent">
    <p>I'm a basic dialogg</p>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias amet animi, asperiores dolor eos exercitationem expedita hic id ipsam itaque nulla
        obcaecati odit, omnis perspiciatis qui quis rem reprehenderit vitae.</p>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias amet animi, asperiores dolor eos exercitationem expedita hic id ipsam itaque nulla
        obcaecati odit, omnis perspiciatis qui quis rem reprehenderit vitae.</p>
</div>
<div id="mycontent2" style="display: none">
    <p>I'm another basic dialogg</p>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias amet animi, asperiores dolor eos exercitationem expedita hic id ipsam itaque nulla
        obcaecati odit, omnis perspiciatis qui quis rem reprehenderit vitae.</p>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias amet animi, asperiores dolor eos exercitationem expedita hic id ipsam itaque nulla
        obcaecati odit, omnis perspiciatis qui quis rem reprehenderit vitae.</p>
</div>


<button id="changecontent">Change content</button>
<button id="opencontent">Open content</button>
<script>
    (function ($) {


        $("#mycontent").dialogg({
            title: "Dialogg",
//            show: false,
//            modal: true,
            buttons: [
                {
                    'text': "Open new dialog",
                    'click': function (e) {

                        $("#mycontent").dialogg("destroy");

//                        $("#mycontent2").dialogg({
//                            title: "Dialogg2",
//                            modal: true,
//                            buttons: [
//                                {
//                                    'text': "Ok",
//                                    'click': function (e) {
//
//                                    }
//                                }
//                            ]
//                        });
                    }
                }
            ]
        });


        $("#changecontent").click(function () {
            $("#mycontent").dialogg("content",
                "posjd fiz eihzoeuhr zoiueh ",
                "new title",
                {

                    width: 200,
                    height: 500

                });
            return false;
        });


        $("#opencontent").click(function () {
            $("#mycontent").dialogg("open");
        });


    })(jQuery);
</script>
</body>
</html>