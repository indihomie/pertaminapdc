<?php
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
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par[id_termin], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "detailJurnal":
            $text = detailJurnal();
            break;
    }

    return $text;
}

function detailJurnal()
{
    global $s, $par, $arrTitle, $menuAccess, $getPajak;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text.="
            <div class=\"pageheader\">
                <h1 class=\"pagetitle\">". strtoupper($arrTitle[$s]) ."</h1>
                ".getBread(ucwords(str_replace("Detail", "", $par["mode"])))."
             </div>
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
                
                <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
                
                <p style=\"position: absolute; right: 20px; top: 10px;\">
		            <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk, id_tagihan, id_termin, id_pajak") . "';\"/>
			    </p>
			    
			    <br>
			    
			    <fieldset>
			        <legend>Jurnal</legend>
			         <p>
                        <label class=\"l-input-small\" >Prepared By</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Posting Date</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Document Date</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                    <p>
                        <label class=\"l-input-small\" >Month</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Company Code</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Document Type</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                    <p>
                        <label class=\"l-input-small\" >Customer Code</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Assignment</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Reference</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                    <p>
                        <label class=\"l-input-small\" >Ref Key 2 </label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                     <p>
                        <label class=\"l-input-small\" >Header Text</label>
                        <span class=\"field\">
                            ".$r["alamat"]." &nbsp;
                        </span>
                    </p>
                    
			    
			    
                </fieldset>
                
                <br>
                
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
                                <th width=\"150\" style=\"vertical-align: middle\">Assignment</th>
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
                                        <td align=\"center\">".comboData("select id, kode from account_gl", "id", "kode", "inp[gl_account][$data[idBiaya]]", "- Pilih Kode -", $data[gl_account], "onchange=\"jQuery('.formGL').submit()\"", "150px", "chosen-select")."</td>
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
                                <th width=\"75\" style=\"vertical-align: middle\">Kontrol</th>
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
                                    <td align=\"center\">".$kontrol."</td>
                                </tr>
                                ";

                                $grandTotal += $data['nilai'];
                            }


                            $text.="
                            <tr>
                                <td colspan=\"4\" align=\"right\"><strong>Total</strong></td>
                                <td align=\"right\"><strong>".getAngka($grandTotal)."</strong></td>
                                <td></td>
                            </tr>

                        </tbody>
                    </table>
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
        $where = " WHERE a.pengajuan_approve_status = 't'";
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
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" class=\"edit\"><span>Detail</span></a>";

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

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
            // "<div align=\"right\"><a href=\"?par[mode]=detailJurnal&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\">".getAngka($r["nilai_plus_ppn"])."</a></div>",
            "<div align=\"right\">".getAngka($r["nilai_plus_ppn"])."</div>",
        );

        $data2 = array();
        foreach ($getPajak as $pjk) {
            $nilai = getField("select nilai from tagihan_pajak where id_tagihan = '$r[id_tagihan]' and id_pajak = '$pjk[kodeData]'");
            $data2[] = [
                "<div align=\"right\">".getAngka($nilai)."</div>",
            ];
        }

        $data3 = array("<div align=\"right\">".getAngka($r["nilai_total"])."</div>");

        $data = array_merge($data, $data2, $data3);

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $dirFileBa, $getPajak;

    //db("delete from tagihan_pajak where id_tagihan = $par[id_tagihan] and id_pajak = $par[id_pajak]");

    $id = getField("select id from tagihan_pajak where id_tagihan = $par[id_tagihan] and id_pajak = $par[id_pajak]");

    if(empty($id)) {
        $sql = "insert into tagihan_pajak set 
                id_tagihan = $par[id_tagihan], 
                id_pajak = $par[id_pajak],
                tarif = '$inp[tarif]',
                nilai = '".setAngka($inp[nilai])."',
                dpp = '".setAngka($inp[dpp])."',
                keterangan = '$inp[keterangan]',
                created_at = now(),
                created_by = '$cID'
                ";
    } else {
        $sql = "update tagihan_pajak set 
                tarif = '$inp[tarif]',
                nilai = '".setAngka($inp[nilai])."',
                dpp = '".setAngka($inp[dpp])."',
                keterangan = '$inp[keterangan]',
                updated_at = now(),
                updated_by = '$cID'
                where id = $id;
                ";
    }
    db($sql);

    $idTermin = getField("select id_termin from tagihan_data where id = $par[id_tagihan]");
    //$termin   = getRow("select * from tagihan_termin where id = $idTermin");
    $nilaiPPH = getField("select sum(nilai) from tagihan_pajak where id_tagihan = $par[id_tagihan]");
   // $total    = ($termin[nilai] + $termin[nilai_ppn]) - $nilaiPPH;
    $nilaiPlusPPN = getField("select nilai_plus_ppn from tagihan_termin where id = $idTermin");

    $total = $nilaiPlusPPN - $nilaiPPH;

    db("update tagihan_termin set nilai_pph = '$nilaiPPH', nilai_total = '$total' where id = $idTermin");

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\");</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode") . "#flagForm';</script>";
}
function form()
{
    global $par, $dirFileBa, $arrTitle, $s;

    $dt = getRow("SELECT b.termin, b.nilai_plus_ppn FROM tagihan_data a
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
            <legend>Informasi</legend>
			    <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\">
                        ".$dt["termin"]." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\" >Nilai</label>
                    <span class=\"field\">
                       Rp. ".getAngka($dt["nilai_plus_ppn"])." &nbsp; 
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
                    <label class=\"l-input-small\">DPP</label>
                    <div class=\"field\">
                        <input type=\"text\" onkeyup=\"cekAngka(this);getNilai();\" id=\"inp[dpp]\" name=\"inp[dpp]\" style=\"width: 110px\" value=\"" . getAngka($r["dpp"]) . "\" />
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Tarif</label>
                    <div class=\"field\">
                        " . comboData("select namaData from mst_data where kodeCategory = 'MDPN' order BY urutanData asc", "namaData", "namaData", "inp[tarif]", "- Pilih Tarif -", $r["tarif"], "onchange=\"getNilai();\"", "100px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_tarif__chosen{ min-width:120px; }
                    </style>
                </p>
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[nilai]\" readonly name=\"inp[nilai]\" style=\"width: 110px\" value=\"" . getAngka($r["nilai"]) . "\" />
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

	$text = table((9 + count($getPajak)), array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15));

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
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 20px;\">No</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">Kontrol</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 150px;\">Tahap</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                        <th colspan=\"".count($getPajak)."\" style=\"vertical-align: middle; min-width: 80px;\">PPH PASAL (%)</th>
                        <th rowspan=\"2\" style=\"vertical-align: middle; min-width: 80px;\">Total</th>
                        
                    </tr>
                    <tr>
                        ";
                        foreach ($getPajak as $pjk) {
                            $text.="<th  style=\"vertical-align: middle; min-width: 100px;\">$pjk[namaData]</th>";
                        }
                        $text.="
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
    global $par, $arrTitle, $s, $getPajak;

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
              "PPH Pasal 15",
              "PPH PASAL 21",
              "PPH Pasal 23",
              "PPH Pasal 26",
              "PPH Pasal 4(2)",
              "Total"];

    $where = " WHERE a.pengajuan_approve_status = 't'";

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

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $result =
        [
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			$r['termin'] . "\t left",
			$r['pengajuan_no_tiket'] . "% \t right",
            getAngka($r["nilai_plus_ppn"]) . "\t right",
		];

        foreach ($getPajak as $pjk) {
            $nilai = getField("select nilai from tagihan_pajak where id_tagihan = '$r[id_tagihan]' and id_pajak = '$pjk[kodeData]'");
            $result[] = getAngka($nilai) . "\t right";
        }

        $result[] = getAngka($r["nilai_total"]) . "\t right";

        $data[] = $result;
    }

    exportXLS($direktori, $namaFile, $judul, 13, $field, $data);
}
