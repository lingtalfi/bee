<?php

require_once 'alveolus/bee/boot/bam1.php';



use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;



AssetCalls::getInst()->callLib([
    'jquery',
    'array2ul',
    'pea',
    'beast',
]);


?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <?php echo AdrTool::getAdrMeta(); ?>

    <title>Html page</title>
</head>

<body>

<div id="zelog"></div>

<script>
    (function ($) {
        $(document).ready(function () {



            var b = new window.beastEngine();
            var d = new window.testDisplayer();
            b.test(function(oMsg){
                oMsg.msg = "ok";
                return true;
            });
            d.inject(b, $('#zelog'));
            
            console.log(b.getResults());



        });
    })(jQuery);
</script>

</body>
</html>