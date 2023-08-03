<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/pesan/";
$fFileE = "files/export/";

if ($_GET["json"] == 1) {

    header("Content-type: application/json");
    // $filter1     = !empty($par['filter']) ? "AND `kategoriPengumuman` = '$par[filter]'" : "";
    $sql = "SELECT * FROM tbl_kontak WHERE kodeKontak IS NOT NULL ";
    $res = db($sql);
    $ret = array();
    while ($r = mysql_fetch_assoc($res)) {
        list($tanggalKontak, $waktuKontak) = explode(" ", $r[createTime]);
        $r["waktuKontak"] = getTanggal($tanggalKontak) . " @ " . substr($waktuKontak, 0, 5);
        $r["statusKontak"] = ($r["statusKontak"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";
        $ret[] = $r;
    }
    echo json_encode(array("sEcho" => 1, "aaData" => $ret));
    exit();
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
                <!-- <?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MKP'", "kodeData", "namaData", "par[filter]", "All Kategori", $par['filter'], "", "200px", "chosen-select") ?> -->
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
                    <th width="20px" ; style="vertical-align:middle;">No</th>
                    <th width="200px" ; style="vertical-align:middle;">Dikirim</th>
                    <th width="200px" ; style="vertical-align:middle;">Oleh</th>
                    <th width="150px" ; style="vertical-align:middle;">Email</th>
                    <th width="*" ; style="vertical-align:middle;">Pesan</th>
                    <th width="50px" ; style="vertical-align:middle;">Status</th>
                    <?= $checkKontrolAccess ?>
                </tr>
            </thead>
        </table>
    </div>
    <?php
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=REPORT PESAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function form()
{
    global $s, $inp, $par, $fFile, $arrModul, $arrTitle, $menuAccess, $cUsername;
    include "plugins/mce.jsp";
    $sql = "SELECT tbl_kontak.*,app_user.namaUser from tbl_kontak INNER JOIN app_user ON app_user.username=tbl_kontak.createBy where kodeKontak='$par[kodeKontak]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    // if (empty($r['tanggalPengumuman'])) $r['tanggalPengumuman'] = date("Y-m-d");
    $checkUsernameForInput = (empty($r['username']) ? "$cUsername" : "$r[username]");
    $false =  $r['statusKontak'] == "f" ? "checked=\"checked\"" : "";
    $true =  empty($false) ? "checked=\"checked\"" : "";


    // setValidation("is_null", "inp[judulPengumuman]", "anda harus mengisi judul");
    // setValidation("is_null", "inp[sumberPengumuman]", "anda haru mengisi sumber");
    setValidation("is_null", "inp[isiKontak]", "anda haru mengisi Isi Kontak");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"> <?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <div style="position:absolute; top: 15px; right: 20px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" id="submit" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeKontak") ?>';" />
            </div>
            <div id="general" class="subcontent">
                <p>
                    <label class="l-input-small">Username</label>
                    <div class="field">
                        <input type="text" id="inp[username]" name="inp[username]" value="<?= $checkUsernameForInput ?>" class="mediuminput" maxlength="150" style="width:350px;" />
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Isi Pesan</label>
                    <div class="field">
                        <textarea id="mce1" name="inp[isiKontak]" cols="50" class="longinput" style="height:50px; width:350px;"><?= $r['isiKontak'] ?></textarea>
                    </div>
                </p>
                <p>
                    <label class="l-input-small">Status</label>
                    <div class="fradio">
                        <input type="radio" id="true" name="inp[statusKontak]" value="t" <?= $true ?> /> <span class="sradio">Aktif</span>
                        <input type="radio" id="false" name="inp[statusKontak]" value="f" <?= $false ?> /> <span class="sradio">Tidak Aktif</span>
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
    repField($inp);
    // $sql = "update tbl_kontak set tanggalPengumuman='" . setTanggal($inp[tanggalPengumuman]) . "', judulPengumuman='$inp[judulPengumuman]', sumberPengumuman='$inp[sumberPengumuman]', resumePengumuman='$inp[resumePengumuman]', detailPengumuman='$inp[detailPengumuman]', filePengumuman='$filePengumuman', statusPengumuman='$inp[statusPengumuman]',kategoriPengumuman='$inp[kategoriPengumuman]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPengumuman='$par[idPengumuman]'";
    $sql = "UPDATE tbl_kontak set username='$inp[username]',isiKontak='$inp[isiKontak]',statusKontak='$inp[statusKontak]',updateBy='$cUsername',updateTime='" . date('Y-m-d H:i:s') . "' WHERE kodeKontak ='$par[kodeKontak]'";
    // var_dump($sql);
    // die();
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,kodeKontak") . "';</script>";
}

function tambah()
{
    global $s, $inp, $par, $acc, $fFile, $cUsername;
    repField($inp);
    $kodeKontak = getField("select kodeKontak from tbl_kontak order by kodeKontak desc") + 1;

    repField("isiKontak");

    // $sql = "insert into dta_pengumuman (idPengumuman, tanggalPengumuman, judulPengumuman, sumberPengumuman, resumePengumuman, detailPengumuman, filePengumuman, statusPengumuman,kategoriPengumuman, createBy, createTime) values ('$idPengumuman', '" . setTanggal($inp[tanggalPengumuman]) . "', '$inp[judulPengumuman]', '$inp[sumberPengumuman]', '$inp[resumePengumuman]', '$inp[detailPengumuman]', '$filePengumuman', '$inp[statusPengumuman]','$inp[kategoriPengumuman]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    $sql = "INSERT INTO tbl_kontak (kodeKontak,username,isiKontak,statusKontak,createBy,createTime) values ('$kodeKontak','$inp[username]','$inp[isiKontak]','$inp[statusKontak]','$cUsername','" . date('Y-m-d H:i:s') . "')";
    // var_dump($sql);
    // die();
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,kodeKontak") . "';</script>";
}



function hapus()
{
    global $s, $inp, $par, $fFile, $cUsername;
    $sql = "delete from tbl_kontak where kodeKontak='$par[kodeKontak]'";
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,kodeKontak") . "';</script>";
}
function xls()
{
    global  $par, $fFileE;
    $direktori = $fFileE;
    $namaFile = "REPORT PESAN.xls";
    $judul = "DATA PESAN";
    $field = array("No",  "Dikirim", "Oleh", "Pesan", "Status");

    // $filter1     = !empty($par['filter']) ? " AND  `kategoriPengumuman` = '$par[filter]' " : "";

    $sql = "SELECT * FROM tbl_kontak WHERE kodeKontak IS NOT NULL ";
    $res = db($sql);
    // $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $no = 0;
    $arrStatus = array('t' => 'Aktif', 'f' => 'Tidak Aktif');

    while ($r = mysql_fetch_array($res)) {
        list($tanggalKontak, $waktuKontak) = explode(" ", $r[createTime]);
        $r["waktuKontak"] = getTanggal($tanggalKontak) . " @ " . substr($waktuKontak, 0, 5);
        $r['tanggalPengumuman'] = getTanggal($r['tanggalPengumuman']);
        $no++;
        $data[] = array(
            $no . "\t center",
            $r['waktuKontak'] . "\t center",
            $r['username'] . "\t center",
            $r['isiKontak'] . "\t center",
            $arrStatus[$r['statusKontak']] . "\t center"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
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
                    "sClass": "alignCenter"
                },
                {
                    "mData": "waktuKontak",
                    "bSortable": true
                },
                {
                    "mData": "username",
                    "bSortable": true
                },

                {
                    "mData": "username",
                    "bSortable": true
                },

                {
                    "mData": "isiKontak",
                    "bSortable": true
                },

                {
                    "mData": "statusKontak",
                    "bSortable": false,
                    "sClass": "alignCenter"
                },
                {
                    "mData": null,
                    "sClass": "alignCenter",
                    "sWidth": "80px",
                    "bSortable": false,
                    "fnRender": function(data) {
                        var ret = '',
                            kodeKontak = data.aData['kodeKontak'];
                        <?php if (isset($menuAccess[$s]['edit'])) { ?>
                            ret += "<a href=\"?par[mode]=edit&par[kodeKontak]=" + kodeKontak + "<?= getPar($par, "mode, kodeKontak"); ?>\"  title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
                        <?php } ?>
                        <?php if (isset($menuAccess[$s]['edit'])) { ?>
                            ret += "<a href=\"?par[mode]=del&par[kodeKontak]=" + kodeKontak + "<?= getPar($par, "mode, kodeKontak"); ?>\"  onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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

        $("#submit").click(function() {
            var textArea = tinymce.activeEditor.getContent({
                format: 'text'
            }).length;
            if (textArea == 0) {
                alert('Isi Pesan Belum Diisi');
                return false;
            } else {
                return true;
            }
        });

        //! Untuk Style MENYAMBUNG TABLE, AWALNYA HEADER DAN BODY KEPOTONG
        $(".dataTables_scrollHeadInner > table").css("border-bottom", "0").css("padding-bottom", "0").css("margin-bottom", "0");
        $(".dataTables_scrollBody > table").css("border-top", "0").css("margin-top", "-5px");
    });
</script>