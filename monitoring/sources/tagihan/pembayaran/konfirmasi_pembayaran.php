<?php

use mikehaertl\wkhtmlto\Pdf;

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_bayar/";

$getPajak = queryAssoc("select * from mst_data where kodeCategory = 'MDPJ' order by urutanData asc");

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

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], true, $par['id_termin'], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "print_sp3":
            $text = print_sp3();
            break;

        case "print_jurnal":
            $text = print_jurnal();
            break;

        case "tax":
            $text = tax();
            break;
    }

    return $text;
}

function print_sp3()
{
	global $par;

	// pc = print collumn

	$pc1 = getRow("SELECT
                        a.id,
                        a.nilai_plus_ppn,
                        a.nilai_total,
                        
                        b.nomor,
                        b.id_proyek,
                        b.id_jenis,
                        
                        c.id AS id_tagihan,
                        c.tgl_terima,
                        c.no_invoice,
                        
                        e.namaData
                        
                        FROM
                        tagihan_termin AS a
                        JOIN tagihan_spk AS b ON (b.id = a.id_spk AND approve_status = 't' AND persen_termin = '100')
                        JOIN tagihan_data AS c ON (c.id_termin = a.id)
                        JOIN costcenter_data AS d ON (d.id = b.id_cc)
                        JOIN mst_data AS e ON (e.kodeData = d.sbu)
                        where c.id = '".$par[id_tagihan]."'");


    if ($pc1['id_jenis'] == 1048)
    {
        $pc2 = getRow("SELECT
                        a.id,
                        c.id AS id_tagihan,
                        b.judul,
                        e.namaSupplier AS namaVendor,
                        d.namaBank,
                        d.pemilikBank AS atasNama,
                        d.rekeningBank AS nomorRekening,
                        f.nama AS namaCostCenter
                        FROM
                        tagihan_termin AS a
                        JOIN tagihan_spk AS b ON (b.id = a.id_spk AND approve_status = 't' AND persen_termin = '100')
                        JOIN tagihan_data AS c ON (c.id_termin = a.id)
                        JOIN dta_supplier_bank AS d ON (d.kodeSupplier = b.id_supplier)
                        JOIN dta_supplier AS e ON (e.kodeSupplier = d.kodeSupplier)
                        JOIN costcenter_data AS f ON (f.id = b.id_cc)
                        where c.id = '".$par[id_tagihan]."'");

    } else {
        $pc2 = getRow("SELECT
                        a.id,
                        c.id AS id_tagihan,
                        b.judul,
                        d.nama AS namaVendor,
                        e.namaData AS namaBank,
                        d.nama_pemilik AS atasNama,
                        d.norek AS nomorRekening,
                        f.nama AS namaCostCenter
                        FROM
                        tagihan_termin AS a
                        JOIN tagihan_spk AS b ON (b.id = a.id_spk AND approve_status = 't' AND persen_termin = '100')
                        JOIN tagihan_data AS c ON (c.id_termin = a.id)
                        JOIN pegawai_data AS d ON (d.id = b.id_supplier)
                        JOIN mst_data AS e ON (e.kodeData = d.bank)
                        JOIN costcenter_data AS f ON (f.id = b.id_cc)
                        where c.id = '".$par[id_tagihan]."'");
    }


	//dd($detail);

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
            	<div style='float: left; width: 500px; font-size: 14px'>
            		No : FAC.PDC.FM.08 <br>
                    Rev : 01/22 <br>
                    Tgl Terbit : ". date("d M Y") ." <br>
				</div>
            	<div style='float: right; margin-top: -55px'>
            		<img src='http://pertaminapdc.com/monitoring/images/logopertaminapdc.png' style='width: 200px;'>
				</div>
			</div>
			
            <br style='clear: both'>
            
            <div style='font-size: 14px; padding: 10px; margin-top: -20px;'>
            
                <h2>SURAT PERMINTAAN PROSES PEMBAYARAN</h2>
				
				<table width='100%'>
				    <tr>
				        <td width='20%'>Nomor</td>
				        <td>:</td>
				        <td>". $pc1['nomor'] ."</td>
                    </tr>
                    <tr>
                        <td width='20%'>Tanggal</td>
				        <td>:</td>
				        <td>". $pc1['tgl_terima'] ."</td>
                    </tr>
                    <tr>
                        <td width='20%'>Kepada</td>
				        <td>:</td>
				        <td>Treasury Manager</td>
                    </tr>
                    <tr>
                        <td width='20%'>Dari</td>
				        <td>:</td>
				        <td>$pc1[namaData]</td>
                    </tr>
                </table>
                
                <br>
                <hr>
                <br>
                
                <strong>Terlampir kami kirimkan Dokumen Pendukung Pembayaran terdiri dari : </strong>
                
                <table width='100%'>
				    <tr>
				        <td width='20%'>1. Invoice No.</td>
				        <td>:</td>
				        <td>". $pc1['no_invoice'] ."</td>
                    </tr>
                    <tr>
				        <td width='20%'>2. Kwitansi No.</td>
				        <td>:</td>
				        <td></td>
                    </tr>
                    <tr>
				        <td width='20%'>3. Faktur Pajak No.</td>
				        <td>:</td>
				        <td></td>
                    </tr>
                    <tr>
				        <td width='20%'>4. SPK No.</td>
				        <td>:</td>
				        <td>". $pc1['nomor']."</td>
                    </tr>
                    <tr>
				        <td width='20%'>5. Akrual No.</td>
				        <td>:</td>
				        <td></td>
                    </tr>
                </table>
				
				<br>
				
				<strong>Untuk Pembayaran : </strong>
				
				<br>
					
                ". $pc2['judul'] ."
                
                <br>
                <br>
                
                <strong>Jumlah Pembayaran : IDR ". getAngka($pc1['nilai_plus_ppn'])." </strong>
                
                <br>
                <br>
				
				<strong>Agar dibayarkan kepada : </strong>
				
				<br>
                <br>
                
                <table width='100%'>
				    <tr>";
	                $pemohon = ($pc1["id_jenis"] == '1048') ? "Vendor" : "Pekerja";
	                $html .= "
				        <td width='30%'>Nama ".$pemohon."</td>
				        <td>:</td>
				        <td>". $pc2['namaVendor']."</td>
                    </tr>
                    <tr>
				        <td width='30%'>Nama Bank</td>
				        <td>:</td>
				        <td>". $pc2['namaBank'] ."</td>
                    </tr>
                    <tr>
				        <td width='30%'>Atas Nama Rekening</td>
				        <td>:</td>
				        <td>". $pc2['atasNama'] ."</td>
                    </tr>
                    <tr>
				        <td width='30%'>Nomor Rekening</td>
				        <td>:</td>
				        <td>". $pc2['nomorRekening'] ."</td>
                    </tr>
                    <tr>
				        <td width='30%'><i>Cost Center</i></td>
				        <td>:</td>
				        <td>".$pc2["namaCostCenter"]."</td>
                    </tr>
                    <tr>
				        <td width='30%'>Kode Project</td>
				        <td>:</td>
				        <td>".getField("SELECT nomor FROM proyek_data WHERE id = $pc1[id_proyek]")."</td>
                    </tr>
                </table>
                
                <br>
                <br>
                
                <strong>
                    Keterangan:
                    <br>
                    Dengan ini kami menyatakan bahwa transaksi yang ditagihkan ini benar dan keabsahan dokumen menjadi tanggungjawab sepenuhnya oleh Pemohon Proses Pembayaran.
                </strong>
				
				<br>
                <br>
				
				Demikian untuk dapat diterima dengan baik dan dapat dilaksanakan proses pembayaran lebih lanjut di tempat Saudara.
				
				<br>
                <br>
                <br>
				
				<table width='100%'>
				    <tr>
				        <td width='50%'>Pemohon,</td>
				        <td>Menyetujui,</td>
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
                    </tr>
                    <tr>
				        <td width='50%'>
				            <br><br><br><br>
				            ......................................
                        </td>
				        <td>
				            <br><br><br><br>
				            ......................................
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

function print_jurnal()
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
				        <th width='30%'>Nomor Dokumen KRs:</th>
				        <td>". getTanggal($pc1['tgl_terima']) ."</td>
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

function tax()
{
    global $s, $par, $arrTitle, $menuAccess, $getPajak;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text.="
            
            
            <div class=\"pageheader\">
                <h1 class=\"pagetitle\">TAX</h1>
                ".(!$popup ? getBread(ucwords(str_replace("Detail", "", $par["mode"]))) : "&nbsp")."
	        </div>
            
            <br />
            <br />
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
            <div id=\"flagForm\"></div>
                
                <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
                
                    <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                        <div class=\"title\">
                            <h3>PPH PASAL (%)</h3>
                        </div>
                    </div>
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                        <thead>
                            <tr>
                                <th width=\"20\" style=\"vertical-align: middle\">No</th>
                                <th width=\"*\" style=\"vertical-align: middle\">Pajak</th>
                                <th width=\"150\" style=\"vertical-align: middle\">DPP</th>
                                <th width=\"150\" style=\"vertical-align: middle\">Tarif</th>
                                <th width=\"150\" style=\"vertical-align: middle\">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            ";

                            $no = 0;
                            foreach ($getPajak as $pjk) {

                                $no++;

                                $data = getRow("select * from tagihan_pajak where id_tagihan = '$par[id_tagihan]' and id_pajak = '$pjk[kodeData]'");

                                $text.="
                                <tr>
                                    <td align=\"center\">".$no."</td>
                                    <td align=\"left\">".$pjk['namaData']."</td>
                                    <td align=\"right\">". getAngka($data['dpp'])."</td>
                                    <td align=\"right\">".$data['tarif']."</td>
                                    <td align=\"right\">".(!empty($data['nilai']) ? getAngka($data['nilai']) : "")."</td>
                                </tr>
                                ";

                                $grandTotal += $data['nilai'];
                            }


                            $text.="
                            <tr>
                                <td colspan=\"4\" align=\"right\"><strong>Total</strong></td>
                                <td align=\"right\"><strong>".getAngka($grandTotal)."</strong></td>
                            </tr>

                        </tbody>
                    </table>
                </form>
            </div>";

    return $text;
}

function hapus()
{
    global $par, $dirFile;

    $file = getField("select bukti_bayar from tagihan_bayar where id = '".$par['id_pembayaran']."'");
    unlink($dirFile.$file);

    db("delete from tagihan_bayar where id = '".$par["id_pembayaran"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=tax" . getPar($par, "mode, id_pembayaran") . "';</script>";
}

function simpan()
{
    global $inp, $par, $cID;

    $sql = "UPDATE
                  `tagihan_termin`
                SET
                  `pembayaran_approve_status` = '".$inp['konf_status']."',
                  `pembayaran_approve_desc` = '".$inp['konf_desc']."',
                  `pembayaran_approve_date` = '".date("Y-m-d")."',
                  `pembayaran_approve_by` = '".$cID."'
                WHERE `id` = '".$par["id_termin"]."'
                ";
    db($sql);

//    $sql = "UPDATE
//                  `tagihan_bayar`
//                SET
//                  `konf_status` = '".$inp['konf_status']."',
//                  `konf_desc` = '".$inp['konf_desc']."',
//                  `konf_date` = '".date("Y-m-d")."',
//                  `konf_by` = '".$cID."'
//                WHERE `id` = '".$par["id_pembayaran"]."'
//                ";
//    db($sql);
//
//    // update total //
//
//    $tagihan = getRow("SELECT b.* FROM tagihan_bayar AS a
//                        JOIN tagihan_data AS b ON (b.id = a.id_tagihan)
//                        WHERE a.id = '".$par["id_pembayaran"]."'");
//
//    $id_termin = $tagihan['id_termin'];
//    $id_tagihan = $tagihan['id'];
//    $id_spk = $tagihan['id_spk'];
//
//    $jumlah = getField("select count(*) from tagihan_bayar where konf_status = 't' and id_tagihan = $id_tagihan");
//    $nilai  = getField("select nilai_total from tagihan_termin where id = $id_termin");
//    $bayar  = getField("select sum(nilai) from tagihan_bayar where konf_status = 't' and id_tagihan = $id_tagihan");
//    $sisa   = $nilai - $bayar;
//
//    db("update tagihan_termin set
//                                jumlah_bayar = '".$jumlah."',
//                                bayar = '".$bayar."',
//                                sisa = '".$sisa."'
//                            where id = $id_termin");
//
//    $status_pelunasan = ($sisa == 0) ? "lunas" : "sebagian";
//
//    db("update tagihan_data set  status_pelunasan = '".$status_pelunasan."' where id = $id_tagihan");
//
//    updatePelunasanSPK($id_spk);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function updatePelunasanSPK($id_spk) {

    $getSpk = getRows("SELECT b.* FROM tagihan_termin AS a
                        LEFT JOIN tagihan_data AS b ON (b.id_termin = a.id)
                        WHERE a.id_spk = '".$id_spk."'");

    $status = "lunas";
    foreach ($getSpk as $spk) {

        if ($spk['status_pelunasan'] != "lunas") $status = "sebagian";

    }

    db("update tagihan_spk set status_pelunasan = '".$status."' where id = '".$id_spk."'");

}

function form()
{
    global $par, $cID, $menuAccess, $s;

    $r = getRow("SELECT * FROM tagihan_bayar WHERE id = '".$par['id_pembayaran']."'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">APPROVAL</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    <!--
				    " . (isset($menuAccess[$s]["apprlv1"]) ? "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>" : "") . "
				    -->
				    <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>
				</p>
			</div>
			<fieldset>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["konf_date"] = $r["konf_date"] ?: date('Y-m-d');
                        $text .= "
                        <input type=\"text\" id=\"inp[konf_date]\" name=\"inp[konf_date]\"  value=\"" . getTanggal($r["konf_date"]) . "\" class=\"hasDatePicker\" maxlength=\"150\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Nama</label>
                    <div class=\"field\">
                        ";
                        $r["konf_by"] = (empty($r["konf_by"])) ? getField("select namaUser from app_user where id = '$cID'") : getField("select namaUser from app_user where id = '" . $r["konf_by"] . "'");
                        $text .= "
                        <input type=\"text\" readonly id=\"inp[konf_by]\" style=\"width:300px;\" name=\"inp[konf_by]\" size=\"10\" maxlength=\"150\" value=\"" . $r["konf_by"] . "\" class=\"vsmallinput\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Keterangan</label>
                    <div class=\"field\">
                        <textarea name=\"inp[konf_desc]\" style=\"width:300px;\" id=\"inp[konf_desc]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >" . nl2br($r["konf_desc"]) . "</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[konf_status]\" name=\"inp[konf_status]\" value=\"t\" " . (($r["konf_status"] == "t" or empty($r["konf_status"])) ? "checked" : "") . "/> <span class=\"sradio\">Setuju</span>
                        <input type=\"radio\" id=\"inp[konf_status]\" name=\"inp[konf_status]\" value=\"p\" " . ($r["konf_status"] == "p" ? "checked" : "") . " /> <span class=\"sradio\">Pending</span>
                        <input type=\"radio\" id=\"inp[konf_status]\" name=\"inp[konf_status]\" value=\"f\" " . ($r["konf_status"] == "f" ? "checked" : "") . " /> <span class=\"sradio\">Tolak</span>
                    </div>
                </p>
            </fieldset>
		</form>
	</div>";

    return $text;
}

/*function form()
{
    global $par, $dirFile;

    $r = getRow("SELECT * FROM tagihan_bayar WHERE id = '".$par['id_pembayaran']."'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">PEMBAYARAN</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Konfirmasi\" onclick=\"return confirm('Konfirmasi?');\"/>
				</p>
			</div>
			<fieldset>
			
			    <p>
                    <label class=\"l-input-small\">Tanggal</label>
                    <span class=\"field\">
                        " . getTanggal($r["tanggal"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Nama Pemohon</label>
                    <span class=\"field\">
                        " .$r["nama_pemohon"] . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">No rekening</label>
                    <span class=\"field\">
                        ".getField("select concat(namaBank, ' - ', rekeningBank, ' (', pemilikBank, ')') from dta_supplier_bank where id = '".$r['id_norek']."'")." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <span class=\"field\">
                        ".getAngka($r["nilai"])." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <span class=\"field\">
                        ".$r["catatan"]." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Bukti Bayar</label>
                    <div class=\"field\">";
                        $text .= empty($r['bukti_bayar'])
                        ?
                        ""
                        :
                        "<a href=\"".$dirFile.$r['bukti_bayar']."\" download><img src=\"".getIcon($r['bukti_bayar'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        <br clear=\"all\">";
                        $text.="
                    </div>
                </p>
			
			</fieldset>
			<br>
			<fieldset>
			    ";
			    $tagihan = getRow("select * from tagihan_data where id = '".$r['id_tagihan']."'");
			    $spk = getRow("SELECT * FROM tagihan_spk WHERE id = '".$tagihan['id_spk']."'");

			    $tmpl = getField("select nilaiParameter from app_parameter where namaParameter = 'template_konfirmasi_pembayaran'");

			    $tmpl = str_replace("{no_invoice}", $tagihan['no_invoice'], $tmpl);
			    $tmpl = str_replace("{judul_spk}", $spk['judul'], $tmpl);
			    $tmpl = str_replace("{nilai}", getAngka($r["nilai"]), $tmpl);
			    $tmpl = str_replace("{norek}", getField("select concat(namaBank, ' - ', rekeningBank) from dta_supplier_bank where id = '".$r['id_norek']."'"), $tmpl);
			    $tmpl = str_replace("{tgl_bayar}", getTanggal($r["tanggal"]), $tmpl);

			    $r["konf_desc"] = empty($r["konf_desc"]) ? $tmpl : $r["konf_desc"];

			    $text.="
			    <textarea id=\"inp[konf_desc]\" name=\"inp[konf_desc]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:95%;\">".$r["konf_desc"]."</textarea>
            </fieldset>
		</form>
	</div>";

    return $text;
}*/

function lData()
{
    global $par, $menuAccess, $s;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE c.tiket_nomor != ''";
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
            a.pembayaran_approve_status,
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
            d.namaSupplier,
            e.nama
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
            -- join tagihan_bayar as f on (f.id_tagihan = c.id)
            $where order by $order $limit";
    $res = db($sql);


    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
                                            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
                                            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
                                            -- join tagihan_bayar as f on (f.id_tagihan = c.id)
                                            $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        if ($r["pembayaran_approve_status"] == "t"){ #ini kalau setuju
            $background = "class=\"labelStatusHijau\"";
        } elseif ($r["pembayaran_approve_status"] == "p") {
            $background = "class=\"labelStatusBiru\""; #ini kalau pending
        } elseif ($r["pembayaran_approve_status"] == "f") { #kalo ditolak
            $background = "class=\"labelStatusMerah\"";
        } else { #kalau belum
            $background = "class=\"labelStatusKuning\"";
        }

        if ($r["pembayaran_approve_status"] == "t") $appr = "Setuju";
        if ($r["pembayaran_approve_status"] == "f") $appr = "Tolak";
        if ($r["pembayaran_approve_status"] == "p") $appr = "Pending";
        if ($r["pembayaran_approve_status"] == "") $appr = "Belum";

        if (isset($menuAccess[$s]["apprlv1"])) {
            $approval = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id_supplier]=".$r['id_supplier']."&par[id_termin]=".$r["id"]."" . getPar($par, "mode, id_termin, id_supplier") . "', 600, 300);\" style=\"text-decoration: none; color:black;\">$appr</a>";
        } else {
            $approval = $appr;
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
            "<div align=\"right\">".getAngka($r["nilai_total"])."</div>",
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"center\">".$r["tiket_nomor"]."</div>",
            "<div align=\"center\"><a href=\"#\" onclick=\"openBox('void.php?par[mode]=print_sp3&par[id_tagihan]=$r[id_tagihan]" . getPar($par, "mode, id_tagihan") . "',900,500);\" title=\"PRINT SP3\" class=\"print\"><span>Print</span></a></div>",
            "<div align=\"center\"><a href=\"#\" onclick=\"openBox('void.php?par[mode]=print_jurnal&par[id_tagihan]=$r[id_tagihan]&par[id_termin]=$r[id]" . getPar($par, "mode, id_tagihan, id_termin") . "',900,500);\" title=\"PRINT SP3\" class=\"print\"><span>Print</span></a></div>",
            "<div align=\"center\"><a  href=\"#\" onclick=\"openBox('popup.php?par[mode]=tax&par[pop_up]=1&par[id_tagihan]=" . $r["id_tagihan"] . getPar($par, "mode, id_tagihan") . "',900,500);\" class=\"detail\"><span>Tax</span></a></div>",
            "<div align=\"center\" $background>".$approval."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $arrTitle, $par;

	$text = table(11, array(3, 4, 5, 6, 7, 8, 9, 10, 11));

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
                        <th style=\"vertical-align: middle; min-width: 80px;\">No Pembayaran</th>
                        <th style=\"vertical-align: middle; min-width:50px;\">sp3</th>
                        <th style=\"vertical-align: middle; min-width:50px;\">form jurnal</th>
                        <th style=\"vertical-align: middle; min-width:50px;\">tax</th>
                        <th style=\"vertical-align: middle; min-width:50px;\">Approval</th>
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
              "No. Pembayaran",
              "Catatan",
              "Approval"];

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
            d.namaSupplier,
            e.nama,
            f.bukti_bayar,
            f.id as idBayar,
            f.konf_status,
            f.konf_date,
            f.catatan
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
            join tagihan_bayar as f on (f.id_tagihan = c.id) $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        if ($r["konf_status"] == "t") $appr = "Setuju";
        if ($r["konf_status"] == "f") $appr = "Tolak";
        if ($r["konf_status"] == "p") $appr = "Pending";
        if ($r["konf_status"] == "") $appr = "Belum";

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"] . "\t center",
			getAngka($r["nilai_total"]) . "\t right",
            $r["pengajuan_no_tiket"] . "\t center",
			$r["tiket_nomor"]."\t center",
			$r['catatan'] . "\t left",
			$appr. "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}