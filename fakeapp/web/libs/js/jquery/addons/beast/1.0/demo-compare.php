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

            
            
            function myTest(a){
                return a + 5;
            }

            var values = [
                2,
                6,
                6
            ];
            
            var expected = [
                7,
                12,
                11
            ];

            
            
            var b = new window.beastEngine();
            var d = new window.testDisplayer();
            b.compare(values, expected, myTest, {
                
            });
            d.inject(b, $('#zelog'));
            



        });
    })(jQuery);
</script>

</body>
</html>