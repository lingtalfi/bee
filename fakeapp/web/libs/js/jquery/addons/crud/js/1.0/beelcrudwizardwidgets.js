if ('undefined' === typeof window.beelCrudWizardWidgets) {

    /**
     * This special wizardWidgets uses the following html:
     *
     * - .bcrudcontainer
     * (like mode)
     * ----- select.likemode
     * (multiple actions)
     * ----- .deleteall
     * (pagination)
     * ----- .pagination
     * --------- .nbpages, indicates the number of pages of the last query
     * --------- input.nbitemsbox, contains the value
     * --------- .nbitemsbutton, trigger
     *
     * --------- .first, button to go to the first page
     * --------- .prev, button to go to the previous page if any
     * --------- input.gotobox, indicates the number of the current page
     * --------- .gotobutton, trigger
     * --------- .next, button to go to the next page if any
     * --------- .last, button to go to the last page
     */
    window.beelCrudWizardWidgets = function (params) {
        var params = $.extend({
            container: null,
            useSearchMode: true,
            useNbItemsPerPage: true,
            useNbPages: true,
            nbItemsPerPage: 10, // this value will be used as a starting point
            usePagination: true
            //useDeleteAll: true
        }, params);

        var jContainer = params.container;
        if (null === params.container) {
            throw new Error("container cannot be null");
        }

        var jLikeMode = null;
        var jNbItemsPerPageButton = null;
        var jNbItemsPerPageInput = null;
        var jNbPages = null;
        var jGotoBox = null;
        var jGotoButton = null;
        var jGotoFirst = null;
        var jGotoPrev = null;
        var jGotoNext = null;
        var jGotoLast = null;
        var jDeleteAll = null;


        this.bind = function (oBCrudWizard) {

            if (true === params.useSearchMode) {
                jLikeMode = $('.likemode:first', jContainer);
                jLikeMode.on('change', function () {
                    oBCrudWizard.setLikeMode($(this).val());
                    oBCrudWizard.refresh();
                });
                oBCrudWizard.setLikeMode(jLikeMode.val());
            }
            if (true === params.useNbItemsPerPage) {
                function updateNbItemsPerPage() {
                    oBCrudWizard.setNbItemsMax(jNbItemsPerPageInput.val());
                    oBCrudWizard.refresh();
                }

                jNbItemsPerPageButton = $('.nbitemsbutton:first', jContainer);
                jNbItemsPerPageInput = $('.nbitemsbox:first', jContainer);
                jNbItemsPerPageInput.val(params.nbItemsPerPage);
                jNbItemsPerPageButton.on('click', updateNbItemsPerPage);
                jNbItemsPerPageInput.on('keydown', function (e) {
                    if (13 === e.which) {
                        updateNbItemsPerPage();
                    }
                });

                oBCrudWizard.setNbItemsMax(jNbItemsPerPageInput.val());
            }

            if (true === params.useNbPages) {
                jNbPages = $('.nbpages:first', jContainer);

                function getNbPages() {
                    return parseInt(jNbPages.html());
                }
            }

            if (true === params.usePagination) {
                jGotoBox = $('.gotobox:first', jContainer);
                jGotoButton = $('.gotobutton:first', jContainer);
                jGotoFirst = $('.first:first', jContainer);
                jGotoPrev = $('.prev:first', jContainer);
                jGotoNext = $('.next:first', jContainer);
                jGotoLast = $('.last:first', jContainer);

                function updateCurrentPage(nb) {
                    if ('undefined' !== typeof nb) {
                        n = nb;
                    }
                    else {
                        n = jGotoBox.val();
                    }
                    if (true === params.useNbPages) {
                        if (n < 1) {
                            n = 1;
                        }
                        var nbPages = getNbPages();
                        if (n > nbPages) {
                            n = nbPages;
                        }
                        jGotoBox.val(n);
                        oBCrudWizard.setCurrentPage(n);
                        oBCrudWizard.refresh();
                    }
                    else {
                        console.log("oops, useNbPages=true is required");
                    }
                }

                function getCurrentPage() {
                    return parseInt(jGotoBox.val());
                }


                jGotoBox.on('keydown', function (e) {
                    if (13 === e.which) {
                        updateCurrentPage();
                    }
                });
                jGotoButton.on('click', function () {
                    updateCurrentPage();
                });
                jGotoFirst.on('click', function () {
                    updateCurrentPage(1);
                });
                jGotoPrev.on('click', function () {
                    var n = getCurrentPage() - 1;
                    updateCurrentPage(n);
                });
                jGotoNext.on('click', function () {
                    var n = getCurrentPage() + 1;
                    updateCurrentPage(n);

                });
                jGotoLast.on('click', function () {
                    if (true === params.useNbPages) {
                        updateCurrentPage(getNbPages());
                    }
                    else {
                        console.log("oops, it seems that you need to set useNbPages=true for gotoLast button");
                    }
                });
            }

            //if (true === params.useDeleteAll) {
            //    jDeleteAll = $('.deleteall:first', jContainer);
            //    jDeleteAll.on('click', function () {
            //        oBCrudWizard.deleteSelectedRows(function (m) {
            //            oBCrudWizard.refresh();
            //        });
            //    });
            //}

            oBCrudWizard.setOnRefreshAfter(function (m) {
                if (true === params.useNbPages) {
                    jNbPages.html(m.nbPages);
                }
            });

        };
    };
}