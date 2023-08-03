<?php
//by DPS
global $s, $par, $menuAccess, $cID;

$path_file     = "../apps/";




$arr_status_access = [
    0 => "<img src='" . APP_URL . "/styles/images/f.png' title='Tidak Aktif'>",
    1 => "<img src='" . APP_URL . "/styles/images/t.png' title='Aktif'>"
];

switch ($par['mode']) {

    case 'add':
        form();
        break;

    case 'edit':
        form();
        break;

    case 'delete':
        delete();
        break;

    case 'remove':
        remove();
        break;

    default:
        view();
        break;
}

function delete()
{

    global $par;

    $res = db("DELETE FROM `mobile_version_controls` WHERE `id` = '$par[id]'");

    if ($res) alertDeleteSuccess();
    else alertDeleteFailed();

    reloadPage($par, "mode, id");
}

function remove()
{

    global $par, $path_file;

    $res = db("SELECT * FROM `mobile_version_controls` WHERE `id` = '$par[id]'");

    $row = mysql_fetch_assoc($res);

    unlink($path_file . $row['target']);

    $res = db("UPDATE `mobile_version_controls` SET `target` = '', `updated_by` = '$cID', `updated_at` = '" . date('Y-m-d H:i:s') . "' WHERE `id` = '$par[id]'");

    if ($res) alertDeleteSuccess();
    else alertDeleteFailed();

    $par['mode'] = 'edit';
    reloadPage($par);
}

function view()
{

    global $arrTitle, $s, $menuAccess, $par, $cID, $path_file, $arr_status_access;

    if ($_GET["json"] == 1) {

        header("Content-type: application/json");

        $res = db("SELECT * FROM `mobile_version_controls` ORDER BY `created_at`, `version`");

        $ret = [];
        while ($row = mysql_fetch_assoc($res)) {

            $row['release']     = getTanggal($row['release']);

            $row['uploader']    = getField("SELECT `namaUser` FROM `app_user` WHERE `idPegawai` = '$row[created_by]'");
            $row['download']    = empty($row['target']) ? "-" : "<a href='" . APP_URL . "/" . $path_file . "$row[target]'><img src='" . APP_URL . "/images/material/outline_cloud_download_black_48dp.png' style='width: 25px; height: 25px;' /></a>";

            $row['status']        = $arr_status_access[$row['status']];

            $ret[] = $row;
        }

        echo json_encode(array("sEcho" => 1, "aaData" => $ret));
        exit();
    }

?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
                <tr>
                    <th width="20">No</th>
                    <th width="100">Versi</th>
                    <th width="100">Release</th>
                    <th width="140">Uploader</th>
                    <th width="*">Change Log</th>
                    <th width="80">Download</th>
                    <th width="50">Status</th>
                    <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                        <th width="80">Kontrol</th>
                    <?php endif; ?>
                </tr>
            </thead>
        </table>

    </div>

    <script type="text/javascript">
        ot = jQuery("#datatable").dataTable({
            "sScrollY": "100%",
            "aLengthMenu": [
                [15, 35, 70, -1],
                [15, 35, 70, "All"]
            ],
            "bSort": true,
            "bFilter": true,
            "iDisplayStart": 0,
            "iDisplayLength": 15,
            "sPaginationType": "full_numbers",
            "sAjaxSource": "ajax.php?json=1<?= getPar($par, "filterGroup") ?>",
            "aoColumns": [

                {
                    "mData": null,
                    "sWidth": "20px",
                    "bSortable": true,
                    "sClass": "alignCenter"
                },
                {
                    "mData": "version",
                    "bSortable": true,
                    "sClass": "alignCenter"
                },
                {
                    "mData": "release",
                    "bSortable": true,
                    "sClass": "alignCenter"
                },
                {
                    "mData": "uploader",
                    "bSortable": true
                },
                {
                    "mData": "change_log",
                    "bSortable": false
                },
                {
                    "mData": "download",
                    "bSortable": false,
                    "sClass": "alignCenter"
                },
                {
                    "mData": "status",
                    "bSortable": false,
                    "sClass": "alignCenter"
                },

                <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?> {
                        "mData": null,
                        "sWidth": "80px",
                        "bSortable": false,
                        "sClass": "alignCenter",
                        "fnRender": function(o) {

                            var ret = '';

                            <?php if (isset($menuAccess[$s]['edit'])) : ?>
                                ret += `<a class='edit' title='Edit Data' onclick='openBox("popup.php?par[mode]=edit&par[id]=` + o.aData['id'] + `<?= getPar($par, "mode, id") ?>", 1000, 600);'><span>Edit Data</span></a>`;
                            <?php endif ?>

                            <?php if (isset($menuAccess[$s]['delete'])) : ?>
                                ret += `<a href='?par[mode]=delete&par[id]=` + o.aData['id'] + `<?= getPar($par, "mode, id") ?>'' class='delete' title='Delete Data' onclick='return confirm("Apakah anda ingin menghapus data ini?")'><span>Delete Data</span></a>`;
                            <?php endif; ?>

                            return ret;
                        }
                    }
                <?php endif; ?>
            ],
            "aaSorting": [
                [0, "asc"]
            ],
            "fnInitComplete": function(oSettings) {
                oSettings.oLanguage.sZeroRecords = "No data available";
            },
            "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
                return nRow;
            },
            "bProcessing": true,
            "oLanguage": {
                "sProcessing": '<img src="<?= APP_URL ?>/styles/images/loader.gif" />'
            }
        });

        jQuery("#datatable_wrapper #datatable_filter").css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
        jQuery("#datatable_wrapper #datatable_filter > label > img").css("margin-top", "8px");

        jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'>`);
        jQuery("#datatable_wrapper #right_panel").append(`&nbsp;&nbsp;<a class='btn btn1 btn_document' onclick='openBox("popup.php?par[mode]=add<?= getPar($par, 'mode, id'); ?>", 1000, 600)'><span>Tambah Data</span></a>`);

        function joinTable() {
            jQuery(".dataTables_scrollHeadInner > table").css("border-bottom", "0").css("padding-bottom", "0").css("margin-bottom", "0");
            jQuery(".dataTables_scrollBody > table").css("border-top", "0").css("margin-top", "-5px");
        }
    </script>

    </div>

<?php
}

function form()
{

    global $s, $arrTitle, $menuAccess, $inp, $par, $_submit, $cID, $path_file;

    $arr_active = [1 => "Aktif", 0 => "Tidak aktif"];

    $res = db("SELECT * FROM `mobile_version_controls` WHERE `id` = '$par[id]'");
    $row = mysql_fetch_assoc($res);

    $row['version'] = $row['version'] ?: date("Y.m.d");
    $row['release'] = $row['release'] ?: date("Y-m-d");

    $row['status']  = isset($row['status']) ? $row['status'] : 1;


    if (isset($inp['submit'])) {
        // var_dump($inp);
        // die();



        $target = uploadFile($_FILES['apk'], $inp['version'], $path_file);
        // $target = uploadFile($_FILES['apk'], "meteran", $path_file);
        $target = $target ? $target : false;

        if ($par['mode'] == 'add') {
            // var_dump($target);
            // die();
            $sql = "INSERT INTO `mobile_version_controls` SET 
            
                `version`       = '$inp[version]',
                `release`       = '" . setTanggal($inp[release]) . "',
                `change_log`    = '$inp[change_log]',
                `status`        = '$inp[status]',
                `target`        = '$target',
                `created_by`    = '$cID',
                `created_at`    = '" . date('Y-m-d H:i:s') . "'
            
            ";

            if (db($sql)) alertInsertSuccess();
            else alertInsertFailed();
        } else if ($par['mode'] == 'edit') {

            $target = $target ?: getField("SELECT `target` FROM `mobile_version_controls` WHERE `id` = '$par[id]'");

            // var_dump($target);
            // die();
            $sql = "UPDATE `mobile_version_controls` SET 
            
                `version`       = '$inp[version]',
                `release`       = '" . setTanggal($inp[release]) . "',
                `change_log`    = '$inp[change_log]',
                `status`        = '$inp[status]',
                `target`        = '$target',
                `updated_by`    = '$cID',
                `updated_at`    = '" . date('Y-m-d H:i:s') . "'
                WHERE `id`      = '$par[id]'
            
            ";

            // var_dump($sql);
            // die();

            if (db($sql)) alertUpdateSuccess();
            else alertUpdateFailed();
        }

        reloadParentPage($par, "mode, id");
        // die();
    }


?>

    <div class="pageheader">
        <h1 class="pagetitle">Setting Aplikasi</h1>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <form name="form" method="POST" action="?<?= getPar($par) ?>" id="form" class="stdform" autocomplete="off" enctype="multipart/form-data" onsubmit="return validation(document.form);">
            <div style="top:13px; right:35px; position:absolute">
                <input type="submit" class="submit radius2" name="inp[submit]" value="Simpan" />
            </div>

            <fieldset>

                <legend>Aplikasi</legend>

                <p>
                    <label class="l-input-small">Versi</label>
                    <div class="field">
                        <input type="text" name="inp[version]" id="inp[version]" class="smallinput" value="<?= $row['version'] ?>" />
                    </div>
                </p>

                <p>
                    <label class="l-input-small">Release</label>
                    <div class="field">
                        <input type="text" name="inp[release]" id="inp[release]" class="hasDatePicker" value="<?= getTanggal($row['release']); ?>" />
                    </div>
                </p>

                <p>
                    <label class="l-input-small">Status</label>
                    <div class="field">
                        <?php foreach ($arr_active as $key => $value) : $checked = $row['status'] == $key ? "checked" : ""; ?>
                            <input type="radio" name="inp[status]" value="<?= $key ?>" class="sradio" <?= $checked ?> /><?= $value ?>&nbsp;
                        <?php endforeach; ?>
                    </div>
                </p>

                <p>
                    <label class="l-input-small">APK</label>
                    <div class="field">
                        <?php if (empty($row['target'])) : ?>
                            <input type="text" name="fileTemp" id="fileTemp" class="smallinput" />
                            <div class="fakeupload">
                                <input type="file" name="apk" accept=".apk" class="realupload" onchange="this.form.fileTemp.value = this.value;" />
                            </div>
                        <?php else : ?>
                            <a href="<?= APP_URL ?>/<?= $path_file . $row['target'] ?>"><img src="<?= APP_URL ?>/images/material/outline_cloud_download_black_48dp.png" style="width: 25px; height: 25px;" /></a>
                            &nbsp;
                            <a href="?par[mode]=remove<?= getPar($par, "mode") ?>" class="action delete" onclick="return confirm('Konfirmasi hapus apk?')">Delete</a>
                            <br clear="all">
                        <?php endif; ?>
                    </div>
                </p>

            </fieldset>

            <br>
            <h4>Change Log</h4>
            <hr>
            <textarea name="inp[change_log]" id="inp[change_log]" class="tinymce"><?= $row[change_log] ?></textarea>

        </form>

        <script type="text/javascript" src="<?= APP_URL ?>/plugins/tinymce/jquery.tinymce.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function() {

                jQuery('textarea.tinymce').tinymce({
                    script_url: '<?= APP_URL ?>/plugins/tinymce/tiny_mce.js',
                    theme: "advanced",
                    skin: "themepixels",
                    width: "100%",
                    plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
                    inlinepopups_skin: "themepixels",
                    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent,blockquote,formatselect,fontselect,fontsizeselect",
                    theme_advanced_buttons2: "pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,image,help,code,|,preview,|,forecolor,backcolor,removeformat,|,charmap,media,|,fullscreen",
                    theme_advanced_buttons3: "table,tablecontrols",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    theme_advanced_statusbar_location: "bottom",
                    theme_advanced_resizing: true,
                    force_br_newlines: true,
                    force_p_newlines: false,
                    convert_newlines_to_brs: false,
                    remove_linebreaks: true,
                    forced_root_block: '',
                    content_css: "plugins/tinymce/tinymce.css",
                    template_external_list_url: "lists/template_list.js",
                    external_link_list_url: "lists/link_list.js",
                    external_image_list_url: "lists/image_list.js",
                    media_external_list_url: "lists/media_list.js",
                    table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
                    table_cell_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
                    table_row_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
                    table_cell_limit: 100,
                    table_row_limit: 10,
                    table_col_limit: 5,
                    setup: function(ed) {
                        ed.onKeyDown.add(function(ed, evt) {
                            if (evt.keyCode === 9) {

                                ed.execCommand('mceInsertRawHTML', false, '\x09');
                                evt.preventDefault();
                                evt.stopPropagation();

                                return false;
                            }
                        });
                    }
                });
            });
        </script>

    <?php
}

function alertInsertSuccess()
{
    echo "<script>alert('Data berhasil disimpan');</script>";
}
function alertInsertFailed()
{
    echo "<script>alert('Data gagal disimpan!');</script>";
}
function alertUpdateSuccess()
{
    echo "<script>alert('Data berhasil dirubah');</script>";
}
function alertUpdateFailed()
{
    echo "<script>alert('Data gagal dirubah!');</script>";
}
function alertDeleteSuccess()
{
    echo "<script>alert('Data berhasil dihapus');</script>";
}
function alertDeleteFailed()
{
    echo "<script>alert('Data gagal dihapus!');</script>";
}

function reloadParentPage($par, $remove)
{
    echo "<script>parent.window.location = 'index.php?" . getPar($par, $remove) . "';</script>";
}

function reloadPage($par, $remove)
{
    echo "<script>window.location = '?" . getPar($par, $remove) . "';</script>";
}
//   // TENNO 

function uploadFile($file, $file_rename = "", $directory)
{

    // if ( !empty($file['tmp_name']) ) {

    if (!is_dir($directory)) mkdir($directory, 0755, true);

    $file_temp = $file['tmp_name'];
    $file_name = $file['name'];

    $extension = explode(".", $file_name);
    $file_renamed = empty($file_rename) ? $file_name : $file_rename . "." . end($extension);

    $file_renamed = str_replace("/", ".", $file_renamed);

    move_uploaded_file($file_temp, $directory . "/" . $file_renamed);

    return $file_renamed;
    // }

    // return false;
}
