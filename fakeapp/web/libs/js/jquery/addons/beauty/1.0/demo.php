<?php

require_once 'alveolus/bee/boot/bam1.php';



use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;
use WebModule\Komin\Beast\Beauty\TestFinder\DirMapTestFinder;


$dir = __DIR__ . '/tests';

$f = new DirMapTestFinder($dir, [
    'extensions' => ['php'],
    'fileToUrl' => function ($file) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], 'http://' . $_SERVER['SERVER_NAME'], $file);
    },
]);
$tests = $f->getTests();



AssetCalls::getInst()->callLib([
    'jquery',
    'jqueryui',
    'jutil',
    'beauty',
    'pea',
]);


?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <?php echo AdrTool::getAdrMeta(); ?>

    <title>Html page</title>
</head>

<body>
<div id="beauty-gui-container"></div>


<script>
    (function ($) {
        $(document).ready(function () {


            var tests = <?php echo json_encode($tests); ?>;


            var jContainer = $('#beauty-gui-container');
            var beauty = new window.beauty({
                tests: tests
            });
            beauty.loadTemplateWithJsonP('default', jContainer, function () {
                beauty.start(jContainer);
                beauty.closeAllGroups();
                beauty.openGroups(['myApp.archimede'], true);
            });


        });
    })(jQuery);
</script>

</body>
</html>