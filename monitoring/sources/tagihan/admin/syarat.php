<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

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

        case "detail":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up']);
            break;

        case "sinkronise":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formSinkron() : simpanSinkron(); else $text = lihat();
            break;

        case "detailForm":
            $text = detailForm();
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "getJenis":
            $text = getJenis();
            break;

    }

    return $text;
}

function getJenis()
{
    global $par;

    $getData = getRows("SELECT * FROM mst_data WHERE kodeInduk = '" . $par['kategori'] . "'");
    echo json_encode($getData);
}

function simpanSinkron()
{
    global $inp, $par, $cID;

    db("delete from tagihan_syarat where id_spk = '$par[id_spk]' and id_termin = '$par[id_termin]'");

    $dokumen = getRows("SELECT * FROM dokumen_pendukung WHERE kategori = '$inp[kategori]' AND jenis = '$inp[jenis]' ORDER BY urut ASC");
    foreach ($dokumen as $dk) {
        $sql = "INSERT
                    `tagihan_syarat`
                SET
                    `id_spk` = '".$par[id_spk]."',
                    `id_termin` = '".$par[id_termin]."',
                    `judul` = '".$dk["dokumen"]."',
                    `jenis_dokumen` = '".$dk["lembar"]."',
                    `mandatory` = '".$dk["mandatory"]."',
                    `catatan` = '".$dk["keterangan"]."',
                    `created_at` = now(),
                    `created_by` = '".$cID."'
                ";
        db($sql);
    }

    update_syarat($par['id_spk'], $par['id_termin']);

    echo "<script>closeBox(); alert(\"Data berhasil disinkron.\"); reloadPage();</script>";
}

function formSinkron()
{
    global $par;

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">SINKRONISE</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>
				</p>
			</div>
			<fieldset>
			    <p>
                    <label class=\"l-input-small\">Kategori SP3</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'MDKS' order BY namaData asc", "kodeData", "namaData", "inp[kategori]", "- Pilih Kategori -", '', "onchange=\"getJenis(this.value, '" . getPar($par, "mode") . "')\"", "250px", "chosen-select") . "
                    </div>
                    <style>#inp_kategori__chosen{min-width:250px;}</style>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Jenis</label>
                    <div class=\"field\">
                        " . comboData("", "", "", "inp[jenis]", "- Pilih Jenis -", '', "", "250px", "chosen-select") . "
                    </div>
                    <style>#inp_jenis__chosen{min-width:250px;}</style>
                </p>
			</fieldset>
		</form>
	</div>";

    return $text;
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal",
              "Judul - Nomor",
              "Pemohon",
              "Kategori",
              "Nilai",
              "APPROVAL"];

    $where = " WHERE approve_status = 't'";

    if (!empty($par['fSearch'])){
        $where .= " and (     
        lower(nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(tanggal) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(tanggal) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and kodeData = '$par[combo3]'";
    if (!empty($par['combo4'])) $where .= " and id_supplier = '$par[combo4]'";
    if (!empty($par['combo5'])){
        $where .= " and id_sbu = '$par[combo5]'";
        
        if (!empty($par['combo6'])){
            $where .= " and id_proyek = '$par[combo6]'";
        }
    }

    $order = "id DESC";

    $sql = "SELECT a.*, c.namaData, b.namaSupplier, d.nama
            from tagihan_spk as a
            join mst_data as c ON (c.kodeData = a.id_jenis)
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS d ON (d.id = a.id_supplier)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $appr = "Menunggu Persetujuan";
        if ($r["approve_status"] == "t") $appr = "Setuju";
        if ($r["approve_status"] == "f") $appr = "Tolak";
        if ($r["approve_status"] == "p") $appr = "Pending";

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["tanggal"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"] . "\t left",
            $r['namaData'] . "\t left",
			getAngka($r["total"]) . "\t right",
			$appr. "\t left"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $text .= "
    ".view_permohonan($arrTitle[$s], $par['id_spk'], '', false, $par['id_termin'])."

    <br>
    <br />

    <div id=\"contentwrapper\" class=\"contentwrapper\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>DOKUMEN PENDUKUNG</h3>
                    ";
                    if (isset($menuAccess[$s]["add"]) and $r['status_serah_terima'] != 't') $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:-20px;\" onclick=\"openBox('popup.php?par[mode]=sinkronise" . getPar($par, "mode") . "', 650, 280); \" class=\"btn btn1 btn_document\"><span>Sinkronise</span></a>";
                    if (isset($menuAccess[$s]["add"]) and $r['status_serah_terima'] != 't') $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:5px;\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 650, 280); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
                    $text.="
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th rowspan=\"2\" style=\"vertical-align: middle; \" width=\"20\">No</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; \" width=\"*\">Judul</th>
                        <th colspan=\"2\"style=\"vertical-align: middle; \" width=\"40\">Jenis</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; \" width=\"40\">Mandatory</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; \" width=\"200\">Catatan</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; \" width=\"75\">Kontrol</th>
                    </tr>
                    <tr>
                        <th width='20'>Asli</th>
                        <th width='20'>Copy</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select * from tagihan_syarat where id_termin = '".$par['id_termin']."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            if ($data['jenis_dokumen'] == 'a'){
                                $asli = "<img src=\"styles/images/icons/check.png\" title='Asli'>";
                            } else {
                                $asli = "";
                            }

                            if ($data['jenis_dokumen'] == 'c'){
                                $copy = "<img src=\"styles/images/icons/check.png\" title='Copy'>";
                            } else {
                                $copy = "";
                            }

                            if ($data['mandatory'] == '1'){
                                $mandatory = "<img src=\"styles/images/icons/check.png\" title='Mandatory'>";
                            } else {
                                $mandatory = "";
                            }

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[status_serah_terima]=$r[status_serah_terima]&par[id]=".$data["id"]."" . getPar($par, "mode, id, status_serah_terima") . "', 650, 280);\" class=\"edit\"><span>Edit</span></a>";
                            if (isset($menuAccess[$s]["delete"]) and $r['status_serah_terima'] != 't') $kontrol .= "<a href=\"?par[mode]=delete&par[id]=".$data["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data['judul']."</td>
                                <td align=\"center\">".$asli."</td>
                                <td align=\"center\">".$copy."</td>
                                <td align=\"center\">".$mandatory."</td>
                                <td align=\"left\">".$data['catatan']."</td>
                                <td align=\"center\">".$kontrol."</td>
                            </tr>
                            ";
                        }

                    } else {

                        $text.="
                        <tr>
                            <td colspan=\"7\"><strong><center>- Data Kosong -</center></strong></td>
                        </tr>
                        ";

                    }
                    $text.="
                </tbody>
            </table>
		</form>
		
		
    </div>";

    return $text;
}

function hapus()
{
    global $par;

    db("delete from tagihan_syarat where id = '".$par["id"]."'");

    update_syarat($par['id_spk'], $par['id_termin']);

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode, id") . "';</script>";
}

function update_syarat($id_spk, $id_termin) {

    $getTermin = getRows("select * from tagihan_termin where id = '".$id_termin."'");
    $statusSyarat = "t";
    foreach ($getTermin as $term) {

        $cekSyarat = getField("select id from tagihan_syarat where id_termin = '".$term['id']."'");
        if (empty($cekSyarat)) $statusSyarat = "f";

    }

    db("update tagihan_spk set status_syarat = '".$statusSyarat."' where id = '".$id_spk."'");

}

function simpan()
{
    global $inp, $par, $cID;


    $setData = "`judul` = '".$inp["judul"]."',
                `jenis_dokumen` = '".$inp["jenis_dokumen"]."',
                `mandatory` = '".$inp["mandatory"]."',
                `catatan` = '".$inp["catatan"]."',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_syarat`
                SET
                  `id_spk` = '".$par["id_spk"]."',
                  `id_termin` = '".$par["id_termin"]."',
                   $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";

    } else {

        $sql = "UPDATE
                  `tagihan_syarat`
                SET
                   $setData
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$par["id"]."'
                ";
    }

    db($sql);

    update_syarat($par['id_spk'], $par['id_termin']);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_syarat WHERE id = '$par[id]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">DOKUMEN PENDUKUNG</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
					if ($par['status_serah_terima'] != 't') $text.="<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
					$text.="
				</p>
			</div>
			<fieldset>
			    <p>
                    <label class=\"l-input-small\">Nama Dokumen</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"".$r["judul"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <table style='width: 100%;'>
                    <tr>
                        <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\">Status</label>
                                <div class=\"fradio\">
                                    <input type=\"radio\" id=\"inp[jenis_dokumen]\" name=\"inp[jenis_dokumen]\" value=\"a\" " . (($r["jenis_dokumen"] == "a" or empty($r["jenis_dokumen"])) ? "checked" : "") . "/> <span class=\"sradio\">Asli</span>
                                    <input type=\"radio\" id=\"inp[jenis_dokumen]\" name=\"inp[jenis_dokumen]\" value=\"c\" " . ($r["jenis_dokumen"] == "c" ? "checked" : "") . " /> <span class=\"sradio\">Copy</span>
                                </div>
                            </p>
                        </td>
                        <td style='width: 40%;'> 
                            <p>
                                <label class=\"l-input-small2\"\">Mandatory</label>
                                <div class=\"fradio\">  
                                     <input type=\"checkbox\" ".($r[mandatory] == '1' ? "checked=\"checked\"" : "")." onclick=\"checkMandatory();\" id=\"inp[mandatory]\" name=\"inp[mandatory]\" value=\"1\"/> Ya
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
			
			</fieldset>
		</form>
	</div>";

    return $text;
}

function lData()
{
    global $par;

    if($_GET[json] == 1){
        header("Content-type: application/json");
    }

     $where = " WHERE approve_status = 't'";

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
    }

    if (!empty($_GET['fSearch'])){
        $where .= " and (     
        lower(nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(tanggal) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and kodeData = '$_GET[combo3]'";
    if (!empty($_GET['combo4'])) $where .= " and id_supplier = '$_GET[combo4]'";
    if (!empty($_GET['combo5'])){ 
        $where .= " and id_sbu = '$_GET[combo5]'";
        
        if (!empty($_GET['combo6'])){
            $where .= " and id_proyek = '$_GET[combo6]'";
        }
    }

    $arrOrder = array("", "tanggal", "judul");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "id DESC";

    $sql = "SELECT a.*, c.namaData
            from tagihan_spk as a
            join mst_data as c ON (c.kodeData = a.id_jenis) 
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) from tagihan_spk as a
                                            join mst_data as c ON (c.kodeData = a.id_jenis) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    $data = array();
    while ($r = mysql_fetch_array($res)) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $data[] = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["tanggal"])."</div>",
            "<div align=\"left\">".$r["judul"]."<br><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detail&par[pop_up]=1&par[id_spk]=".$r["id"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a></div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
            "<div align=\"left\">".$r["namaData"]."</div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
        );

        $getTermin = getRows("select * from tagihan_termin where id_spk = '".$r["id"]."' order by id asc");
        foreach ($getTermin as $trm) {

            $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$trm['id']."'");

            $background = $syarat > 0 ? "class=\"labelStatusHijau\"" : "class=\"labelStatusKuning\"";

            $data[] = array(
                "<div align=\"center\"></div>",
                "<div align=\"center\">".getTanggal($trm["target"])."</div>",
                "<div align=\"left\">".$trm["termin"]."</div>",
                "<div align=\"left\"></div>",
                "<div align=\"right\">".getAngka($trm["nilai_plus_ppn"])."</div>",
                "<div align=\"left\">".getAngka($trm["persen"], 2)."%</div>",
                "<div align=\"center\" ".$background.">".getAngka($syarat)."</div>",
                "<div align=\"center\"><a href=\"?par[mode]=detailForm&par[id_termin]=".$trm["id"]."&par[id_spk]=".$r["id"].getPar($par, "mode, id_spk, id_termin")."\" class=\"edit\"><span>Detail</span></a></div>",
            );

        }


    }

    $json['aaData'] = $data;

    return json_encode($json);
}

function lihat()
{
    global $s, $arrTitle, $par;

    $text = table(8, array( 4, 5, 6, 7, 8, 9));

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
			<div id=\"pos_l\" style=\"float:left; width:750px; display:flex;\">
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$fSearch."\" style=\"width:250px;\"/>
                &nbsp;
                ".comboMonth("combo1", $combo1, "", "120px", "ALL")." &nbsp;
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
            </div>
            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Kategori SP3</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'MDKS' order BY urutanData asc", "kodeData", "namaData", "combo3", "Semua Kategori", $combo3, "", "250px", "chosen-select") . "
                                    </div>
                                    <style>#combo3_chosen{min-width:210px;}</style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Pemohon</label>
                                    <div class=\"field\">
                                        " . comboData("select * from dta_supplier where kodeSupplier in (SELECT DISTINCT(id_supplier) from tagihan_spk) order by namaSupplier asc", "kodeSupplier", "namaSupplier", "combo4", "Semua Pemohon", $combo4, "", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo4_chosen{ min-width:210px; }
                                    </style>
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
                        </tr>
                    </table>
                </fieldset>
            </div>
		</form>
		
		<br clear=\"all\" />

		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th width=\"20\">No</th>
                    <th width=\"80\">Tanggal</th>
                    <th width=\"*\">Judul - Nomor - Termin</th>
                    <th width=\"150\">Pemohon</th>
                    <th width=\"70\">Nilai</th>
                    <th width=\"100\">Jenis</th>
                    <th width=\"60\">Syarat</th>
                    <th width=\"50\">Detail</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
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



function detail(){

    global $s, $_submit, $menuAccess, $arrTitle, $par;

    $judul = strtoupper($arrTitle[$s]);

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">". $judul ."</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">";

    $text .= view_permohonan($par['id_spk'], $par['pop_up'], false);
    $text .= "</div>";
    return $text;
}

function sinkronise()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_syarat WHERE id = '$par[id]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">DOKUMEN PENDUKUNG: SINKRONISE</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
    if ($par['status_serah_terima'] != 't') $text.="<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
    $text.="
				</p>
			</div>
			<fieldset>
			    <p>
                    <label class=\"l-input-small\">Nama Dokumen</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"".$r["judul"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
                <table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\">Status</label>
                                <div class=\"fradio\">
                                    <input type=\"radio\" id=\"inp[jenis_dokumen]\" name=\"inp[jenis_dokumen]\" value=\"a\" " . (($r["jenis_dokumen"] == "a" or empty($r["jenis_dokumen"])) ? "checked" : "") . "/> <span class=\"sradio\">Asli</span>
                                    <input type=\"radio\" id=\"inp[jenis_dokumen]\" name=\"inp[jenis_dokumen]\" value=\"c\" " . ($r["jenis_dokumen"] == "c" ? "checked" : "") . " /> <span class=\"sradio\">Copy</span>
                                </div>
                            </p>
                    </td>
                    <td style='width: 40%;'> 
                            <p>
                                <label class=\"l-input-small2\"\">Mandatory</label>
                                <div class=\"fradio\">  
                                     <input type=\"checkbox\" ".($r[mandatory] == '1' ? "checked=\"checked\"" : "")." onclick=\"checkMandatory();\" id=\"inp[mandatory]\" name=\"inp[mandatory]\" value=\"1\"/> Ya
                                </div>
                            </p>
                    </td>
                </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
			
			</fieldset>
		</form>
	</div>";

    return $text;
}