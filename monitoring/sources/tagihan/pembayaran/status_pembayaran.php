<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_bayar/";

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

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function hapus()
{
    global $par, $dirFile;

    $file = getField("select bukti_bayar from tagihan_bayar where id = '".$par['id_pembayaran']."'");
    unlink($dirFile.$file);

    db("delete from tagihan_bayar where id = '".$par["id_pembayaran"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode, id_pembayaran") . "';</script>";
}

function simpan()
{
    global $inp, $par, $cID;

    $sql = "UPDATE
                  `tagihan_bayar`
                SET
                  `konf_status` = 't',
                  `konf_desc` = '".$inp['konf_desc']."',
                  `konf_date` = '".date("Y-m-d")."',
                  `konf_by` = '".$cID."'
                WHERE `id` = '".$par["id_pembayaran"]."'
                ";
    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
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
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>
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
}

function detailForm()
{
    global $s, $par, $arrTitle;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");
    $spk = getRow("select * from tagihan_spk where id = $tagihan[id_spk]");
    $termin = getRow("select * from tagihan_termin where id = $tagihan[id_termin]");

    $text .= "
            
            ".view_permohonan($arrTitle[$s], $tagihan['id_spk'], '', false, $tagihan['id_termin'], true, false)."  
            
            <br />
            <br />
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
            
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
                    $idSPK = getField("select id_spk from tagihan_data where id = '$par[id_tagihan]'");
                    $spk = getRow("select * from tagihan_spk where id = $idSPK");
                    $getData = getRows("select * from tagihan_bayar where id_tagihan = '".$par['id_tagihan']."' order by id asc");
                    if ($getData) {

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
            
            <br>
            
            <table style=\"width:100%\">
                <tbody>
                    <tr>
                        <td style=\"width: 33%\">
                            <div class=\"bintang-box2 pink\">
                                <div class=\"bintang-box2-header\">
                                    <p class=\"bintang-box2-title\">TOTAL TAGIHAN</p>
                                </div>
                                <div class=\"bintang-box2-content\">
                                    <p class=\"bintang-box3-number\">".getAngka($termin['nilai_total'])."</p>
                                </div>
                            </div>
                        </td>

                        <td style=\"width: 33%\">
                            <div class=\"bintang-box2 murky-green\">
                                <div class=\"bintang-box2-header\">
                                    <p class=\"bintang-box2-title\">BAYAR</p>
                                </div>
                                <div class=\"bintang-box2-content\">
                                    <p class=\"bintang-box2-number\">".getAngka($termin['bayar'])."</p>
                                </div>
                            </div>
                        </td>

                        <td style=\"width: 33%\">
                            <div class=\"bintang-box2 moon-raker\">
                                <div class=\"bintang-box2-header\">
                                    <p class=\"bintang-box2-title\">SISA</p>
                                </div>
                                <div class=\"bintang-box2-content\">
                                    <p class=\"bintang-box2-number\">".getAngka($termin['sisa'])."</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
		
		
    </div>";

    return $text;
}

function lData()
{
    global $par;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE a.pengajuan_approve_status = 't'";
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
            a.pengajuan_no_tiket,
            a.persen,
            a.id_spk,
            a.bayar,
            a.sisa,
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
            c.status_pelunasan,
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
                                            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
                                            $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        if ($r['status_pelunasan'] == "proses") {
            $background = "class=\"labelStatusKuning\"";
        }
        if ($r['status_pelunasan'] == "lunas") {
           $background = "class=\"labelStatusHijau\"";
        }
        if ($r['status_pelunasan'] == "sebagian") {
            $background = "class=\"labelStatusBiru\"";
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
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"center\">".$r["tiket_nomor"]."</div>",
            "<div align=\"right\">".getAngka($r["nilai_total"])."</div>",
            "<div align=\"right\"><a href=\"?par[mode]=detailForm&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" style=\"text-decoration:none;\">".getAngka($r["bayar"])."</a></div>",
            "<div align=\"right\">".getAngka($r["sisa"])."</div>",
            "<div align=\"center\" ".$background.">".ucfirst($r["status_pelunasan"])."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $arrTitle, $par;

	$text = table(10, array(3, 4, 5, 6, 7, 8, 9, 10));

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
                        <th style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">No Pembayaran</th>
                        <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th style=\"vertical-align: middle; min-width:80px;\">Bayar</th>
                        <th style=\"vertical-align: middle; min-width:80px;\">Sisa</th>
                        <th style=\"vertical-align: middle; min-width:80px;\">Status<br>Pembayaran</th>
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
              "No. Tiket",
              "No. Pembayaran",
              "Nilai",
              "Bayar",
              "Sisa",
              "Status Pembayaran"
              ];

    $where = "WHERE a.pengajuan_approve_status = 't'";

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
            a.pengajuan_no_tiket,
            a.persen,
            a.id_spk,
            a.bayar,
            a.sisa,
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
            c.status_pelunasan,
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

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"] . "\t center",
			$r["pengajuan_no_tiket"] . "\t center",
            $r["tiket_nomor"] . "\t center",
			getAngka($r["nilai_total"]) . "\t right",
			getAngka($r["bayar"]) . "\t right",
			getAngka($r["sisa"]) . "\t right",
			ucfirst($r["status_pelunasan"]) . "\t center",
		];
    }

    exportXLS($direktori, $namaFile, $judul, 10, $field, $data);
}