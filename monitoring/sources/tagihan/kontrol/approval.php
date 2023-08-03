<?php

use mikehaertl\wkhtmlto\Pdf;

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_data/";
$dirFileBa = "files/tagihan_ba/";

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

        case "detailForm":
            $text = detailForm();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par['id_termin'], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "print_dokumen":
            $text = print_dokumen();
            break;

        case "approval":

            if ($par['lvl'] == 1) {
                if (isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form_approval() : simpan_approval(); else $text = lihat();
            }

            if ($par['lvl'] == 2) {
                if (isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? form_approval() : simpan_approval(); else $text = lihat();
            }

            break;
    }

    return $text;
}

function print_dokumen()
{
	global $par;

	// pc = print collumn

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
                    a.nilai_pph,
                    a.nilai_total,
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
                    c.id as id_tagihan
                    
                    FROM tagihan_termin AS a
                    JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                    JOIN tagihan_data AS c ON (c.id_termin = a.id)
                    where a.id = '".$par['id_termin']."'");


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
            	<div style='float: left; width: 500px; font-size: 10px'>
            		<h1>TAX CONTROL</h1>
				</div>
            	<div style='float: right; margin-top: -55px'>
            		<img src='http://pertaminapdc.com/monitoring/images/logopertaminapdc.png' style='width: 200px;'>
				</div>
			</div>
			
            
            <div style='font-size: 12px; padding: 20px; margin-top: -20px;'>
				
				".headerPrint($par[id_termin], $pc1, false)."
                
                <br>
                
                <table width='100%' style='border: 1px #000000 solid; border-collapse: collapse;' border='1'>
				    <tr>
				        <td width='33%' align='left'>Total Permohonan</td>
				        <td>Rp. ". getAngka($pc1['nilai_plus_ppn']) ."</td>
                    </tr>
                    <tr>
                        <td width='33%' align='left'>Pajak PPH</td>
				        <td>Rp. ". getAngka($pc1['nilai_pph']) ."</td>
                    </tr>
                    <tr>
                        <td width='33%' align='left'>Nilai</td>
				        <td>Rp. ". getAngka($pc1['nilai_total']) ."</td>
                    </tr>
                </table>
                
                <br>
                
                <table width='100%' style='border: 1px #000000 solid; border-collapse: collapse;' border='1'>
				    <tr>
				        <th width='10%'>NO</th>
				        <th width='20%'>DPP</th>
				        <th width='20%'>TARIF PAJAK</th>
				        <th width='20%'>PPH PASAL</th>
				        <th width='20%'>NILAI</th>
                    </tr>
                    ";
                    $getPajak = getRows("SELECT * FROM mst_data AS a 
                                        JOIN tagihan_pajak AS b ON (b.`id_pajak` = a.`kodeData`)
                                        WHERE a.kodeCategory = 'MDPJ' 
                                        AND b.id_tagihan = '$par[id_tagihan]'
                                        ORDER BY a.urutanData ASC
                                        ");
                    $no = 0;
                    foreach ($getPajak as $pjk) {

                        $no++;
                        $html.="
                        <tr>
                            <td align='center'>$no</td>
                            <td align='right'>".getAngka($pjk[dpp])."</td>
                            <td align='center'>$pjk[tarif]%</td>
                            <td align='center'>".str_replace("Pph Pasal ", "", $pjk[namaData])."</td>
                            <td align='right'>".getAngka($pjk[nilai])."</td>
                        </tr>
                        ";

                    }
                    $html.="
                    <tr>
                        <th colspan='4' align='center'>NET PAYMENT</th>
				        <td align='right'>".getAngka($pc1[nilai_pph])."</td>
                    </tr>
                    <tr>
                        <th colspan='2' align='center'>NO WHT</th>
				        <th width='33%' align='center'>NON PKP</th>
				        <th colspan='2' align='center'>PKP</th>
                    </tr>
                    <tr>
                        <th colspan='2'>&nbsp;</th>
				        <th width='33%'>&nbsp;</th>
				        <th colspan='2'>&nbsp;</th>
                    </tr>
                    <tr>
				        <th colspan='5' align='center'>TAX Verified By</th>
                    </tr>
                    <tr>
				        <td colspan='5'>
				        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                        </td>
				        
                    </tr>
                </table>
                
                
                
                <br>
                
                
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
    global $inp, $par, $cID;

    $setData = "
                `id_termin` = '".$par['id_termin']."',
                `approve_lvl` = '".$par['lvl']."',
                `approve_date` = '".setTanggal($inp["approve_date"])."',
                `approve_by` = '".$cID."',
                `approve_desc` = '".$inp["approve_desc"]."',
                `approve_status` = '".$inp["approve_status"]."',";

    $id = getField("select id from tagihan_approval_tax where id_termin = '".$par['id_termin']."' and approve_lvl = '".$par['lvl']."' ");

    if (empty($id)) {

        $sql = "INSERT INTO
                  `tagihan_approval_tax`
                SET
                  $setData
                  `created_at` = now(),
                  `updated_at` = now(),
                  `created_by` = '".$cID."'
                ";

    } else {

        $sql = "UPDATE
                  `tagihan_approval_tax`
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

        $cek = getField("select id from tagihan_approval_tax where id_termin = '".$par['id_termin']."' and approve_lvl = '".$i."'");
        if (empty($cek)) $statusApproval = 'p';

    }

    db("update tagihan_termin set tax_approval_status = '$statusApproval', tax_approval_date = '".date("Y-m-d")."' where id = '".$par['id_termin']."'");

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\");</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode") . "#flagForm';</script>";
}

function form_approval()
{
    global $par, $cID;

    $r = getRow("select * from tagihan_approval_tax where id_termin = '".$par['id_termin']."' and approve_lvl = '".$par['lvl']."'");

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

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess, $getPajak;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text.="
          
            ".view_permohonan($arrTitle[$s], $tagihan['id_spk'], '', false, $tagihan['id_termin'], true)."  
            
            <br />
            <br />
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
                
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

                                $kontrol = "";
                                if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id_pajak]=".$pjk[kodeData]."" . getPar($par, "mode, id_pajak") . "', 650, 470);\" class=\"edit\"><span>Edit</span></a>";

                                $text.="
                                <tr>
                                    <td align=\"center\">".$no."</td>
                                    <td align=\"left\">".$pjk['namaData']."</td>
                                    <td align=\"right\">". getAngka($data['dpp'])."</td>
                                    <td align=\"right\">".$data['tarif']."</td>
                                    <td align=\"right\">".(!empty($data['nilai']) ? getAngka($data['nilai']) : "")."</td>
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
                                <th colspan=\"3\">APPROVAL</th>
                            </tr>
                            <tr>
                                <th width=\"33%\">1</th>
                                <th width=\"33%\">2</th>
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
                                        $dtAppr = getRow("select * from tagihan_approval_tax where id_termin = '".$par['id_termin']."' and approve_lvl = '$i'");

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
                    
	<div id=\"flagForm\"></div>
        
                </form>
            </div>";

    return $text;
}

function lData()
{
    global $s, $par, $menuAccess, $arrParam, $getPajak;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE 1 = 1";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(c.no_permohonan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
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
            a.nilai_ppn, 
            a.nilai_pph, 
            a.nilai_plus_ppn,
            a.nilai_total,
            a.persen,
            a.id_spk,
            a.created_by,
            a.pengajuan_no_tiket,
            b.nomor, 
            b.id_supplier,
            b.tanggal,
            b.judul,
            b.id_jenis,
            c.id as id_tagihan,
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

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_termin]=$r[id]&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" class=\"edit\"><span>Detail</span></a>";


        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $approval = getField("select count(*) from tagihan_approval_tax where id_termin = '$r[id]' and approve_status = 't'");

        if ($approval == 2){ #ini kalau full
            $background = "class=\"labelStatusHijau\"";
        } elseif ($approval > 0) {
            $background = "class=\"labelStatusBiru\""; #ini kalau kriterianya ada, tapi ga full
        } else { #kalo 0
            $background = "class=\"labelStatusKuning\"";
        }

        if ($approval == 2){
            $print = "<a href=\"#\" onclick=\"openBox('void.php?par[mode]=print_dokumen&par[id_termin]=$r[id]&par[id_tagihan]=$r[id_tagihan]" . getPar($par, "mode, id_tagihan, id_termin") . "',800,425);\" title=\"PRINT SP3\" class=\"print\"><span>Print</span></a>";
        } else {
            $print = "";
        }

        $appr = "";
        if (isset($menuAccess[$s]["edit"])) $appr .= "<a href=\"?par[mode]=detailForm&par[id_termin]=$r[id]&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" ><span> 2 / ".$approval."</span></a>";

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".$kontrol."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&&par[id_termin]=" . $r["id"]."&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"left\">".$r["termin"]."</div>",
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"right\">".getAngka($r["nilai"])."</div>",
            "<div align=\"right\">".getAngka($r["nilai_ppn"])."</div>",
            "<div align=\"right\">".getAngka($r["nilai_pph"])."</div>",
            "<div align=\"right\">".getAngka($r["nilai_total"])."</div>",
            "<div align=\"center\" ".$background.">$appr</div>",
            "<div align=\"center\">".$print."</div>"
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $par, $arrTitle, $arrParam, $getPajak;

	$text = table(13, array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13));

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
                        <th style=\"vertical-align: middle; min-width: 80px;\">Kontrol</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Tahap</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">PPN</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">PPH</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Total</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Approval</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Print</th>
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
              "Tahap",
              "No. Tiket",
              "Nilai",
              "PPN",
              "PPH",
              "Total",
              "Approval"];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(c.no_permohonan) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(b.judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
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
            a.nilai_ppn, 
            a.nilai_pph, 
            a.nilai_plus_ppn,
            a.nilai_total,
            a.persen,
            a.id_spk,
            a.created_by,
            a.pengajuan_no_tiket,
            b.nomor, 
            b.id_sbu,
            b.id_supplier,
            b.tanggal,
            b.judul,
            b.id_jenis,
            c.id as id_tagihan,
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

        $approval = getField("select count(*) from tagihan_approval_tax where id_termin = '$r[id]' and approve_status = 't'");

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
            $r["termin"] . "\t center",
			$r["pengajuan_no_tiket"] . "\t center",
			getAngka($r["nilai"]) . "\t right",
			getAngka($r["nilai_ppn"]) . "\t right",
			getAngka($r["nilai_pph"]) . "\t right",
			getAngka($r["nilai_total"]) . "\t right",
			"2 / ".getAngka($approval). "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 11, $field, $data);
}