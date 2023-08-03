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

        case "formTiket":
            $text = empty($_submit) ? formTiket() : simpanTiket();
            break;

        case "addGL":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formGL() : simpanGL(); else $text = lihat();
            break;

        case "editGL":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formGL() : simpanGL(); else $text = lihat();
            break;

        case "deleteGL":
            if (isset($menuAccess[$s]["delete"])) $text = hapusGL(); else $text = lihat();
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
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par["id_termin"], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "detailForm":
            $text = detailForm();
            break;


    }

    return $text;
}

function hapusGL()
{
    global $par;

    db("delete from tagihan_termin_biaya where id = '".$par["id"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode, id") . "';</script>";
}

function formGL()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_termin_biaya WHERE id = '$par[id]'");

    $trm = getrow("select * from tagihan_termin where id = '$par[id_termin]'");
    $biaya = getField("select sum(nilai_plus_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]'");
    $sisa = $trm['nilai_plus_ppn'] - $biaya;

    $defaultProyek = getField("select id_proyek from tagihan_spk where id = $trm[id_spk]");

    $r["proyek"] = empty($r["proyek"]) ? $defaultProyek : $r["proyek"];

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">DETIL BIAYA</h1>
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
			
			    <input type='hidden' name='ppnSPK' id='ppnSPK' value='".getField("select ppn from tagihan_spk where id = '$par[id_spk]'")."'>
			    <input type='hidden' name='sisaAwal' id='sisaAwal' value='".$sisa."'>
			
			    <p>
                    <label class=\"l-input-small\">Biaya</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[biaya]\" name=\"inp[biaya]\"  value=\"".$r["biaya"]."\" class=\"mediuminput\" style=\"width:495px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <table width='100%'>
                    <tr>
                        <td width='50%'>  
                            <p>
                                <label class=\"l-input-small2\">Nilai DPP</label>
                                <div class=\"fieldA\">  
                                     <input type=\"text\" onkeyup='cekAngka(this); getNilai();' id=\"inp[nilai]\" name=\"inp[nilai]\"  value=\"".getAngka($r["nilai"])."\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"50\"/>
                                </div>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">Nilai PPN</label>
                                <div class=\"fieldA\">  
                                     <input type=\"text\" id=\"inp[nilai_ppn]\" readonly name=\"inp[nilai_ppn]\" value=\"".getAngka($r[nilai_ppn])."\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"100\">
                                </div>
                            </p>
                        </td>
                        <td width='50%'>
                            <p>
                                <label class=\"l-input-small2\">Sisa</label>
                                <div class=\"fieldA\">
                                    <input type=\"text\" id=\"sisa\" readonly name=\"sisa\" value=\"".getAngka($sisa)."\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"100\">
                                </div>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">PPN</label>
                                <div class=\"fradio\">
                                    <input type=\"radio\" name=\"inp[ppn]\" id=\"ppn_yes\" value=\"t\" ".( ($r[ppn] == 't' or empty($r[ppn])) ? 'checked=""' : '')." onclick='getNilai()'/> <span class=\"sradio\">Ya</span>
                                    <input type=\"radio\" name=\"inp[ppn]\" id=\"ppn_no\" value=\"f\" ".($r[ppn] == 'f' ? 'checked=""' : '')." onclick='getNilai()'/> <span class=\"sradio\">Tidak</span>
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Total</label>
                    <div class=\"field\">  
                         <input type=\"text\" id=\"inp[nilai_plus_ppn]\" readonly name=\"inp[nilai_plus_ppn]\" value=\"".getAngka($r[nilai_plus_ppn])."\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"100\">
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Proyek</label>
                    <div class=\"field\">
                        " . comboData("select *, concat(nomor, ' - ', proyek) as namaProyek from proyek_data order by proyek asc", "id", "namaProyek", "inp[proyek]", "- Pilih Proyek -", $r["proyek"], "", "490px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_proyek__chosen{ min-width:490px; }
                    </style>
                </p>
                
			</fieldset>
		</form>
	</div>";

    return $text;
}



function formTiket()
{
    global $par, $dirFile;

    $r = getRow("SELECT a.*
                    FROM tagihan_data as a
                    JOIN tagihan_termin as b ON (b.id = a.id_termin)
                    WHERE a.id_spk = '".$par['id_spk']."' and a.id_termin = '".$par['id_termin']."'");
//    $idSPK = getField("select id_spk from tagihan_termin where id = $par[id_termin]");
//    $spk = getRow("select * from tagihan_spk where id = $idSPK");
//    $kodeOrganisisi = getField("select kode_organisasi from costcenter_data where id = '$spk[id_cc]'");
//    echo $spk[id_cc];die;

    //debugVar($r);die;

//    setValidation("is_null", "inp[id_termin]", "Termin Pembayaran tidak boleh kosong");
//    setValidation("is_null", "inp[no_invoice]", "No. Invoice tidak boleh kosong");
//    setValidation("is_null", "inp[pengirim]", "Pengirim tidak boleh kosong");
//    $text .= getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">PERMOHONAN</h1>
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
			    <legend>Dasar Permohonan</legend>
			    ";
			    $spk = getRow("select * from tagihan_spk where id = $par[id_spk]");
			    $text.="
                
                <p>
                    <label class=\"l-input-small\">Judul</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$spk["judul"]." &nbsp;
                    </span>
                </p>
                
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal Input</label>
                                <span class=\"field\">
                                    " . getTanggal($spk["tanggal"]) . " &nbsp;
                                </span>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">Cost Center</label>
                                <span class=\"field\">
                                    " . getField("select nama from costcenter_data where id = $spk[id_cc]") . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor</label>
                                <span class=\"field\">
                                    ".$spk["nomor"]." &nbsp;
                                </span>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">Nilai</label>
                                <span class=\"field\">
                                    Rp. " . getAngka($spk["total"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
            </fieldset>
			
			<br>
			
			<!-- <fieldset>
			    <legend>Termin</legend>
			    <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select termin from tagihan_termin where id = $par[id_termin]")." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Besaran</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select persen from tagihan_termin where id = $par[id_termin]")."%&nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        Rp. ".getAngka(getField("select nilai_total from tagihan_termin where id = $par[id_termin]"))." &nbsp;
                    </span>
                </p>
            </fieldset> 
			
			<br> -->
			
			<fieldset>
			    <legend>Tagihan</legend>
                <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select termin from tagihan_termin where id = $par[id_termin]")." &nbsp;
                    </span>
                </p>
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal</label>
                                <div class=\"fieldA\">  
                                    ";
                                    $r["tgl_terima"] = empty($r["tgl_terima"]) ? date("Y-m-d") : $r["tgl_terima"];
                                    $text.="
                                    <input type=\"text\" id=\"inp[tgl_terima]\" name=\"inp[tgl_terima]\"  value=\"" . getTanggal($r["tgl_terima"]) . "\" class=\"hasDatePicker\"/>
                                </div>
                            </p>
                           <p>
                                <label class=\"l-input-small2\">No. Invoice</label>
                                <div class=\"fieldA\">  
                                    <input type=\"text\" id=\"inp[no_invoice]\" name=\"inp[no_invoice]\"  value=\"".$r["no_invoice"]."\" class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor SP3</label>
                                <div class=\"field\">  
                                    <input type=\"text\" id=\"inp[no_permohonan]\" name=\"inp[no_permohonan]\"  value=\"".(empty($r["no_permohonan"]) ? generateNomorSP3($par[id_termin]) : $r["no_permohonan"])."\" readonly class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">Pemohon</label>
                                <div class=\"field\">  
                                    <input type=\"text\" id=\"inp[pengirim]\" name=\"inp[pengirim]\"  value=\"".$r["pengirim"]."\" class=\"mediuminput\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:660px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">No. Akrual</label>
                    <div class=\"field\">  
                        <input type=\"text\" id=\"inp[no_akrual]\" name=\"inp[no_akrual]\"  value=\"".$r["no_akrual"]."\" class=\"smallinput\" maxlength=\"200\"/>
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

function simpanGL()
{
    global $inp, $par, $cID, $dirFile;

    $nilai = setAngka($inp["nilai"]);
    $nilai_ppn = setAngka($inp["nilai_ppn"]);
    $nilai_plus_ppn = setAngka($inp["nilai_plus_ppn"]);

    $setData = "`biaya` = '".$inp["biaya"]."',
                `ppn` = '".$inp["ppn"]."',
                `nilai` = '$nilai',
                `nilai_ppn` = '$nilai_ppn',
                `nilai_plus_ppn` = '$nilai_plus_ppn',
                `proyek` = '".$inp["proyek"]."',
                ";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_termin_biaya`
                SET
                  `id_termin` = '".$par["id_termin"]."',
                   $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";
    } else {

        $sql = "UPDATE
                  `tagihan_termin_biaya`
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

function simpanTiket()
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

    $setData = "`id_spk` = '".$par["id_spk"]."',
                `id_termin` = '".$par["id_termin"]."',
                `id_supplier` = '".$par["id_supplier"]."',
                `tgl_terima` = '".setTanggal($inp["tgl_terima"])."',
                `no_invoice` = '".$inp["no_invoice"]."',
                `no_akrual` = '".$inp["no_akrual"]."',
                `pengirim` = '".$inp["pengirim"]."',
                `catatan` = '".$inp["catatan"]."',
                 $updateFIle
               ";

    $idTagihan = getField("select id from tagihan_data where id_spk = '$par[id_spk]' and id_termin = '$par[id_termin]'");

    if (empty($idTagihan)) {

        $sql = "INSERT
                  `tagihan_data`
                SET
                  $setData
                  `no_permohonan` = '$inp[no_permohonan]',
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
                WHERE `id` = '".$idTagihan."'
                ";

    }

    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $spk = getRow("select * from tagihan_spk where id = '$par[id_spk]'");

    $text .= "
	
    ".view_permohonan($arrTitle[$s], $par['id_spk'], false, false, $par['id_termin'], true)."
            
            
    <br />
    <br />

    <div id=\"contentwrapper\" class=\"contentwrapper\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>DETIL PERMOHONAN</h3>
                    ";
                    $totalNilai = getField("select sum(nilai) from tagihan_termin_biaya where id_termin = '$par[id_termin]'");
                    $nilaiPPN = getField("select sum(nilai_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]'");
                    $grandTotal = getField("select sum(nilai_plus_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]'");
                    $nilaiTermin = getField("SELECT nilai_plus_ppn from tagihan_termin where id = '".$par["id_termin"]."'");

                    db("update tagihan_termin set nilai_biaya_dpp = '$totalNilai', nilai_biaya_ppn = '$nilaiPPN', nilai_biaya_total = '$grandTotal' where id = '$par[id_termin]'");

                    if ($grandTotal != $nilaiTermin) {
                        $background = "style=\"background-color: #fdd400\"";
                    } else {
                        $background = "style=\"background-color: #02e819\"";
                    }

                    if (isset($menuAccess[$s]["add"]) and $grandTotal != $nilaiTermin) $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:-20px;\" onclick=\"openBox('popup.php?par[mode]=addGL" . getPar($par, "mode, id") . "', 750, 370); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
                    $text.="
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" style=\"vertical-align: middle\">No</th>
                        <th width=\"*\" style=\"vertical-align: middle\">Biaya</th>
                        <th width=\"150\" style=\"vertical-align: middle\">Nilai</th>
                        <th width=\"150\" style=\"vertical-align: middle\">PPN</th>
                        <th width=\"150\" style=\"vertical-align: middle\">TOTAL</th>
                        <th width=\"150\" style=\"vertical-align: middle\">Proyek</th>
                        <th width=\"75\" style=\"vertical-align: middle\">Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select * from tagihan_termin_biaya where id_termin = '".$par["id_termin"]."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editGL&par[id]=$data[id]" . getPar($par, "mode, id") . "', 750, 370);\" class=\"edit\"><span>Edit</span></a>";
                            if (isset($menuAccess[$s]["delete"])  and $r['status_syarat'] != 't') $kontrol .= "<a href=\"?par[mode]=deleteGL&par[id]=".$data["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data["biaya"]."</td>
                                <td align=\"right\">".getAngka($data["nilai"])."</td>
                                <td align=\"right\">".getAngka($data["nilai_ppn"])."</td>
                                <td align=\"right\">".getAngka($data["nilai_plus_ppn"])."</td>
                                <td align=\"left\">".getField("select nomor from proyek_data where id = $data[proyek]")."</td>
                                <td align=\"center\">".$kontrol."</td>
                            </tr>
                            ";
                        }

                        $text.="
                        
                        <tr>
                            <td colspan=\"4\" align=\"right\"><strong>Total</strong></td>
                            <td align=\"right\" ".$background."><strong>".getAngka($grandTotal)."</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        ";

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
        </div>
    </div>";

    return $text;
}

function generateNoTiket($str = "V")
{
    return date('m').$str.generateRandomString(4, 'num');
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

function lData()
{
    global $s, $par, $menuAccess, $arrParam;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    $where = " WHERE b.id_jenis = '".$arrParam[$s]."'";

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
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
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(a.target) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.target) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and b.id_supplier = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and c.status_pelunasan = '".$_GET['combo4']."'";
    if (!empty($_GET['combo5'])){
        $where .= " and b.id_sbu = '$_GET[combo5]'";

        if (!empty($_GET['combo6'])){
            $where .= " and b.id_proyek = '$_GET[combo6]'";
        }
    }

    $arrOrder = array("", "a.target");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "a.id DESC";

    $sql = "SELECT 
            a.*, 
            a.id as idTermin, 
            a.nilai_plus_ppn as nilaiPlusPPNTermin,
            b.*,
            d.namaSupplier, 
            b.id as idSpk,
            b.id_supplier as idSupplier,
            b.tanggal as tglSPK,
            c.tgl_terima,
            c.no_invoice, 
            c.no_permohonan 
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND b.approve_status = 't' AND b.persen_termin = '100')
            LEFT JOIN tagihan_data AS c ON (c.id_termin = a.id)
            JOIN dta_supplier as d ON (d.kodeSupplier = b.id_supplier)
            $where order by $order $limit
            ";
    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND b.approve_status = 't' AND b.persen_termin = '100')
                                            LEFT JOIN tagihan_data AS c ON (c.id_termin = a.id) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=formTiket&par[id_spk]=".$r["id_spk"]."&par[id_termin]=".$r["idTermin"]."&par[id_supplier]=".$r["idSupplier"] . getPar($par, "mode, id_spk, id_termin, id_supplier") . "',  980, 500);\" class=\"edit\"><span>Edit</span></a>";
        // if (isset($menuAccess[$s]["delete"]) and $r['tiket_status'] != 't') $kontrol .= "<a href=\"?par[mode]=delete&par[id]=".$r["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $nama = getField("select namasupplier from dta_supplier where kodeSupplier = '$r[id_supplier]'");

        if (empty($r[nilai_biaya_total]) or $r[nilai_biaya_total] == '0'){
            $background = "class=\"labelStatusKuning\"";
        }
        else
        {
            if ($r[nilai_biaya_total] != $r[nilaiPlusPPNTermin]) {
                $background = "class=\"labelStatusBiru\"";
            } else {
                $background = "class=\"labelStatusHijau\"";
            }
        }

        if ($r["no_permohonan"] != ""){ #ini ada isinya
            $backgroundsp3 = "class=\"labelStatusHijau\" style=\"font-weight: bold; color: black;\"";
            $sp3 = $r["no_permohonan"];
        }else { #kalo gaada
            $backgroundsp3 = "class=\"labelStatusKuning\"";

            $sp3 = "Belum";
        }

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."&par[id_termin]=".$r["idTermin"] . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">$nama</div>",
            "<div align=\"left\">".$r["termin"]."</div>",
            "<div align=\"right\">".getAngka($r["persen"], 2)."%</div>",
            "<div align=\"right\" ".$background."><a href=\"?par[mode]=detailForm&par[id_spk]=".$r["idSpk"]."&par[id_termin]=".$r["idTermin"]. getPar($par, "mode, id_spk, id_termin") . "\"><strong>".getAngka($r["nilaiPlusPPNTermin"])."</strong></a></div>",
            "<div align=\"center\" ".$backgroundsp3.">".$sp3."</div>",
            "<div align=\"center\">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFile;

    $setData = "`biaya` = '".$inp["biaya"]."',
                `nilai` = '".setAngka($inp["nilai"])."',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_termin_biaya`
                SET
                  `id_termin` = '".$par["id_termin"]."',
                   $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";
    } else {

        $sql = "UPDATE
                  `tagihan_termin_biaya`
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
                                        ".comboKey("combo4", $arrNilai, $combo4, "", "250px", "- Semua Status -", "chosen-select")."
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
                    <th style=\"vertical-align: middle; min-width: 60px;\">Tanggal</th>
                    <th style=\"vertical-align: middle; min-width: 240px;\">Judul - Nomor</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">Vendor</th>
                    <th style=\"vertical-align: middle; min-width: 100px;\">Termin</th>
                    <th style=\"vertical-align: middle; min-width: 20px;\">%</th>
                    <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                    <th style=\"vertical-align: middle; min-width: 170px;\">Nomor SP3</th>
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
              "Vendor",
              "Termin",
              "%",
              "Nilai",
              "Nomor SP3"];

    $where = " WHERE b.id_jenis = '".$arrParam[$s]."'";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(c.no_permohonan) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(d.namaSupplier) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(a.target) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(a.target) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and b.id_supplier = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and c.status_pelunasan = '".$par['combo4']."'";
    if (!empty($par['combo5'])){
        $where .= " and b.id_sbu = '$par[combo5]'";

        if (!empty($par['combo6'])){
            $where .= " and b.id_proyek = '$par[combo6]'";
        }
    }

    $order = "a.id DESC";

    $sql = "SELECT 
            a.*, 
            a.id as idTermin, 
            a.nilai_plus_ppn as nilaiPlusPPNTermin,
            b.*,
            d.namaSupplier, 
            b.id as idSpk,
            b.id_supplier as idSupplier,
            b.tanggal as tglSPK,
            c.tgl_terima,
            c.no_invoice, 
            c.no_permohonan 
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND b.approve_status = 't' AND b.persen_termin = '100')
            LEFT JOIN tagihan_data AS c ON (c.id_termin = a.id)
            JOIN dta_supplier as d ON (d.kodeSupplier = b.id_supplier)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $data[]=[
			$no . "\t center",
			getTanggal($r["tglSPK"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["namaSupplier"] . "\t center",
			$r["termin"] . "\t center",
			$r['persen'] . "\t left",
			getAngka($r["nilaiPlusPPNTermin"]) . "\t right",
			$r["no_permohonan"]. "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}