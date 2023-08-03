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

        case "form":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "detailForm":
            $text = detailForm();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par['id_termin'], true);
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

function generateNoTiket()
{
    $getlastNumber = getField("SELECT SUBSTR(tiket_nomor, 7, 4) FROM tagihan_data WHERE SUBSTR(tiket_nomor, 3, 2) = '" . date('m') . "' and SUBSTR(tiket_nomor, 5, 2) = '" . date('y') . "' ORDER BY SUBSTR(tiket_nomor, 7, 4) DESC LIMIT 1");
    $str = (empty($getlastNumber)) ? "0000" : $getlastNumber;
    $incNum = str_pad($str + 1, 4, "0", STR_PAD_LEFT);
    return "PY" . date('m')  . date('y')  . $incNum;
}

function simpan()
{
    global $inp, $par, $cID;

    $tiket = getField("select tiket_nomor from tagihan_data where `id` = '".$par['id_tagihan']."'");

    $nomor_tiket = empty($tiket) ? generateNoTiket() : $tiket;

    $sql = "UPDATE
                  `tagihan_data`
                SET
                  `tiket_nomor` = '".$nomor_tiket."',
                  `tiket_tanggal` = '".setTanggal($inp["tiket_tanggal"])."',
                  `tiket_catatan` = '".$inp['tiket_catatan']."',
                  `tiket_status` = '".$inp['tiket_status']."',
                  `rencana_bayar` = '".setTanggal($inp["rencana_bayar"])."',
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$par['id_tagihan']."'
                ";
    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
{
    global $par;

    $r = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">TIKET PEMBAYARAN</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
				    $cek = getField("select id from tagihan_bayar where id_tagihan = '".$par['id_tagihan']."' limit 1");
				    if (empty($cek)) $text.="<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
				    $text.="
				</p>
			</div>
			<fieldset>
			    
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal</label>
                                <div class=\"fieldA\">  
                                    ";
                                    $r["tiket_tanggal"] = $r["tiket_tanggal"] ?: date('Y-m-d');
                                    $text.="
                                    <input type=\"text\" id=\"inp[tiket_tanggal]\" name=\"inp[tiket_tanggal]\"  value=\"" . getTanggal($r["tiket_tanggal"]) . "\" readonly class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor</label>
                                <div class=\"fieldA\">  
                                    ";
                                    $r["tiket_nomor"] = $r["tiket_nomor"] ?: generateNoTiket();
                                    $text.="
                                    <input type=\"text\" id=\"inp[tiket_nomor]\" name=\"inp[tiket_nomor]\" readonly  value=\"".$r["tiket_nomor"]."\" class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <div class=\"field\">
                        <textarea name=\"inp[tiket_catatan]\" style=\"width:300px;\" id=\"inp[tiket_catatan]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >" . nl2br($r["tiket_catatan"]) . "</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[tiket_status]\" name=\"inp[tiket_status]\" value=\"t\" ".( ($r["tiket_status"] == "t" or empty($r["tiket_status"])) ? "checked" : "")."/> <span class=\"sradio\">Lanjut</span>
                        <input type=\"radio\" id=\"inp[tiket_status]\" name=\"inp[tiket_status]\" value=\"p\" ".($r["tiket_status"] == "p" ? "checked" : "")." /> <span class=\"sradio\">Pending</span>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Rencana Bayar</label>
                    <div class=\"field\">
                        ";
                        $r["rencana_bayar"] = $r["rencana_bayar"] ?: date('Y-m-d');
                        $text.="
                        <input type=\"text\" id=\"inp[rencana_bayar]\" name=\"inp[rencana_bayar]\"  value=\"" . getTanggal($r["rencana_bayar"]) . "\" class=\"hasDatePicker\" maxlength=\"150\"/>
                    </div>
                </p>
            </fieldset>
		</form>
	</div>";

    return $text;
}

function lData()
{
    global $s, $par, $menuAccess;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE a.pengajuan_approve_status = 't'";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(c.no_permohonan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(d.namaSupplier) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(e.nama) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(a.pengajuan_no_tiket) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(c.tiket_nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }


    if (!empty($_GET['combo1'])) $where .= " and month(a.target) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.target) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and b.id_jenis = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and b.id_supplier = '".$_GET['combo4']."'";
    if (!empty($_GET['combo5'])) {
        $where .= " and b.id_sbu = '".$_GET['combo5']."'";
        if (!empty($_GET['combo6'])) $where .= " and b.id_proyek = '".$_GET['combo6']."'";
    }

    $arrOrder = array("", "a.target");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.nilai_total, 
            a.persen,
            a.id_spk,
            a.pengajuan_no_tiket,
            c.id as id_tagihan,
            b.id_jenis,
            b.judul,
            b.nomor,
            b.tanggal, 
            b.id_supplier,
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
            c.no_permohonan,
            c.tiket_tanggal,
            c.tiket_status,
            c.tiket_nomor,
            c.rencana_bayar,
            d.namaSupplier,
            e.nama
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
            $where order by $order $limit";
    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
                                            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
                                            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
                                            $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        if ($r['tiket_status'] == "") {
            $tiket_status = "Proses";
            $background = "class=\"labelStatusKuning\"";
        }
        if ($r['tiket_status'] == "t") {
            $tiket_status = $r['tiket_nomor'];
            $background = "class=\"labelStatusHijau\"";
        }
        if ($r['tiket_status'] == "p") {
            $tiket_status = $r['tiket_nomor'];
            $background = "class=\"labelStatusBiru\"";
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a style=\"text-decoration: none !important;\" onclick=\"openBox('popup.php?par[mode]=form&par[id_tagihan]=".$r['id_tagihan'] . getPar($par, "mode, id_tagihan") . "', 700, 320);\" href=\"#\" >$tiket_status</a>";
        else $kontrol = $tiket_status;

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&&par[id_termin]=" . $r["id"]."&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_total"])."</div>",
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"center\">".getTanggal($r['rencana_bayar'])."</div>",
            "<div align=\"center\" ".$background.">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $par, $arrTitle;

	$text = table(8, array( 3, 4, 5, 6, 7, 8));

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

        <div style=\"overflow-x: scroll\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
                <thead>
                    <tr>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 70px;\">Rencana</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th colspan=\"2\" style=\"vertical-align: middle; min-width: 120px;\">Pembayaran</th>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor</th>
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
              "Judul - Nomor",
              "Pemohon",
              "Nilai",
              "No. Tiket",
              "Pembayaran" => ["Tanggal", "Nomor"]];

    $where = " WHERE a.pengajuan_approve_status = 't'";

     if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(c.no_permohonan) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(d.namaSupplier) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(e.nama) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(b.judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(a.pengajuan_no_tiket) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(c.tiket_nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }


    if (!empty($par['combo1'])) $where .= " and month(a.target) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(a.target) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and b.id_jenis = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and b.id_supplier = '".$par['combo4']."'";
    if (!empty($par['combo5'])) {
        $where .= " and b.id_sbu = '".$par['combo5']."'";
        if (!empty($par['combo6'])) $where .= " and b.id_proyek = '".$par['combo6']."'";
    }

    $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.nilai_total, 
            a.persen,
            a.id_spk,
            a.pengajuan_no_tiket,
            c.id as id_tagihan,
            b.id_jenis,
            b.judul,
            b.nomor,
            b.tanggal, 
            b.id_supplier,
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
            c.no_permohonan,
            c.tiket_tanggal,
            c.tiket_status,
            c.tiket_nomor,
            c.rencana_bayar,
            d.namaSupplier,
            e.nama
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier) $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;
        if ($r['tiket_status'] == "") {
            $tiket_status = "Proses";
        }
        if ($r['tiket_status'] == "t") {
            $tiket_status = $r['tiket_nomor'];
        }
        if ($r['tiket_status'] == "p") {
            $tiket_status = $r['tiket_nomor'];
        }

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";


        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			getAngka($r["nilai_total"]) . "\t right",
            $r["pengajuan_no_tiket"]."\t center",
			getTanggal($r['rencana_bayar']). "\t center",
			$tiket_status. "\t center",
		];
    }

    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}