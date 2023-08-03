<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/catatan/catatan/";
$fDokumen = "files/catatan/dokumen/";
$fDiskusi = "files/catatan/diskusi/";

function uploadFile($id)
{
    global $fFile;
    $fileUpload = $_FILES["file"]["tmp_name"];
    $fileUpload_name = $_FILES["file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $file = "catatan-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $file);
    }
    if (empty($file)) $file = getField("select file from catatan_sistem where id='$id'");

    return $file;
}

function uploadDokumen($id)
{
    global $fDokumen;
    $fileUpload = $_FILES["file"]["tmp_name"];
    $fileUpload_name = $_FILES["file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $file = "dokumen-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fDokumen);
        fileRename($fDokumen, $fileUpload_name, $file);
    }
    if (empty($file)) $file = getField("select file from catatan_dokumen where id='$id'");

    return $file;
}

function uploadDiskusi($id)
{
    global $fDiskusi;
    $fileUpload = $_FILES["file"]["tmp_name"];
    $fileUpload_name = $_FILES["file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $file = "diskusi-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fDiskusi);
        fileRename($fDiskusi, $fileUpload_name, $file);
    }
    if (empty($file)) $file = getField("select file from catatan_diskusi where id='$id'");

    return $file;
}

function hapusFile()
{
    global $par, $fFile;

    $file = getField("select file from catatan_sistem where id='$par[id]'");
    if (file_exists($fFile . $file) and $file != "") unlink($fFile . $file);

    $sql = "update catatan_sistem set file='' where id='$par[id]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapusFileDokumen()
{
    global $par, $fDokumen;

    $file = getField("select file from catatan_dokumen where id='$par[id]'");
    if (file_exists($fDokumen . $file) and $file != "") unlink($fDokumen . $file);

    $sql = "update catatan_dokumen set file='' where id='$par[id]'";
    db($sql);

    echo "<script>window.location='?par[mode]=editDokumen" . getPar($par, "mode") . "';</script>";
}

function hapusFileDiskusi()
{
    global $par, $fDiskusi;

    $file = getField("select file from catatan_diskusi where id='$par[id]'");
    if (file_exists($fDiskusi . $file) and $file != "") unlink($fDiskusi . $file);

    $sql = "update catatan_diskusi set file='' where id='$par[id]'";
    db($sql);

    echo "<script>window.location='?par[mode]=editDiskusi" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $file = getField("select file from catatan_sistem where id='$par[id]'");
    if (file_exists($fFile . $file) and $file != "") unlink($fFile . $file);

    $sql = "delete from catatan_sistem where id='$par[id]'";
    db($sql);
    $sql = "delete from catatan_dokumen where id_catatan='$par[id]'";
    db($sql);
    $sql = "delete from catatan_diskusi where id_catatan='$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function hapusDokumen()
{
    global $par, $fDokumen;

    $file = getField("select file from catatan_dokumen where id='$par[idDokumen]'");
    if (file_exists($fDokumen . $file) and $file != "") unlink($fDokumen . $file);

    $sql = "delete from catatan_dokumen where id='$par[idDokumen]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,idDokumen") . "';</script>";
}

function hapusDiskusi()
{
    global $par, $fDiskusi;

    $file = getField("select file from catatan_diskusi where id='$par[idDiskusi]'");
    if (file_exists($fDiskusi . $file) and $file != "") unlink($fDiskusi . $file);

    $sql = "delete from catatan_diskusi where id='$par[idDiskusi]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,idDiskusi") . "';</script>";
}

function tambah()
{
    global $inp, $cUsername, $par;

    repField();

    $id = getLastId("catatan_sistem", "id");

    list($hari, $bulan, $tahun) = explode("/", $inp[tanggal]);
    $kodeMaster = getField("SELECT kodeMaster FROM mst_data where kodeData = '$inp[id_group]'");
    $getLastMaster = getField("SELECT SUBSTR(nomor, 4, 3) FROM catatan_sistem WHERE id_group = '$inp[id_group]' AND month(tanggal) = '$bulan' AND year(tanggal) = '$tahun'  ORDER BY SUBSTR(nomor, 4, 3) DESC LIMIT 1");
    $getLastMaster   = empty($getLastMaster) ? "000" : $getLastMaster;
    $lastMaster = str_pad($getLastMaster + 1, 3, "0", STR_PAD_LEFT);
    $getLastNumber = getField("SELECT SUBSTR(nomor, 8, 3) FROM catatan_sistem WHERE month(tanggal) = '$bulan' AND year(tanggal) = '$tahun'  ORDER BY SUBSTR(nomor, 8, 3) DESC LIMIT 1");
    $getLastNumber   = empty($getLastNumber) ? "000" : $getLastNumber;
    $lastNumber = str_pad($getLastNumber + 1, 3, "0", STR_PAD_LEFT);
    $bulan = getRomawi($bulan);
    $tahun = substr($tahun, 2, 2);
    $inp[nomor] = $kodeMaster . $lastMaster . "/" . $lastNumber . "/" . $bulan . "/" . $tahun;

    $inp[file] = uploadFile($id);
    $inp[tanggal] = setTanggal($inp[tanggal]);
    $inp[tanggal_mulai] = setTanggal($inp[tanggal_mulai]);
    $inp[tanggal_selesai] = setTanggal($inp[tanggal_selesai]);
    $inp[tanggal_aktual] = setTanggal($inp[tanggal_aktual]);
    $inp[tanggal_test] = setTanggal($inp[tanggal_test]);
    $inp[create_by] = $cUsername;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO catatan_sistem SET id = '$id', id_kategori = '$inp[id_kategori]', id_group = '$inp[id_group]', id_prioritas = '$inp[id_prioritas]', nomor = '$inp[nomor]', tanggal = '$inp[tanggal]', temuan = '$inp[temuan]', penjelasan = '$inp[penjelasan]', tanggal_mulai = '$inp[tanggal_mulai]', tanggal_selesai = '$inp[tanggal_selesai]', tanggal_aktual = '$inp[tanggal_aktual]', pic = '$inp[pic]', keterangan = '$inp[keterangan]', testing = '$inp[testing]', file = '$inp[file]', tanggal_test = '$inp[tanggal_test]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUsername;

    repField();

    $inp[file] = uploadFile($par[id]);
    $inp[tanggal] = setTanggal($inp[tanggal]);
    $inp[tanggal_mulai] = setTanggal($inp[tanggal_mulai]);
    $inp[tanggal_selesai] = setTanggal($inp[tanggal_selesai]);
    $inp[tanggal_aktual] = setTanggal($inp[tanggal_aktual]);
    $inp[tanggal_test] = setTanggal($inp[tanggal_test]);
    $inp[update_by] = $cUsername;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "UPDATE catatan_sistem SET id_kategori = '$inp[id_kategori]', id_group = '$inp[id_group]', id_prioritas = '$inp[id_prioritas]', nomor = '$inp[nomor]', tanggal = '$inp[tanggal]', temuan = '$inp[temuan]', penjelasan = '$inp[penjelasan]', tanggal_mulai = '$inp[tanggal_mulai]', tanggal_selesai = '$inp[tanggal_selesai]', tanggal_aktual = '$inp[tanggal_aktual]', pic = '$inp[pic]', keterangan = '$inp[keterangan]', testing = '$inp[testing]', tanggal_test = '$inp[tanggal_test]', status = '$inp[status]', file = '$inp[file]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function tambahDokumen()
{
    global $inp, $cUsername, $par;

    repField();

    $id = getLastId("catatan_dokumen", "id");

    $inp[file] = uploadDokumen($id);
    $inp[create_by] = $cUsername;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO catatan_dokumen SET id = '$id', id_catatan = '$par[id]', nama = '$inp[nama]', keterangan = '$inp[keterangan]', file = '$inp[file]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubahDokumen()
{
    global $inp, $par, $cUsername;

    repField();

    $inp[file] = uploadDokumen($par[idDokumen]);
    $inp[update_by] = $cUsername;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "UPDATE catatan_dokumen SET nama = '$inp[nama]', keterangan = '$inp[keterangan]', file = '$inp[file]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$par[idDokumen]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambahDiskusi()
{
    global $inp, $cUsername, $par;

    repField();

    $id = getLastId("catatan_diskusi", "id");

    $inp[file] = uploadDiskusi($id);
    $inp[create_by] = $cUsername;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO catatan_diskusi SET id = '$id', id_catatan = '$par[id]', judul = '$inp[judul]', uraian = '$inp[uraian]', file = '$inp[file]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambahKomentar()
{
    global $inp, $cUsername, $par;

    repField();

    $id = getLastId("catatan_diskusi", "id");

    $inp[file] = uploadDokumen($id);
    $inp[create_by] = $cUsername;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO catatan_diskusi SET id = '$id', id_diskusi = '$par[idDiskusi]', id_catatan = '$par[id]', judul = '$inp[judul]', uraian = '$inp[uraian]', file = '$inp[file]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubahDiskusi()
{
    global $inp, $par, $cUsername;

    repField();

    $inp[file] = uploadDiskusi($par[idDiskusi]);
    $inp[update_by] = $cUsername;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "UPDATE catatan_diskusi SET judul = '$inp[judul]', uraian = '$inp[uraian]', file = '$inp[file]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$par[idDiskusi]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $s, $arrTitle, $par, $ui, $cNama, $menuAccess, $fDokumen;

    $sql = "SELECT * FROM catatan_sistem WHERE id = '$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $queryKategori = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'KC' and statusData = 't' order by urutanData";
    $queryGroup = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'GC' and statusData = 't' order by urutanData";
    $queryPrioritas = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'MCT' and statusData = 't' order by urutanData";
    $queryStatus = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'MCS' and statusData = 't' order by urutanData";

    $namaUser = empty($r[create_by]) ? $cNama : getField("SELECT namaUser FROM app_user WHERE username = '$r[create_by]'");
    $styleNomor = $par[mode] == "add" ? "style=\"display:none\"" : "style=\"display:block\"";
    $onClickDokumen = $par[mode] == "add" ? "alert('HARAP SIMPAN TERLEBIH DAHULU')" : "openBox('popup.php?par[mode]=addDokumen" . getPar($par, 'mode') . "', 800,300)";
    $onClickDiskusi = $par[mode] == "add" ? "alert('HARAP SIMPAN TERLEBIH DAHULU')" : "openBox('popup.php?par[mode]=addDiskusi" . getPar($par, 'mode') . "', 800,300)";
?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p style="position:absolute;top:5px;right:10px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>" />
            </p>
            <br clear="all" />
            <ul class="hornav">
                <li class="current"><a href="#tabCatatan">Catatan</a></li>
                <li><a href="#tabDokumen">Dokumen</a></li>
                <li><a href="#tabDiskusi">Diskusi</a></li>
            </ul>

            <div id="tabCatatan" class="subcontent" style="margin:0">
                <fieldset>
                    <p><?= $ui->createDatePicker("Tanggal", "inp[tanggal]", $r[tanggal], "", "", "", "", "4") ?></p>
                    <div <?= $styleNomor ?>>
                        <p><?= $ui->createField("Nomor", "inp[nomor]", $r[nomor], "", "", "style=\"width:30%\"", "", "", "t", "", "4") ?></p>
                    </div>
                    <p><?= $ui->createField("Nama", "", $namaUser, "", "", "style=\"width:30%\"", "", "", "t", "", "4") ?></p>
                    <p><?= $ui->createField("Judul", "inp[temuan]", $r[temuan], "", "", "", "", "", "", "", "4") ?></p>
                    <p><?= $ui->createTextArea("Penjelasan", "inp[penjelasan]", $r[penjelasan], "style=\"width:60%\"", "", "", "", "4") ?></p>
                    <p><?= $ui->createComboData("Kategori", $queryKategori, "id", "description", "inp[id_kategori]", $r[id_kategori], "", "", "t", "", "", "", "4") ?></p>
                    <p><?= $ui->createComboData("Group", $queryGroup, "id", "description", "inp[id_group]", $r[id_group], "", "", "t", "", "", "", "4") ?></p>
                    <p><?= $ui->createComboData("Prioritas", $queryPrioritas, "id", "description", "inp[id_prioritas]", $r[id_prioritas], "", "", "t", "", "", "", "4") ?></p>
                    <p><?= $ui->createFile("File", "file", $r[file], "", "", "catatanFile", $r[id], "delFile", "4") ?></p>
                </fieldset>
                <br clear="all" />
                <fieldset>
                    <legend> PENYELESAIAN </legend>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createDatePicker("Tanggal Mulai", "inp[tanggal_mulai]", $r[tanggal_mulai], "", "t") ?></p>
                                <p><?= $ui->createField("PIC", "inp[pic]", $r[pic], "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                <p><?= $ui->createDatePicker("Target Selesai", "inp[tanggal_selesai]", $r[tanggal_selesai]) ?></p>
                                <p><?= $ui->createDatePicker("Target Aktual", "inp[tanggal_aktual]", $r[tanggal_aktual]) ?></p>
                            </td>
                        </tr>
                    </table>
                    <p><?= $ui->createTextArea("Keterangan", "inp[penjelasan]", $r[penjelasan], "style=\"width:60%\"", "", "", "", "4") ?></p>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createRadio("Testing", "inp[testing]", array("f" => "Belum", "t" => "Sudah"), $r[testing], "t") ?></p>
                                <p><?= $ui->createComboData("Status", $queryStatus, "id", "description", "inp[status]", $r[status], "", "", "t", "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                <p><?= $ui->createDatePicker("Tanggal Test", "inp[tanggal_test]", $r[tanggal_test]) ?></p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <br clear="all" />

                <fieldset>
                    <legend>HISTORY</legend>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createSpan("Input Date", $r[create_date], "", "t") ?></p>
                                <p><?= $ui->createSpan("Update Date", $r[update_date], "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                <p><?= $ui->createSpan("Input By", getField("SELECT namaUser FROM app_user WHERE username = '$r[create_by]'")) ?></p>
                                <p><?= $ui->createSpan("Update By", getField("SELECT namaUser FROM app_user WHERE username = '$r[update_by]'")) ?></p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <div id="tabDokumen" class="subcontent" style="display:none;margin-top:0px">
                <div class="widgetbox" style="margin-bottom:0px;">
                    <div class="title">
                        <h3 style="float: left;padding-top:15px;"> DATA DOKUMEN </h3>
                        <?php if (isset($menuAccess[$s]["add"])) echo "<a style=\"float:right;margin-right:-15px\" href=\"#\" onclick=\"$onClickDokumen\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>"; ?>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
                    <thead>
                        <tr>
                            <th width="20">No.</th>
                            <th width="*">DOKUMEN</th>
                            <th width="50">VIEW</th>
                            <th width="50">DL</th>
                            <th width="100">UPLOAD</th>
                            <th width="150">USER</th>
                            <th width="100">SIZE</th>
                            <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT *,date(create_date) as tanggal FROM catatan_dokumen WHERE id_catatan = '$par[id]'";
                        $res = db($sql);
                        $no = 0;
                        while ($r = mysql_fetch_assoc($res)) {
                            $no++;
                            $view = "<a href=\"#\" onclick=\"openBox('view.php?doc=catatanDokumen&id=$r[id]" . getPar($par, "mode") . "',725,500);\" class=\"detail\"><span>Detail</span></a>";
                            $download = "<a href=\"download.php?d=catatanDokumen&f=$r[id]\"><img src=\"" . getIcon($r[file]) . "\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
                            if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                                $control = "<td align=\"center\">";
                                if (!empty($menuAccess[$s]["edit"]))
                                    $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editDokumen&par[idDokumen]=$r[id]" . getPar($par, "mode,id") . "', 800, 300)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                                if (!empty($menuAccess[$s]["delete"]))
                                    $control .= "<a href=\"?par[mode]=delDokumen&par[idDokumen]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                $control .= " </td>";
                            }
                        ?>
                            <tr>
                                <td><?= $no ?>.</td>
                                <td><?= $r[nama] ?></td>
                                <td align="center"><?= $view ?></td>
                                <td align="center"><?= $download ?></td>
                                <td align="center"><?= getTanggal($r[tanggal]) ?></td>
                                <td><?= getField("SELECT namaUser FROM app_user WHERE username = '$r[create_by]'") ?></td>
                                <td align="right"><?= getSizeFile($fDokumen . $r[file]) ?></td>
                                <?= $control ?>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="tabDiskusi" class="subcontent" style="display:none;margin-top:0px">
                <div class="widgetbox" style="margin-bottom:0px;">
                    <div class="title">
                        <h3 style="float: left;padding-top:15px;"> DATA DISKUSI </h3>
                        <?php if (isset($menuAccess[$s]["add"])) echo "<a style=\"float:right;margin-right:-15px\" href=\"#\" onclick=\"$onClickDiskusi\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>"; ?>
                    </div>
                </div>
                <?php
                $sql = "SELECT t1.id, t1.judul, t1.create_date, t1.uraian, t1.file, t2.namaUser, t2.fotoUser FROM catatan_diskusi t1 join app_user t2 on t1.create_by = t2.username WHERE id_catatan = '$par[id]' AND id_diskusi = '0'";
                $res = db($sql);
                while ($r = mysql_fetch_assoc($res)) {
                    $fileDownload = !empty($r[file]) ? "<a href=\"download.php?d=catatanDiskusi&f=$r[id]\"><img src=\"" . getIcon($r[file]) . "\" align=\"center\" style=\"margin-bottom:-3px;max-width:20px; max-height:20px;\"></a> |" : "";
                ?>
                    <fieldset>
                        <table style="width:100%">
                            <tr>
                                <td style="width: 15%; padding-left: 10px; padding-right: 10px; padding-top: 5px;">
                                    <p><img width="130px" height="100px" src="images/user/<?= $r[fotoUser] ?>"></p>
                                    <p style="font-size:11px"><?= $r[namaUser] ?></p>
                                </td>
                                <td style="width:80%;vertical-align: top;">
                                    <p><b><?= $r[judul] ?></b></p>
                                    <p><i><?= $r[create_date] ?></i></p>
                                    <p><?= $r[uraian] ?></p>
                                    <br>
                                    <p><?= $fileDownload ?><a href="#" onclick="openBox('popup.php?par[mode]=addKomentar&par[idDiskusi]=<?= $r[id] . getPar($par, 'mode') ?>', 800, 300)"><b>Balas</b></a></p>
                                    <?php
                                    $sql_ = "SELECT t1.id, t1.judul, t1.create_date, t1.uraian, t2.namaUser, t2.fotoUser FROM catatan_diskusi t1 join app_user t2 on t1.create_by = t2.username WHERE id_diskusi = '$r[id]'";
                                    $res_ = db($sql_);
                                    while ($r_ = mysql_fetch_assoc($res_)) {
                                    ?>
                                        <div style="background-color: #E0E0E0;border-radius:5px;padding:10px;margin-bottom:5px;">
                                            <p><b><?= $r_[namaUser] ?></b></p>
                                            <p><i><?= $r_[create_date] ?></i></p>
                                            <p><?= $r_[uraian] ?></p>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <br clear="all" />
                <?php
                }
                ?>
            </div>

        </form>
    </div>
<?php
}

function formDokumen()
{
    global $par, $ui;

    $sql = "SELECT * FROM catatan_dokumen WHERE id = '$par[idDokumen]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);
?>

    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle">DOKUMEN</h1>
            <?= getBread(ucwords($par[mode] . " data")) ?>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
                <p style="position:absolute;top:5px;right:10px;">
                    <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                    <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>" />
                </p>
                <br clear="all" />
                <fieldset>
                    <p><?= $ui->createField("Nama", "inp[nama]", $r[nama]) ?></p>
                    <p><?= $ui->createFile("File", "file", $r[file], "", "", "catatanDokumen", $r[id], "delFileDokumen") ?></p>
                    <p><?= $ui->createTextArea("Keterangan", "inp[keterangan]", $r[keterangan], "style=\"width:350px;height:50px;\"") ?></p>
                </fieldset>

            </form>
        </div>
    </div>
<?php
}

function formDiskusi()
{
    global $par, $ui;

    $sql = "SELECT * FROM catatan_diskusi WHERE id = '$par[idDiskusi]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);
?>

    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle">DISKUSI</h1>
            <?= getBread(ucwords($par[mode] . " data")) ?>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
                <p style="position:absolute;top:5px;right:10px;">
                    <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                    <input type="button" class="cancel radius2" value="Kembali" onclick="closeBox();" />
                </p>
                <br clear="all" />
                <fieldset>
                    <p><?= $ui->createField("Judul", "inp[judul]", $r[judul]) ?></p>
                    <p><?= $ui->createTextArea("Uraian", "inp[uraian]", $r[uraian], "style=\"width:350px;height:50px;\"") ?></p>
                    <p><?= $ui->createFile("File", "file", $r[file], "", "", "catatanDiskusi", $r[id], "delFileDiskusi") ?></p>
                </fieldset>

            </form>
        </div>
    </div>
<?php
}

function formKomentar()
{
    global $par, $ui;

    $sql = "SELECT * FROM catatan_diskusi WHERE id = '$par[idDiskusi]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);
?>

    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle">DISKUSI</h1>
            <?= getBread(ucwords($par[mode] . " data")) ?>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
                <p style="position:absolute;top:5px;right:10px;">
                    <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                    <input type="button" class="cancel radius2" value="Kembali" onclick="closeBox();" />
                </p>
                <br clear="all" />
                <fieldset>
                    <p><?= $ui->createSpan("Judul", $r[judul]) ?></p>
                    <p><?= $ui->createTextArea("Uraian", "inp[uraian]", "", "style=\"width:350px;height:50px;\"") ?></p>
                    <p><?= $ui->createFile("File", "file", "", "", "", "catatanDiskusi", $r[id], "delFileDiskusi") ?></p>
                </fieldset>

            </form>
        </div>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'MCS'");
    $queryGroup = "SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory = 'GC'";

?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <?= comboData($queryGroup, "id", "description", "par[idGroup]", " - PILIH GROUP -", $par[idGroup], "", "200px", "chosen-select") ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <?php if (isset($menuAccess[$s]["add"])) echo "<a href=\"?par[mode]=add" . getPar($par, 'mode') . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>"; ?>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="100">TANGGAL</th>
                    <th width="*">CATATAN</th>
                    <th width="100">PIC</th>
                    <th width="100">RENCANA</th>
                    <th width="100">SELESAI</th>
                    <th width="100">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $filter = " WHERE id is not null";
                if (!empty($par[filterData]))
                    $filter .= " AND lower(temuan) LIKE '%" . strtolower($par[filterData]) . "%'";
                if (!empty($par[idGroup]))
                    $filter .= " AND id_group = '$par[idGroup]'";
                $sql = "SELECT id, tanggal, temuan, pic, tanggal_mulai, tanggal_selesai, status FROM catatan_sistem $filter order by tanggal desc";
                $res = db($sql);
                $no = 0;
                while ($r = mysql_fetch_assoc($res)) {
                    $no++;
                    if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                        $control = "<td align=\"center\">";
                        if (!empty($menuAccess[$s]["edit"]))
                            $control .= "<a href=\"?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                        if (!empty($menuAccess[$s]["delete"]))
                            $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        $control .= " </td>";
                    }
                ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td align="center"><?= getTanggal($r[tanggal]) ?></td>
                        <td><?= $r[temuan] ?></td>
                        <td><?= $r[pic] ?></td>
                        <td align="center"><?= getTanggal($r[tanggal_mulai]) ?></td>
                        <td align="center"><?= getTanggal($r[tanggal_selesai]) ?></td>
                        <td style="background-color:<?= getField("SELECT kodeMaster FROM mst_data WHERE kodeData = '$r[status]'") ?>"><b><?= $arrMaster[$r[status]] ?></b></td>
                        <?= $control ?>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
}

function xls()
{
    global $s, $arrTitle, $fExport, $areaCheck, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'S13'");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "pegawai", "nik", "nama bank", "cabang", "no rekening", "atas nama", "status");

    $filter = "WHERE t3.location IN ($areaCheck)";
    if (!empty($par[filterData]))
        $filter .= " and (lower(t2.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t2.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";
    if (!empty($par[idLokasi]))
        $filter .= " and t3.location = '$par[idLokasi]'";
    if (!empty($par[idGroup]))
        $filter .= " and t3.proses_id = '$par[idGroup]'";
    if (!empty($par[idPangkat]))
        $filter .= " and t3.rank = '$par[idPangkat]'";
    if (!empty($par[idJabatan]))
        $filter .= " and t3.pos_name = '$par[idJabatan]'";
    $sql = "SELECT t1.bank_id, t1.branch, t1.account_no, t1.account_name, t1.status, t2.name, t2.reg_no FROM emp_bank t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[status] = $r[status] == "t" ? "Aktif" : "Tidak Aktif";
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $arrMaster[$r[bank_id]] . "\t left",
            $r[branch] . "\t left",
            $r[account_no] . "\t left",
            $r[account_name] . "\t left",
            $r[status] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par)
{
    global $menuAccess, $s, $_submit, $m;
    switch ($par[mode]) {
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        case "addDokumen":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDokumen() : tambahDokumen();
            else $text = lihat();
            break;
        case "editDokumen":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDokumen() : ubahDokumen();
            else $text = lihat();
            break;
        case "delDokumen":
            if (isset($menuAccess[$s]["delete"])) $text = hapusDokumen();
            else $text = lihat();
            break;
        case "addDiskusi":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDiskusi() : tambahDiskusi();
            else $text = lihat();
            break;
        case "addKomentar":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formKomentar() : tambahKomentar();
            else $text = lihat();
            break;
            // case "editDiskusi":
            //     if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDiskusi() : ubahDiskusi();
            //     else $text = lihat();
            //     break;
            // case "delDiskusi":
            //     if (isset($menuAccess[$s]["delete"])) $text = hapusDiskusi();
            //     else $text = lihat();
            //     break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "delFileDokumen":
            $text = hapusFileDokumen();
            break;
        case "delFileDiskusi":
            $text = hapusFileDiskusi();
            break;
        case "delFile":
            $text = hapusFile();
            break;
        default:
            $text = lihat();
            break;
    }

    return $text;
}
?>