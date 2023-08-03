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
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpan();
            else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan();
            else $text = lihat();
            break;

        case "delete":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;

        case "detail":
            $text = view_spk($arrTitle[$s], $par['id_spk'], $par['pop_up'], false);
            break;

        case "getAtasan":
            $text = getAtasan();
            break;
    }

    return $text;
}

function getAtasan()
{
    global $par;

    $whereidpegawai = "";
    if ($par[idPegawai] != '0') {
        $whereidpegawai = "and id != $par[idPegawai]";
    }


    $getData = getRows("SELECT * FROM pegawai_data WHERE unit = '" . $par['unit'] . "' $whereidpegawai order by nama asc");
    echo json_encode($getData);
}

function lihat()
{
    global $s, $par, $arrTitle;

    $text = table(8, array(4, 5, 6, 7, 8));
    $filterJabatan = getField("SELECT DISTINCT jabatan FROM pegawai_data");
    $filterJenis = getField("SELECT DISTINCT jenis FROM pegawai_data");

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
                " . comboData("select * from mst_data where kodeCategory = 'KSBU' order BY namaData asc", "kodeData", "namaData", "combo3", "All Bisnis Unit", $combo3, "", "250px", "chosen-select") . "

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
                <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 430); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>
		

        <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">	
                                
                                <p>
                                    <label class=\"l-input-small\">Jabatan</label>
                                    <div class=\"field\">
                                        " . comboData("select distinct(jabatan) as jabatan from pegawai_data order BY jabatan asc", "jabatan", "jabatan", "combo1", "All Jabatan", $combo1, "", "250px", "chosen-select") . "
                                    </div>
                                    <style>#combo1_chosen{min-width:250px;}</style>
                                </p>
                                <p>
                                <label class=\"l-input-small\">Jenis</label>
                                <div class=\"field\">
                                    " . comboData("select * from mst_data where kodeCategory = 'JPG' order BY namaData asc", "kodeData", "namaData", "combo2", "All Jenis Pegawai", $combo2, "", "250px", "chosen-select") . "
                                </div>
                                <style>
                                    #combo2_chosen{ min-width:245px; }
                                </style>
                                </p>
                            </td>
                            <td style=\"width:50%\"></td>
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
                    <th width=\"250\">Nama</th>
                    <th width=\"100\">Nik</th>
                    <th width=\"150\">Jabatan</th>
                    <th width=\"150\">Bisnis Unit</th>
                    <th width=\"100\">No HP</th>
                    <th width=\"100\">Jenis</th>
                    <th width=\"50\">Kontrol</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
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

function form()
{
    global $par;

    $r = getRow("SELECT * FROM pegawai_data WHERE id = '$par[id]'");
    $r[id] = ($r[id] == '') ? '0' : $r[id];

    setValidation("is_null", "inp[nama]", "anda harus mengisi nama pegawai");
    setValidation("is_null", "inp[nik]", "anda harus mengisi nik");
    setValidation("is_null", "inp[jenis]", "anda harus mengisi jenis karyawan");
    setValidation("is_null", "inp[nohp]", "anda harus mengisi nomor hp");
    setValidation("is_null", "inp[jabatan]", "anda harus mengisi jabatan");
    setValidation("is_null", "inp[unit]", "anda harus mengisi unit");
    setValidation("is_null", "inp[bank]", "anda harus mengisi bank");
    setValidation("is_null", "inp[cabang]", "anda harus mengisi cabang");
    setValidation("is_null", "inp[norek]", "anda harus mengisi norek");
    setValidation("is_null", "inp[atasan]", "anda harus mengisi atasan");
    setValidation("is_null", "inp[admin]", "anda harus mengisi admin");
    $text = getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Area Akses</h1>
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
				<label class=\"l-input-small\" >Nama Pegawai</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[nama]\" name=\"inp[nama]\"  value=\"" . $r["nama"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">NIK</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[nik]\" name=\"inp[nik]\"  value=\"" . $r["nik"] . "\" class=\"mediuminput\" style=\"width:220px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small\">Jenis</label>
                            <div class=\"field\">
                                " . comboData("select * from mst_data where kodeCategory = 'JPG' order BY namaData asc", "kodeData", "namaData", "inp[jenis]", "- Pilih Jenis -", $r["jenis"], "", "610px", "chosen-select") . "
                            </div>
                            <style>
                                #inp_jenis__chosen{ min-width:245px; }
                            </style>
                        </p>
					</td>
				</tr>
			</table>

            <p>
				<label class=\"l-input-small\" >Nomor HP</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[nohp]\" name=\"inp[nohp]\"  value=\"" . $r["nohp"] . "\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"50\"/>
				</div>
			</p>

            <p>
				<label class=\"l-input-small\" >Jabatan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[jabatan]\" name=\"inp[jabatan]\"  value=\"" . $r["jabatan"] . "\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"50\"/>
				</div>
			</p>
			
			<p>
                <label class=\"l-input-small\">Bisnis Unit</label>
                <div class=\"field\">
                    " . comboData("select * from mst_data where kodeCategory = 'KSBU' order BY namaData asc", "kodeData", "namaData", "inp[unit]", "- Pilih Bisnis Unit -", $r["unit"], "onchange=\"getAtasan($r[id], this.value, '" . getPar($par, "mode") . "')\"", "610px", "chosen-select") . "
                </div>
                <style>
                    #inp_unit__chosen{ min-width:210px; }
                </style>
            </p>
			<p>
				<label class=\"l-input-small\" >Keterangan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"" . $r["keterangan"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			
			</fieldset>
            <br>
            <fieldset>
            <legend>Rekening</legend>
            <p>
        <td style=\"width:50%\">
            <p>
                <label class=\"l-input-small\">Nama Bank</label>
                <div class=\"field\">
                    " . comboData("select * from mst_data where kodeCategory = 'BNK' order BY namaData asc", "kodeData", "namaData", "inp[bank]", "- Pilih Bank -", $r["bank"], "", "610px", "chosen-select") . "
                </div>
                <style>
                    #inp_bank__chosen{ min-width:240px; }
                </style>
            </p>
        </td>
            <p>
                <label class=\"l-input-small\">Cabang</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[cabang]\" name=\"inp[cabang]\"  value=\"" . $r["cabang"] . "\" class=\"mediuminput\" style=\"width:420px;\" maxlength=\"100\">
                </div>
            </p>
            <p>
                <label class=\"l-input-small\">No Rekening</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[norek]\" name=\"inp[norek]\"  value=\"" . $r["norek"] . "\" class=\"mediuminput\" style=\"width:220px;\" maxlength=\"100\">
                </div>
            </p>

			</fieldset>
            <br>

            <fieldset>
            <legend>Administrasi</legend>
            <p>
            <td style=\"width:50%\">
            <p>
                <label class=\"l-input-small\">Atasan Langsung</label>
                <div class=\"field\">
                    " . comboData("select * from pegawai_data WHERE unit = '" . $r['unit'] . "' and id != $r[id] order BY nama asc", "id", "nama", "inp[atasan]", "- Pilih Atasan -", $r["atasan"], "", "610px", "chosen-select") . "
                </div>
                <style>
                    #inp_atasan__chosen{ min-width:245px; }
                </style>
            </p>
            <p>
                <label class=\"l-input-small\">Petugas Admin</label>
                <div class=\"field\">
                    " . comboData("select * from pegawai_data WHERE unit = '" . $r['unit'] . "'  and id != $r[id] order BY nama asc", "id", "nama", "inp[admin]", "- Pilih Admin -", $r["admin"], "", "610px", "chosen-select") . "
                </div>
                <style>
                    #inp_admin__chosen{ min-width:245px; }
                </style>
            </p>
        </td>
			
            
			</fieldset>
		</form>
	</div>";

    return $text;
}

function simpan()
{
    global $inp, $par, $cID;

    $setData = "`nama`  = '" . $inp["nama"] . "',
                  `nik` = '" . $inp["nik"] . "',
                  `jenis` = '" . $inp["jenis"] . "',
                  `jabatan` = '" . $inp["jabatan"] . "',
                  `nohp` = '" . $inp["nohp"] . "',
                  `unit` = '" . $inp["unit"] . "',
                  `keterangan` = '" . $inp["keterangan"] . "',
                  `bank` = '" . $inp["bank"] . "',
                  `cabang` = '" . $inp["cabang"] . "',
                  `norek` = '" . $inp["norek"] . "',
                  `atasan` = '" . $inp["atasan"] . "',
                  `admin` = '" . $inp["admin"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `pegawai_data`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `pegawai_data`
                SET
                  $setData
                  `updated_at` = now(),
                  `updated_by` = '" . $cID . "'
                WHERE `id` = '" . $par["id"] . "'
                ";
    }

    db($sql);

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function lData()
{
    global $s, $par, $menuAccess;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
        $where = " WHERE 1 = 1";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (  
        lower(nama) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(nik) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) {
        $where .= " and jabatan = '$_GET[combo1]'";
    }

    if (!empty($_GET['combo2'])) {
        $where .= " and jenis = '$_GET[combo2]'";
    }

    if (!empty($_GET['combo3'])) {
        $where .= " and unit = '$_GET[combo3]'";
    }

    /*  if (!empty($_GET['combo1'])) $where .= " and month(a.tanggal) = '" . $_GET['combo1'] . "'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.tanggal) = '" . $_GET['combo2'] . "'"; */

    $arrOrder = array("", "nama", "nik");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";

    $sql = "SELECT
    a.*, b.namaData AS namaUnit, c.namaData AS namaJenis
    FROM pegawai_data AS a
    JOIN mst_data AS b ON (b.kodeData = a.unit)
    JOIN mst_data AS c ON (c.kodeData = a.jenis)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(a.id) FROM pegawai_data AS a 
        JOIN mst_data AS b ON (b.kodeData = a.unit)
        JOIN mst_data AS c ON (c.kodeData = a.jenis) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" . $r["id"] . "" . getPar($par, "mode, id") . "',  980, 500);\" class=\"edit\"><span>Edit</span></a>";
        if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r['nama'] . "</div>",
            "<div align=\"center\">" . $r["nik"] . "</div>",
            "<div align=\"left\">" . $r["jabatan"] . "</div>",
            "<div align=\"left\">" . $r['namaUnit'] . "</div>",
            "<div align=\"left\">" . $r["nohp"] . "</div>",
            "<div align=\"left\">" . $r["namaJenis"] . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from pegawai_data where id = '" . $par["id"] . "'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])) . ".xls";
    $judul = $arrTitle[$s];

    $field = [
        "NO",
        "Nama",
        "NIK",
        "Jabatan",
        "Bisnis Unit",
        "NO HP",
        "Jenis Pegawai"
    ];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(a.nama) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(b.nik) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    // if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '" . $par['combo1'] . "'";
    // if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '" . $par['combo2'] . "'";

    $order = "id DESC";

    $sql = "SELECT
    a.*, b.namaData AS namaUnit, c.namaData AS namaJenis
    FROM pegawai_data AS a 
    JOIN mst_data AS b ON (b.kodeData = a.unit)
    JOIN mst_data AS c ON (c.kodeData = a.jenis)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        // $appr = "Menunggu Persetujuan";
        // if ($r["approve_status"] == "t") $appr = "Setuju";
        // if ($r["approve_status"] == "f") $appr = "Tolak";
        // if ($r["approve_status"] == "p") $appr = "Pending";

        $data[] = [
            $no . "\t center",
            $r["nama"] . "\t left",
            $r["nik"] . "\t center",
            $r['jabatan'] . "\t left",
            $r['namaUnit'] . "\t left",
            $r['nohp'] . "\t left",
            $r['namaJenis'] . "\t left"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}
