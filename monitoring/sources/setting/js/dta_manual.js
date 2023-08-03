jQuery(document).ready(function ($) {
  ot = $("#datatable").dataTable({
    sScrollY: "100%",

    aLengthMenu: [
      [20, 35, 70, -1],
      [20, 35, 70, "All"],
    ],
    bSort: true,
    bFilter: true,
    iDisplayStart: 0,
    iDisplayLength: 20,
    sPaginationType: "full_numbers",
    sDom: "Rfrtlip",
    // "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
    fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      jQuery("td:first", nRow).html(iDisplayIndexFull + 1 + ".");
      return nRow;
    },
  });
  //! Untuk Style MENYAMBUNG TABLE, AWALNYA HEADER DAN BODY KEPOTONG
  $(".dataTables_scrollHeadInner > table")
    .css("border-bottom", "0")
    .css("padding-bottom", "0")
    .css("margin-bottom", "0");
  $(".dataTables_scrollBody > table")
    .css("border-top", "0")
    .css("margin-top", "-5px");

  $("#par\\[filter\\]").on("keyup", function () {
    // ot.fnFilter($(this).val()).draw();
    var text = $("#par\\[filter\\]").val();
    $("#par\\[filter\\]").val(text);
    console.log(text);
  });
});
