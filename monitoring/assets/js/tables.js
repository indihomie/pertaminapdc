jQuery.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {

    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": oSettings._iDisplayLength,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};

jQuery(document).ready(function () {

    _page = parseInt(document.getElementById("_page").value);
    _len = parseInt(document.getElementById("_len").value);

    jQuery('#dynscroll_v').dataTable({
        "sScrollY": "235px",
        "bInfo": null,
        "bPaginate": false,
        "bLengthChange": false,
        "bSort": false,
        "bFilter": false,
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },

    });

    jQuery('#dynscroll_hv').dataTable({
        "sScrollY": "225px",
        "sScrollX": "100%",
        "bInfo": null,
        "bPaginate": false,
        "bLengthChange": false,
        "bSort": false,
        "bFilter": false,

    });

    var dTable1 = jQuery('#dynscroll').dataTable({
        "sScrollX": "100%",
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "bSort": false,
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable1.fnPageChange(_page);

    var dTable2 = jQuery('#dyntable').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "bSort": false,
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable2.fnPageChange(_page);

    var dTable3 = jQuery('#dynreport').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "bSort": true,
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        },
        "aoColumnDefs": [
            {'bSortable': false, 'aTargets': [0]}
        ],
    });
    dTable3.fnPageChange(_page);

    for (i = 1; i <= 10; i++) {

        var dTable4 = jQuery('#dyntable' + i).dataTable({
            "sPaginationType": "full_numbers",
            "iDisplayLength": _len,
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "bSort": false,
            "bFilter": false,
            "sDom": "rt<'bottom'lip><'clear'>",
            "oLanguage": {
                "sEmptyTable": "&nbsp;"
            },
            "fnDrawCallback": function () {
                jQuery("#_page").val(this.fnPagingInfo().iPage);
                jQuery("#_len").val(this.fnPagingInfo().iLength);
            }
        });

        dTable4.fnPageChange(_page);
    }

    var dTable5 = jQuery('#dynpopup').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": 5,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "bSort": false,
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable5.fnPageChange(_page);


    var dTable6 = jQuery('#dynsimple').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "bSort": false,
        "bFilter": false,
        "bLengthChange": false,
        "bInfo": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable6.fnPageChange(_page);

    for (i = 1; i <= 10; i++) {

        var dTable7 = jQuery('#dynsimple' + i).dataTable({
            "sPaginationType": "full_numbers",
            "iDisplayLength": 5,
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "bSort": false,
            "bFilter": false,
            "bLengthChange": false,
            "bInfo": false,
            "sDom": "rt<'bottom'lip><'clear'>",
            "oLanguage": {
                "sEmptyTable": "&nbsp;"
            },
            "fnDrawCallback": function () {
                jQuery("#_page").val(this.fnPagingInfo().iPage);
                jQuery("#_len").val(this.fnPagingInfo().iLength);
            }
        });

        dTable7.fnPageChange(_page);
    }

    var dTable8 = jQuery('#table_batal').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "aoColumns": [
            {'bSortable': false},
            null,
            null,
            null,
            null,
            null,
            null
        ],
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable8.fnPageChange(_page);

    var dTable9 = jQuery('#table_bayar').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "aoColumns": [
            {'bSortable': false},
            null,
            null,
            null,
            null,
            null,
            null,
            {'bSortable': false}
        ],
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable9.fnPageChange(_page);

    var dTable10 = jQuery('#table_transaksi').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "aoColumns": [
            {'bSortable': false},
            null,
            null,
            null,
            null,
            null,
            null,
            {'bSortable': false},
            {'bSortable': false}
        ],
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable10.fnPageChange(_page);

    var dTable11 = jQuery('#table_diskon').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "aoColumns": [
            {'bSortable': false},
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable11.fnPageChange(_page);

    var dTable11 = jQuery('#table_per_menu').dataTable({
        "sPaginationType": "full_numbers",
        "iDisplayLength": _len,
        "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
        "aoColumns": [
            {'bSortable': false},
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ],
        "bFilter": false,
        "sDom": "rt<'bottom'lip><'clear'>",
        "oLanguage": {
            "sEmptyTable": "&nbsp;"
        },
        "fnDrawCallback": function () {
            jQuery("#_page").val(this.fnPagingInfo().iPage);
            jQuery("#_len").val(this.fnPagingInfo().iLength);
        }
    });
    dTable11.fnPageChange(_page);

    jQuery('input:radio').uniform();
    jQuery('input:checkbox').uniform();
});


