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
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par["id_termin"], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "print_dokumen":
            $text = print_dokumen();
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

function print_dokumen()
{
	global $par;

	// pc = print collumn

	$pc1 = getRow("SELECT
                        a.id,
                        a.nilai_plus_ppn,
                        a.nilai_total,
                        
                        b.id AS idSpk,
                        b.nomor,
                        b.id_proyek,
                        b.id_jenis,
                        
                        c.id AS id_tagihan,
                        c.tgl_terima,
                        c.no_invoice,
                        c.no_akrual,
                        c.no_permohonan,
                        
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
                        b.id as idSpk,
                        c.id AS id_tagihan,
                        b.judul,
                        b.catatan,
                        e.namaSupplier AS namaVendor,
                        d.namaBank,
                        d.pemilikBank AS atasNama,
                        d.rekeningBank AS nomorRekening,
                        f.cost
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
                        b.id as idSpk,
                        c.id AS id_tagihan,
                        b.judul,
                        b.catatan,
                        d.nama AS namaVendor,
                        e.namaData AS namaBank,
                        d.nama_pemilik AS atasNama,
                        d.norek AS nomorRekening,
                        f.cost
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
                    Rev : 01/19 <br>
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
				        <td>". $pc1['no_permohonan'] ."</td>
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
				    <tr>";
	                    $kwitansi = getField("select ba_nomor from tagihan_syarat where id_spk = '".$pc1['idSpk']."' AND judul like '%Kwitansi%'");
	                    $fakturPajak = getField("select ba_nomor from tagihan_syarat where id_spk = '".$pc1['idSpk']."' AND judul like '%Faktur%'");
	                    $html .= "
				        <td width='20%'>1. Invoice No.</td>
				        <td>:</td>
				        <td>". $pc1['no_invoice'] ."</td>
                    </tr>
                    <tr>
				        <td width='20%'>2. Kwitansi No.</td>
				        <td>:</td>
				        <td>". $kwitansi ."</td>
                    </tr>
                    <tr>
				        <td width='20%'>3. Faktur Pajak No.</td>
				        <td>:</td>
				        <td>". $fakturPajak ."</td>
                    </tr>
                    <tr>
				        <td width='20%'>4. SPK No.</td>
				        <td>:</td>
				        <td>". $pc1['nomor']."</td>
                    </tr>
                    <tr>
				        <td width='20%'>5. Akrual No.</td>
				        <td>:</td>
				        <td>". $pc1['no_akrual'] ."</td>
                    </tr>
                </table>
				
				<br>
				
				<strong>Untuk Pembayaran : </strong>
				
				<br>
					
                ". $pc2['judul'] ."
                
                <br>
                <br>
					
                ". $pc2['catatan'] ."
                
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
				        <td>".$pc2["cost"]."</td>
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


function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess, $getPajak;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text.="
          
            ".view_permohonan($arrTitle[$s], $tagihan['id_spk'], '', false, $tagihan['id_termin'], true)."  
            ";

    return $text;
}

function lData()
{
    global $s, $par, $menuAccess, $arrParam, $getPajak;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    $where = " WHERE c.approval_status = 't'";

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
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
    if (!empty($_GET['combo5'])) {
        $where .= " and b.id_sbu = '$_GET[combo5]'";
        if (!empty($_GET['combo6'])) $where .= " and b.id_proyek = '$_GET[combo6]'";
    }

    $arrOrder = array("", "a.target", "namaSupplier");

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
            a.persen,
            a.id_spk,
            a.created_by,
            b.judul,
            b.id_jenis,
            b.id_supplier,
            d.namaSupplier,
            b.nomor, 
            b.id_proyek,
            c.id as id_tagihan,
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
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
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id) $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" class=\"detail\"><span>Detail</span></a>";

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id_spk"]."&par[id_termin]=".$r["id"] . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai"])."</div>",
            "<div align=\"right\">".getAngka($r["nilai_ppn"])."</div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
            "<div align=\"center\"><a href=\"#\" onclick=\"openBox('void.php?par[mode]=print_dokumen&par[id_tagihan]=$r[id_tagihan]" . getPar($par, "mode, id_tagihan") . "',900,500);\" title=\"PRINT SP3\" class=\"print\"><span>Print</span></a></div>",
            "<div align=\"center\">".$kontrol."</div>"

        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFileBa;

    db("delete from tagihan_pajak where id_tagihan = $par[id_tagihan] and id_pajak = $par[id_pajak]");

    $id = getField("select id from tagihan_pajak where id_tagihan = $par[id_tagihan] and id_pajak = $par[id_pajak]");

    if(empty($id)) {
        $sql = "insert into tagihan_pajak set 
                id_tagihan = $par[id_tagihan], 
                id_pajak = $par[id_pajak],
                tarif = '$inp[tarif]',
                nilai = '$inp[nilai]',
                keterangan = '$inp[keterangan]',
                created_at = now(),
                created_by = '$cID'
                ";
    } else {
        $sql = "update tagihan_pajak set 
                tarif = '$inp[tarif]',
                nilai = '$inp[nilai]',
                keterangan = '$inp[keterangan]',
                updated_at = now(),
                updated_by = '$cID'
                where id = $id;
                ";
    }
    db($sql);

    echo "<script>closeBox()</script>";
    echo "<script>alert('Data berhasil disimpan')</script>";
    echo "<script>reloadPage()</script>";
}
function form()
{
    global $par, $dirFileBa, $arrTitle, $s;

    $dt = getRow("SELECT b.termin, b.nilai FROM tagihan_data a
                JOIN tagihan_termin b ON (b.id = a.id_termin)
                WHERE a.id = '$par[id_tagihan]'");

    $r = getRow("select * from tagihan_pajak where id_tagihan = '$par[id_tagihan]' and id_pajak = '$par[id_pajak]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$arrTitle[$s]</h1>
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
            <legend>Dokumen</legend>
			    <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\">
                        ".$dt["termin"]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\" >Nilai</label>
                    <span class=\"field\">
                       Rp. ".getAngka($dt["nilai"])." &nbsp; 
                    </span>
                </p>
			</fieldset>
			
			<br>
			
			<fieldset>
			    <legend>Pajak</legend>
                <p>
                    <label class=\"l-input-small\">PPH Pasal</label>
                    <span class=\"field\">
                        ".getField("select namaData from mst_data where kodeData = '$par[id_pajak]'")." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Tarif</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[tarif]\" name=\"inp[tarif]\" style=\"width: 75px\" value=\"" . $r["tarif"] . "\" />
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[nilai]\" name=\"inp[nilai]\" style=\"width: 75px\" value=\"" . $r["nilai"] . "\" />
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Keterangan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[keterangan]\" name=\"inp[keterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["keterangan"]."</textarea>
                    </div>
                </p>
			</fieldset>
		</form>
	</div>";

    return $text;
}

function lihat()
{
	global $s, $par, $arrTitle, $arrParam, $getPajak;

	$text = table(9, array(3, 4, 5, 6, 7, 8, 9));

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
                        <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">Nilai</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">ppn</th>
                        <th style=\"vertical-align: middle; min-width: 70px;\">total</th>
                        <th style=\"vertical-align: middle; min-width: 50px;\">print</th>
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
              "Judul - Nomor",
              "Pemohon",
              "Nilai",
              "PPN",
              "Total"];

    $where = " WHERE c.approval_status = 't'";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(b.judul) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
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
    if (!empty($par['combo5'])) {
        $where .= " and b.id_sbu = '$par[combo5]'";
        if (!empty($par['combo6'])) $where .= " and b.id_proyek = '$par[combo6]'";
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
            a.persen,
            a.id_spk,
            a.created_by,
            b.judul,
            b.id_jenis,
            b.id_supplier,
            d.namaSupplier,
            b.nomor, 
            b.id_sbu,
            b.id_proyek,
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

//        $syarat = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."'");
//        $ba = getField("select count(*) from tagihan_syarat where id_termin = '".$r['id']."' and ba_verifikasi = 't'");

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			getAngka($r['nilai']) . "\t right",
			getAngka($r['nilai_ppn']) . " \t right",
			getAngka($r["nilai_plus_ppn"]) . "\t right"
//			getAngka($syarat)." / ".getAngka($ba). "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}