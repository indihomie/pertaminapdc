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
    }

    return $text;
}



function lihat()
{
    global $s, $par, $arrTitle;

    $text = table(7, array(7));

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
			</div>

			<div id=\"pos_r\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
                <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 370); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>
		

        
            </form>
            <br clear=\"all\" />


		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th width=\"20\">No</th>
                    <th width=\"250\">Nama CC & PC</th>
                    <th width=\"100\">Cost</th>
                    <th width=\"100\">Profit</th>
                    <th width=\"100\">Kode Organisasi</th>
                    <th width=\"100\">Sbu</th>
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

    $r = getRow("SELECT * FROM costcenter_data WHERE id = '$par[id]'");

    setValidation("is_null", "inp[nama]", "anda harus mengisi nama cost center");
    setValidation("is_null", "inp[cost]", "anda harus mengisi cost");
    setValidation("is_null", "inp[profit]", "anda harus mengisi profit");
    setValidation("is_null", "inp[sbu]", "anda harus mengisi sbu");
    $text = getValidation();


    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Data Cost Center</h1>
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
				<label class=\"l-input-small\" >Nama CC & PC</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[nama]\" name=\"inp[nama]\"  value=\"" . $r["nama"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Cost</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[cost]\" name=\"inp[cost]\"  value=\"" . $r["cost"] . "\" class=\"mediuminput\" style=\"width:220px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Profit</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[profit]\" name=\"inp[profit]\"  value=\"" . $r["profit"] . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
				</tr>
			</table>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\" >Kode Group</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[group]\" name=\"inp[group]\"  value=\"" . $r["group"] . "\" class=\"mediuminput\" style=\"width:220px;\" maxlength=\"50\"/>
                            </div>
			            </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Kode Organisasi</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[kode_organisasi]\" name=\"inp[kode_organisasi]\"  value=\"" . $r["kode_organisasi"] . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
				</tr>
			</table>
            <p>
				<label class=\"l-input-small\" >Keterangan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"" . $r["keterangan"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
            <p>
            <label class=\"l-input-small\">SBU</label>
            <div class=\"field\">
                " . comboData("select * from mst_data where kodeCategory = 'KSBU' order BY namaData asc", "kodeData", "namaData", "inp[sbu]", "- Pilih SBU -", $r["sbu"], "onchange=\"getAtasan($r[id], this.value, '" . getPar($par, "mode") . "')\"", "610px", "chosen-select") . "
            </div>
            <style>
                #inp_sbu__chosen{ min-width:210px; }
            </style>
			</p>
            <p>
                    <label class=\"l-input-small\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" " . (($r["status"] == "1" or empty($r["status"])) ? "checked" : "") . "/> <span class=\"sradio\">Aktif</span>
                        <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" " . ($r["status"] == "0" ? "checked" : "") . " /> <span class=\"sradio\">Tidak</span>
                    </div>
                </p>
			</fieldset>
		</form>
	</div>";

    return $text;
}

function simpan()
{
    global $inp, $par, $cID;

    $setData = "`nama`  = '" . $inp["nama"] . "',
                  `cost` = '" . $inp["cost"] . "',
                  `profit` = '" . $inp["profit"] . "',
                  `group` = '" . $inp["group"] . "',
                  `kode_organisasi` = '" . $inp["kode_organisasi"] . "',
                  `sbu` = '" . $inp["sbu"] . "',
                  `keterangan` = '" . $inp["keterangan"] . "',
                  `status` = '" . $inp["status"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `costcenter_data`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `costcenter_data`
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
        lower(`group`) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(sbu) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    $arrOrder = array("", "nama", "cost", "profit", "group", "sbu");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "nama asc";


    $sql = "SELECT
    a.*, b.namaData AS namaSbu
    FROM costcenter_data AS a
    JOIN mst_data AS b ON (b.kodeData = a.sbu)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(a.id) FROM costcenter_data AS a JOIN mst_data AS b ON (b.kodeData = a.sbu) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" . $r["id"] . "" . getPar($par, "mode, id") . "',  980, 370);\" class=\"edit\"><span>Edit</span></a>";
        if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r["nama"] . "</div>",
            "<div align=\"center\">" . $r["cost"] . "</div>",
            "<div align=\"center\">" . $r["profit"] . "</div>",
            "<div align=\"center\">" . $r['kode_organisasi'] . "</div>",
            "<div align=\"left\">" . $r["namaSbu"] . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from costcenter_data where id = '" . $par["id"] . "'");

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
        "Nama CC & PC",
        "Cost",
        "Profit",
        "Group",
        "SBU"
    ];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(sbu) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(`group`) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(sbu) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    // if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '" . $par['combo1'] . "'";
    // if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '" . $par['combo2'] . "'";

    $order = "nama asc";

    $sql = "SELECT
    a.*, b.namaData AS namaSbu
    FROM costcenter_data AS a
    JOIN mst_data AS b ON (b.kodeData = a.sbu)
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
            $r["cost"] . "\t right",
            $r['profit'] . "\t right",
            $r['group'] . "\t center",
            $r['namaSbu'] . "\t center"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}
