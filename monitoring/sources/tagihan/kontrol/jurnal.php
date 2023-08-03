<?php

use mikehaertl\wkhtmlto\Pdf;

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

        case "detailForm":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detailForm() : simpan();
            else $text = lihat();
            break;

        case "detailFormApproval":
            $text = detailFormApproval();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], 'popup', false, $par['id_termin'], true);
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "getKodeCompany":
            $text = getKodeCompany();
            break;

        case "detailGL":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detailGL() : simpanGL();
            else $text = lihat();
            break;

        case "addGL":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formGL() : simpanGLDetail(); else $text = lihat();
            break;

        case "editGL":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formGL() : simpanGLDetail(); else $text = lihat();
            break;

        case "deleteGL":
            if (isset($menuAccess[$s]["delete"])) $text = hapusGLDetail(); else $text = lihat();
            break;

        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpanGL(); else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpanGL(); else $text = lihat();
            break;

        case "delete":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;

        case "approval":
            if (isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form_approval() : simpan_approval();
            else $text = lihat();
            break;

        case "print_dokumen":
            $text = print_dokumen();
            break;
    }

    return $text;

}

function formGL()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_termin_biaya WHERE id = '$par[id]'");

    $nilaiTermin = getField("select nilai_plus_ppn from tagihan_termin where id = '$par[id_termin]'");
    $biaya = getField("select sum(nilai_plus_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]'");
    $sisa = $nilaiTermin - $biaya;

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
			    <legend>Nilai DPP</legend>
			
			    <input type='hidden' name='ppnSPK' id='ppnSPK' value='".getField("select ppn from tagihan_spk where id = '$par[id_spk]'")."'>
			    <input type='hidden' name='sisaAwal' id='sisaAwal' value='".$sisa."'>
			
			    <p>
                    <label class=\"l-input-small\">Uraian</label>
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
			
			<br>
			
			<fieldset>
			    <legend>GL</legend>
			    
			    <p>
                    <label class=\"l-input-small\">Kode</label>
                    <div class=\"field\">
                        " . comboData("select id, kode from account_gl", "id", "kode", "inp[gl_account]", "- Pilih Kode -", $r["gl_account"], "", "490px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_gl_account__chosen{ min-width:300px; }
                    </style>
                </p>
                
                <p>
                    <label class=\"l-input-small\">PK</label>
                    <div class=\"field\">
                        " . comboData("select kodeData, namaData from mst_data where kodeCategory = 'MDTG'", "kodeData", "namaData", "inp[tipe]", "- Pilih PK -", $r["tipe"], "", "490px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_tipe__chosen{ min-width:300px; }
                    </style>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <div class=\"field\">
                        <textarea name=\"inp[catatan]\" style=\"width:300px;\" id=\"inp[catatan]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >" . nl2br($r["catatan"]) . "</textarea>
                    </div>
                </p>
			    
			</fieldset>
			
		</form>
	</div>";

    return $text;
}

function hapusGLDetail()
{
    global $par;

    db("delete from tagihan_termin_biaya where id = '".$par["id"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailGL" . getPar($par, "mode, id") . "';</script>";
}

function simpanGLDetail()
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
                `gl_account` = '".$inp["gl_account"]."',
                `tipe` = '".$inp["tipe"]."',
                `catatan` = '".$inp["catatan"]."',
                ";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_termin_biaya`
                SET
                  `id_termin` = '".$par["id_termin"]."',
                  `jenis` = 'k',
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

function print_dokumen()
{
	global $par;

	$pc1 = getRow("SELECT

                    a.id,
                    a.target,
                    a.termin, 
                    a.nilai, 
                    a.persen,
                    a.id_spk,
                    a.pengajuan_approve_status,
                    a.pengajuan_no_tiket,
                    a.nilai_plus_ppn,
                    a.pengajuan_prep_by,
                    a.document_type,
                    a.document_kr_no,
                    a.document_kz_no,
                    a.pengajuan_post_date,
                    
                    b.id as idSpk,
                    b.id_jenis,
                    b.judul,
                    b.nomor,
                    b.tanggal, 
                    b.id_supplier,
                    
                    c.tgl_terima, 
                    c.file_tagihan,
                    c.no_invoice,
                    c.no_permohonan,
                    c.id as id_tagihan,
                    
                    d.namaSupplier,
                    d.nomorSupplier,
                    e.nama
                    
                    FROM tagihan_termin AS a
                    JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                    JOIN tagihan_data AS c ON (c.id_termin = a.id)
                    LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
                    LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
                    where a.id = '".$par['id_termin']."'");

	$pdf = new Pdf([
		"margin-top" => 10,
		"margin-right" => 10,
		"margin-bottom" => 10,
		"margin-left" => 10,
		"title" => "PRINT SP3",
		"disable-smart-shrinking",
		"page-size" => 'A4'
	]);

	$html = "
	
	    <!DOCTYPE html>
        <html>
        <head>
        </head>
        <body>
            
            
            <div style='margin: auto; width: auto; height: 55px; padding: 10px; margin-top: 50px;'>		
            	<div style='float: left; width: 500px; font-size: 10px'>
            		<h1>FORM JURNAL</h1>
				</div>
            	<div style='float: right; margin-top: -55px'>
            		<img src='http://pertaminapdc.com/monitoring/images/logopertaminapdc.png' style='width: 200px;'>
				</div>
			</div>
			
            
            <div style='font-size: 12px; padding: 20px; margin-top: -20px;'>
				
				".headerPrint($par[id_termin], $pc1, true)."
                
                <br>
                
                <strong>Total : IDR ". getAngka($pc1['nilai_plus_ppn'])." </strong>
                    
				
				<br>
                <br>
				
				<Strong>
				<table width='100%' border='0'>
				    <tr>
				        <td width='50%'>Prepared By,</td>
				        <td>Reviewed by,</td>
				        <td>Approved By,</td>
                    </tr>
                    <tr>
				        <td width='50%'>
				            ......................................
				            <br>
                            (Setara Manager)
                        </td>
				        <td>
				            ......................................
				            <br>
                            (Sesuai SK Otorisasi)
                        </td>
                        <td>
				            ......................................
				            <br>
                            (Sesuai SK Otorisasi)
                        </td>
                    </tr>
                    <tr>
				        <td width='50%'>
				            <br><br><br>
				            <br>
                            
                            <br>
                            (Account Payable Ast.)
                        </td>
				        <td>
				            <br><br><br>
				            <br>
                            
                            <br>
                            (SuperVisor)
                        </td>
                        <td>
                            <br><br><br>
				            <br>
                            
                            <br>
                            (Controller Manager)
                        </td>
                    </tr>
                </table>
                </Strong>
                
                <br>
                
                <table width='100%'>
				    <tr>
				        <th width='30%'>Nomor Dokumen KR:</th>
				        <td>". $pc1['document_kr_no'] ."</td>
                    </tr>
                    <tr>
				        <th width='30%'>Nomor Dokumen KZ:</th>
				        <td>". $pc1['document_kz_no'] ."</td>
                    </tr>
                </table>
                
                <br>
                
                <table width='100%' style='border: 1px black solid; border-collapse: collapse;' border='1'>
				    <tr>
				       <strong><td width='10%'>Notes : </td></strong>
                    </tr>
                    <tr>
				        <td width='50%'>
				        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                        </td>
				        
                    </tr>
                </table>
            </div>
        </body>
        </html>
	
	";

	$pdf->addPage($html);

    if (!$pdf->send()) {
        $error = $pdf->getError();
    }
}


function simpan_approval()
{
    global $s, $inp, $par, $cID, $arrParam;

    $setData = "
                `id_termin` = '".$par['id_termin']."',
                `approve_lvl` = '".$par['lvl']."',
                `approve_date` = '".setTanggal($inp["approve_date"])."',
                `approve_by` = '".$cID."',
                `approve_desc` = '".$inp["approve_desc"]."',
                `approve_status` = '".$inp["approve_status"]."',";

    $id = getField("select id from tagihan_approval_jurnal where id_termin = '".$par['id_termin']."' and approve_lvl = '".$par['lvl']."' ");

    if (empty($id)) {

        $sql = "INSERT INTO
                  `tagihan_approval_jurnal`
                SET
                  $setData
                  `created_at` = now(),
                  `updated_at` = now(),
                  `created_by` = '".$cID."'
                ";

    } else {

        $sql = "UPDATE
                  `tagihan_approval_jurnal`
                SET
                  $setData
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$id."'
                ";
    }

    db($sql);

    $statusApproval = 't';

    for ($i = 1; $i <= 2; $i++) {

        $cek = getField("select id from tagihan_approval_jurnal where id_termin = '".$par['id_termin']."' and approve_lvl = '".$i."' and approve_status = 't'");
        if (empty($cek)) $statusApproval = 'p';

    }

    $sql = "UPDATE
              `tagihan_termin`
            SET
              `pengajuan_approve_status` = '$statusApproval',
              `pengajuan_approve_date` = now()
            WHERE `id` = '" . $par["id_termin"] . "'
            ";
    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form_approval()
{
    global $par, $cID;

    $r = getRow("select * from tagihan_approval_jurnal where id_termin = '".$par['id_termin']."' and approve_lvl = '".$par['lvl']."'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">APPROVAL</h1>
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
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["approve_date"] = $r["approve_date"] ?: date('Y-m-d');
                        $text.="
                        <input type=\"text\" id=\"inp[approve_date]\" name=\"inp[approve_date]\"  value=\"" . getTanggal($r["approve_date"]) . "\" class=\"hasDatePicker\" maxlength=\"150\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Nama</label>
                    <div class=\"field\">
                        ";
                        $r["approve_by"] = (empty($r["approve_by"])) ? getField("select namaUser from app_user where id = '$cID'") : getField("select namaUser from  app_user where id = '".$r["approve_by"]."'") ;
                        $text.="
                        <input type=\"text\" readonly id=\"inp[approve_by]\" style=\"width:300px;\" name=\"inp[approve_by]\" size=\"10\" maxlength=\"150\" value=\"".$r["approve_by"]."\" class=\"vsmallinput\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Keterangan</label>
                    <div class=\"field\">
                        <textarea name=\"inp[approve_desc]\" style=\"width:300px;\" id=\"inp[approve_desc]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >" . nl2br($r["approve_desc"]) . "</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"t\" ".( ($r["approve_status"] == "t" or empty($r["approve_status"])) ? "checked" : "")."/> <span class=\"sradio\">Setuju</span>
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"p\" ".($r["approve_status"] == "p" ? "checked" : "")." /> <span class=\"sradio\">Pending</span>
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"f\" ".($r["approve_status"] == "f" ? "checked" : "")." /> <span class=\"sradio\">Tolak</span>
                    </div>
                </p>
            </fieldset>
		</form>
	</div>";

    return $text;
}

function simpanGL()
{
    global $inp, $par, $cID;


    $getData = getRows("select * from tagihan_termin_biaya 
                        where id_termin = '".$par["id_termin"]."' 
                        order by id asc");

    foreach ($getData as $data) {
        db("update tagihan_termin_biaya set gl_account = '".$inp[gl_account][$data[id]]."', tipe = '".$inp[tipe][$data[id]]."' where id = '$data[id]'");
    }

    echo "<script>alert('Data berhasil disimpan')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailGL" . getPar($par, "mode") . "#flagForm';</script>";
}

function form()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_gl WHERE id = '$par[id_gl]'");
    $termin = getRow("select * from tagihan_termin where id = $par[id_termin]");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">GL ACCOUNT</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
				    if ($termin[pengajuan_approve_status] != 't') {
				        $text.="<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
				    }
				    $text.="
				</p>
			</div>
			<fieldset>
			    <legend>Permohonan</legend>
			    ";

			    $text.="
			    <p>
                    <label class=\"l-input-small\">Permohonan</label>
                    <span class=\"field\">
                        ".$termin[termin]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Total Nilai</label>
                    <span class=\"field\">
                        Rp. ".getAngka($termin[nilai_total])."
                    </span>
                </p>
            </fieldset>
			<br>
			<fieldset>
			    <legend>DETIL BIAYA</legend>
			    <p>
                    <label class=\"l-input-small\">Judul</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"".$r["judul"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
			
			    <table width=\"100%\">
			        <tr>
			            <td width=\"50%\">
			                <p>
                                <label class=\"l-input-small2\">Ammount</label>
                                <div class=\"field\">
                                    ".comboData("select * from mst_data where kodeCategory = 'CUR' order by urutanData asc", "kodeData", "namaData", "inp[curr]", "", $r[curr], "", "80px", "chosen-select")." 
                                    <style>#inp_curr__chosen{min-width:80px;}</style>
                                    &nbsp
                                    <input type=\"text\" onkeyup='cekAngka(this)' id=\"inp[ammount]\" name=\"inp[ammount]\"  value=\"".getAngka($r["ammount"])."\" class=\"mediuminput\" style=\"width:80px;\" maxlength=\"50\"/>
                                </div>
                            </p>
                        </td>
			            <td width=\"50%\">
			                <p>
                                <label class=\"l-input-small2\">Jumlah</label>
                                <div class=\"field\">
                                    <input type=\"text\" onkeyup='cekAngka(this)' id=\"inp[jumlah]\" name=\"inp[jumlah]\"  value=\"".getAngka($r["jumlah"])."\" class=\"mediuminput\" style=\"width:80px;\" maxlength=\"50\"/>
                                    &nbsp
                                    ".comboData("select * from mst_data where kodeCategory = 'STR' order by urutanData asc", "kodeData", "namaData", "inp[satuan]", "", $r[satuan], "", "80px", "chosen-select")." 
                                    <style>#inp_satuan__chosen{min-width:80px;}</style>
                                </div>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Keterangan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[keterangan]\" name=\"inp[keterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["keterangan"]."</textarea>
                    </div>
                </p>
			</fieldset>
			
			<br>
			
			<fieldset>
			    <legend>JURNAL</legend>
			    <p>
                    <label class=\"l-input-small\">GL Account</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[gl_account]\" name=\"inp[gl_account]\"  value=\"".$r["gl_account"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">GL Name</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[gl_name]\" name=\"inp[gl_name]\"  value=\"".$r["gl_name"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">PK</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[pk]\" name=\"inp[pk]\"  value=\"".$r["pk"]."\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Ref Key 1</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[ref_key_1]\" name=\"inp[ref_key_1]\"  value=\"".$r["ref_key_1"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
			
			    <p>
                    <label class=\"l-input-small\">Cost Center</label>
                    <div class=\"field\">
                        ".comboData("SELECT DISTINCT(cost) as cost FROM costcenter_data WHERE cost != ''", "cost", "cost", "inp[cost_center]", "", $r[cost_center], "", "250px", "chosen-select")." 
                        <style>#inp_cost_center__chosen{min-width:250px;}</style>
                    </div>
                </p>
			    
			</fieldset>
		</form>
	</div>";

    return $text;
}

function detailGL()
{
    global $s, $par, $arrTitle, $menuAccess;

    $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
    $spk = getRow("select * from tagihan_spk where id = $termin[id_spk]");

    $text .= "
    ".view_permohonan($arrTitle[$s], $termin['id_spk'], '', false, $par[id_termin], true)."
    
    
    <br>
    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
	
	    <!--
	
        <form id=\"form\" class=\"stdform\">
            <fieldset>
                <legend>Pengajuan</legend>
            
                <p>
                    <label class=\"l-input-small\">Prepared By</label>
                    <span class=\"field\">
                        " . getField("select nama from pegawai_data where id = '$termin[pengajuan_prep_by]'") . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Posting Date</label>
                    <span class=\"field\">  
                        " . getTanggal($termin["pengajuan_post_date"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Document Date</label>
                    <span class=\"field\">  
                        " .  getTanggal($termin["pengajuan_doc_date"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Month</label>
                    <span class=\"field\">   
                         " . getBulan($termin['pengajuan_month'], 'string') . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Company Name</label>
                    <span class=\"field\">
                        " . getField("select namaSupplier from dta_supplier where kodeSupplier = '$termin[pengajuan_company]'"). " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Customer Code</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_customer_code"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Assignment</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_assignment"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Reference</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_refrence"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Ref Key 2</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_ref_key_2"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Header Text</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_header_text"] . " &nbsp;
                    </span>
                </p>
            </fieldset>
        </form>
        
        <br>
        --> 
        <br>
        <div id=\"flagForm\"></div>
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform formGL\" action=\"?_submit=1" . getPar($par) . "\" enctype=\"multipart/form-data\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>GL ACCOUNT</h3>
                    ";
                    if (isset($menuAccess[$s]["add"])) $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:50px;\" onclick=\"openBox('popup.php?par[mode]=addGL" . getPar($par, "mode, id") . "', 750, 550); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
                    $text.="
                </div>
                <input style=\"position: relative; float: right; margin-top: -57px; margin-right: 5px;\" type=\"button\" class=\"cancel radius2\" onclick=\"jQuery('.formGL').submit()\" value=\"Simpan\"/>
                <!--
                <input style=\"position: relative; float: right; margin-top: -55px; margin-right: 5px;\" type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
                -->
            </div>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" style=\"vertical-align: middle\">No</th>
                        <th width=\"150\" style=\"vertical-align: middle\">kode</th>
                        <th width=\"250\" style=\"vertical-align: middle\">account</th>
                        <th width=\"100\" style=\"vertical-align: middle\">pk</th>
                        <th width=\"50\" style=\"vertical-align: middle\">curr</th>
                        <th width=\"100\" style=\"vertical-align: middle\">ammount</th>
                        <th width=\"100\" style=\"vertical-align: middle\">REF  KEY</th>
                        <th width=\"100\" style=\"vertical-align: middle\">cost center</th>
                        <th width=\"*\" style=\"vertical-align: middle\">Uraian</th>
                        <th width=\"75\" style=\"vertical-align: middle\">Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select *, a.id as idBiaya from tagihan_termin_biaya as a
                                        left join account_gl as b on (b.id = a.gl_account)
                                        where a.id_termin = '".$par["id_termin"]."' 
                                        order by a.id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $kontrol = "";
                            if ($data[jenis] == 'k') {
                                if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editGL&par[id]=$data[idBiaya]" . getPar($par, "mode, id") . "', 750, 550);\" class=\"edit\"><span>Edit</span></a>";
                                if (isset($menuAccess[$s]["delete"])  and $r['status_syarat'] != 't') $kontrol .= "<a href=\"?par[mode]=deleteGL&par[id]=".$data["idBiaya"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";
                            }

                            $idcc = getField("select costcenter from proyek_data where id = '$data[proyek]'");
                            $costCenter = getField("select cost from costcenter_data where id = '$idcc'");

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"center\">".comboData("select id, kode from account_gl", "id", "kode", "inp[gl_account][$data[idBiaya]]", "- Pilih Kode -", $data[gl_account], "", "150px", "chosen-select")."</td>
                                <td align=\"left\">".$data[judul]."</td>
                                <td align=\"center\">".comboData("select kodeData, namaData from mst_data where kodeCategory = 'MDTG'", "kodeData", "namaData", "inp[tipe][$data[idBiaya]]", "- Pilih PK -", $data[tipe], "", "70px", "chosen-select")."</td>
                                <td align=\"center\">".$data[currency]."</td>
                                <td align=\"right\">".getAngka($data["nilai_plus_ppn"])."</td>
                                <td align=\"center\">".getField("select nomor from proyek_data where id = $data[proyek]")."</td>
                                <td align=\"center\">$costCenter</td>
                                <td align=\"left\">".$data["biaya"]."</td>
                                <td align=\"center\">".$kontrol."</td>
                            </tr>
                            ";

                            $totalNilai += $data["nilai"];
                            $nilaiPPN += $data["nilai_ppn"];
                            $grandTotal += $data["nilai_plus_ppn"];
                        }

                         //db("update tagihan_termin set nilai_biaya_dpp = '$totalNilai', nilai_biaya_ppn = '$nilaiPPN', nilai_biaya_total = '$grandTotal' where id = '$par[id_termin]'");


                        $idDebit = getField("select nilaiParameter from app_parameter where namaParameter = 'idJurnalDebit'");
                        $idKredit = getField("select nilaiParameter from app_parameter where namaParameter = 'idJurnalKredit'");

                        $totalDebit = getField("select sum(nilai_plus_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]' and tipe in ($idDebit)");
                        $totalKredit = getField("select sum(nilai_plus_ppn) from tagihan_termin_biaya where id_termin = '$par[id_termin]' and tipe in ($idKredit)");

                        $balance = $totalDebit - $totalKredit;

                        if ($balance == 0) $statusBalance = 1;
                        else $statusBalance = 0;

                        db("update tagihan_termin set balance_status = '$statusBalance' where id = '$par[id_termin]'");

                        $text.="
                        
                        <tr>
                            <td colspan=\"5\" align=\"right\"><strong>Total</strong></td>
                            <td align=\"right\"><strong>".getAngka($totalDebit)."</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        
                        <tr>
                            <td colspan=\"5\" align=\"right\"><strong>Balance</strong></td>
                            <td align=\"right\"><strong>".getAngka($balance)."</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        ";

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

function getKodeCompany()
{
    global $par;

    $getData = getRows("select nomorSupplier from dta_supplier where kodeSupplier = '" . $par['idCompany'] . "'");
    echo json_encode($getData);
}

function simpan()
{
    global $inp, $par, $cID, $s, $arrParam, $dirFile;

    $sql = "UPDATE
                  `tagihan_termin`
                SET
                  `pengajuan_prep_by` = '$inp[pengajuan_prep_by]',
                  `pengajuan_post_date` = '".setTanggal($inp[pengajuan_post_date])."',
                  `pengajuan_doc_date` = '".setTanggal($inp[pengajuan_doc_date])."',
                  `pengajuan_month` = '$inp[pengajuan_month]',
                  `pengajuan_company` = '$inp[pengajuan_company]',
                  `pengajuan_customer_code` = '$inp[pengajuan_customer_code]',
                  `pengajuan_assignment` = '$inp[pengajuan_assignment]',
                  `pengajuan_refrence` = '$inp[pengajuan_refrence]',
                  `pengajuan_ref_key_2` = '$inp[pengajuan_ref_key_2]',
                  `pengajuan_header_text` = '$inp[pengajuan_header_text]',
                  `updated_at` = now(),
                  `updated_by` = '" . $cID . "'
                WHERE `id` = '" . $par["id_termin"] . "'
                ";
    db($sql);

    echo "<script>alert(\"Data berhasil disimpan.\");</script>";
    echo "<script>window.location='?par[mode]=detailForm".getPar($par, 'mode')."'</script>";
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function detailFormApproval()
{
    global $s, $par, $arrTitle, $menuAccess;

    $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
    $spk = getRow("select * from tagihan_spk where id = $termin[id_spk]");

    $text .= "
    ".view_permohonan($arrTitle[$s], $termin['id_spk'], '', false, $par[id_termin], true)."
    
    
    <br>
    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
	
	    <!--
	
        <form id=\"form\" class=\"stdform\">
            <fieldset>
                <legend>Pengajuan</legend>
            
                <p>
                    <label class=\"l-input-small\">Prepared By</label>
                    <span class=\"field\">
                        " . getField("select nama from pegawai_data where id = '$termin[pengajuan_prep_by]'") . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Posting Date</label>
                    <span class=\"field\">  
                        " . getTanggal($termin["pengajuan_post_date"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Document Date</label>
                    <span class=\"field\">  
                        " .  getTanggal($termin["pengajuan_doc_date"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Month</label>
                    <span class=\"field\">   
                         " . getBulan($termin['pengajuan_month'], 'string') . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Company Name</label>
                    <span class=\"field\">
                        " . getField("select namaSupplier from dta_supplier where kodeSupplier = '$termin[pengajuan_company]'"). " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Customer Code</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_customer_code"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Assignment</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_assignment"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Reference</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_refrence"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Ref Key 2</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_ref_key_2"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Header Text</label>
                    <span class=\"field\">
                        " . $termin["pengajuan_header_text"] . " &nbsp;
                    </span>
                </p>
            </fieldset>
        </form>
        
        <br>
        --> 
        <br>
        <div id=\"flagForm\"></div>
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform formGL\" action=\"?_submit=1" . getPar($par) . "\" enctype=\"multipart/form-data\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>GL ACCOUNT</h3>
                </div>
                <!--
                <input style=\"position: relative; float: right; margin-top: -55px; margin-right: 5px;\" type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
                -->
            </div>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" style=\"vertical-align: middle\">No</th>
                        <th width=\"150\" style=\"vertical-align: middle\">kode</th>
                        <th width=\"250\" style=\"vertical-align: middle\">account</th>
                        <th width=\"150\" style=\"vertical-align: middle\">pk</th>
                        <th width=\"150\" style=\"vertical-align: middle\">curr</th>
                        <th width=\"150\" style=\"vertical-align: middle\">ammount</th>
                        <th width=\"150\" style=\"vertical-align: middle\">cost center</th>
                        <th width=\"150\" style=\"vertical-align: middle\">REF  KEY</th>
                        <th width=\"150\" style=\"vertical-align: middle\">Kode Proyek</th>
                        <th width=\"150\" style=\"vertical-align: middle\">Text</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select *, a.id as idBiaya from tagihan_termin_biaya as a
                                        left join account_gl as b on (b.id = a.gl_account)
                                        where a.id_termin = '".$par["id_termin"]."' 
                                        order by a.id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"center\">".$data[kode]."</td>
                                <td align=\"left\">".$data[judul]."</td>
                                <td align=\"center\"></td>
                                <td align=\"center\">".$data[currency]."</td>
                                <td align=\"right\">".getAngka($data["nilai_plus_ppn"])."</td>
                                <td align=\"center\"></td>
                                <td align=\"center\"></td>
                                <td align=\"center\"></td>
                                <td align=\"left\">".$data["biaya"]."</td>
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
            
            <br>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" width=\"100%\">
            <thead>
                <tr>
                    <th colspan=\"2\">APPROVAL</th>
                </tr>
                <tr>
                    <th width=\"50%\">1</th>
                    <th width=\"50%\">2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    ";
                    for ($i = 1; $i <= 2; $i++) {

                        $text.="
                        <td align=\"center\">
                            <br>                        
                            ";
                            $dtAppr = getRow("select * from tagihan_approval_jurnal where id_termin = '".$par['id_termin']."' and approve_lvl = '$i'");

                            if ($dtAppr["approve_status"] == "t"){ #ini kalau setuju
                                $background = "class=\"labelStatusHijau\" style=\"width: 35%\"";
                            } elseif ($dtAppr["approve_status"] == "p") {
                                $background = "class=\"labelStatusBiru\" style=\"width: 35%\""; #ini kalau pending
                            } elseif ($dtAppr["approve_status"] == "f") { #kalo ditolak
                                $background = "class=\"labelStatusMerah\" style=\"width: 35%\"";
                            } else { #kalau belum
                                $background = "class=\"labelStatusKuning\" style=\"width: 35%\"";
                            }

                            $appr = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[lvl]=$i". getPar($par, "mode") . "', 600, 300);\">Belum</a>";

                            if ($dtAppr["approve_status"] == "t") $appr = "Setuju";
                            if ($dtAppr["approve_status"] == "f") $appr = "Tolak";
                            if ($dtAppr["approve_status"] == "p") $appr = "Pending";

                            if (isset($menuAccess[$s]["apprlv$i"])) {
                                $approval = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[lvl]=$i". getPar($par, "mode") . "', 600, 300);\">$appr</a>";
                            } else {
                                $approval = "$appr";
                            }

                            $text.="
                            <div align=\"center\" ".$background.">" . $approval . "</div>
                            ";
                             if (!empty($dtAppr)) {
                                $text.="
                                    <br><br>
                                    ".getField("select namaUser from app_user where id = '".$dtAppr['approve_by']."'")." &nbsp;
                                    <br>
                                    ".getTanggal($dtAppr['approve_date'])." &nbsp;
                                    <br>
                                    ".$dtAppr['approve_desc']."
                                ";
                            }
                            $text.="
                        </td>
                        ";
                    }
                    $text.="
                    
                </tr>
            </tbody>
        </table>
            
        </form>
            
    </div>";

    return $text;
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $termin = getRow("select * from tagihan_termin where id = '".$par['id_termin']."'");
    $spk = getRow("select * from tagihan_spk where id = $termin[id_spk]");
    $idTermin = ($spk[id_jenis] == 1048) ? $par[id_termin] : '';

    $text .= "
    ".view_permohonan($arrTitle[$s], $termin['id_spk'], '', false, $idTermin)."
    
    <br>
    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
            <fieldset>
                <legend>Pengajuan</legend>
            
                <p>
                    <label class=\"l-input-small\">Prepared By</label>
                    <div class=\"field\">
                        " . comboData("select * from pegawai_data where bagian = '1110' order by nama asc", "id", "nama", "inp[pengajuan_prep_by]", "- Pilih -", $termin["pengajuan_prep_by"], "", "210px", "chosen-select") . "
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Posting Date</label>
                    <div class=\"field\">  
                        ";
                        $termin["pengajuan_post_date"] = empty($termin["pengajuan_post_date"]) ? date("Y-m-d") : $termin["pengajuan_post_date"];
                        $text .= "
                        <input type=\"text\" id=\"inp[pengajuan_post_date]\" name=\"inp[pengajuan_post_date]\"  value=\"" . getTanggal($termin["pengajuan_post_date"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Document Date</label>
                    <div class=\"field\">  
                        ";
                        $termin["pengajuan_doc_date"] = empty($termin["pengajuan_doc_date"]) ? date("Y-m-d") : $termin["pengajuan_doc_date"];
                        $text .= "
                        <input type=\"text\" id=\"inp[pengajuan_doc_date]\" name=\"inp[pengajuan_doc_date]\"  value=\"" . getTanggal($termin["pengajuan_doc_date"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Month</label>
                    <div class=\"field\">   
                        ";
                        $termin['pengajuan_month'] = empty($termin['pengajuan_month']) ? date("m") : $termin['pengajuan_month'];
                        $text.="
                        ".comboMonth('inp[pengajuan_month]', $termin['pengajuan_month'], '150px')."
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Company Name</label>
                    <div class=\"field\">
                        " . comboData("select * from dta_supplier where tipe = 'customer' order by namaSupplier asc", "kodeSupplier", "namaSupplier", "inp[pengajuan_company]", "- Pilih -", $termin["pengajuan_company"], "onchange=\"getKodeCompany(this.value, '".getPar($par, 'mode')."')\"", "210px", "chosen-select") . "
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Customer Code</label>
                    <div class=\"field\">
                        <input type=\"text\" readonly id=\"inp[pengajuan_customer_code]\" name=\"inp[pengajuan_customer_code]\"  value=\"" . $termin["pengajuan_customer_code"] . "\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Assignment</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[pengajuan_assignment]\" name=\"inp[pengajuan_assignment]\"  value=\"" . $termin["pengajuan_assignment"] . "\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Reference</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[pengajuan_refrence]\" name=\"inp[pengajuan_refrence]\"  value=\"" . $termin["pengajuan_refrence"] . "\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Ref Key 2</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[pengajuan_ref_key_2]\" name=\"inp[pengajuan_ref_key_2]\"  value=\"" . $termin["pengajuan_ref_key_2"] . "\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Header Text</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[pengajuan_header_text]\" name=\"inp[pengajuan_header_text]\"  value=\"" . $termin["pengajuan_header_text"] . "\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
                    </div>
                </p>
                
                <input style='position: relative; float: right; margin-top: -380px;' type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>
                
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
        $where = " WHERE a.pengajuan_no_tiket != '' ";
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
    else $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.persen,
            a.id_spk,
            a.pengajuan_approve_status,
            a.pengajuan_no_tiket,
            a.nilai_plus_ppn,
            a.balance_status,
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

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");




        $appr = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[id_termin]=" . $r["id"] . getPar($par, "mode, id_termin") . "', 600, 300);\" style=\"text-decoration: none; color:black;\">Belum</a>";
//
//        if ($r["pengajuan_approve_status"] == "t") $appr = "Setuju";
//        if ($r["pengajuan_approve_status"] == "f") $appr = "Tolak";
//        if ($r["pengajuan_approve_status"] == "p") $appr = "Pending";

        $totalAppr = 2;
        $appr = getField("select count(*) from tagihan_approval_jurnal where id_termin = '".$r['id']."' and approve_status = 't'");

        if ($totalAppr == $appr){ #ini kalau full
            $background = "class=\"labelStatusHijau\"";
        } elseif ($appr > 0) {
            $background = "class=\"labelStatusBiru\""; #ini kalau kriterianya ada, tapi ga full
        } else { #kalo 0
            $background = "class=\"labelStatusKuning\"";
        }

//        if (isset($menuAccess[$s]["apprlv1"])) {
//            $approval = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[id_termin]=" . $r["id"] . getPar($par, "mode, id_termin") . "', 600, 300);\">$appr</a>";
//        } else {
//            $approval = $appr;
//        }

//        $gl = getField("select count(*) from tagihan_termin_biaya where id_termin = '$r[id]'");
//        $map = getField("select count(*) from tagihan_termin_biaya where id_termin = '$r[id]' and gl_account != ''");
//
//        if ($gl == $map){ #ini kalau full
//            $backgroundMap = "class=\"labelStatusHijau\"";
//        } elseif ($map > 0) {
//            $backgroundMap = "class=\"labelStatusBiru\""; #ini kalau kriterianya ada, tapi ga full
//        } else { #kalo 0
//            $backgroundMap = "class=\"labelStatusKuning\"";
//        }

        if ($r[balance_status] == 1){
            $backgroundMap = "class=\"labelStatusHijau\"";
            $status = 'Sudah Balance';
        } else {
            $backgroundMap = "class=\"labelStatusKuning\"";
            $status = 'Belum Balance';
        }


        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&&par[id_termin]=" . $r["id"]."&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"center\" ". $backgroundMap ."><a href=\"?par[mode]=detailGL&par[id_termin]=".$r["id"]."&par[id_spk]=".$r["id_spk"]."". getPar($par, "mode, id_spk, id_termin")."\">$status</a></div>",
            "<div align=\"center\"><a href=\"#\" onclick=\"openBox('void.php?par[mode]=print_dokumen&par[id_tagihan]=$r[id_tagihan]&par[id_termin]=$r[id]" . getPar($par, "mode, id_tagihan, id_termin") . "',800,425);\" title=\"PRINT SP3\" class=\"print\"><span>Print</span></a></div>",
            "<div align=\"center\" ". $background ."><a href=\"?par[mode]=detailFormApproval&par[id_termin]=".$r["id"]."&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\">".getAngka($totalAppr)." / ".getAngka($appr)."</a></div>",
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
                        <th style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Jurnal</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Print</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">Approval</th>
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
              "Jurnal",
              "Approval"];

    $where = " WHERE a.nilai_biaya_total = a.nilai_plus_ppn";

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
            a.pengajuan_approve_status,
            a.pengajuan_no_tiket,
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

        $gl = getField("select count(*) from tagihan_termin_biaya where id_termin = '$r[id]'");
        $map = getField("select count(*) from tagihan_termin_biaya where id_termin = '$r[id]' and gl_account != ''");

        if ($r["pengajuan_approve_status"] == "t") $appr = "Setuju";
        if ($r["pengajuan_approve_status"] == "f") $appr = "Tolak";
        if ($r["pengajuan_approve_status"] == "p") $appr = "Pending";

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			getAngka($r['nilai_plus_ppn']) . "\t right",
			$r['pengajuan_no_tiket'] . "\t center",
			getAngka($gl)." / ".getAngka($map). "\t center",
            $appr . "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}