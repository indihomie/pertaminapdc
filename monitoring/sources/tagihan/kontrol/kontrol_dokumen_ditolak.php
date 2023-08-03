<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFileBa = "files/tagihan_ba/";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $arrTitle;

    switch ($par[mode]) {

        default:
            $text = lihat();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], true, $par['id_termin'], true);
            break;

        case "lst":
            $text=lData();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "delFile":
            $text=delFile();
            break;

    }

    return $text;
}

function update_serah_terima($id_termin) {

//    $getTermin = getRows("select * from tagihan_termin where id = '".$id_termin."'");
//    $status = "t";
//    foreach ($getTermin as $term) {
//
//        $cekSyarat = getField("select ba_file from tagihan_syarat where id_termin = '".$term['id']."'");
//        if (empty($cekSyarat)) $status = "f";
//
//    }
//
//    db("update tagihan_spk set status_serah_terima = '".$status."' where id = '".$term['id_spk']."'");

    $status = "t";
    $getDok = getRows("select * from tagihan_syarat where id_termin = '$id_termin'");
    foreach ($getDok as $dok) {

        if ($dok[ba_verifikasi_status] != 't') $status = "f";

    }

    db("update tagihan_termin set verifikasi_dokumen = '".$status."' where id = '".$id_termin."'");

}

function delFile()
{
    global $par, $dirFileBa;

    $file = getField("select ba_file from tagihan_syarat where id = '".$par['id']."'");
    db("update tagihan_syarat set ba_file = '' where id = '".$par['id']."'");
    unlink($dirFileBa.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function simpan()
{
    global $inp, $par, $cID, $dirFileBa;

//    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
//    $fileIcon_name = $_FILES["fileUpload"]["name"];
//    if (($fileIcon != "") and ($fileIcon != "none"))
//    {
//        fileUpload($fileIcon, $fileIcon_name, $dirFileBa);
//        $fileDokumen = "ba_".time().".".getExtension($fileIcon_name);
//        fileRename($dirFileBa, $fileIcon_name, $fileDokumen);
//        $updateFIle .= "ba_file = '".$fileDokumen."',";
//    }
//
//    $sql = "UPDATE
//                  `tagihan_syarat`
//                SET
//                  $updateFIle
//                  `ba_tanggal` = '".setTanggal($inp["ba_tanggal"])."',
//                  `ba_catatan` = '".$inp["ba_catatan"]."',
//                  `ba_nomor` = '".$inp["ba_nomor"]."',
//                  `updated_at` = now(),
//                  `updated_by` = '".$cID."',
//                  `ba_verifikasi_status` = '".$inp["ba_verifikasi"]."',
//                  `ba_verifikasi_date` = '".date("Y-m-d")."',
//                  `ba_verifikasi_by` = '".$cID."'
//                WHERE `id` = '".$par["id"]."'
//                ";
//
//    db($sql);
//
//    update_serah_terima($par['id_termin']);

    $sql = "UPDATE
                  `tagihan_syarat`
                SET
                  `kontrol_verifikasi_catatan` = '".$inp["kontrol_verifikasi_catatan"]."',
                  `kontrol_verifikasi` = '".$inp["kontrol_verifikasi"]."',
                  `kontrol_verifikasi_by` = '".$cID."',
                  `kontrol_verifikasi_date` = '".date("Y-m-d H:i:s")."',
                  `kontrol_verifikasi_reapprove` = '".$inp["kontrol_verifikasi_reapprove"]."'
                WHERE `id` = '".$par["id"]."'
                ";
    db($sql);

    if ($inp["kontrol_verifikasi"] == '0' && $inp["kontrol_verifikasi_reapprove"] == '1')
    {
        db("delete from tagihan_approval where id_termin = $par[id_termin]");
        db("update tagihan_data set approval_status = 'p', approval_date = '(NULL)' where id_termin = '$par[id_termin]'");
        db("update tagihan_termin set verifikasi_dokumen = 'p' where id = '$par[id_termin]'");
        db("UPDATE `tagihan_syarat` SET `ba_verifikasi_status` = 'f' WHERE `id` = '".$par["id"]."'");
    }

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
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
		<h1 class=\"pagetitle\">VERIFIKASI DOKUMEN</h1>
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
			<legend>Dokumen</legend>
			    <p>
                    <label class=\"l-input-small\">Judul</label>
                    <span class=\"field\">
                        ".$r["judul"]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\" >Perbaikan</label>
                    <span class=\"field\">
                       ".$r["kontrol_verifikasi_komen"]." &nbsp; 
                    </span>
                </p>
			</fieldset>
			
			<br>
			
			<fieldset>
			    <legend>Verifikasi</legend>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[kontrol_verifikasi_catatan]\" name=\"inp[kontrol_verifikasi_catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["kontrol_verifikasi_catatan"]."</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Verifikasi</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"verif_diterima\" onclick='showAppr()' name=\"inp[kontrol_verifikasi]\" value=\"1\" ".( ($r["kontrol_verifikasi"] == "1" or empty($r["kontrol_verifikasi"])) ? "checked" : "")."/> <span class=\"sradio\">Diterima</span>
                        <input type=\"radio\" id=\"verif_ditolak\" onclick='showAppr()' name=\"inp[kontrol_verifikasi]\" value=\"0\" ".($r["kontrol_verifikasi"] == "0" ? "checked" : "")." /> <span class=\"sradio\">Ditolak</span>
                    </div>
                </p>
                ";
                $display = ($r["kontrol_verifikasi"] == '0') ? "block" : "none";
                $text.="
                <div id='appr_ulang' style='display: $display'>
                    <p>
                        <label class=\"l-input-small\">Approve Ulang</label>
                        <div class=\"fradio\">
                            <input type=\"radio\" id=\"inp[kontrol_verifikasi_reapprove]\" name=\"inp[kontrol_verifikasi_reapprove]\" value=\"1\" ".( ($r["kontrol_verifikasi_reapprove"] == "1" ) ? "checked" : "")."/> <span class=\"sradio\">Ya</span>
                            <input type=\"radio\" id=\"inp[kontrol_verifikasi_reapprove]\" name=\"inp[kontrol_verifikasi_reapprove]\" value=\"0\" ".(($r["kontrol_verifikasi_reapprove"] == "0" or empty($r["kontrol_verifikasi_reapprove"])) ? "checked" : "")." /> <span class=\"sradio\">Tidak</span>
                        </div>
                    </p>
                </div>
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

    $where = " WHERE a.kontrol_verifikasi = '0' and a.ba_verifikasi_status = 't' and d.approval_status = 't'";

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(a.judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(a.ba_tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.ba_tanggal) = '".$_GET['combo2']."'";


    $arrOrder = array("", "a.ba_tanggal");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "a.id DESC";

    $sql = "SELECT 
            a.id,
            a.id_spk,
            a.id_termin,
            a.ba_tanggal, 
            a.judul AS judulDokumen,
            a.kontrol_verifikasi,
            a.kontrol_verifikasi_catatan AS catatan, 
            a.kontrol_verifikasi_komen AS perbaikan,
            a.ba_file,
            b.judul,
            b.nomor, 
            c.namaData AS jenis
            FROM tagihan_syarat AS a 
            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
            JOIN mst_data AS c ON (c.kodeData = b.id_jenis)
            JOIN tagihan_data AS d ON (d.id_termin = a.id_termin) 
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) 
                                            FROM tagihan_syarat AS a 
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                            JOIN tagihan_data AS d ON (d.id_termin = a.id_termin) 
                                            JOIN mst_data AS c ON (c.kodeData = b.id_jenis) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        if ($r[kontrol_verifikasi] == '1'){
            $background = "class=\"labelStatusHijau\"";
            $status = "Diterima";
        }
        if ($r[kontrol_verifikasi] == '0') {
            $background = "class=\"labelStatusMerah\"";
            $status = "Ditolak";
        }
        if ($r[kontrol_verifikasi] == '') {
            $background = "class=\"labelStatusKuning\"";
            $status = "Belum";
        }

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r[ba_tanggal])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."&par[id_termin]=".$r["id_termin"]. getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$r["jenis"]."</div>",
            "<div align=\"left\">".$r["judulDokumen"]."</div>",
            "<div align=\"left\">".$r["catatan"]."</div>",
            "<div align=\"left\">".$r["perbaikan"]."</div>",
            "<div align=\"center\"><a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$r[id]".getPar($par, "mode, id")."',900,500);\"><img src=\"".getIcon($r['ba_file'])."\" height=\"20\"></div>",
            "<div align=\"center\" $background><strong><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id_termin]=$r[id_termin]&par[id]=".$r["id"]."" . getPar($par, "mode, id, id_termin") . "', 700, 580);\">$status</a></strong></div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $par, $arrTitle;

	$text = table(9, array(3, 4, 5, 6, 7, 8, 9));

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
                        <th style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Kategori</th>
                        <th style=\"vertical-align: middle; min-width: 200px;\">Dokumen</th>
                        <th style=\"vertical-align: middle; min-width: 200px;\">Catatan</th>
                        <th style=\"vertical-align: middle; min-width: 200px;\">Perbaikan</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Detil</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Verifikasi</th>
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

?>