<?php

$crudId = 'undefined';
if (array_key_exists('table', $params)) {
    $crudId = $params['table'];
}



?>
<div class="bcrudcontainer">


    <div>
        <a class="newrecord" href="#">Insert a new record</a><span class="ajaxloader hidden"></span>
    </div>
    <br>

    Like search mode:
    <select class="likemode">
        <option value="mysql">Mysql</option>
        <option value="default" selected="selected">Default</option>
    </select>

    <div class="tableholder"></div>
    <div class="multipleactions">
        <button class="multiple-action-delete">delete all</button>
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

            //------------------------------------------------------------------------------/
            // INIT
            //------------------------------------------------------------------------------/
            var crudId = "<?php echo $crudId; ?>";
            var serverId = 'pragmatikCrudServer';
            var serviceId = 'crud';
            var serverUrl = 'service/ajaxserver.php';
            var jContainer = $('.bcrudcontainer');

            var crud = new window.crudAutoAdmin.wizard({
                container: jContainer,
                serverUrl: serverUrl,
                serverId: serverId,
                serviceId: serviceId,
                crudId: crudId
            });

            /**
             * Could be generated by php
             */
            crud.setClickAction('edit', function (jTarget, oWizard, rowValues) {
                /**
                 * Assuming ajaxloader.css is loaded
                 **/
                var jLoader = jTarget.next('.ajaxloader');
                if (0 === jLoader.length) {
                    jLoader = $('<span class="ajaxloader"></span>');
                    jTarget.after(jLoader);
                }

                var callParams = {
                    mode: 'auto',
                    crudId: crudId,
                    type: 'rowAction',
                    rowValues: rowValues,
                    actionName: 'edit',
                    actionPhase: 'form'
                };
                var onFormReady = function (jForm) {
                    
                    jLoader.remove();
                };
                var postParams = {
                    mode: 'auto',
                    crudId: crudId,
                    type: 'rowAction',
                    rowValues: rowValues,
                    actionName: 'edit',
                    actionPhase: 'update'
                };
                oWizard.getBeefWizard().callAndPost(callParams, postParams, function (r) {
                    if ('ok' === r) {
                        oWizard.refreshList();
                    }
                }, onFormReady);
            });
            
            crud.setRowClickAction('delete', function (response, jTarget, oWizard, rowValues) {
                if ('ok' === response) {
                    oWizard.refreshList();
                }
            });
            
            crud.setMultipleRowsClickAction('delete', function (response, jTarget, oWizard, rows) {
                oWizard.refreshList();
            });
            
            
            
//            $(".newrecord:first").trigger('click');

        });
    })(jQuery);
</script>