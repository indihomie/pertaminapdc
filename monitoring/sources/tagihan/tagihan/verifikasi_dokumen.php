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

        case "delFile":
            $text=delFile();
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
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], true, $par['id_termin'], true);
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

function delFile()
{
    global $par, $dirFileBa;

    $file = getField("select ba_file from tagihan_syarat where id = '".$par['id']."'");
    db("update tagihan_syarat set ba_file = '' where id = '".$par['id']."'");
    unlink($dirFileBa.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
    $spk = getRow("select * from tagihan_spk where id = $termin[id_spk]");
    $text .= "
        
	    ".view_permohonan($arrTitle[$s], $termin['id_spk'], '', false, $par[id_termin], true)."

        <br>
        <br />
       <div id=\"contentwrapper\" class=\"contentwrapper\">    
            <div id=\"flagForm\"></div>
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>SYARAT & KETENTUAN</h3>
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                        <th width=\"300\" rowspan=\"2\" style=\"vertical-align: middle\">Dokumen</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Nomor</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Last Update</th>
                        <th width=\"*\" colspan=\"2\">File</th>
                        <th width=\"*\" colspan=\"3\" style=\"vertical-align: middle\">Verifikasi</th>
                        <th width=\"75\" rowspan=\"2\" style=\"vertical-align: middle\">Kontrol</th>
                    </tr>
                    <tr>
                        <th width=\"75\">View</th>
                        <th width=\"75\">D / L</th>
                        <th width=\"75\">Status</th>
                        <th width=\"75\">Tanggal</th>
                        <th width=\"150\">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select * from tagihan_syarat where id_termin = '".$par['id_termin']."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $view = $data['ba_file'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$data[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                            $download = $data['ba_file'] ? "<a href=\"download.php?d=fileTagihanBa&f=$data[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($data['ba_file'])."\" height=\"20\"></a>" : "";

                            $status = "";
                            $background = "";
                            if ($data['ba_verifikasi_status'] == 't'){
                                $status = "Diterima";
                                $background = "class=\"labelStatusHijau\"";
                            }
                            if ($data['ba_verifikasi_status'] == 'f'){
                                $status = "Ditolak";
                                $background = "class=\"labelStatusMerah\"";
                            }

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$data["id"]."" . getPar($par, "mode, id") . "', 650, 500);\" class=\"edit\"><span>Edit</span></a>";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data['judul']."</td>
                                <td align=\"center\">".$data['ba_nomor']."</td>
                                <td align=\"center\">".$data['updated_at']."</td>
                                <td align=\"center\">".$view."</td>
                                <td align=\"center\">".$download."</td>
                                <td align=\"center\" ".$background.">".$status."</td>
                                <td align=\"center\">".getTanggal($data['ba_verifikasi_date'])."</td>
                                <td align=\"left\">".getField("select namaUser from app_user where id = '".$data['ba_verifikasi_by']."'")."</td>
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
        $where = " WHERE a.nilai_biaya_total = a.nilai_plus_ppn and c.no_permohonan != ''";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(c.no_permohonan) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(d.namaSupplier) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(e.nama) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(a.target) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.target) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and b.id_jenis = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and b.id_supplier = '".$_GET['combo4']."'";
    if (!empty($_GET['combo5'])){
        $where .= " and b.id_sbu = '$_GET[combo5]'";

        if (!empty($_GET['combo6'])){
            $where .= " and b.id_proyek = '$_GET[combo6]'";
        }
    }

    $arrOrder = array("", "a.target");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "b.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.persen,
            a.id_spk,
            a.nilai_plus_ppn,
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
                                            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."'");
        $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and ba_verifikasi_status = 't'");

        if ($syarat == $ba){ #ini kalau full
            $background = "class=\"labelStatusHijau\"";
        } elseif ($ba > 0) {
            $background = "class=\"labelStatusBiru\""; #ini kalau kriterianya ada, tapi ga full
        } else { #kalo 0
            $background = "class=\"labelStatusKuning\"";
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_termin]=".$r["id"].getPar($par, "mode, id_termin")."\" class=\"edit\"><span>Detail</span></a>";

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."&par[id_termin]=".$r["id"]. getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
            "<div align=\"center\">".$r["no_permohonan"]."</div>",
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
                  `ba_nomor` = '".$inp["ba_nomor"]."',
                  `updated_at` = now(),
                  `updated_by` = '".$cID."',
                  `ba_verifikasi_status` = '".$inp["ba_verifikasi_status"]."',
                  `ba_verifikasi_date` = '".date("Y-m-d")."',
                  `ba_verifikasi_by` = '".$cID."'
                WHERE `id` = '".$par["id"]."'
                ";

    db($sql);

    update_serah_terima($par['id_termin']);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\");</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode") . "#flagForm';</script>";
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
                    <label class=\"l-input-small\" >Catatan</label>
                    <span class=\"field\">
                       ".$r["catatan"]." &nbsp; 
                    </span>
                </p>
			</fieldset>
			
			<br>
			
			<fieldset>
			    <legend>Verifikasi</legend>
			    <p>
                    <label class=\"l-input-small\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["ba_tanggal"] = (empty($r["ba_tanggal"]) or $r["ba_tanggal"] == "0000-00-00") ? date("Y-m-d") : $r["ba_tanggal"];
                        $text.="
                        <input type=\"text\" id=\"inp[ba_tanggal]\" name=\"inp[ba_tanggal]\"  value=\"" . getTanggal($r["ba_tanggal"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Nomor</label>
                    <div class=\"field\">
                        <input type=\"text\"  id=\"inp[ba_nomor]\" name=\"inp[ba_nomor]\"  value=\"" . $r["ba_nomor"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"100\">
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
                        <input type=\"radio\" id=\"inp[ba_verifikasi_status]\" name=\"inp[ba_verifikasi_status]\" value=\"t\" ".( ($r["ba_verifikasi_status"] == "t" or empty($r["ba_verifikasi_status"])) ? "checked" : "")."/> <span class=\"sradio\">Diterima</span>
                        <input type=\"radio\" id=\"inp[ba_verifikasi_status]\" name=\"inp[ba_verifikasi_status]\" value=\"f\" ".($r["ba_verifikasi_status"] == "f" ? "checked" : "")." /> <span class=\"sradio\">Ditolak</span>
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

	$text = table(8, array(3, 4, 5, 6, 7, 8));

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
                        <th style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">No SP3</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Dok</th>
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
    global $par, $arrTitle, $s, $arrParam;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal",
              "Judul - Nomor",
              "Pemohon",
              "Nilai",
              "No. Permohonan",
              "Dok"];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(c.no_permohonan) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(d.namaSupplier) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(e.nama) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(a.target) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(a.target) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and b.id_jenis = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and b.id_supplier = '".$par['combo4']."'";
    if (!empty($par['combo5'])){
        $where .= " and b.id_sbu = '$par[combo5]'";

        if (!empty($par['combo6'])){
            $where .= " and b.id_proyek = '$par[combo6]'";
        }
    }

    $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.persen,
            a.id_spk,
            a.nilai_plus_ppn,
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

        $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."'");
        $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and ba_verifikasi_status = 't'");

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			getAngka($r["nilai_plus_ppn"]) . "\t right",
			$r["no_permohonan"] . "\t center",
			getAngka($syarat)." / ".getAngka($ba). "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}