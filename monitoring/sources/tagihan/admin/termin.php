<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

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

        case "termin":
            $text = termin();
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

        case "detail":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false);
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
    global $par, $arrTitle, $s, $arrParam;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal",
              "Judul - Nomor",
              "Pemohon",
              "Kategori",
              "Nilai",
              "Termin"];

    $where = " WHERE approve_status = 't'";

    if (!empty($par['fSearch'])){
        $where .= " and (     
        lower(nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(tanggal) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(tanggal) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and kodeData = '$par[combo3]'";
    if (!empty($par['combo4'])) $where .= " and id_supplier = '$par[combo4]'";
    if (!empty($par['combo5'])){
        $where .= " and id_sbu = '$par[combo5]'";

        if (!empty($par['combo6'])){
            $where .= " and id_proyek = '$par[combo6]'";
        }
    }

    $order = "id DESC";

    $sql = "SELECT a.*, c.namaData, b.namaSupplier, d.nama
            from tagihan_spk as a
            join mst_data as c ON (c.kodeData = a.id_jenis)
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS d ON (d.id = a.id_supplier)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $termin = getField("select count(*) from tagihan_termin where id_spk = '".$r["id"]."'");

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["tanggal"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"] . "\t left",
            $r['namaData'] . "\t left",
			getAngka($r["total"]) . "\t right",
			$termin. "\t center"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}

function hapus()
{
    global $par;

    db("delete from tagihan_termin where id = '".$par["id"]."'");

    $totalPersen = getField("select sum(persen) from tagihan_termin where id_spk = '".$par["id_spk"]."'");
    db("update tagihan_spk set persen_termin = '".$totalPersen."' where id = '".$par["id_spk"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=termin" . getPar($par, "mode, id") . "';</script>";
}

function simpan()
{
    global $inp, $par, $cID;

    $ppn_spk = getField("select ppn from tagihan_spk where id = $par[id_spk]");
    $nilai = setAngka($inp["nilai"]);
    $nilai_ppn = $nilai * ($ppn_spk / 100);
    $nilai_ppn = round($nilai_ppn);
    $total = $nilai + $nilai_ppn;
    $total = round($total);

    $setData = "`termin` = '".$inp["termin"]."',
                `catatan` = '".$inp["catatan"]."',
                `persen` = '".$inp["persen"]."',
                `nilai` = '$nilai',
                `nilai_ppn` = '$nilai_ppn',
                `nilai_plus_ppn` = '$total',
                `nilai_total` = '$total',
                `target` = '".setTanggal($inp["target"])."',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_termin`
                SET
                  `id_spk` = '".$par["id_spk"]."',
                   $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";
    } else {

        $sql = "UPDATE
                  `tagihan_termin`
                SET
                   $setData
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$par["id"]."'
                ";
    }
    db($sql);

    $totalPersen = getField("select sum(persen) from tagihan_termin where id_spk = '".$par["id_spk"]."'");
    db("update tagihan_spk set persen_termin = '".round($totalPersen)."' where id = '".$par["id_spk"]."'");

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form()
{
    global $par;

    $r = getRow("SELECT * FROM tagihan_termin WHERE id = '$par[id]'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">TERMIN PEMBAYARAN</h1>
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
                    <label class=\"l-input-small\">Termin</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[termin]\" name=\"inp[termin]\"  value=\"".$r["termin"]."\" class=\"mediuminput\" style=\"width:380px;\" maxlength=\"50\"/>
                    </div>
                </p>
			
			    <table width=\"100%\">
			        <tr>
			            <td width=\"50%\">
			                <p>
                                <label class=\"l-input-small2\">Prosentase</label>
                                <div class=\"field\">
                                    <input type=\"text\" onkeyup='cekAngka(this);getNilai(this.value);' id=\"inp[persen]\" name=\"inp[persen]\"  value=\"".getAngka($r["persen"],2)."\" class=\"mediuminput\" style=\"width:30px;\" maxlength=\"50\"/> %
                                    <input type='hidden' value='$par[nilai]' id='total'>
                                </div>
                            </p>
                        </td>
			            <td width=\"50%\">
			                <p>
                                <label class=\"l-input-small2\">Tersedia</label>
                                <span class=\"field\">
                                ";
                                $sisa = 100 - $par['persen_termin'];
                                $text.="
                                    <font id=\"sisa\">".round($sisa)."</font>%
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <div class=\"field\">
                        <input type=\"text\" onkeyup='cekAngka(this)' id=\"inp[nilai]\" name=\"inp[nilai]\"  value=\"".getAngka($r["nilai"])."\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/> 
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Tanggal Perkiraan</label>
                    <div class=\"field\">  
                        ";
                        $r["target"] = empty($r["target"]) ? date("Y-m-d") : $r["target"];
                        $text .= "
                        <input type=\"text\" id=\"inp[target]\" name=\"inp[target]\"  value=\"" . getTanggal($r["target"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:380px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
			
			</fieldset>
		</form>
	</div>";

    return $text;
}

function termin()
{
    global $s, $par, $arrTitle, $menuAccess;

    $text .= "
	
    ".view_permohonan($arrTitle[$s], $par['id_spk'], false, false, '')."
            
            
            <br />
            <br />

    <div id=\"contentwrapper\" class=\"contentwrapper\">
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>TAHAPAN PEMBAYARAN</h3>
                    ";
                    $r = getRow("SELECT * from tagihan_spk WHERE id = '$par[id_spk]'");
                    if (isset($menuAccess[$s]["add"])) $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:-20px;\" onclick=\"openBox('popup.php?par[mode]=add&par[nilai]=$r[nilai]&par[persen_termin]=$r[persen_termin]" . getPar($par, "mode, id, status_syarat, persen_termin, nilai") . "', 650, 385); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
                    $text.="
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
                        <th width=\"75\" style=\"vertical-align: middle\">Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select * from tagihan_termin where id_spk = '".$par["id_spk"]."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $kontrol = "";
                            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[nilai]=$r[nilai]&par[persen_termin]=$r[persen_termin]&par[status_syarat]=$r[status_syarat]&par[id]=".$data["id"]."" . getPar($par, "mode, id, status_syarat, persen_termin, nilai") . "', 650, 385);\" class=\"edit\"><span>Edit</span></a>";
                            if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=".$data["id"].getPar($par, "mode, id")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

                            $total = $data["nilai"] + $data["nilai_ppn"];

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"center\">".getTanggal($data["target"])."</td>
                                <td align=\"left\">".$data["termin"]."</td>
                                <td align=\"left\">".$data["catatan"]."</td>
                                <td align=\"right\">".getAngka($data["persen"],2)."%</td>
                                <td align=\"right\">".getAngka($data["nilai"])."</td>
                                <td align=\"right\">".getAngka($data["nilai_ppn"])."</td>
                                <td align=\"right\">".getAngka($total)."</td>
                                <td align=\"center\">".$kontrol."</td>
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
                            <td align=\"right\"><strong>".getAngka($persen, 2)."%</strong></td>
                            <td align=\"right\"><strong>".getAngka($totalNilai)."</strong></td>
                            <td align=\"right\"><strong>".getAngka($totalNilaiPPN)."</strong></td>
                            <td align=\"right\"><strong>".getAngka($grandTotal)."</strong></td>
                            <td colspan=\"2\"></td>
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
        </div>
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
        $where = " WHERE approve_status = 't'";
    }

    if (!empty($_GET['fSearch'])){
        $where .= " and (     
        lower(nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(tanggal) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and kodeData = '$_GET[combo3]'";
    if (!empty($_GET['combo4'])) $where .= " and id_supplier = '$_GET[combo4]'";
    if (!empty($_GET['combo5'])){
        $where .= " and id_sbu = '$_GET[combo5]'";

        if (!empty($_GET['combo6'])){
            $where .= " and id_proyek = '$_GET[combo6]'";
        }
    }

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
                                            join mst_data as c ON (c.kodeData = a.id_jenis) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $termin = getField("select count(*) from tagihan_termin where id_spk = '".$r["id"]."'");
        // $persen = getField("select sum(persen) from tagihan_termin where id_spk = '".$r["id"]."'");

        $background = $r[persen_termin] == '100' ? "class=\"labelStatusHijau\"" : "class=\"labelStatusKuning\"";

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["tanggal"])."</div>",
            "<div align=\"left\">".$r["judul"]."<br><a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detail&par[pop_up]=1&par[id_spk]=".$r["id"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a></div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
            "<div align=\"left\">".$r['namaData']."</div>",
             "<div align=\"center\" ".$background."><a style=\"text-decoration: none;\" href=\"?par[mode]=termin&par[id_spk]=".$r["id"].getPar($par, "mode, id_spk")."\">".getAngka($termin)."</a></div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
    global $s, $arrTitle, $par;

    $text = table(7, array( 4, 5, 6, 7, 8));

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
                ".comboMonth("combo1", $combo1, "", "120px", "ALL")."&nbsp;
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
                    <th width=\"80\">Termin</th>
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
