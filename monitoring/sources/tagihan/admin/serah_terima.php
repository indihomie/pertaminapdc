<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_ba/";

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

        case "detail":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false);
            break;

        case "detailForm":
            $text = detailForm();
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

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;


    $text .= "

            ".view_permohonan($arrTitle[$s], $par['id_spk'], '', false, $par['id_termin'])."
            
            <br />
            <br>

    <div id=\"contentwrapper\" class=\"contentwrapper\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>SYARAT & KETENTUAN</h3>
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                        <th width=\"300\" rowspan=\"2\" style=\"vertical-align: middle\">Syarat</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Tanggal</th>
                        <th width=\"*\" colspan=\"2\">File</th>
                    </tr>
                    <tr>
                        <th width=\"75\">View</th>
                        <th width=\"75\">D / L</th>
                    </tr>
                </thead>
                <tbody> -->
                    "  ;
                    $getData = getRows("select * from tagihan_syarat where id_termin = '".$par['id_termin']."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $view = $data['ba_file'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$data[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                            $download = $data['ba_file'] ? "<a href=\"download.php?d=fileTagihanBa&f=$data[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($data['ba_file'])."\" height=\"20\"></a>" : "";

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$data["id"]."" . getPar($par, "mode, id") . "', 650, 400);\" class=\"edit\"><span>Edit</span></a>";
                            //if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=".$data["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

//                            $cek = getRow("select * from tagihan_data where id_spk = '".$data['id_spk']."' limit 1");
//                            if (!empty($cek)) $kontrol = "-";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data['judul']."</td>
                                <td align=\"center\">".(!empty($data['ba_tanggal']) ? getTanggal($data['ba_tanggal']) : "")."</td>
                                <td align=\"center\">".$view."</td>
                                <td align=\"center\">".$download."</td>
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
		
		</div> 
    </div>";

    return $text;
}

function delFile()
{
    global $par, $dirFile;

    $file = getField("select ba_file from tagihan_syarat where id = '".$par['id']."'");
    db("update tagihan_syarat set ba_file = '' where id = '".$par['id']."'");
    unlink($dirFile.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function hapus()
{
    global $par, $dirFile;

    $file = getField("select ba_file from tagihan_syarat where id = '".$par['id']."'");

    unlink($dirFile.$file);

    $sql = "UPDATE
                    `tagihan_syarat`
                SET
                    `ba_file` = '',
                    `ba_tanggal` = '',
                    `ba_catatan` = ''
                WHERE `id` = '".$par["id"]."'
                ";
    db($sql);

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode, id") . "';</script>";
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

function simpan()
{
    global $inp, $par, $cID, $dirFile;

    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
    $fileIcon_name = $_FILES["fileUpload"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none"))
    {
        fileUpload($fileIcon, $fileIcon_name, $dirFile);
        $fileDokumen = "ba_".time().".".getExtension($fileIcon_name);
        fileRename($dirFile, $fileIcon_name, $fileDokumen);
        $updateFIle .= "ba_file = '".$fileDokumen."',";
    }

    $sql = "UPDATE
                `tagihan_syarat`
            SET
                $updateFIle
                `ba_tanggal` = '".setTanggal($inp["ba_tanggal"])."',
                `ba_catatan` = '".$inp["ba_catatan"]."',
                `updated_at` = now(),
                `updated_by` = '".$cID."'
            WHERE `id` = '".$par["id"]."'
            ";

    db($sql);

    update_serah_terima($par['id_termin']);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
{
    global $par, $dirFile;

    $r = getRow("SELECT * FROM tagihan_syarat WHERE id = '$par[id]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">SYARAT & KETENTUAN</h1>
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
                        "<a href=\"".$dirFile.$r['ba_file']."\" download><img src=\"".getIcon($r['ba_file'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        ".((empty($cek)) ? "<a href=\"?par[mode]=delFile&par[id_syarat]=".$r['id'].getPar($par,"mode, id_syarat")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                        <br clear=\"all\">";
                        $text.="
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

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE approve_status = 't' and persen_termin = '100' and status_syarat = 't'";
    }

    if (!empty($_GET['fSearch'])){
        $where .= " and (     
        lower(nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(namaSupplier) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(tanggal) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and kodeData = '$_GET[combo3]'";
    if (!empty($_GET['combo4'])) $where .= " and id_supplier = '$_GET[combo4]'";
    if (!empty($_GET['combo5'])) $where .= " and id_sbu = '$_GET[combo5]'";
    if (!empty($_GET['combo6'])) $where .= " and id_proyek = '$_GET[combo6]'";

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
                                            join mst_data as c ON (c.kodeData = a.id_jenis)  $where"),
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
            "<div align=\"right\">".getAngka($r["total"])."</div>",
            "<div align=\"left\">".$r["namaData"]."</div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
        );

        $getTermin = getRows("select * from tagihan_termin where id_spk = '".$r["id"]."'");
        foreach ($getTermin as $trm) {

            $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$trm['id']."'");
            $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$trm['id']."' and ba_file != ''");

            $background = (($syarat == $ba) and ($syarat != 0)) ? "style=\"background-color: #02e819\"" : "style=\"background-color: #E1A300\"";

            $data[] = array(
                "<div align=\"center\"></div>",
                "<div align=\"center\"></div>",
                "<div align=\"left\">".$trm["termin"]."</div>",
                "<div align=\"center\"></div>",
                "<div align=\"right\">".getAngka($trm["nilai"])."</div>",
                "<div align=\"left\">".$trm["persen"]."%</div>",
                "<div align=\"center\" ".$background.">".getAngka($syarat)." / ".getAngka($ba)."</div>",
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
                    <th width=\"*\">Judul - Nomor</th>
                    <th width=\"150\">Pemohon</th>
                    <th width=\"70\">Nilai</th>
                    <th width=\"100\">Jenis</th>
                    <th width=\"60\">Syarat</th>
                    <th width=\"50\">Detil</th>
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

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal",
              "Judul",
              "Nomor",
              "Pemohon",
              "Kategori",
              "Nilai",
              "APPROVAL"];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(a.nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(a.judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '".$par['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and kodeData = '$_GET[combo3]'";
    if (!empty($_GET['combo4'])) $where .= " and id_supplier = '$_GET[combo4]'";
    if (!empty($_GET['combo5'])) $where .= " and id_sbu = '$_GET[combo5]'";
    if (!empty($_GET['combo6'])) $where .= " and id_proyek = '$_GET[combo6]'";

    $order = "id DESC";

    $sql = "SELECT a.*, c.namaData
            from tagihan_spk as a
            join mst_data as c ON (c.kodeData = a.id_jenis)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $appr = "Menunggu Persetujuan";
        if ($r["approve_status"] == "t") $appr = "Setuju";
        if ($r["approve_status"] == "f") $appr = "Tolak";
        if ($r["approve_status"] == "p") $appr = "Pending";

        $data[]=[
			$no . "\t center",
			getTanggal($r["tanggal"]) . "\t center",
			$r["judul"]."\t left",
			$r["nomor"] . "\t center",
			$pemohon . "\t left",
            $r['namaData'] . "\t left",
			getAngka($r["total"]) . "\t right",
			$appr. "\t left"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}