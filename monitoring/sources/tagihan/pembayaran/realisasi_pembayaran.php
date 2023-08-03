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

        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
            break;

        case "delete":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;

        case "delFile":
            if (isset($menuAccess[$s]["delete"])) $text = delFile(); else $text = lihat();
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

function hapus()
{
    global $par, $dirFile;

    $file = getField("select bukti_bayar from tagihan_bayar where id = '".$par['id_pembayaran']."'");
    unlink($dirFile.$file);

    db("delete from tagihan_bayar where id = '".$par["id_pembayaran"]."'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode, id_pembayaran") . "';</script>";
}

function delFile()
{
    global $par, $dirFile;

    $file = getField("select bukti_bayar from tagihan_bayar where id = '".$par['id']."'");
    db("update tagihan_bayar set bukti_bayar = '' where id = '".$par['id']."'");
    unlink($dirFile.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function simpan()
{
    global $inp, $par, $cID, $dirFile;

    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
    $fileIcon_name = $_FILES["fileUpload"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none"))
    {
        fileUpload($fileIcon, $fileIcon_name, $dirFile);
        $fileDokumen = "pembayaran_".time().".".getExtension($fileIcon_name);
        fileRename($dirFile, $fileIcon_name, $fileDokumen);
        $updateFIle .= "bukti_bayar = '".$fileDokumen."',";
    }

    $setData = "`tanggal` = '".setTanggal($inp["tanggal"])."',
                `id_norek` = '".$inp["id_norek"]."',
                `nama_pemohon` = '".$inp["nama_pemohon"]."',
                `nilai` = '".setAngka($inp["nilai"])."',
                `catatan` = '".$inp["catatan"]."',
                 $updateFIle
               ";

    if (empty($par["id_pembayaran"])) {

        $sql = "INSERT
                  `tagihan_bayar`
                SET
                  `id_tagihan` = '".$par["id_tagihan"]."',
                   $setData
                  `created_at` = now(),
                  `created_by` = '".$cID."'
                ";
        db($sql);

        $idPembayaran = getField("select id from tagihan_bayar where created_by = '$cID' order by id desc limit 1");

    } else {

        $sql = "UPDATE
                  `tagihan_bayar`
                SET
                   $setData
                  `updated_at` = now(),
                  `updated_by` = '".$cID."'
                WHERE `id` = '".$par["id_pembayaran"]."'
                ";
        db($sql);

        $idPembayaran = $par["id_pembayaran"];
    }

    // update total

    $tagihan = getRow("SELECT b.* FROM tagihan_bayar AS a
                        JOIN tagihan_data AS b ON (b.id = a.id_tagihan)
                        WHERE a.id = '".$idPembayaran."'");

    $id_termin = $tagihan['id_termin'];
    $id_tagihan = $tagihan['id'];
    $id_spk = $tagihan['id_spk'];

    $jumlah = getField("select count(*) from tagihan_bayar where id_tagihan = $id_tagihan");
    $nilai  = getField("select nilai_total from tagihan_termin where id = $id_termin");
    $bayar  = getField("select sum(nilai) from tagihan_bayar where id_tagihan = $id_tagihan");
    $sisa   = $nilai - $bayar;

    db("update tagihan_termin set
                                jumlah_bayar = '".$jumlah."',
                                bayar = '".$bayar."',
                                sisa = '".$sisa."'
                            where id = $id_termin");

    $status_pelunasan = ($sisa == 0) ? "lunas" : "sebagian";

    db("update tagihan_data set  status_pelunasan = '".$status_pelunasan."' where id = $id_tagihan");

    updatePelunasanSPK($id_spk);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
    echo "<script>parent.window.location='index.php?par[mode]=detailForm" . getPar($par, "mode") . "#flagForm';</script>";
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
    global $par, $dirFile;

    $r = getRow("SELECT * FROM tagihan_bayar WHERE id = '".$par['id_pembayaran']."'");

    $idSPK = getField("select id_spk from tagihan_data where id = '$par[id_tagihan]'");
    $spk = getRow("select * from tagihan_spk where id = $idSPK");

    if ($spk[id_jenis] == '1048') {
        $pemohon = getField("select namaSupplier from dta_supplier where kodeSupplier = $par[id_supplier]");
    } else {
        $pemohon = getField("select nama_pemilik from pegawai_data where id = $par[id_supplier]");
    }

    setValidation("is_null", "inp[tanggal]", "Tanggal tidak boleh kosong");
    //setValidation("is_null", "inp[id_norek]", "No. Rekening tidak boleh kosong");
    setValidation("is_null", "inp[nilai]", "Nilai tidak boleh kosong");
    $text .= getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">PEMBAYARAN</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    ";
				    if ($r['konf_status'] != "t") $text.="<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
				    $text.="
				</p>
			</div>
			<fieldset>
			
			    <p>
                    <label class=\"l-input-small\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["tanggal"] = empty($r["tanggal"]) ? date("Y-m-d") : $r["tanggal"];
                        $text.="
                        <input type=\"text\" id=\"inp[tanggal]\" name=\"inp[tanggal]\"  value=\"" . getTanggal($r["tanggal"]) . "\" class=\"hasDatePicker\"/>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Nama Pemohon</label>
                    <div class=\"field\">
                        ";

                        $r["nama_pemohon"] = empty($r["nama_pemohon"]) ? $pemohon : $r["nama_pemohon"];
                        $text.="
                        <input type=\"text\" id=\"inp[nama_pemohon]\" name=\"inp[nama_pemohon]\" value=\"".$r["nama_pemohon"]."\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"100\">
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">No. Rekening</label>
                    <div class=\"field\">
                        ".comboData("select id, concat(namaBank, ' - ', rekeningBank, ' (', pemilikBank, ')') as bank from dta_supplier_bank where kodeSupplier = ".$par['id_supplier']." order by pemilikBank asc", "id", "bank", "inp[id_norek]", "- Pilih Nomor Rekening -", $r["id_norek"], "", "430px", "chosen-select")."
                    </div>
                </p>
                <style>
                    #inp_id_norek__chosen{ min-width:430px; }
                </style>
                
                <p>
                    <label class=\"l-input-small\">Nilai</label>
                    <div class=\"field\">
                        ";
                        $r["nilai"] = empty($r["nilai"]) ? $par[total] : $r["nilai"];
                        $text.="
                        <input type=\"text\" id=\"inp[nilai]\" name=\"inp[nilai]\" onkeyup=\"cekAngka(this)\" value=\"".getAngka($r["nilai"])."\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\" >Catatan</label>
                    <div class=\"field\">
                        <textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:430px;\">".$r["catatan"]."</textarea>
                    </div>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Bukti Bayar</label>
                    <div class=\"field\">";
                        $text .= empty($r['bukti_bayar'])
                        ?
                        "<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
                        <div class=\"fakeupload\">
                            <input type=\"file\" id=\"fileUpload\" name=\"fileUpload\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
                        </div>"
                        :
                        "<a href=\"".$dirFile.$r['bukti_bayar']."\" download><img src=\"".getIcon($r['bukti_bayar'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        ".(($r['konf_status'] != "t") ? "<a href=\"?par[mode]=delFile&par[id]=".$r['id'].getPar($par,"mode, id")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                        <br clear=\"all\">";
                        $text.="
                    </div>
                </p>
			
			</fieldset>
		</form>
	</div>";

    return $text;
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");
    $spk = getRow("select * from tagihan_spk where id = $tagihan[id_spk]");

    $text .= "
            
    ".view_permohonan($arrTitle[$s], $tagihan['id_spk'], '', false, $tagihan['id_termin'], true, false)."  
            
    <br />
    <br />
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
    
    <div class=\"widgetbox\" style=\"margin-top:-20px;\">
    	<div id=\"flagForm\"></div>

        <div class=\"title\">
            <h3>PEMBAYARAN</h3>
            ";
            $total = getField("select nilai_total from tagihan_termin where id = $tagihan[id_termin]");
            if (isset($menuAccess[$s]["add"]) and $spk['status_pelunasan'] != "lunas") $text.="<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:-20px;\" onclick=\"openBox('popup.php?par[mode]=add&par[total]=".$total . getPar($par, "mode, id_pembayaran") . "', 700, 400); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
            $text.="
        </div>
    </div>
    
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
        <thead>
            <tr>
                <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                <th width=\"70\" rowspan=\"2\" style=\"vertical-align: middle\">tanggal</th>
                <th width=\"250\" rowspan=\"2\" style=\"vertical-align: middle\">Rekening</th>
                <th width=\"*\" rowspan=\"2\" style=\"vertical-align: middle\">catatan</th>
                <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Nilai</th>
                <th colspan=\"2\">Bukti</th>
                <th width=\"75\" rowspan=\"2\" style=\"vertical-align: middle\">Kontrol</th>
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

                    $kontrol = "";
                    if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id_supplier]=".$par['id_supplier']."&par[id_pembayaran]=".$data["id"]."" . getPar($par, "mode, id_pembayaran, id_supplier") . "', 700, 400);\" class=\"edit\"><span>Edit</span></a>";
                    if (isset($menuAccess[$s]["delete"]) and $data['konf_status'] != "t") $kontrol .= "<a href=\"?par[mode]=delete&par[id_pembayaran]=".$data["id"].getPar($par, "mode, id_pembayaran")."\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

                    if ($spk[id_jenis] == '1048') {
                        $rekening = getField("select concat(namaBank, ' - ', rekeningBank, ' (', pemilikBank, ')') from dta_supplier_bank where id = ".$data['id_norek']." ");
                    } else {
                        $rekening = $data[nama_pemohon];
                    }

                    $text.="
                    <tr>
                        <td align=\"center\">".$no."</td>
                        <td align=\"center\">".getTanggal($data['tanggal'])."</td>
                        <td align=\"left\">".$rekening."</td>
                        <td align=\"left\">".$data['catatan']."</td>
                        <td align=\"right\">".getAngka($data['nilai'])."</td>
                        <td align=\"center\">".$view."</td>
                        <td align=\"center\">".$download."</td>
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
                                <td></td>
                                <td></td>
                            </tr>
                        ";

            } else {

                $text.="
                <tr>
                    <td colspan=\"8\"><strong><center>- Data Kosong -</center></strong></td>
                </tr>
                ";

            }
            $text.="
        </tbody>
    </table>
    </div>
    ";

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
        $where = " WHERE a.pembayaran_approve_status = 't'";
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

        $kontrol = "<a href=\"?par[mode]=detailForm&par[id_supplier]=$r[id_supplier]&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan, id_supplier")."\" class=\"edit\"><span>Detail</span></a>";

        $bayar = getField("select sum(nilai) from tagihan_bayar where id_tagihan = '".$r[id_tagihan]."'");
        $persen = $bayar / $r['nilai_total'] * 100;

        if ($persen == "100"){ #ini kalau 100
            $background = "class=\"labelStatusHijau\"";
        } elseif ($persen > 0) {
            $background = "class=\"labelStatusBiru\""; #ini kalau terisi tapi gasampe 100
        } else { #kalau belum
            $background = "class=\"labelStatusKuning\"";
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
            "<div align=\"center\" ".$background.">".getAngka($persen)."%</div>",
            "<div align=\"center\">".$kontrol."</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat()
{
	global $s, $arrTitle, $par;

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
                    <th style=\"vertical-align: middle;\" width=\"100\">Pembayaran</th>
                    <th style=\"vertical-align: middle;\" width=\"50\">Realisasi</th>
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

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal Rencana",
              "Judul - Nomor",
              "Pemohon",
              "Nilai",
              "No. Tiket",
              "No. Pembayaran",
              "Pembayaran"];

    $where = "  WHERE a.pengajuan_approve_status = 't'";

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

        $bayar = getField("select sum(nilai) from tagihan_bayar where id_tagihan = '".$r[id_tagihan]."'");
        $persen = $bayar / $r['nilai_total'] * 100;

        $pemohon = ($r["id_jenis"] == '1048') ? "namaSupplier" : "nama";

        $data[]=[
			$no . "\t center",
			getTanggal($r["target"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
			$r["$pemohon"]."\t left",
			getAngka($r["nilai_total"]) . "\t right",
			$r['pengajuan_no_tiket'] . "\t center",
            $r["tiket_nomor"]."\t center",
			getAngka($persen) . "%\t right"
		];
    }

    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}