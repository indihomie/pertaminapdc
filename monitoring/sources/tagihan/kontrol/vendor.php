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
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "detailForm":
            $text = detailForm();
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

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread(ucwords(str_replace("Detail", "", $par["mode"]))) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		    <p style=\"position: absolute; right: 20px; top: 10px;\">
		        <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk, id_termin") . "';\"/>
			</p>
			
			<br>
			
			<fieldset>
			    <legend>Dasar Permohonan</legend>
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal</label>
                                <span class=\"field\">  
                                    " . getTanggal($r["tanggal"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor</label>
                                <span class=\"field\">
                                    ".$r["nomor"]." &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Vendor</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select namaSupplier from dta_supplier where kodeSupplier = '".$r["id_supplier"]."'")." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Judul</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["judul"]." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["catatan"]." &nbsp;
                    </span>
                </p>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal Realisasi</label>
                                <span class=\"field\">  
                                    " . getTanggal($r["target_realisasi"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                             <p>
                                <label class=\"l-input-small2\">Total Nilai</label>
                                <span class=\"field\">  
                                    Rp. " . getAngka($r["nilai"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Alamat Tujuan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["alamat"]." &nbsp;
                    </span>
                </p>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">PPN</label>
                                <span class=\"field\">  
                                    " . getAngka($r["ppn"]) . "% &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:25%\">
                            <p>
                                <label class=\"l-input-small2\">PPH</label>
                                <span class=\"field\">  
                                    " . getAngka($r["pph"]) . "% &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:25%\">
                            <p>
                                <label class=\"l-input-small2\">Diskon</label>
                                <span class=\"field\">  
                                    " . getAngka($r["diskon"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Grand Total</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        Rp. ".getAngka($r["total"])." &nbsp;
                    </span>
                </p>
                
            </fieldset>
            
            <br />
            ";
            $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
            $text .= "
            
            <fieldset>
                <legend>Termin Pembayaran</legend>
                <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$termin['termin']." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Besaran</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getAngka($termin['persen'])."% &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Senilai</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        Rp. ".getAngka($termin['nilai'])." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Target</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getTanggal($termin['target'])." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$termin['catatan']." &nbsp;
                    </span>
                </p>
            </fieldset>
            
            <br />
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>SYARAT & KETENTUAN</h3>
                </div>
            </div>
            <div id=\"pos_l\" style=\"float:right;\">
                    <div style=\"float:right; top:20px; right:20px;\">
                        <input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
                    </div>
                </div>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" style=\"vertical-align: middle\">No</th>
                        <th width=\"300\" style=\"vertical-align: middle\">Dokumen</th>
                        <th width=\"100\" style=\"vertical-align: middle\">Update</th>
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

                            $checked = $arrValue[$data['id']] ? "checked=\"checked\"" : "";

                            $checklist = "
                                            <input type=\"checkbox\" id=\"det_[" . $data['id'] . "]\" name=\"det_[" . $data['id'] . "]\" value=\"" . $data['id'] . "\" $checked />
                                            ";

                            $view = $data['ba_file'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$data[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                            $download = $data['ba_file'] ? "<a href=\"download.php?d=fileTagihanBa&f=$data[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($data['ba_file'])."\" height=\"20\"></a>" : "";

                            if ($data['ba_verifikasi_status'] == 't') $data['ba_verifikasi_status'] = "Diterima";
                            if ($data['ba_verifikasi_status'] == 'f') $data['ba_verifikasi_status'] = "Ditolak";

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$data["id"]."" . getPar($par, "mode, id") . "', 650, 430);\" class=\"detail\"><span>Edit</span></a>";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data['judul']."</td>
                                <td align=\"center\">".$data['updated_at']."</td>
                                <td align=\"center\">".$checklist."</td>
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
    global $s, $par, $menuAccess;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE 1 = 1";
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
    if (!empty($_GET['combo3'])) $where .= " and d.kodeSupplier = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and c.status_pelunasan = '".$_GET['combo4']."'";

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
        $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and ba_verifikasi_status = 't'");

        if ($syarat == $ba){ #ini kalau full
            $background = "style=\"background-color: #02e819\"";
        } elseif ($ba > 0) {
            $background = "style=\"background-color: #67b7dc\""; #ini kalau kriterianya ada, tapi ga full
        } else { #kalo 0
            $background = "style=\"background-color: #fdd400\"";
        }



        $view = $r['file_tagihan'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanData&par[id]=$r[id_tagihan]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
        $download = $r['file_tagihan'] ? "<a href=\"download.php?d=fileTagihanData&f=$r[id_tagihan]".getPar($par, "mode, id")."\"><img src=\"".getIcon($r['file_tagihan'])."\" height=\"20\"></a>" : "";

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_termin]=".$r["id"].getPar($par, "mode, id_termin")."\" class=\"edit\"><span>Detail</span></a>";

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["tgl_terima"])."</div>",
            "<div align=\"left\">".$r["namaSupplier"]."</div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailTagihan&par[pop_up]=1&par[id_tagihan]=".$r["id_tagihan"]."" . getPar($par, "mode, id_tagihan") . "',  980, 400);\">".$r["no_invoice"]."</a></div>",
            "<div align=\"center\"><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a></div>",
            "<div align=\"left\">".$r["termin"]."</div>",
            "<div align=\"right\">".getAngka($r["nilai"])."</div>",
            "<div align=\"center\" ".$background.">".getAngka($syarat)." / ".getAngka($ba)." </div>",
            "<div align=\"center\">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFileBa;

    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
    $fileIcon_name = $_FILES["fileUpload"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none"))
    {
        fileUpload($fileIcon, $fileIcon_name, $dirFileBa);
        $fileDokumen = "ba_".time().".".getExtension($fileIcon_name);
        fileRename($dirFileBa, $fileIcon_name, $fileDokumen);
        $updateFIle .= "ba_file = '".$fileDokumen."',";
    }

    $sql = "UPDATE
                  `tagihan_syarat`
                SET
                  $updateFIle
                  `ba_tanggal` = '".setTanggal($inp["ba_tanggal"])."',
                  `ba_catatan` = '".$inp["ba_catatan"]."',
                  `updated_at` = now(),
                  `updated_by` = '".$cID."',
                  `ba_verifikasi_status` = '".$inp["ba_verifikasi"]."',
                  `ba_verifikasi_date` = '".date("Y-m-d")."',
                  `ba_verifikasi_by` = '".$cID."'
                WHERE `id` = '".$par["id"]."'
                ";

    db($sql);

    update_serah_terima($par['id_termin']);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
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
                    <div class=\"field\">
                        ";
                        $r["ba_tanggal"] = (empty($r["ba_tanggal"]) or $r["ba_tanggal"] == "0000-00-00") ? date("Y-m-d") : $r["ba_tanggal"];
                        $text.="
                        <input type=\"text\" id=\"inp[ba_tanggal]\" name=\"inp[ba_tanggal]\"  value=\"" . getTanggal($r["ba_tanggal"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[ba_catatan]\" name=\"inp[ba_catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["ba_catatan"]."</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">File</label>
                    <div class=\"field\">";
                        $text .= empty($r['ba_file'])
                        ?
                        "<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
                        <div class=\"fakeupload\">
                            <input type=\"file\" id=\"fileUpload\" name=\"fileUpload\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
                        </div>"
                        :
                        "<a href=\"".$dirFileBa.$r['ba_file']."\" download><img src=\"".getIcon($r['ba_file'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        ".((empty($cek)) ? "<a href=\"?par[mode]=delFile&par[id_syarat]=".$r['id'].getPar($par,"mode, id_syarat")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                        <br clear=\"all\">";
                        $text.="
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Verifikasi</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[ba_verifikasi]\" name=\"inp[ba_verifikasi]\" value=\"t\" ".( ($r["ba_verifikasi"] == "t" or empty($r["ba_verifikasi"])) ? "checked" : "")."/> <span class=\"sradio\">Diterima</span>
                        <input type=\"radio\" id=\"inp[ba_verifikasi]\" name=\"inp[ba_verifikasi]\" value=\"f\" ".($r["ba_verifikasi"] == "f" ? "checked" : "")." /> <span class=\"sradio\">Ditolak</span>
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

	$text = table(9, array(4, 5, 6, 7, 8, 9));

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
            </div>
            
            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
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
                            <td style=\"width:50%\"></td>
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
                        <th style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th style=\"vertical-align: middle; min-width: 250px;\">Vendor</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">No. Invoice</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">No. SPK</th>
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