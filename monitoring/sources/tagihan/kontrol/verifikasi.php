<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_data/";
$dirFileBa = "files/tagihan_ba/";

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

        case "edit":
            $text = form();
            break;

        case "detailForm":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detailForm() : simpan(); else $text = lihat();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up']);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;
    }

    return $text;
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '".$termin['id_spk']."'");

    $arrValue = arrayQuery("SELECT * FROM tagihan_syarat WHERE id = '$par[id]' ");

    $text.="
          
            ".view_permohonan($arrTitle[$s], $termin['id_spk'], '', false)."  
            
            <br />
            <br />
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
                
                <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
                
                    <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                        <div class=\"title\">
                            <h3>SYARAT & KETENTUAN</h3>
                        </div>
                        <input style=\"float: right; margin-top: -55px\" type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
                    </div>
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                        <thead>
                            <tr>
                                <th width=\"20\" style=\"vertical-align: middle\">No</th>
                                <th width=\"*\" style=\"vertical-align: middle\">Dokumen</th>
                                <th width=\"200\" style=\"vertical-align: middle\">Update</th>
                                <th width=\"50\" style=\"vertical-align: middle\">Cek</th>
                                <th width=\"75\" style=\"vertical-align: middle\">Kontrol</th>
                            </tr>
                        </thead>
                        <tbody>
                            ";
                            $getData = getRows("select * from tagihan_syarat where id_termin = '".$par['id_termin']."' order by id asc");
                            if ($getData) {

                                $no = 0;
                                foreach ($getData as $data) {

                                    $no++;

                                    if ($data['ba_verifikasi_status'] == 't') $data['ba_verifikasi_status'] = "Diterima";
                                    if ($data['ba_verifikasi_status'] == 'f') $data['ba_verifikasi_status'] = "Ditolak";

                                    $kontrol = "";
                                    if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$data["id"]."" . getPar($par, "mode, id") . "', 650, 470);\" class=\"detail\"><span>Edit</span></a>";

                                    $checked = ($data[kontrol_verifikasi] == '1') ? "checked=\"\"" : "";

                                    $text.="
                                    <tr>
                                        <td align=\"center\">".$no."</td>
                                        <td align=\"left\">".$data['judul']."</td>
                                        <td align=\"center\">".$data['updated_at']."</td>
                                        <td align=\"center\">
                                            <input type=\"checkbox\" id=\"inp[verifikasi][$data[id]]\" name=\"inp[verifikasi][$data[id]]\" value=\"1\" $checked/>
                                        </td>
                                        <td align=\"center\">".$kontrol."</td>
                                    </tr>
                                    ";
                                }

                            } else {

                                $text.="
                                <tr>
                                    <td colspan=\"6\"><strong><center>- Data Kosong -</center></strong></td>
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
        lower(no_invoice) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(namaSupplier) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(termin) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(c.tgl_terima) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(c.tgl_terima) = '".$_GET['combo2']."'";
    $arrOrder = array("", "tgl_terima", "namaSupplier");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.termin, 
            a.nilai, 
            a.persen,
            a.id_spk,
            c.id as id_tagihan,
            b.nomor, 
            b.id_sbu,
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
            d.namaSupplier 
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100' and status_syarat = 't')
            JOIN tagihan_data AS c ON (c.id_termin = a.id)
            JOIN dta_supplier AS d ON (d.kodeSupplier = c.id_supplier) $where order by $order $limit";
    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100' and status_syarat = 't')
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                            JOIN dta_supplier AS d ON (d.`kodeSupplier` = c.id_supplier) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."'");
        $cek = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and kontrol_verifikasi = '1'");

        if ($syarat == $cek){ #ini kalau full
            $background = "style=\"background-color: #02e819\"";
        } elseif ($cek > 0) {
            $background = "style=\"background-color: #67b7dc\""; #ini kalau kriterianya ada, tapi ga full
        } else { #kalo 0
            $background = "style=\"background-color: #fdd400\"";
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_termin]=".$r["id"].getPar($par, "mode, id_termin")."\" class=\"edit\"><span>Detail</span></a>";

        $title = $arrParam[$s] == 1048 ? $r["namaSupplier"] : getField("select namaData from mst_data where kodeData = '$r[id_sbu]'");

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["tgl_terima"])."</div>",
            "<div align=\"left\">".$title."</div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailTagihan&par[pop_up]=1&par[id_tagihan]=".$r["id_tagihan"]."" . getPar($par, "mode, id_tagihan") . "',  980, 400);\">".$r["no_invoice"]."</a></div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a></div>",
            "<div align=\"left\">".$r["termin"]."</div>",
            "<div align=\"right\">".getAngka($r["nilai"])."</div>",
            "<div align=\"center\" ".$background.">".getAngka($syarat)." / ".getAngka($cek)." </div>",
            "<div align=\"center\">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFileBa;

    $getData = getRows("select * from tagihan_syarat where id_termin = '".$par['id_termin']."'");

    foreach ($getData as $dt) {

        $val = $inp[verifikasi][$dt[id]] ? 1 : 0;
        db("update tagihan_syarat set kontrol_verifikasi = '$val', kontrol_verifikasi_by = '$cID' where id = '$dt[id]'");

    }

    echo "<script>alert('Data berhasil disimpan')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id, id_termin") . "';</script>";
}

function update_serah_terima($id_termin) {

    $getTermin = getRows("select * from tagihan_termin where id = '".$id_termin."'");
    $status = "t";
    foreach ($getTermin as $term) {

        $cekSyarat = getField("select ba_file from tagihan_syarat where id_termin = '".$term['id']."'");
        if (empty($cekSyarat)) $status = "f";

    }

    db("update tagihan_spk set status_serah_terima = '".$status."' where id = '".$term['id_spk']."'");

}

function form()
{
    global $par, $dirFileBa;

    $r = getRow("SELECT a.*, b.tiket_status 
                FROM tagihan_syarat as a  
                join tagihan_data as b on (b.id_termin = a.id_termin and b.id_spk = a.id_spk)
                WHERE a.id = '$par[id]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">VERIFIKASI</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<!--
				<p>
				    ";
				    if ($r['tiket_status'] != "t") $text .= "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
				    $text .= "
				</p>
				-->
			</div>
			<fieldset>
            <legend>Dokumen</legend>
			    <p>
                    <label class=\"l-input-small\">Judul</label>
                    <span class=\"field\">
                        ".$r["judul"]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <span class=\"field\">
                       ".$r["catatan"]." &nbsp; 
                    </span>
                </p>
			</fieldset>
			
			<br>
			
			<fieldset>
			    <legend>Persetujuan</legend>
			    <p>
                    <label class=\"l-input-small\">Tgl Pelaksanaan</label>
                    <span class=\"field\">
                        " . getTanggal($r["ba_tanggal"]) . " &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\">
                        ".$r["ba_catatan"]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">File</label>
                    <div class=\"field\">";
                        $text .= empty($r['ba_file'])
                        ?
                        ""
                        :
                        "<a href=\"".$dirFileBa.$r['ba_file']."\" download><img src=\"".getIcon($r['ba_file'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>";
                        $text.="
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Verifikasi</label>
                    <span class=\"field\">
                        ".($r["ba_verifikasi"] == "f" ? "Ditolak" : "Diterima")." &nbsp;
                    </span>
                </p>
			</fieldset>
		</form>
	</div>";

    return $text;
}

function lihat()
{
	global $s, $par, $arrTitle, $arrParam;

	$text = table(9, array(3, 4, 5, 6, 7, 8, 9));

    $combo1 = empty($combo1) ? date("m") : $combo1;
    $combo2 = empty($combo2) ? date("Y") : $combo2;

    $yearStart = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) asc limit 1");
    $yearEnd = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) desc limit 1");

    $title = $arrParam[$s] == 1048 ? 'Pemohon' : 'Bisnis Unit';

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
                
			</div>
			
			<div id=\"pos_r\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
            </div>
            
		</form>
		
		<br clear=\"all\" />
		
        <div style=\"overflow-x: scroll\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
                <thead>
                    <tr>
                        <th style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th style=\"vertical-align: middle; min-width: 250px;\">$title</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">No. Invoice</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">No. Permohonan</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Tahap</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Kontrol</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Detil</th>
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
              "Vendor",
              "No. Invoice",
              "No. SPK",
              "Tahap",
              "%",
              "Nilai",
              "Dok"];

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

    if (!empty($par['combo1'])) $where .= " and month(c.tgl_terima) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(c.tgl_terima) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and d.kodeSupplier = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and c.status_pelunasan = '".$par['combo4']."'";

    $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.termin, 
            a.nilai, 
            a.persen,
            c.id as id_tagihan,
            b.nomor, 
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
            d.namaSupplier 
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100' and status_syarat = 't')
            JOIN tagihan_data AS c ON (c.id_termin = a.id)
            JOIN dta_supplier AS d ON (d.kodeSupplier = c.id_supplier) $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."'");
        $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and ba_verifikasi = 't'");

        $data[]=[
			$no . "\t center",
			getTanggal($r["tgl_terima"]) . "\t center",
			$r["namaSupplier"]."\t left",
			$r["nomor"] . "\t center",
			$r["no_invoice"] . "\t center",
			$r['termin'] . "\t left",
			$r['persen'] . "% \t right",
			getAngka($r["nilai"]) . "\t right",
			getAngka($syarat)." / ".getAngka($ba). "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}