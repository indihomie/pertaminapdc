<?php

function view_permohonan($judul = "", $idSPK = "", $popup = "", $dataTermin = false, $idTermin = "", $dataDokumen = false, $statusPembayaran = true)
{

    $r = getRow("SELECT a.*, concat(c.nomor, ' - ', c.proyek) as namaProyek, d.nama as namaCC, e.namaData as jenisPermohonan, f.dokumen as namaDokumen, g.namaData as nilaiBertahap, h.namaData as tahapanTagihan
                from tagihan_spk as a
                -- join dta_supplier as b on (b.kodeSupplier = a.id_supplier)
                join proyek_data as c on (c.id = a.id_proyek)
                join costcenter_data as d on (d.id = a.id_cc)
                join mst_data as e on (e.kodeData = a.jenis_permohonan)
                left join dokumen_pendukung as f on (f.kategori = a.id_jenis)
                left join mst_data as g on (g.kodeMaster = a.nilai_tahapan_bertahap)
                left join mst_data as h on (h.kodeMaster = a.tahapan_tagihan)
                WHERE a.id = '$idSPK'");

    $termin = getRow("select * from tagihan_termin where id = '$idTermin'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">". strtoupper($judul) ."</h1>
		".(!$popup ? getBread(ucwords(str_replace("Detail", "", $par["mode"]))) : "&nbsp")."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\" >
		    <p style=\"position: absolute; right: 20px; top: 10px;\">
		        ".(!$popup ? "<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk, id_tagihan, id_termin, id_pajak") . "';\"/>" : "")."
			</p>

			".(!$popup ? "<br>" :  "")."
			
			<ul class=\"hornav\">
                <li class=\"current\"><a href=\"#1\">Dasar Permohonan</a></li>
                <li class=\"\"><a href=\"#2\">Relasi Data</a></li>
                <li class=\"\"><a href=\"#3\">Pembayaran</a></li>";
                if ($dataDokumen == true)
                {
                    $text .= "<li class=\"\"><a href=\"#4\">Dokumen</a></li>";
                }
                $text .= "
            </ul>
			
		    <div id=\"1\" class=\"subcontent\" style=\"display: block;\">
		    
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal Input</label>
                                <span class=\"field2\">
                                    " . getTanggal($r["tanggal"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor</label>
                                <span class=\"field2\">
                                    ".$r["nomor"]." &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>

                <p>
                    <label class=\"l-input-small\">Judul Pekerjaan</label>
                    <span class=\"field\">
                        ".$r["judul"]." &nbsp;
                    </span>
                </p>

                <p>
                    <label class=\"l-input-small\">Uraian</label>
                    <span class=\"field\">
                        ".$r["catatan"]." &nbsp;
                    </span>
                </p>

                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal Mulai</label>
                                <span class=\"field2\">
                                    " . getTanggal($r["target_realisasi"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                             <p>
                                <label class=\"l-input-small2\">Tanggal Selesai</label>
                                <span class=\"field2\">
                                    " . getTanggal($r["target_realisasi_selesai"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">File Perintah Kerja</label>
                    <div class=\"field\">";
                        $text .= empty($r['file_spk'])
                        ?
                        "&nbsp;"
                        :
                        "&nbsp; &nbsp; <a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanSpk&par[id]=$idSPK".getPar($par, "mode, id")."', 700, 400);\"><img src=\"".getIcon($r['file_spk'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>";
                        $text.="
                    </div>
                </p>

                <p>
                    <label class=\"l-input-small\" >Lokasi Pelaksanaan</label>
                    <span class=\"field\">
                        ".$r["alamat"]." &nbsp;
                    </span>
                </p>
                
               <!-- <p>
                    <label class=\"l-input-small\" >Control Balance</label>
                    <span class=\"field\">
                        ".$r["control_balance"]." &nbsp;
                    </span>
                </p> -->

                <table style='width: 100%'>
                <tr>
                    <td style='width: 50%'>
                        <p>
                            <label class=\"l-input-small2\">Nilai DPP</label>
                            <span class=\"field2\">
                                Rp. " . getAngka($r["nilai"]) . " &nbsp;
                            </span>
                        </p>
                    </td>
                    <td style='width: 50%'>
                        <p>
                            <label class=\"l-input-small2\">PPN</label>
                            <span class=\"field2\">
                                " . getAngka($r["ppn"]) . "% &nbsp;
                            </span>
                        </p>
                    </td>
                </tr>
                </table>

                <p>
                    <label class=\"l-input-small\">Grand Total</label>
                    <span class=\"field\">
                        Rp. ".getAngka($r["nilai_plus_ppn"])." &nbsp;
                    </span>
                </p>
        </div>
        
        <div id=\"2\" class=\"subcontent\" style=\"display: none;\">
        
            <p>
                <label class=\"l-input-small\">Cost Center</label>
                <span class=\"field\">
                ".$r["namaCC"]." &nbsp;
                </span>
            </p>

            <p>
                <label class=\"l-input-small\">Proyek</label>
                <span class=\"field\">
                ".$r["namaProyek"]." &nbsp;
                </span>
            </p>";

            if ($r[id_jenis] == '1048') {

                $text.="
                <p>
                    <label class=\"l-input-small\">Vendor</label>
                    <span class=\"field\">
                    ".getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]")." &nbsp;
                    </span>
                </p>";

            }else {

                $text.="
                <p>
                    <label class=\"l-input-small\">Pemohon</label>
                    <span class=\"field\">
                   ".getField("select nama from pegawai_data where id = $r[id_supplier]")." &nbsp;
                    </span>
                </p>";

            }

            $text.="
            <p>
                <label class=\"l-input-small\">Kategori</label>
                <span class=\"field\">
                ".$r["jenisPermohonan"]." &nbsp;
                </span>
            </p>
        </div>

        ";



        $text .= "
                <div id=\"3\" class=\"subcontent\" style=\"display: none;\">
                
                <table width='100%'>
                    <tr>
                        <td width='50%'>  
                            <p>
                                <label class=\"l-input-small2\"\">Uang Muka</label>
                                <span class=\"field2\" style=\"min-width: 40px ;\">  
                                     <input disabled type=\"checkbox\" ".($r[uang_muka] == '1' ? "checked=\"checked\"" : "")."/>
                                </span>
                            </p>
                            
                            <p>
                                <label class=\"l-input-small2\"\">Retensi</label>
                                <span class=\"field2\" style=\"min-width: 40px ;\">  
                                     <input disabled type=\"checkbox\" ".($r[retensi] == '1' ? "checked=\"checked\"" : "")."/>
                                </span>
                            </p>
                            
                        </td>
                        <td>
                            <p>
                                <label class=\"l-input-small\"\">Nilai</label>
                                <span class=\"field2\" style=\"min-width: 100px ;\">
                                    " . getAngka($r["nilai_uang_muka"]) . "%
                                </span>
                            </p>
                            
                            <p>
                                <label class=\"l-input-small\"\">Nilai</label>
                                <span class=\"field2\" style=\"min-width: 100px ;\">
                                    " . getAngka($r["nilai_retensi"]) . "%
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                
                <p>
                    <label class=\"l-input-small\">Tahapan Tagihan</label>
                    <span class=\"field\">
                         " . $r["tahapanTagihan"] . "
                         
                         ";
                         if ($r[tahapan_tagihan] == 'TR') {
                            $display_full = "none";
                            $display_termin = "block";
                            $display_bertahap = "none";
                         }
                         elseif ($r[tahapan_tagihan] == 'FP') {
                            $display_full = "block";
                            $display_termin = "none";
                            $display_bertahap = "none";
                         }
                         elseif ($r[tahapan_tagihan] == 'BT') {
                            $display_full = "none";
                            $display_termin = "none";
                            $display_bertahap = "block";
                         }
                         else {
                            $display_full = "none";
                            $display_termin = "none";
                            $display_bertahap = "none";
                         }
                         $text.="
                         <span id='nilai_tahapan_fullpayment' style=\"display: $display_full; ".(($r[tahapan_tagihan] == 'FP') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")." \">
                            &nbsp;
                            ";
                            $text.="
                            " . getAngka($r["nilai_tahapan_fullpayment"]) . " %
                         </span>
                         
                         <span id='nilai_tahapan_termin' style=\"display: $display_termin; ".(($r[tahapan_tagihan] == 'TR') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")."\">
                            &nbsp;
                            " . getAngka($r["nilai_tahapan_termin"]) . " Kali
                         </span>
                         
                         <span id='nilai_tahapan_bertahap' style=\"display: $display_bertahap; ".(($r[tahapan_tagihan] == 'BT') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")."\">
                            &nbsp;
                            " . $r["nilaiBertahap"] . "        
                         </span>
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Dokumen Pendukung</label>
                    <span class=\"field\">
                        " . $r['namaDokumen'] . "
                    </span>
                </p>
                
                </div>
        ";

        if ($dataDokumen == true) {

            $text .= " 
            <div id=\"4\" class=\"subcontent\" style=\"display: none;\">
                <br>
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                    <thead>
                        <tr>
                            <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                            <th width=\"300\" rowspan=\"2\" style=\"vertical-align: middle\">Dokumen</th>
                            <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Nomor</th>
                            <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Last Update</th>
                            <th width=\"*\" colspan=\"2\">File</th>
                            <th width=\"*\" colspan=\"3\" style=\"vertical-align: middle\">Verifikasi</th>
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
                        $getData = getRows("select * from tagihan_syarat where id_termin = '$idTermin' order by id asc");
                        if ($getData) {

                            $no = 0;
                            foreach ($getData as $data) {

                                $no++;

                                $view = $data['ba_file'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$data[id]" . getPar($par, "mode, id") . "',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                                $download = $data['ba_file'] ? "<a href=\"download.php?d=fileTagihanBa&f=$data[id]" . getPar($par, "mode, id") . "\"><img src=\"" . getIcon($data['ba_file']) . "\" height=\"20\"></a>" : "";

                                if ($data['ba_verifikasi_status'] == 't') $data['ba_verifikasi_status'] = "Diterima";
                                if ($data['ba_verifikasi_status'] == 'f') $data['ba_verifikasi_status'] = "Ditolak";

                                $text .= "
                                <tr>
                                    <td align=\"center\">" . $no . "</td>
                                    <td align=\"left\">" . $data['judul'] . "</td>
                                    <td align=\"center\">" . $data['ba_nomor'] . "</td>
                                    <td align=\"center\">" . $data['updated_at'] . "</td>
                                    <td align=\"center\">" . $view . "</td>
                                    <td align=\"center\">" . $download . "</td>
                                    <td align=\"center\">" . $data['ba_verifikasi_status'] . "</td>
                                    <td align=\"center\">" . getTanggal($data['ba_verifikasi_date']) . "</td>
                                    <td align=\"center\">" . getField("select namaUser from app_user where id = '" . $data['ba_verifikasi_by'] . "'") . "</td>
                                </tr>
                                ";
                            }

                        } else {

                            $text .= "
                            <tr>
                                <td colspan=\"9\"><strong><center>- Data Kosong -</center></strong></td>
                            </tr>
                            ";
                        }
                    $text.="
                </tbody>
            </table>
        </div>
        ";
        }

        if ($idTermin != "") {

            $text .= "

            <br>
            
            <fieldset>
                <legend>Termin Pembayaran</legend>
                <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\">
                        " . $termin['termin'] . " &nbsp;
                    </span>
                </p>
                
                <table style='width: 100%'>
                    <tr>
                        <td style='width: 50%'>
                            <p>
                                <label class=\"l-input-small2\">Prosentase</label>
                                <span class=\"field2\">
                                    " . getAngka($termin['persen']) . "% &nbsp;
                                </span>
                            </p>
                            <p>
                                <label class=\"l-input-small2\">PPN</label>
                                <span class=\"field2\">
                                    Rp. " . getAngka($termin['nilai_ppn']) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style='width: 50%'>   
                            <p>
                                <label class=\"l-input-small2\">Nilai DPP</label>
                                <span class=\"field2\">
                                    Rp. " . getAngka($termin['nilai']) . " &nbsp;
                                </span>
                            </p>    
                            <p>
                                <label class=\"l-input-small2\">PPH</label>
                                <span class=\"field2\">
                                    Rp. " . getAngka($termin['nilai_pph']) . " &nbsp;
                                </span>
                            </p>       
                        </td>
                    </tr>
                </table>
                <p>
                    <label class=\"l-input-small\">Total</label>
                    <span class=\"field\">
                        Rp. " . getAngka($termin['nilai_total']) . " &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\">
                        " . $termin['catatan'] . " &nbsp;
                    </span>
                </p>
            </fieldset>
            ";

        }

        if ($dataTermin == true) {

            $text .= "
            <br />

            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>TERMIN PEMBAYARAN</h3>
                </div>
            </div>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" style=\"vertical-align: middle\">No</th>
                        <th width=\"50\" style=\"vertical-align: middle\">Tanggal Perkiraan</th>
                        <th width=\"200\" style=\"vertical-align: middle\">Termin</th>
                        <th width=\"200\" style=\"vertical-align: middle\">Catatan</th>
                        <th width=\"50\" style=\"vertical-align: middle\">Termin Pembayaran</th>
                        <th width=\"50\" style=\"vertical-align: middle\">Nilai</th>
                        <th width=\"50\" style=\"vertical-align: middle\">Nilai PPN</th>
                        <th width=\"50\" style=\"vertical-align: middle\">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                ";
                $getData = getRows("select * from tagihan_termin where id_spk = '$idSPK' order by id asc");
                if ($getData) {

                    $no = 0;
                    foreach ($getData as $data) {

                        $no++;

                        $total = $data["nilai"] + $data["nilai_ppn"];

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"center\">".getTanggal($data["target"])."</td>
                                <td align=\"left\">".$data["termin"]."</td>
                                <td align=\"left\">".$data["catatan"]."</td>
                                <td align=\"right\">".getAngka($data["persen"])."%</td>
                                <td align=\"right\">".getAngka($data["nilai"])."</td>
                                <td align=\"right\">".getAngka($data["nilai_ppn"])."</td>
                                <td align=\"right\">".getAngka($total)."</td>
                            </tr>
                            ";

                            $persen += $data["persen"];
                            $totalNilai += $data["nilai"];
                            $totalNilaiPPN += $data["nilai_ppn"];
                            $grandTotal += $data["nilai_plus_ppn"];
                        }

                        $text.="
                        <tr>
                            <td colspan=\"4\" align=\"right\"><strong>TOTAL</strong></td>
                            <td align=\"right\"><strong>".getAngka($persen)."%</strong></td>
                            <td align=\"right\"><strong>".getAngka($totalNilai)."</strong></td>
                            <td align=\"right\"><strong>".getAngka($totalNilaiPPN)."</strong></td>
                            <td align=\"right\"><strong>".getAngka($grandTotal)."</strong></td>
                        </tr>
                        ";

                    } else {

                        $text.="
                        <tr>
                            <td colspan=\"8\"><strong><center>- Data Kosong -</center></strong></td>
                        </tr>
                        ";

                    }
                $text .= "
                </tbody>
            </table>
            ";
        }

        $idTagihan = getField("select id from tagihan_data where id_termin = '$idTermin'");
        $idSPK = getField("select id_spk from tagihan_data where id = '$idTagihan'");
        $spk = getRow("select * from tagihan_spk where id = $idSPK");
        $getData = getRows("select * from tagihan_bayar where id_tagihan = '$idTagihan' order by id asc");

        if ($getData && $statusPembayaran == true) {

            $text.="
            <br>
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>PEMBAYARAN</h3>
                </div>
            </div>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                        <th width=\"70\" rowspan=\"2\" style=\"vertical-align: middle\">tanggal <br> bayar</th>
                        <th width=\"*\" rowspan=\"2\" style=\"vertical-align: middle\">catatan</th>
                        <th width=\"250\" rowspan=\"2\" style=\"vertical-align: middle\">Rekening</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Nilai</th>
                        <th colspan=\"2\">Bukti</th>
                    </tr>
                    <tr>
                        <th width=\"75\">View</th>
                        <th width=\"75\">D / L</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $no = 0;
                    foreach ($getData as $data) {

                        $no++;

                        $view = $data['bukti_bayar'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBayar&par[id]=$data[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                        $download = $data['bukti_bayar'] ? "<a href=\"download.php?d=fileTagihanBayar&f=$data[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($data['bukti_bayar'])."\" height=\"20\"></a>" : "";

                        if ($spk[id_jenis] == '1048') {
                            $rekening = getField("select concat(namaBank, ' - ', rekeningBank, ' (', pemilikBank, ')') from dta_supplier_bank where id = ".$data['id_norek']." ");
                        } else {
                            $rekening = $data[nama_pemohon];
                        }

                        $text.="
                        <tr>
                            <td align=\"center\">".$no."</td>
                            <td align=\"center\">".getTanggal($data['tanggal'])."</td>
                            <td align=\"left\">".$data['catatan']."</td>
                            <td align=\"left\">".$rekening."</td>
                            <td align=\"right\">".getAngka($data['nilai'])."</td>
                            <td align=\"center\">".$view."</td>
                            <td align=\"center\">".$download."</td>
                        </tr>
                        ";
                    }

                    $text.="
                        <tr>
                            <td align=\"right\" colspan=\"4\"><strong>TOTAL</strong></td>
                            <td align=\"right\">".getAngka($termin['bayar'])."</td>
                            <td align=\"center\" colspan=\"2\"></td>
                        </tr>
                        ";
                    $text.="
                </tbody>
            </table>";
        }
        $text.="

        </form>
    </div>";

    return $text;
}

function headerPrint($idTermin, $pc1, $glAccount = false) {

    $html = "
    <table width='100%' style='border: 1px black solid; border-collapse: collapse;' border='1'>
        <tr>
            <th width='20%' align='left'>Prepared By</th>
            <td>". getField("select nama from pegawai_data where id = $pc1[pengajuan_prep_by]") ."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Posting Date</th>
            <td>". getTanggal($pc1['pengajuan_post_date']) ."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Document Date</th>
            <td>".getTanggal($pc1['target'])."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Month</th>
            <td>".getBulan(date('m', strtotime($pc1['tgl_terima'])))."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Company Code</th>
            <td>2090</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Document Type</th>
            <td>". $pc1['document_type'] ."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>No SP3</th>
            <td>".$pc1[no_permohonan]."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Vendor Code</th>
            <td>".$pc1["nomorSupplier"]."</td>
        </tr>
         <tr>
            <th width='8%' align='left'>Assignment</th>
            <td>".$pc1["nomor"]."</td>
        </tr>
        <tr>";
            $fakturPajak = getField("select ba_nomor from tagihan_syarat where id_spk = '".$pc1['idSpk']."' AND judul like '%Faktur%'");
            $reference = ($fakturPajak != '') ? $fakturPajak : " - ";
            $html .= "
            <th width='8%' align='left'>Reference</th>
            <td>". $reference ."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Ref Key 3</th>
            <td>".$pc1[no_invoice]."</td>
        </tr>
        <tr>
            <th width='8%' align='left'>Header Text</th>
            ";
             $pemohon = ($pc1["id_jenis"] == '1048') ? "namaSupplier" : "nama";
             $html .="
            <td>".$pc1["$pemohon"]."</td>
        </tr>
    </table>
    
    
    ";
    if ($glAccount == true) {
        $html .= "
    <br>
    <br>
    
    <table width='100%' style='border: 1px black solid; border-collapse: collapse;' border='1'>
        <tr>
            <th width='5%'>No.</th>
            <th>GL Account</th>
            <th>GL Account Name</th>
            <th>PK</th>
            <th>Curr</th>
            <th>Amount</th>
            <th>Kode Proyek</th>
            <th>Cost Center</th>
            <th>Text</th>
        </tr>
        ";
        $getData = getRows("SELECT *, a.id AS idBiaya, b.kode AS gl_account, e.cost AS costcenter, f.namaData as pk
                            FROM tagihan_termin_biaya AS a
                            LEFT JOIN account_gl AS b ON (b.id = a.gl_account)
                            JOIN tagihan_termin AS c ON (c.id = a.id_termin)
                            LEFT JOIN tagihan_spk AS d ON (d.id = c.id_spk) 
                            LEFT JOIN costcenter_data AS e ON (e.id = d.id_cc)
                            JOIN mst_data AS f ON (f.kodeData = a.tipe)
                            where a.id_termin = '" . $idTermin . "' 
                            order by a.id asc");
        if ($getData) {

            $no = 0;
            foreach ($getData as $data) {

                $no++;

                $html .= "
                <tr>
                    <td align=\"center\">" . $no . "</td>
                    <td align=\"center\">" . $data[gl_account] . "</td>
                    <td align=\"left\">" . $data[judul] . "</td>
                    <td align=\"center\">". $data[pk] ."</td>
                    <td align=\"center\">" . $data[currency] . "</td>
                    <td align=\"right\">" . getAngka($data["nilai_plus_ppn"]) . "</td>
                    <td align=\"center\">".getField("select nomor from proyek_data where id = $data[proyek]")."</td>
                    <td align=\"center\">".$data["costcenter"]."</td>
                    <td align=\"left\">" . $data["biaya"] . "</td>
                </tr>
                ";
            }

        } else {

            $html .= "
            <tr>
                <td colspan=\"9\"><strong><center>- Data Kosong -</center></strong></td>
            </tr>
            ";

        }
        $html .= "
    </table>
    ";
    }

    return $html;
}

function generateNomorSP3($idTermin) {

    $idSPK = getField("select id_spk from tagihan_termin where id = '$idTermin'");
    $idCC = getField("select id_cc from tagihan_spk where id = '$idSPK'");
    $kodeOrganisasi = getField("select kode_organisasi from costcenter_data where id = '$idCC'");
    $tahun = date('Y');

    $getlastNumber = getField("select SUBSTR(no_permohonan, 1, 5) from tagihan_data 
                                where 
                                SUBSTR(no_permohonan, 11, 7) = '$kodeOrganisasi'
                                AND SUBSTR(no_permohonan, 19, 4) = '$tahun'
                                ORDER BY SUBSTR(no_permohonan, 1, 5) DESC LIMIT 1
                                ");
    $str    = (empty($getlastNumber)) ? "00000" : $getlastNumber;
    $incNum = str_pad($str + 1, 5, "0", STR_PAD_LEFT);

    return $incNum ."/SP3/". $kodeOrganisasi ."/". $tahun . "-S4";

}

?>