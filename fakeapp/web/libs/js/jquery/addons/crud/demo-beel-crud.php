<?php


use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;
use WebModule\Komin\Base\Db\Mysql\QuickMysql;
use WebModule\Komin\Beel\ListRenderer\CrudAdminTableListRenderer;
use WebModule\Komin\User\UserToken\UserTokenTool;

require_once 'alveolus/bee/boot/bam1.php';

$items = QuickMysql::fetchAll('select * from jettmp.nassap_employe limit 0,10');


$token = UserTokenTool::connect([
    'login' => 'ling',
    'pass' => 'ling',
]);


AssetCalls::getInst()->callLib([
    'jquery',
    'uii',
    'ajaxtim',
    'pea',
    'beel',
]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <?php echo AdrTool::getAdrMeta(); ?>
    <title>Beel Crud demo page</title>
</head>

<body>

<h1>Beel Crud Demo</h1>

<h2>Crud AdminTable list</h2>

<div id="log"></div>
<div class="bcrudcontainer">

    Like search mode:
    <select class="likemode">
        <option value="mysql">Mysql</option>
        <option value="default" selected="selected">Default</option>
    </select>

    <div class="tableholder"></div>
    <div class="multipleactions">
        <button class="deleteall">delete all</button>
    </div>
    <div class="pagination">
        <div class="bloc nav-widget">
            <button class="first">&#x21E4;</button>
            <button class="prev">&#x21E0;</button>

            <input class="gotobox" type="text" value="1"/>
            <button class="gotobutton">&#x27f2;</button>


            <button class="next">&#x21E2;</button>
            <button class="last">&#x21E5;</button>
        </div>
        <div class="bloc info-widget">
            <span class="nbpages"></span> pages total
        </div>
        <div class="bloc lastbloc nbitems-widget">
            Nb items per page:
            <input class="nbitemsbox" type="text" value="5"/>
            <button class="nbitemsbutton">&#x27f2;</button>
        </div>
    </div>
</div>

<script>
    (function ($) {
        $(document).ready(function () {

            var jContainer = $('.bcrudcontainer');


            var widgets = new window.beelCrudWizardWidgets({
                container: jContainer,
                nbItemsPerPage: 10
            });


            var wiz = new window.beelCrudWizard({
                container: jContainer,
                serviceId: 'pragmatikCrudServer',
                url: 'service/ajaxservice.php',
                crudReadId: 'r.jettmp.nassap_employe',
                crudDeleteId: 'd.jettmp.nassap_employe'
            });


            // before we start the wizard, let's take the widgets into accounts
            widgets.bind(wiz);
            // now, let's start the wizard
            wiz.start();


        });
    })(jQuery);
</script>

</body>
</html>


