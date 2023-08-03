<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_data/";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $arrTitle;

    switch ($par[mode]) {

        default:
            $text = lihat();
            break;

        case "lst":
            $text=lData();
            break;

        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "delete":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;

        case "delFile":
            if (isset($menuAccess[$s]["delete"])) $text = delFile(); else $text = lihat();
            break;

        case "getTermin":
            $text = getTermin();
        break;

        case "getSPK":
            $text = getSPK();
        break;

        case "detailSPK":
            $text = view_spk($arrTitle[$s], $par['id_spk'], $par['pop_up']);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "getFilter":
            $text = getFilter();
            break;
    }

    return $text;
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function delFile()
{
    global $par, $dirFile;

    $file = getField("select file_tagihan from tagihan_data where id = '".$par['id']."'");
    db("update tagihan_data set file_tagihan = '' where id = '".$par['id']."'");
    unlink($dirFile.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function getSPK()
{
    global $par;

    $getData = getRows("SELECT id, CONCAT(nomor, ' - ', judul) as nomor from tagihan_spk WHERE approve_status = 't' and persen_termin = '100' and status_syarat = 't' and status_pelunasan != 'lunas' and id_supplier = '".$par['id_supplier']."' order by nomor asc");
    echo json_encode($getData);
}

function getTermin()
{
    global $par;

    $getData = getRows("SELECT id, concat(persen, '%', ' - ', termin) as termin FROM tagihan_termin WHERE id_spk = '".$par['id_spk']."' and id not in (select id_termin from tagihan_data) order by termin asc");
    echo json_encode($getData);
}

function hapus()
{
    global $par;

    $file = getField("select file_tagihan from tagihan_data where id = '".$par['id']."'");
    unlink($dirFile.$file);

    db("delete from tagihan_data where id = '".$par["id"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function lData()
{
    global $s, $par, $menuAccess, $arrParam;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE b.id_jenis = '".$arrParam[$s]."'";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(c.no_invoice) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(b.tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(b.tanggal) = '".$_GET['combo2']."'";

    if (!empty($_GET['combo3'])) $where .= " and b.id_supplier = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and c.status_pelunasan = '".$_GET['combo4']."'";
    if (!empty($_GET['combo5'])) $where .= " and b.id_sbu = '$_GET[combo5]'";
    if (!empty($_GET['combo6'])) $where .= " and b.id_proyek = '$_GET[combo6]'";
    $arrOrder = array("", "b.tanggal");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "a.id DESC";

//    $sql = "SELECT a.*, b.id as id_spk, b.nomor, c.termin, c.persen, c.nilai, d.namaSupplier FROM tagihan_data AS a
//            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100' and status_syarat = 't')
//            JOIN tagihan_termin AS c ON (c.id = a.id_termin)
//            JOIN dta_supplier AS d ON (d.kodeSupplier = a.id_supplier) $where order by $order $limit";

    $sql = "SELECT * FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND b.approve_status = 't' AND b.persen_termin = '100' AND b.status_syarat = 't')
            LEFT JOIN tagihan_data AS c ON (c.id_termin = a.id)
            $where order by $order $limit
            ";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND b.approve_status = 't' AND b.persen_termin = '100' AND b.status_syarat = 't')
                                            LEFT JOIN tagihan_data AS c ON (c.id_termin = a.id) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$r["id"]."" . getPar($par, "mode, id") . "',  980, 430);\" class=\"edit\"><span>Edit</span></a>";
        // if (isset($menuAccess[$s]["delete"]) and $r['tiket_status'] != 't') $kontrol .= "<a href=\"?par[mode]=delete&par[id]=".$r["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $nama = getField("select namasupplier from dta_supplier where kodeSupplier = '$r[id_supplier]'");

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["tgl_terima"])."</div>",
            "<div align=\"left\">".$nama."</div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailTagihan&par[pop_up]=1&par[id_tagihan]=".$r["id"]."" . getPar($par, "mode, id_tagihan") . "',  980, 400);\">".$r["no_invoice"]."</a></div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a></div>",
            "<div align=\"left\">".$r["termin"]."</div>",
            "<div align=\"right\">".$r["persen"]."%</div>",
            "<div align=\"right\">".getAngka($r["nilai"])."</div>",
            "<div align=\"left\">".$r["pengirim"]."</div>",
            "<div align=\"center\">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFile;

    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
    $fileIcon_name = $_FILES["fileUpload"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none"))
    {
        fileUpload($fileIcon, $fileIcon_name, $dirFile);
        $fileDokumen = "tagihan_".time().".".getExtension($fileIcon_name);
        fileRename($dirFile, $fileIcon_name, $fileDokumen);
        $updateFIle .= "file_tagihan = '".$fileDokumen."',";
    }

    $setData = "`id_spk` = '".$inp["id_spk"]."',
                `id_termin` = '".$inp["id_termin"]."',
                `id_supplier` = '".$inp["id_supplier"]."',
                `tgl_terima` = '".setTanggal($inp["tgl_terima"])."',
                `no_invoice` = '".$inp["no_invoice"]."',
                `pengirim` = '".$inp["pengirim"]."',
                `catatan` = '".$inp["catatan"]."',
                 $updateFIle
               ";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_data`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";

    } else {

        $sql = "UPDATE
                  `tagihan_data`
                SET
                  $setData
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$par["id"]."'
                ";

    }

    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
{
    global $par, $dirFile;

    $r = getRow("SELECT * FROM tagihan_data WHERE id = '$par[id]'");

    setValidation("is_null", "inp[id_termin]", "Termin Pembayaran tidak boleh kosong");
    setValidation("is_null", "inp[no_invoice]", "No. Invoice tidak boleh kosong");
    setValidation("is_null", "inp[pengirim]", "Pengirim tidak boleh kosong");
    $text .= getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">TAGIHAN</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
				    if ($r['tiket_status'] != "t") $text .= "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
				    $text .= "
				</p>
			</div>
			<fieldset>
			
                <p>
                    <label class=\"l-input-small\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["tgl_terima"] = empty($r["tgl_terima"]) ? date("Y-m-d") : $r["tgl_terima"];
                        $text.="
                        <input type=\"text\" id=\"inp[tgl_terima]\" name=\"inp[tgl_terima]\"  value=\"" . getTanggal($r["tgl_terima"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Supplier</label>
                    <div class=\"field\">
                        ";

                        if ($par['mode'] == "add") {
                            $sqlSupplier = "SELECT kodeSupplier, namaSupplier FROM dta_supplier WHERE kodeSupplier IN (SELECT DISTINCT(id_supplier) FROM tagihan_spk AS a
                                          JOIN tagihan_termin AS b ON (b.id_spk = a.id)
                                          WHERE 
                                          a.status_syarat = 't' AND b.id NOT IN (SELECT DISTINCT(id_termin) FROM tagihan_data))";
                        }

                        if ($par['mode'] == "edit") {
                            $sqlSupplier = "SELECT kodeSupplier, namaSupplier FROM dta_supplier WHERE kodeSupplier IN (SELECT DISTINCT(id_supplier) FROM tagihan_spk WHERE approve_status = 't' AND persen_termin = '100' AND status_syarat = 't') order by namaSupplier asc";
                        }

                        $text.="
                        ".comboData($sqlSupplier, "kodeSupplier", "namaSupplier", "inp[id_supplier]", "- Pilih Supplier -", $r["id_supplier"], "onchange=\"getSPK(this.value, '".getPar($par, "mode")."')\"", "630px", "chosen-select")."
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">No. SPK</label>
                    <div class=\"field\">
                        ".comboData("SELECT id, CONCAT(nomor, ' - ', judul) as nomor from tagihan_spk WHERE approve_status = 't' and persen_termin = '100' and status_syarat = 't' and id_supplier = '$r[id_supplier]' order by nomor asc", "id", "nomor", "inp[id_spk]", "- Pilih SPK -", $r["id_spk"], "onchange=\"getTermin(this.value, '".getPar($par, "mode")."')\"", "630px", "chosen-select")."
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Termin Pembayaran</label>
                    <div class=\"field\">
                        ".comboData("SELECT id, concat(persen, '%', ' - ', termin) as str_termin from tagihan_termin WHERE id_spk = '".$r['id_spk']."' and id not in (select id_termin from tagihan_data where id_termin != '".$r["id_termin"]."') order by termin asc", "id", "str_termin", "inp[id_termin]", "- Pilih Termin -", $r["id_termin"], "", "630px", "chosen-select")."
                    </div>
                </p>
                
                <style>
                    #inp_id_supplier__chosen{ min-width:630px; }
                    #inp_id_spk__chosen{ min-width:630px; }
                    #inp_id_termin__chosen{ min-width:630px; }
                </style>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">No. Invoice</label>
                                <div class=\"fieldA\">  
                                    <input type=\"text\" id=\"inp[no_invoice]\" name=\"inp[no_invoice]\"  value=\"".$r["no_invoice"]."\" class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Pengirim</label>
                                <div class=\"fieldA\">  
                                    <input type=\"text\" id=\"inp[pengirim]\" name=\"inp[pengirim]\"  value=\"".$r["pengirim"]."\" class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:620px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">File</label>
                    <div class=\"field\">";
                        $text .= empty($r['file_tagihan'])
                        ?
                        "<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
                        <div class=\"fakeupload\">
                            <input type=\"file\" id=\"fileUpload\" name=\"fileUpload\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
                        </div>"
                        :
                        "<a href=\"".$dirFile.$r['file_tagihan']."\" download><img src=\"".getIcon($r['file_tagihan'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        ".($r['tiket_status'] != "t" ? "<a href=\"?par[mode]=delFile&par[id]=".$r['id'].getPar($par,"mode, id")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                        <br clear=\"all\">";
                        $text.="
                    </div>
                </p>
			
			</fieldset>
		</form>
	</div>";

    return $text;
}

function lihat()
{
	global $s, $par, $arrTitle;

	$text = table(10, array(4, 5, 6, 7, 8, 9, 10));

    $combo1 = empty($combo1) ? date("m") : $combo1;
    $combo2 = empty($combo2) ? date("Y") : $combo2;

    $yearStart = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) asc limit 1");
    $yearEnd = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) desc limit 1");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left; display: flex;\">
			
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$fSearch."\" style=\"width:250px;\"/>
				&nbsp;
                ".comboMonth("combo1", $combo1, "", "100px", "ALL")." &nbsp;
                ".comboYear("combo2", $combo2, "", "", "80px", "ALL", $yearStart, $yearEnd)."
                
                <span id=\"bView\">
                    <input type=\"button\" value=\"+\" style=\"font-size:12px;\" class=\"btn btn_search btn-small\" onclick=\"
                        document.getElementById('bView').style.display = 'none';
                        document.getElementById('bHide').style.display = 'block';
                        document.getElementById('dFilter').style.display = 'block';							
                        document.getElementById('fSet').style.height = 'auto';
                        \" />
                </span>
                    
                <span id=\"bHide\" style=\"display:none\">
                    <input type=\"button\" value=\"-\" style=\"font-size:12px;\" class=\"btn btn_search btn-small\" onclick=\"
                        document.getElementById('bView').style.display = 'block';
                        document.getElementById('bHide').style.display = 'none';
                        document.getElementById('dFilter').style.display = 'none';							
                        document.getElementById('fSet').style.height = '0px';
                        \" />	
                </span>
                
			</div>
			
			<div id=\"pos_r\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
                <!-- <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 430); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a> -->
            </div>
            
            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Vendor</label>
                                    <div class=\"field\">
                                        ".comboData("select * from dta_supplier order by namaSupplier asc", "kodeSupplier", "namaSupplier", "combo3", "All", $combo3, "", "250px", "chosen-select")."
                                    </div>
                                    <style>#combo3_chosen{min-width:250px;}</style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Status Bayar</label>
                                    <div class=\"field\">
                                        ";
                                        $arrNilai = ['proses' => "Proses", 'sebagian' => "Sebagian", 'lunas' => "Lunas"];
                                        $text.="
                                        ".comboKey("combo4", $arrNilai, $combo4, "", "200px", "- Semua Status -", "chosen-select")."
                                    </div>
                                    <style>#combo4_chosen{min-width:250px;}</style>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                    <p>
                                        <label class=\"l-input-small\">Bisnis Unit</label>
                                        <div class=\"field\">
                                            " . comboData("select * from mst_data where kodeCategory = 'KSBU' order by urutanData", "kodeData", "namaData", "combo5", "Semua SBU", $combo5, "onchange=\"getFilter(this.value, '" . getPar($par, "mode") . "')\"", "200px", "chosen-select") . "
                                        </div>
                                        <style>
                                                #combo5_chosen{ min-width:210px; }
                                        </style>
                                    </p>
                                    <p>
                                        <label class=\"l-input-small\">Proyek</label>
                                        <div class=\"field\">
                                            " . comboData("select * from proyek_data where sbu = '" . $r['id_sbu'] . "' order by proyek asc", "id", "proyek", "combo6", "Semua Proyek", $combo6, "", "200px", "chosen-select") . "
                                        </div>
                                        <style>
                                                #combo6_chosen{ min-width:210px; }
                                        </style>
                                    </p>
                            </td>
                    </table>
                </fieldset>
            </div>
            
		</form>
		
		<br clear=\"all\" />

        <div style=\"overflow-x: scroll\">
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th style=\"vertical-align: middle; min-width: 20px;\">No</th>
                    <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                    <th style=\"vertical-align: middle; min-width: 250px;\">Supplier</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">No. Invoice</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">No. SPK</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">Tahap</th>
                    <th style=\"vertical-align: middle; min-width: 20px;\">%</th>
                    <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">Pengirim</th>
                    <th style=\"vertical-align: middle; min-width: 50px;\">Kontrol</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        </div>
	</div>
	";

	if ($par[mode] == "xls"){
        xls();
        $text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    $text.="
    <script>
    	jQuery(\"#btnExport\").live('click', function(e){
    		e.preventDefault();
    		window.location.href=\"?par[mode]=xls".getPar($par,"mode, fSearch, combo1, combo2, combo3, combo4, combo5, combo6")."&par[fSearch]=\"+jQuery(\"#fSearch\").val() + \"&par[combo1]=\"+jQuery(\"#combo1\").val() + \"&par[combo2]=\"+jQuery(\"#combo2\").val() + \"&par[combo3]=\"+jQuery(\"#combo3\").val() + \"&par[combo4]=\"+jQuery(\"#combo4\").val() + \"&par[combo5]=\"+jQuery(\"#combo5\").val() + \"&par[combo6]=\"+jQuery(\"#combo6\").val() ;
    	});
    </script>
    ";

    return $text;
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal",
              "Supplier",
              "No. Invoice",
              "No. SPK",
              "Tahap",
              "%",
              "Nilai",
              "Pengirim"];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(no_invoice) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(namaSupplier) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(termin) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(tgl_terima) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(tgl_terima) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and d.kodeSupplier = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and a.status_pelunasan = '".$par['combo4']."'";

    $order = "id DESC";

    $sql = "SELECT a.*, b.nomor, c.termin, c.persen, c.nilai, d.namaSupplier FROM tagihan_data AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100' and status_syarat = 't')
            JOIN tagihan_termin AS c ON (c.id = a.id_termin)
            JOIN dta_supplier AS d ON (d.kodeSupplier = a.id_supplier) $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $data[]=[
			$no . "\t center",
			getTanggal($r["tgl_terima"]) . "\t center",
			$r["namaSupplier"]."\t left",
			$r["no_invoice"] . "\t center",
			$r["nomor"] . "\t center",
			$r['termin'] . "\t left",
			$r['persen'] . "%\t right",
			getAngka($r["nilai"]) . "\t right",
			$r["pengirim"]. "\t left"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}