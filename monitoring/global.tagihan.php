<?php

function view_spk($judul = "SPK", $idSPK = "", $popup = "", $termin = true)
{
    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '$idSPK'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$judul</h1>
		".(!$popup ? getBread(ucwords(str_replace("Detail", "", $par["mode"]))) : "&nbsp")."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		    <p style=\"position: absolute; right: 20px; top: 10px;\">
		        ".(!$popup ? "<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk") . "';\"/>" : "")."
			</p>
			
			".(!$popup ? "<br>" :  "")."
			
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
            
            ";

            if ($termin == true) {

                $text.="
                <br />
            
                <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                    <div class=\"title\">
                        <h3>TERMIN PEMBAYARAN</h3>
                    </div>
                </div>
                
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                    <thead>
                        <tr>
                            <th width=\"20\">No</th>
                            <th width=\"200\">Termin</th>
                            <th width=\"100\">Persen</th>
                            <th width=\"150\">Nilai</th>
                            <th width=\"100\">Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        ";
                        $getData = getRows("select * from tagihan_termin where id_spk = '$idSPK' order by id asc");
                        if ($getData) {

                            $no = 0;
                            foreach ($getData as $data) {

                                $no++;

                                $text.="
                                <tr>
                                    <td align=\"center\">".$no."</td>
                                    <td align=\"left\">".$data["termin"]."</td>
                                    <td align=\"right\">".getAngka($data["persen"])."%</td>
                                    <td align=\"right\">".getAngka($data["nilai"])."</td>
                                    <td align=\"center\">".getTanggal($data["target"])."</td>
                                </tr>
                                ";

                                $persen += $data["persen"];
                                $total += $data["nilai"];
                            }

                            $text.="
                            <tr>
                                <td colspan=\"2\" align=\"right\"><strong>TOTAL</strong></td>
                                <td align=\"right\"><strong>".getAngka($persen)."%</strong></td>
                                <td align=\"right\"><strong>".getAngka($total)."</strong></td>
                                <td></td>
                            </tr>
                            ";

                        } else {

                            $text.="
                            <tr>
                                <td colspan=\"5\"><strong><center>- Data Kosong -</center></strong></td>
                            </tr>
                            ";

                        }
                        $text.="
                    </tbody>
                </table>
                ";

            }

            $text.="
            
            
		</form>
    </div>";

    return $text;
}

function view_tagihan($judul = "Invoice", $idTagihan = "", $popup = "", $termin = true)
{
    $tagihan = getRow("select * from tagihan_data where id = '$idTagihan'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$judul</h1>
		".(!$popup ? getBread(ucwords(str_replace("Detail", "", $par["mode"]))) : "&nbsp")."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		    <p style=\"position: absolute; right: 20px; top: 10px;\">
		        ".(!$popup ? "<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk, id_tagihan") . "';\"/>" : "")."
			</p>
			
			".(!$popup ? "<br>" :  "")."
			
			<fieldset>
                <legend>Tagihan</legend>
                <p>
                    <label class=\"l-input-small\">Tanggal</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        " . getTanggal($tagihan["tgl_terima"]) . " &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Supplier</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select namaSupplier from dta_supplier where kodeSupplier = '".$tagihan["id_supplier"]."'")." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">No. SPK</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select nomor from tagihan_spk where id = '".$tagihan["id_spk"]."'")." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Termin Pembayaran</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select termin from tagihan_termin where id = '".$tagihan["id_termin"]."'")." &nbsp;
                    </span>
                </p>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">No. Invoice</label>
                                <span class=\"field\">  
                                    ".$tagihan["no_invoice"]." &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Pengirim</label>
                                <span class=\"field\">
                                    ".$tagihan["pengirim"]." &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$tagihan["catatan"]." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">File</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        <a href=\"files/tagihan_data/".$tagihan['file_tagihan']."\" download><img src=\"".getIcon($tagihan['file_tagihan'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a> &nbsp;
                    </span>
                </p>
                
            </fieldset>
            
		</form>
    </div>";

    return $text;
}

?>