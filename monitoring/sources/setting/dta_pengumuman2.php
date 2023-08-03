<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/pengumuman/";
$fFileE = "files/export/";

if ($_GET["json"] == 1) {

    header("Content-type: application/json");
    $filter1     = !empty($par['filter']) ? "AND `kategoriPengumuman` = '$par[filter]'" : "";
    $sql = "SELECT * FROM dta_pengumuman WHERE idPengumuman IS NOT NULL $filter1";
    $res = db($sql);
    $ret = array();
    while ($r = mysql_fetch_assoc($res)) {
        // $r['iconOrImage'] = "<a href = \"#\" onclick=\"openBox('view.php?doc=filePengumuman&par[idPengumuman]=$r[idPengumuman]" . getPar($par, "mode") . "',725,500);\"><img src = \"" . $fFile . $r[filePengumuman] . "\" height=\"50\"></a>";

        //Judul
        $r['tanggalPengumuman'] = getTanggal($r['tanggalPengumuman']);
        $r['filePengumuman'] = empty($r['filePengumuman']) ? "" : "<a href = \"#\" onclick=\"openBox('view.php?doc=filePengumuman&par[idPengumuman]=$r[idPengumuman]" . getPar($par, "mode") . "',725,500);\"><img src=\"" . getIcon($r['filePengumuman']) . "\"></a>";
        $r["statusPengumuman"] = ($r["statusPengumuman"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";
        $ret[] = $r;
    }
    echo json_encode(array("sEcho" => 1, "aaData" => $ret));
    exit();
}

function xls()
{
    global  $par, $fFileE;
    $direktori = $fFileE;
    $namaFile = "REPORT PENGUMUMAN.xls";
    $judul = "DATA PENGUMUMAN";
    $field = array("No",  "Judul", "Tanggal", "Sumber", "Status");

    $filter1     = !empty($par['filter']) ? " AND  `kategoriPengumuman` = '$par[filter]' " : "";

    $sql = "SELECT * FROM dta_pengumuman WHERE idPengumuman IS NOT NULL $filter1";
    $res = db($sql);
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $no = 0;
    $arrStatus = array('t' => 'Aktif', 'f' => 'Tidak Aktif');

    while ($r = mysql_fetch_array($res)) {
        $r[tanggalPengumuman] = getTanggal($r[tanggalPengumuman]);
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[judulPengumuman] . "\t left",
            $r[tanggalPengumuman] . "\t center",
            $r[sumberPengumuman] . "\t left",
            $arrStatus[$r[statusPengumuman]] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;
    $checkKontrolAccess = (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) ? "<th width=\"20\" style=\"vertical-align: middle\">Kontrol</th>" : "");
    $checkAddAccess = (isset($menuAccess[$s]["add"]) ? "<a href=\"?par[mode]=add" . getPar($par, "mode,idPengumuman") . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>" : "");

?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <input type="text" id="par[filterSearch]" placeholder="Search.." name="par[filterSearch]" value="<?= $par[filterSearch] ?>" style="width:200px;" />
                <?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MKP'", "kodeData", "namaData", "par[filter]", "All Kategori", $par['filter'], "", "200px", "chosen-select") ?>
            </div>
            <div id="pos_r">
                <a href="?par[mode]=xls<?= getPar($par, "mode") ?>" id="btnExport" class="btn btn1 btn_inboxo">
                    <span>Export</span>
                </a>
                <?= $checkAddAccess ?>
            </div>
        </form>
        <br clear="all">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th>Judul</th>
                    <th width="75">Tanggal</th>
                    <th width="50">File</th>
                    <th width="50">Status</th>
                    <?= $checkKontrolAccess ?>
                </tr>
            </thead>
        </table>
    </div>
    <?php
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=REPORT PENGUMUMAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function form()
{
    global $s, $inp, $par, $fFile, $arrModul, $arrTitle, $menuAccess;
    include "plugins/mce.jsp";
    $sql = "SELECT dta_pengumuman.*,app_user.namaUser from dta_pengumuman INNER JOIN app_user ON app_user.idPegawai=dta_pengumuman.updateBy where idPengumuman='$par[idPengumuman]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (empty($r['tanggalPengumuman'])) $r['tanggalPengumuman'] = date("Y-m-d");

    $false =  $r['statusPengumuman'] == "f" ? "checked=\"checked\"" : "";
    $true =  empty($false) ? "checked=\"checked\"" : "";

    $iconOrImage = (strpos($r['filePengumuman'], ".docx") !== false ? "<img src=\" " . getIcon($r['filePengumuman']) . " \" style=\"padding-right:5px; padding-top:10px;\">" : "<img src=\" " . $fFile . $r['filePengumuman'] . " \" width=\"50px\">");
    setValidation("is_null", "inp[judulPengumuman]", "anda harus mengisi judul");
    setValidation("is_null", "inp[sumberPengumuman]", "anda haru mengisi sumber");
    setValidation("is_null", "inp[resumePengumuman]", "anda haru mengisi ringkasan");
    echo getValidation();
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"> <?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <div style="position:absolute; top: 15px; right: 20px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeFaq") ?>';" />
            </div>
            <div id="general" class="subcontent">
                <p>
                    <label class="l-input-small">Judul</label>
                    <div class="field">
                        <input type="text" id="inp[judulPengumuman]" name="inp[judulPengumuman]" value="<?= $r['judulPengumuman'] ?>" class="mediuminput" maxlength="150" style="width:95%;" />
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Tanggal</label>
                    <div class="field">
                        <input type="text" id="inp[tanggalPengumuman]" name="inp[tanggalPengumuman]" value="<?= getTanggal($r['tanggalPengumuman']) ?>" style="width: 335px" class="hasDatePicker" maxlength="150" />
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Sumber</label>
                    <div class="field">
                        <input type="text" id="inp[sumberPengumuman]" name="inp[sumberPengumuman]" value="<?= $r['sumberPengumuman'] ?>" class="mediuminput" maxlength="150" style="width:350px;" />
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Ringkasan</label>
                    <div class="field">
                        <textarea id="inp[resumePengumuman]" name="inp[resumePengumuman]" rows="3" cols="50" class="longinput" style="height:50px; width:95%;"><?= $r['resumePengumuman'] ?></textarea>
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Detail</label>
                    <div class="field">
                        <textarea id="mce1" name="inp[detailPengumuman]" rows="3" cols="50" class="longinput" style="height:50px; width:96.5%;"><?= $r['detailPengumuman'] ?></textarea>
                    </div>
                </p>
                <p>
                    <label class="l-input-small">File</label>
                    <div class="field">
                        <?php if (empty($r['filePengumuman'])) { ?>
                            <input type="text" id="iconTemp" name="iconTemp" class="input" style="width:295px;" maxlength="100" />
                            <div class="fakeupload">
                                <input type="file" id="filePengumuman" name="filePengumuman" class="realupload" size="50" onchange="this.form.iconTemp.value=this.value;" />
                            </div>
                        <?php } else { ?>
                            <?= $iconOrImage ?>
                            <!-- <a href="<?= $fFile ?><?= $r['filePengumuman'] ?> "><img src="<?= getIcon($r['filePengumuman']) ?>" style="padding-right:5px; padding-top:10px;"></a> -->
                            <a href="?par[mode]=delFile<?= getPar($par, "mode") ?>" onclick="return confirm('anda yakin akan menghapus file ?')" class="action delete"><span>Delete</span></a>
                            <br clear="all">
                        <?php } ?>
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Kategori Emergency</label>
                    <div class="field">
                        <?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MKP'", "kodeData", "namaData", "inp[kategoriPengumuman]", "Kategori Pengumuman", "$r[kategoriPengumuman]", "", "200px", "chosen-select") ?>
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Status</label>
                    <div class="fradio">
                        <input type="radio" id="true" name="inp[statusPengumuman]" value="t" <?= $true ?> /> <span class="sradio">Active</span>
                        <input type="radio" id="false" name="inp[statusPengumuman]" value="f" <?= $false ?> /> <span class="sradio">Not Active</span>
                    </div>
                </p>
            </div>
            <fieldset>
                <?= show_history($r) ?>
            </fieldset>
        </form>
    </div>
<?php
}

function ubah()
{
    global $s, $inp, $par, $acc, $fFile, $cUsername;

    $fileIcon = $_FILES["filePengumuman"]["tmp_name"];
    $fileIcon_name = $_FILES["filePengumuman"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none")) {
        fileUpload($fileIcon, $fileIcon_name, $fFile);
        $filePengumuman = "doc-" . $par[idPengumuman] . "." . getExtension($fileIcon_name);
        fileRename($fFile, $fileIcon_name, $filePengumuman);
    }
    if (empty($filePengumuman)) $filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");
    repField();

    $sql = "update dta_pengumuman set tanggalPengumuman='" . setTanggal($inp[tanggalPengumuman]) . "', judulPengumuman='$inp[judulPengumuman]', sumberPengumuman='$inp[sumberPengumuman]', resumePengumuman='$inp[resumePengumuman]', detailPengumuman='$inp[detailPengumuman]', filePengumuman='$filePengumuman', statusPengumuman='$inp[statusPengumuman]',kategoriPengumuman='$inp[kategoriPengumuman]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPengumuman='$par[idPengumuman]'";
    // var_dump($sql);
    // die();
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idPengumuman") . "';</script>";
}

function tambah()
{
    global $s, $inp, $par, $acc, $fFile, $cUsername;
    $idPengumuman = getField("select idPengumuman from dta_pengumuman order by idPengumuman desc") + 1;

    $fileIcon = $_FILES["filePengumuman"]["tmp_name"];
    $fileIcon_name = $_FILES["filePengumuman"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none")) {
        fileUpload($fileIcon, $fileIcon_name, $fFile);
        $filePengumuman = "doc-" . $idPengumuman . "." . getExtension($fileIcon_name);
        fileRename($fFile, $fileIcon_name, $filePengumuman);
    }
    repField("detailPengumuman");

    $sql = "insert into dta_pengumuman (idPengumuman, tanggalPengumuman, judulPengumuman, sumberPengumuman, resumePengumuman, detailPengumuman, filePengumuman, statusPengumuman,kategoriPengumuman, createBy, createTime) values ('$idPengumuman', '" . setTanggal($inp[tanggalPengumuman]) . "', '$inp[judulPengumuman]', '$inp[sumberPengumuman]', '$inp[resumePengumuman]', '$inp[detailPengumuman]', '$filePengumuman', '$inp[statusPengumuman]','$inp[kategoriPengumuman]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,idPengumuman") . "';</script>";
}


function hapusFile()
{
    global $s, $inp, $par, $fFile, $cUsername;
    $filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");
    $fileSame = str_replace(".", "same.", $file);
    $fileThumb = str_replace(".", "thumb.", $file);
    if (file_exists($fFoto . $file) and $file != "") {
        unlink($fFoto . $file);
        unlink($fFoto . $fileSame);
        unlink($fFoto . $fileThumb);
    }

    $sql = "update dta_pengumuman set filePengumuman='' where idPengumuman='$par[idPengumuman]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "'</script>";
}

function hapus()
{
    global $s, $inp, $par, $fFile, $cUsername;
    $filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");
    if (file_exists($fFile . $filePengumuman) and $filePengumuman != "") unlink($fFile . $filePengumuman);

    $sql = "delete from dta_pengumuman where idPengumuman='$par[idPengumuman]'";
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,idPengumuman") . "';</script>";
}

function show_history($r = null)
{
    global $menuAccess, $s, $par;
    $result = "";
    if ($par[mode] == "edit") {
        $result = "
        <fieldset>
            <legend>History</legend>
            <table width=\"100%\" >
                <tr>
                    <td width=\"50%\">
                    <p>
                        <label class=\"l-input-small2\" >Created Date</label>
                        <span class=\"field\" id=\"created_date\">
                        " . $r[createTime] . "

                        </span>
                    </p>	
                    </td> 
                    <td >
                        <p>
                            <label class=\"l-input-small2\" >Created By</label>
                            <span class=\"field\" id=\"created_by\">
                            " . $r[namaUser] . "

                            </span>
                        </p>	
                    </td> 
                <tr>
                <tr>
                    <td width=\"50%\">
                    <p>
                        <label class=\"l-input-small2\" >Update Date</label>
                        <span class=\"field\" id=\"update_date\">
                        " . $r[updateTime] . "

                        </span>
                    </p>	
                    </td> 
                    <td >
                        <p>
                            <label class=\"l-input-small2\" >Update By</label>
                            <span class=\"field\" id=\"update_by\">
                            " . $r[namaUser] . "

                            </span>
                        </p>	
                    </td> 
                <tr>
            </table>
        </fieldset>";
    } else {
        $result = "";
    }
    return $result;
}



function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "delFile":
            if (isset($menuAccess[$s]["edit"])) $text = hapusFile();
            else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
} ?>

<style type="text/css">
    .alignRight {
        text-align: right;
    }

    .alignCenter {
        text-align: center;
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function($) {

        ot = $('#datatable').dataTable({
            "sScrollY": "100%",

            "aLengthMenu": [
                [20, 35, 70, -1],
                [20, 35, 70, "All"]
            ],
            "bSort": true,
            "bFilter": true,
            "iDisplayStart": 0,
            "iDisplayLength": 20,
            "sPaginationType": "full_numbers",
            "sAjaxSource": "ajax.php?json=1<?= getPar($par, "mode, filterGroup"); ?>",
            "aoColumns": [

                {
                    "mData": null,
                    "sWidth": "20px",
                    "bSortable": false,
                },
                {
                    "mData": "judulPengumuman",
                    "bSortable": true,
                },
                {
                    "mData": "tanggalPengumuman",
                    "bSortable": true,
                    "sClass": "alignCenter"

                },
                {
                    "mData": "filePengumuman",
                    "bSortable": true,
                },
                {
                    "mData": "statusPengumuman",
                    "bSortable": true,
                    "sClass": "alignCenter"

                },
                {
                    "mData": null,
                    "sClass": "alignCenter",
                    "sWidth": "80px",
                    "bSortable": false,
                    "fnRender": function(data) {
                        var ret = '',
                            idPengumuman = data.aData['idPengumuman'];
                        <?php if (isset($menuAccess[$s]['edit'])) { ?>
                            ret += "<a href=\"?par[mode]=edit&par[idPengumuman]=" + idPengumuman + "<?= getPar($par, "mode, idPengumuman"); ?>\"  title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
                        <?php } ?>
                        <?php if (isset($menuAccess[$s]['delete'])) { ?>
                            ret += "<a href=\"?par[mode]=del&par[idPengumuman]=" + idPengumuman + "<?= getPar($par, "mode, idPengumuman"); ?>\"  onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        <?php } ?>

                        return ret;
                    }
                },
            ],
            "sDom": "Rfrtlip",
            // "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
                return nRow;
            },
        });
        //! Untuk Filter Search
        $("#par\\[filterSearch\\]").on('keyup', function() {
            ot.fnFilter($(this).val()).draw();
        });
        //! Untuk Filter DropDown
        $("#par\\[filter\\]").change(function() {
            var data = $(this).val();
            window.location = '?par[filter]=' + data + '	<?= getPar($par, 'filter,mode') ?>';
        });

        //! Untuk Style MENYAMBUNG TABLE, AWALNYA HEADER DAN BODY KEPOTONG
        $(".dataTables_scrollHeadInner > table").css("border-bottom", "0").css("padding-bottom", "0").css("margin-bottom", "0");
        $(".dataTables_scrollBody > table").css("border-top", "0").css("margin-top", "-5px");
    });
</script>