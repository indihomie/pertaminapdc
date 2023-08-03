<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $arrTitle;

    switch ($par[mode]) {

        default:
            $text = lihat();
            break;

        case "lst":
            $text = lData();
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

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], true, $par['id_termin'], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
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

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])) . ".xls";
    $judul = $arrTitle[$s];

    $field = ["NO",
        "Tanggal",
        "Judul - Nomor",
        "Pemohon",
        "Nilai",
        "No. Permohonan",
        "Verifikasi",
        "Approval",
        "Kontrol",
        "Tax",
        "Jurnal",
        "Bayar"];

    $where = " WHERE c.approval_status = 't'";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(judul) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(namaSupplier) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(nama) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '" . $par['combo1'] . "'";
    if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '" . $par['combo2'] . "'";

    if (!empty($par['combo3'])) $where .= " and id_jenis = '" . $par['combo3'] . "'";
    if (!empty($par['combo4'])) $where .= " and id_supplier = '" . $par['combo4'] . "'";
    if (!empty($par['combo5'])) {
        $where .= " and b.id_sbu = '$par[combo5]'";

        if (!empty($par['combo6'])) {
            $where .= " and b.id_proyek = '$par[combo6]'";
        }
    }

    $order = "id DESC";

    $sql = "SELECT a.*, b.namaSupplier, d.nama, c.id as id_tagihan
            from tagihan_spk as a
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS d ON (d.id = a.id_supplier)
            join tagihan_data as c on (c.id_spk = a.id)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $appr = "Menunggu Persetujuan";
        if ($r["approve_status"] == "t") $appr = "Setuju";
        if ($r["approve_status"] == "f") $appr = "Tolak";
        if ($r["approve_status"] == "p") $appr = "Pending";

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[] = [
            $no . "\t center",
            getTanggal($r["tanggal"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
            $r["$pemohon"] . "\t left",
            getAngka($r["nilai_plus_ppn"]) . "\t right",

        ];

        $getTermin = getRows("select * from tagihan_termin where id_spk = '" . $r["id"] . "' order by id asc");
        foreach ($getTermin as $trm) {

            $tiket = getField("select tgl_terima from tagihan_data where id_termin = '" . $trm['id'] . "'");
            $verifikasi = getField("select updated_at from tagihan_syarat where id_termin = '" . $trm['id'] . "' order by id desc");
            $tagihan = getField("select approve_date from tagihan_approval where id_termin = '" . $trm['id'] . "' order by id desc");
            $kontrol = getField("select updated_at from tagihan_data where id_termin = '" . $trm['id'] . "' order by id desc");
            $tax = getField("select tax_approval_date from tagihan_termin where id = '" . $trm['id'] . "' order by id desc");
            $jurnal = getField("select pengajuan_approve_date from tagihan_termin where id = '" . $trm['id'] . "'");
            $bayar = getField("select created_at from tagihan_bayar where id_tagihan = '" . $r['id_tagihan'] . "' order by id desc");

            $tiket = empty($tiket) ? '' : getTanggal($tiket);
            $verifikasi = empty($verifikasi) ? '' : date(("d/m/Y"), strtotime($verifikasi));
            $tagihan = empty($tagihan) ? '' : getTanggal($tagihan);
            $kontrol = empty($kontrol) ? '' : date(("d/m/Y"), strtotime($kontrol));
            $tax = empty($tax) ? '' : date(("d/m/Y"), strtotime($tax));
            $jurnal = empty($jurnal) ? '' : date(("d/m/Y"), strtotime($jurnal));
            $bayar = empty($bayar) ? '' : date(("d/m/Y"), strtotime($bayar));

            $data[] = [
                "\t right",
                getTanggal($trm["target"]) . "\t center",
                $trm["termin"] . "\t left",
                getAngka($trm["persen"], 2) . "%\t left",
                getAngka($trm["nilai_plus_ppn"]) . "\t right",
                $tiket . "\t center",
                $verifikasi . "\t center",
                $tagihan . "\t center",
                $kontrol . "\t center",
                $tax . "\t center",
                $jurnal . "\t center",
                $bayar . "\t center"
            ];

        }
    }

    exportXLS($direktori, $namaFile, $judul, 12, $field, $data);
}

function detailForm()
{
    global $s, $par, $arrTitle;

    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '$par[id_spk]'");

    $text .= "
    
    " . view_permohonan($arrTitle[$s], $r['id'], '', false, $par['id_termin'], true ) . "
            
		
		
    </div>";

    return $text;
}

function lData()
{
    global $par;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    $where = " WHERE c.approval_status = 't'";

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(namaSupplier) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(nama) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(a.tanggal) = '" . $_GET['combo1'] . "'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.tanggal) = '" . $_GET['combo2'] . "'";

    if (!empty($_GET['combo3'])) $where .= " and id_jenis = '" . $_GET['combo3'] . "'";
    if (!empty($_GET['combo4'])) $where .= " and id_supplier = '" . $_GET['combo4'] . "'";
    if (!empty($_GET['combo5'])) {
        $where .= " and b.id_sbu = '$_GET[combo5]'";

        if (!empty($_GET['combo6'])) {
            $where .= " and b.id_proyek = '$_GET[combo6]'";
        }
    }

    $arrOrder = array("", "tanggal", "judul");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";

    $sql = "SELECT a.*, b.namaSupplier, d.nama, c.id as id_tagihan
            from tagihan_spk as a
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS d ON (d.id = a.id_supplier)
            join tagihan_data as c on (c.id_spk = a.id)
            $where GROUP BY a.id order by $order $limit";
    $res = db($sql);
    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => mysql_num_rows(db("SELECT count(*) 
                                            from tagihan_spk as a
                                            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
                                            LEFT JOIN pegawai_data AS d ON (d.id = a.id_supplier)
                                            join tagihan_data as c on (c.id_spk = a.id)
                                            $where GROUP BY a.id")),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    $data = array();
    while ($r = mysql_fetch_array($res)) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        $data[] = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"center\">" . getTanggal($r["tanggal"]) . "</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=" . $r["id"] . "&par[id_termin]=" . $r["idTermin"] . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">" . $r["nomor"] . "</a>
            </div>",
            "<div align=\"left\">" . $pemohon . "</div>",
            "<div align=\"right\">" . getAngka($r["nilai_plus_ppn"]) . "</div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
            "<div align=\"center\"></div>",
        );

        $getTermin = getRows("select * from tagihan_termin where id_spk = '" . $r["id"] . "' order by id asc");
        foreach ($getTermin as $trm) {

            $tiket = getField("select tgl_terima from tagihan_data where id_termin = '" . $trm['id'] . "'");
            $verifikasi = getField("select updated_at from tagihan_syarat where id_termin = '" . $trm['id'] . "' order by id desc");
            $tagihan = getField("select approve_date from tagihan_approval where id_termin = '" . $trm['id'] . "' order by id desc");
            $kontrol = getField("select pengajuan_post_date from tagihan_termin where id = '" . $trm['id'] . "'");

//            $tax = getField("select tax_approval_date from tagihan_termin where id = '" . $trm['id'] . "' order by id desc");
//            $jurnal = getField("select pengajuan_approve_date from tagihan_termin where id = '" . $trm['id'] . "'");

            $tax = getField("select approve_date from tagihan_approval_tax where id_termin = '" . $trm['id'] . "' and approve_lvl = '2' and approve_status = 't' order by id desc");
            $jurnal = getField("select approve_date from tagihan_approval_jurnal where id_termin = '" . $trm['id'] . "' and approve_lvl = '2' and approve_status = 't' order by id desc");

            $idTagihan = getField("select id from tagihan_data where id_termin = '$trm[id]'");
            $bayar = getField("select tanggal from tagihan_bayar where id_tagihan = '" . $idTagihan . "' order by id desc");

            $tiket = empty($tiket) ? '' : getTanggal($tiket);
            $verifikasi = empty($verifikasi) ? '' : date(("d/m/Y"), strtotime($verifikasi));
            $tagihan = empty($tagihan) ? '' : getTanggal($tagihan);
            $kontrol = empty($kontrol) ? '' : date(("d/m/Y"), strtotime($kontrol));
            $tax = empty($tax) ? '' : date(("d/m/Y"), strtotime($tax));
            $jurnal = empty($jurnal) ? '' : date(("d/m/Y"), strtotime($jurnal));
            $bayar = empty($bayar) ? '' : date(("d/m/Y"), strtotime($bayar));

            $data[] = array(
                "<div align=\"center\"></div>",
                "<div align=\"center\">" . getTanggal($trm["target"]) . "</div>",
                "<div align=\"left\">" . $trm["termin"] . "</div>",
                "<div align=\"left\">" . getAngka($trm["persen"], 2) . "%</div>",
                "<div align=\"right\">" . getAngka($trm["nilai_plus_ppn"]) . "</div>",
                "<div align=\"center\">" . $trm["pengajuan_no_tiket"] . "</div>",
                "<div align=\"center\">" . $tiket . "</div>",
                "<div align=\"center\">" . $verifikasi . "</div>",
                "<div align=\"center\">" . $tagihan . "</div>",
                "<div align=\"center\">" . $kontrol . "</div>",
                "<div align=\"center\">" . $jurnal . "</div>",
                "<div align=\"center\">" . $tax . "</div>",
                "<div align=\"center\">" . $bayar . "</div>",
                "<div align=\"center\"><a href=\"?par[mode]=detailForm&par[id_termin]=" . $trm["id"] . "&par[id_spk]=" . $r["id"] . getPar($par, "mode, id_spk, id_termin") . "\" class=\"detail\"><span>Detail</span></a></div>",
            );

        }


    }

    $json['aaData'] = $data;

    return json_encode($json);
}

function lihat()
{
    global $s, $arrTitle, $par;

    $text = table(14, array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14));
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
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"" . $fSearch . "\" style=\"width:250px;\"/>
				&nbsp;
				
				" . comboMonth("combo1", $combo1, "", "120px", "ALL") . " &nbsp;
                " . comboYear("combo2", $combo2, "", "", "80px", "ALL", $yearStart, $yearEnd) . "
               
                
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
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th colspan=\"7\" style=\"vertical-align: middle; min-width: 100px;\">Data Permohonan</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 50px;\">Detil</th>
                    </tr>
                     <tr>               
                        <th>Permohonan</th>
                        <th>Verifikasi</th>
                        <th>SP3</th>
                        <th>Loket</th>
                        <th>Jurnal</th>
                        <th>Tax</th>
                        <th>Payment</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
	</div>
	";

    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=" . ucwords(strtolower($arrTitle[$s])) . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    $text .= "
    <script>
    	jQuery(\"#btnExport\").live('click', function(e){
    		e.preventDefault();
    		window.location.href=\"?par[mode]=xls" . getPar($par, "mode, fSearch, combo1, combo2, combo3, combo4, combo5, combo6") . "&par[fSearch]=\"+jQuery(\"#fSearch\").val() + \"&par[combo1]=\"+jQuery(\"#combo1\").val() + \"&par[combo2]=\"+jQuery(\"#combo2\").val() + \"&par[combo3]=\"+jQuery(\"#combo3\").val() + \"&par[combo4]=\"+jQuery(\"#combo4\").val() + \"&par[combo5]=\"+jQuery(\"#combo5\").val() + \"&par[combo6]=\"+jQuery(\"#combo6\").val() ;
    	});
    </script>
    ";

    return $text;
}