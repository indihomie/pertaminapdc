/**
 * The following options are allowed:
l - Length changing
f - Filtering input
t - The table!
i - Information
p - Pagination
r - pRocessing
 */

jQuery(document).ready(function ($) {
  var table = $("#data-table").DataTable({
    bSort: true,
    iDisplayStart: 0,
    iDisplayLength: 10,
    sPaginationType: "full_numbers",
    sDom: "rtipl", // Format Variable Pada Datatable
  });
  $("#searchFilter").keyup(function () {
    table.fnFilter($(this).val()).draw();
  });
  $("#selectFilter").on("change", function () {
    var selectedValue = $(this).val();
    table.fnFilter("^" + selectedValue + "$", 4, true); //Exact value, column, reg
  });
});

jQuery(document).ready(function ($) {
  $("#isiBerita").tinymce({
    script_url: "plugins/TinyMCE/tiny_mce.js",
    theme: "advanced",
    skin: "themepixels",
    width: "100%",
    plugins:
      "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
    inlinepopups_skin: "themepixels",
    theme_advanced_buttons1:
      "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent,blockquote,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2:
      "pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,image,help,code,|,preview,|,forecolor,backcolor,removeformat,|,charmap,media,|,fullscreen",
    theme_advanced_buttons3: "table,tablecontrols",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align: "left",
    theme_advanced_statusbar_location: "bottom",
    theme_advanced_resizing: true,
    force_br_newlines: true,
    force_p_newlines: false,
    convert_newlines_to_brs: false,
    remove_linebreaks: true,
    forced_root_block: "",
    content_css: "plugins/tinymce/tinymce.css",
    template_external_list_url: "lists/template_list.js",
    external_link_list_url: "lists/link_list.js",
    external_image_list_url: "lists/image_list.js",
    media_external_list_url: "lists/media_list.js",
    table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
    table_cell_styles:
      "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
    table_row_styles:
      "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
    table_cell_limit: 100,
    table_row_limit: 10,
    table_col_limit: 5,
    setup: function (ed) {
      ed.onKeyDown.add(function (ed, evt) {
        if (evt.keyCode === 9) {
          ed.execCommand("mceInsertRawHTML", false, "\x09");
          evt.preventDefault();
          evt.stopPropagation();
          return false;
        }
      });
    },
  });
});

function exportF(elem) {
  var dateNow = new Date().toISOString().split("T")[0];
  var table = document.getElementById("data-table");
  var html = table.outerHTML;
  var url = "data:application/vnd.ms-excel," + escape(html); // Set your html table into url
  elem.setAttribute("href", url);
  elem.setAttribute("download", `Berita-${dateNow}.xls`); // Choose the file name
  return false;
}
