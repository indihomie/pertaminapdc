<?php
$fFileE = "files/export/";
$fFoto = "files/berita/";


function lihat()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrColor, $cID;
    $text .= "
        <div class=\"pageheader\">
            <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
            " . getBread() . "
            <span class=\"pagedesc\">&nbsp;</span>
        </div> 
        <p style=\"position: absolute; right: 20px; top: 10px;\"></p>
        <div id=\"contentwrapper\" class=\"contentwrapper\">
            <form id=\"filter\" class=\"stdform\" method=\"POST\" action=\"\">
                <div id=\"pos_l\" style=\"float:left; width:50%;\">
                    <p>
                        <input type=\"text\" id=\"searchFilter\" name=\"searchFilter\" placeholder=\"Search...\" style=\"width:200px;\"/>					
                        " . comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MDB'", "kodeData", "namaData", "selectFilter", "Kategori") . "
                    </p>
                </div>
            </form>
            <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
                <a href=\"?par[mode]=xls" . getPar($par, "mode") . "\"  id=\"btnExport\" class=\"btn btn1 btn_inboxo\" ><span>Export</span></a>
                " . checkPermissionAdd() . "
            </div>	
            <br clear=\"all\" />

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" class=\"stdtable \" id=\"data-table\">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>JUDUL</th>
                        <th>TANGGAL</th>
                        <th>RINGKASAN</th>
                        <th style=\"display:none;\">KATEGORI</th>
                        <th>STATUS</th>
                        <th>KONTROL</th>
                    </tr>
                </thead>
                <tbody>
                    ";
    $sqlBerita = "SELECT * from tbl_berita ";
    $resultBerita = db($sqlBerita);
    while ($rowB = mysql_fetch_array($resultBerita)) {
        $no++;
        $rowB['statusBerita'] = ($rowB['statusBerita'] == 't' ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>");
        $text .= "
            <tr align=\"center\">
                <td>$no</td>
                <td>$rowB[judulBerita]</td>
                <td>" . getTanggal($rowB[tanggalBerita]) . "</td>
                <td>$rowB[ringkasanBerita]</td>
                <td style=\"display:none;\">$rowB[kategoriBerita]</td>
                <td>$rowB[statusBerita]</td>
                <td>
                    " . checkPermissionEdit($rowB[kodeBerita]) . "
                    " . checkPermissionDelete($rowB[kodeBerita]) . "
                </td>
            </tr>

        ";
    }
    $text .= "
                </tbody>
            </table>
        </div>";
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=REPORT BERITA.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    $text .= "

    ";

    return $text;
}

function form()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrParam, $folder_upload;
    $sql = "SELECT tbl_berita.*,app_user.namaUser from tbl_berita INNER JOIN app_user ON app_user.idPegawai=tbl_berita.updateBy where kodeBerita='$par[kodeBerita]'";
    $fFoto = "files/berita/";

    $res = db($sql);
    $r = mysql_fetch_array($res);
    $t = $r[statusBerita] == 't' ? "checked" : "";
    $f = $r[statusBerita] == 'f' ? "checked" : "";
    $default = empty($r[statusBerita]) ? "checked" : "";

    setValidation("is_null", "inp[judulBerita]", "anda harus mengisi Judul Berita");
    setValidation("is_null", "inp[sumberBerita]", "anda harus mengisi Sumber Berita");
    setValidation("is_null", "inp[ringkasanBerita]", "anda harus mengisi Ringkasa Berita");
    setValidation("is_null", "inp[fotoBerita]", "anda harus mengisi Foto Berita");
    $text = getValidation();
    $text .= "
    
    <div class=\"pageheader\">
        <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        <span class=\"pagedesc\">&nbsp;</span>
    </div> 
    <div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\"  enctype=\"multipart/form-data\">
            <div style=\"position:absolute; right:20px; top:14px;\">
                <input type=\"submit\" id=\"buttonValidate\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
                <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Kembali\" onclick=\"window.location='index.php?" . getPar($par, "mode,lantai,id_denah,tahun,bulan") . "';\"/>
            </div>
            <fieldset>
                    <legend> Edit Data </legend>
                    <p>
                        <label class=\"l-input-small\">Judul Berita</label>
                        <div class=\"field\">
                            <input type=\"text\" id=\"inp[judulBerita]\" name=\"inp[judulBerita]\" value=\"$r[judulBerita]\" style=\"width: 95%;\" />
                        </div>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Sumber</label>
                        <div class=\"field\">
                            <input type=\"text\" id=\"inp[sumberBerita]\" name=\"inp[sumberBerita]\" class=\"smallinput\" value=\"$r[sumberBerita]\" style=\"width: 95%;\" />
                        </div>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Tanggal</label>
                        <div class=\"field\">
                            <input type=\"text\" id=\"inp[tanggalBerita]\" name=\"inp[tanggalBerita]\" class=\"smallinput hasDatePicker\" value=\"$r[tanggalBerita]\" />
                        </div>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Ringkasan</label>
                        <div class=\"field\">
                            <textarea id=\"inp[ringkasanBerita]\" name=\"inp[ringkasanBerita]\" style=\"width: 95%;\"> $r[ringkasanBerita]</textarea>
                        </div>
                    </p>
                    <p>
                        <textarea id=\"isiBerita\" name=\"inp[isiBerita]\"> $r[isiBerita] </textarea>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Foto</label>
                        <div class=\"field\">";
    $checkFotoBerita = (empty($r[fotoBerita])  ?
        "
                        <input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"smallinput\">
                                <div class=\"fakeupload\" style=\"padding-left: 40px;\">
                                    <input type=\"file\" id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\">
                                </div>"
        :
        "<img src=\" " . $fFoto . $r[fotoBerita] . " \" width=\"50px\">
                    <a href=\"?par[mode]=delFoto" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>");
    $text .= "
                            $checkFotoBerita
                        </div>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Kategori Berita</label>
                        <div class=\"field\">
                        " . comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MDB'", "kodeData", "namaData", "inp[kategoriBerita]", "Kategori", "$r[kategoriBerita]", "required") . "
                            
                        </div>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Status</label>
                        <div class=\"field\">
                            <div class=\"sradio\" style=\"padding-top:5px;padding-left:8px;\">
                                <input type=\"radio\" name=\"inp[statusBerita]\" value=\"t\"  $t . \" \" . $default > <span style=\"padding-right:10px;\">Tampil</span>
                                <input type=\"radio\" name=\"inp[statusBerita]\" value=\"f\"  $f > <span style=\"padding-right:10px;\">Tidak Tampil</span>
                            </div>
                        </div>
                    </p>
            </fieldset>
            " . show_history($r) . "
        </form>
    </div> 
    <script type=\"text/javascript\" src=\"plugins/TinyMCE/jquery.tinymce.js\"></script>";

    return $text;
}

function tambah()
{
    global $s, $inp, $par, $cUsername, $fFoto, $cID;
    repField();
    $first = array("-", " ", ":");
    $end = array("", "", "");
    $curdate = str_replace($first, $end, date('Y-m-d H:i'));
    $file = $_FILES["file"]["tmp_name"];
    $file_name = $_FILES["file"]["name"];
    if (($file != "") and ($file != "none")) {
        fileUpload($file, $file_name, $fFoto);
        $file = "berita_" . uniqid() . "_" . $curdate . "." . getExtension($file_name);
        fileRename($fFoto, $file_name, $file);
    }
    if (empty($file)) $file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");
    $kodeBerita = getField("SELECT `kodeBerita` FROM `tbl_berita` ORDER BY `kodeBerita` DESC LIMIT 1") + 1;
    $sql = "
            INSERT INTO 
            `tbl_berita` (`kodeBerita`, `tanggalBerita`, `judulBerita`, `ringkasanBerita`, `isiBerita`, `fotoBerita`, `sumberBerita`, `statusBerita`,`kategoriBerita`, `createBy`, `createTime`)
            VALUES ('$kodeBerita', '" . setTanggal($inp[tanggalBerita]) . "', '$inp[judulBerita]', '$inp[ringkasanBerita]', '$inp[isiBerita]', '$file', '$inp[sumberBerita]', '$inp[statusBerita]','" . strtolower($inp[kategoriBerita]) . "', '$cID', '" . date('Y-m-d H:i:s)') . "');";

    // var_dump($sql);
    // die();
    db($sql);
    echo "
                <script>
                    alert('DATA BERHASIL DISIMPAN');
                    window.location = '?par[mode]=edit&par[kodeBerita]=$kodeBerita" . getPar($par, "mode") . "';
                </script>
                ";
}
function ubah()
{
    global $s, $inp, $par, $cUsername, $fFoto, $cID;
    repField();
    $first = array("-", " ", ":");
    $end = array("", "", "");
    $curdate = str_replace($first, $end, date('Y-m-d H:i'));
    $file = $_FILES["file"]["tmp_name"];
    $file_name = $_FILES["file"]["name"];
    if (($file != "") and ($file != "none")) {
        fileUpload($file, $file_name, $fFoto);
        $file = "berita_" . uniqid() . "_" . $curdate . "." . getExtension($file_name);
        fileRename($fFoto, $file_name, $file);
    }
    if (empty($file)) $file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");
    $kodeBerita = getField("SELECT `kodeBerita` FROM `tbl_berita` ORDER BY `kodeBerita` DESC LIMIT 1") + 1;
    $sql = "UPDATE `tbl_berita` SET
			`tanggalBerita`     = '" . setTanggal($inp[tanggalBerita]) . "',
			`judulBerita`       = '$inp[judulBerita]',
			`ringkasanBerita`   = '$inp[ringkasanBerita]',
			`isiBerita`         = '$inp[isiBerita]',
			`fotoBerita`        = '$file',
            `sumberBerita`      = '$inp[sumberBerita]',
			`statusBerita`      = '$inp[statusBerita]',
            `kategoriBerita`    = '" . strtolower($inp[kategoriBerita]) . "',
			`updateBy`          = '$cID',
			`updateTime`        = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeBerita`  = '$par[kodeBerita]'
			";
    $kodeBerita = $par['kodeBerita'];
    // var_dump($sql);
    // die();
    db($sql);
    echo "
		<script>
			alert('DATA BERHASIL DISIMPAN');
			window.location = '?par[mode]=edit&par[kodeBerita]=$kodeBerita" . getPar($par, "mode") . "';
		</script>
		";
}

function hapus()
{
    global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
    repField();

    $file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");

    if (file_exists($fFoto . $file) and $file != "") unlink($fFoto . $file);

    db("DELETE FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");

    echo "
	<script type=\"text/javascript\">
		window.location.href='?" . getPar($par, 'mode, kodeBerita') . "';
	</script>
	";
}

function hapusFoto()
{
    global $s, $inp, $par, $dFile, $cUsername, $fFoto;

    $file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");

    if (file_exists($fFoto . $file) and $file != "")
        unlink($fFoto . $file);

    $sql = "UPDATE `tbl_berita` SET `fotoBerita` = '' WHERE `kodeBerita` = '$par[kodeBerita]'";
    db($sql);

    echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeBerita]=$par[kodeBerita]" . getPar('kodeBerita') . "';
	</script>";
}

function xls()
{
    global $fFileE;

    $direktori = $fFileE;

    $namaFile = "REPORT BERITA.xls";

    $judul = "DATA BERITA";

    $field = array("no",  "Judul", "Tanggal", "Sumber", "Status");

    $sql = "SELECT * FROM tbl_berita";

    $res = db($sql);

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $no = 0;

    $arrStatus = array('t' => 'Aktif', 'f' => 'Tidak Aktif');

    while ($r = mysql_fetch_array($res)) {

        $r[tanggalBerita] = getTanggal($r[tanggalBerita]);

        $no++;

        $data[] = array(
            $no . "\t center",

            $r[judulBerita] . "\t left",

            $r[tanggalBerita] . "\t center",

            $r[sumberBerita] . "\t left",

            $arrStatus[$r[statusBerita]] . "\t left"
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
function checkPermissionDelete($kodeBerita = null)
{
    global $menuAccess, $s, $par;

    $checkPermissionDelete = (isset($menuAccess[$s]['delete']) ? "<a href=\"index.php?par[mode]=delete&par[kodeBerita]=$kodeBerita" . getPar($par, 'mode') . "\" class=\"delete\" title=\"Delete Data\" onclick=\"return confirm('Apakah anda ingin menghapus data ini?');\"><span>Delete Data</span></a>" : "");
    return $checkPermissionDelete;
}
function checkPermissionEdit($kodeBerita = null)
{
    global $menuAccess, $s, $par;

    $checkPermissionEdit = (isset($menuAccess[$s]['edit']) ? "<a href=\"index.php?par[mode]=edit&par[kodeBerita]=$kodeBerita" . getPar($par, 'mode') . "\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>" : "");
    return $checkPermissionEdit;
}
function checkPermissionAdd()
{
    global $menuAccess, $s, $par;
    $checkAddPermission = (isset($menuAccess[$s]["add"]) ? "<a href=\"index.php?par[mode]=add" . getPar($par, "mode") . "\" id=\"\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>" : "");
    return $checkAddPermission;
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "delFoto":
            $text = hapusFoto();
            break;
        case "delete":
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
}
