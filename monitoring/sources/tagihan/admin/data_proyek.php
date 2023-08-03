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

    $text = table(8, array(8));

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
                <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 500); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>

            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">	
                                
                                <p>
                                    <label class=\"l-input-small\">Cost Center</label>
                                    <div class=\"field\">
                                        " . comboData("select * from costcenter_data order BY nama asc", "id", "nama", "combo1", "All", $combo1, "", "250px", "chosen-select") . "
                                    </div>
                                    <style>#combo1_chosen{min-width:250px;}</style>
                                </p>
                                <p>
                                <label class=\"l-input-small\">Customer</label>
                                <div class=\"field\">
                                    " . comboData("select * from dta_supplier where tipe = 'customer' order BY namaSupplier asc", "kodeSupplier", "namaSupplier", "combo2", "All", $combo2, "", "250px", "chosen-select") . "
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
                    <th width=\"200\">Proyek</th>
                    <th width=\"100\">Nomor</th>
                    <th width=\"100\">No Kontrak</th>
                    <th width=\"100\">WBS</th>
                    <th width=\"150\">Cost Center</th>
                    <th width=\"80\">Status</th>
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

    $r = getRow("SELECT * FROM proyek_data WHERE id = '$par[id]'");

    setValidation("is_null", "inp[proyek]", "anda harus mengisi nama proyek");
    setValidation("is_null", "inp[nomor]", "anda harus mengisi nomor");
    setValidation("is_null", "inp[kontrak]", "anda harus mengisi no kontrak");
    setValidation("is_null", "inp[costcenter]", "anda harus mengisi cost center");
    setValidation("is_null", "inp[wbs]", "anda harus mengisi wbs");
    setValidation("is_null", "inp[customer]", "anda harus mengisi customer");
    $text = getValidation();



    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">DATA PROYEK</h1>
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
				<label class=\"l-input-small\" >Nama Proyek</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[proyek]\" name=\"inp[proyek]\"  value=\"" . $r["proyek"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Nomor</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"" . $r["nomor"] . "\" class=\"mediuminput\" style=\"width:220px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">No Kontrak</label>
                            <div class=\"fieldA\">
                                <input type=\"text\" id=\"inp[kontrak]\" name=\"inp[kontrak]\"  value=\"" . $r["kontrak"] . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                            </div>
                        </p>
					</td>
				</tr>
			</table>
            <p>
				<label class=\"l-input-small\" >WBS</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[wbs]\" name=\"inp[wbs]\"  value=\"" . $r["wbs"] . "\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" >Keterangan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"" . $r["keterangan"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
            <p>
                    <label class=\"l-input-small\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" " . (($r["status"] == "1" or empty($r["status"])) ? "checked" : "") . "/> <span class=\"sradio\">Aktif</span>
                        <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" " . ($r["status"] == "0" ? "checked" : "") . " /> <span class=\"sradio\">Tidak</span>
                    </div>
                </p>
			</fieldset>
            <br>
            <fieldset>
            <legend>Administrasi</legend>
                <td style=\"width:50%\">
            <p>
                    <label class=\"l-input-small\">Nama CC & PC</label>
                    <div class=\"field\">
                        " . comboData("select id, CONCAT(cost,' - ', nama) as name from costcenter_data order BY nama asc", "id", "name", "inp[costcenter]", "- Pilih CC & PC -", $r["costcenter"], "", "610px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_costcenter__chosen{ min-width:410px; }
                    </style>
            </p>
            <p>
                    <label class=\"l-input-small\">Customer</label>
                    <div class=\"field\">
                        " . comboData("select * from dta_supplier where tipe = 'customer' order BY namaSupplier asc", "kodeSupplier", "namaSupplier", "inp[customer]", "- Pilih Customer -", $r["customer"], "", "610px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_customer__chosen{ min-width:210px; }
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

    $setData = "`proyek`  = '" . $inp["proyek"] . "',
                  `nomor` = '" . $inp["nomor"] . "',
                  `kontrak` = '" . $inp["kontrak"] . "',
                  `wbs` = '" . $inp["wbs"] . "',
                  `keterangan` = '" . $inp["keterangan"] . "',
                  `costcenter` = '" . $inp["costcenter"] . "',
                  `customer` = '" . $inp["customer"] . "',
                  `status` = '" . $inp["status"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `proyek_data`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `proyek_data`
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
        lower(proyek) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(wbs) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) {
        $where .= " and costcenter = '$_GET[combo1]'";
    }

    if (!empty($_GET['combo2'])) {
        $where .= " and customer = '$_GET[combo2]'";
    }

    $arrOrder = array("", "proyek", "nomor", "kontrak", "wbs", "costcenter");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";


    $sql = "SELECT
    a.*, b.nama as namaCost, b.cost
    FROM proyek_data AS a
    join costcenter_data AS b ON (b.id = a.costcenter)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT
                                                    count(a.id)
                                                    FROM proyek_data AS a
                                                    join costcenter_data AS b ON (b.id = a.costcenter) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        if ($r["status"] == 1){ #ini kalau full
            $background = "class=\"labelStatusHijau\"";
            $status = "Aktif";
        }else { #kalo 0
            $background = "class=\"labelStatusMerah\"";

            $status = "Tidak Aktif";
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" . $r["id"] . "" . getPar($par, "mode, id") . "',  980, 500);\" class=\"edit\"><span>Edit</span></a>";
        if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r["proyek"] . "</div>",
            "<div align=\"center\">" . $r["nomor"] . "</div>",
            "<div align=\"center\">" . $r["kontrak"] . "</div>",
            "<div align=\"center\">" . $r['wbs'] . "</div>",
            "<div align=\"left\">" . $r["cost"] . "</div>",
             "<div align=\"center\" $background>".$status."</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from proyek_data where id = '" . $par["id"] . "'");

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
        "Proyek",
        "Nomor",
        "No Kontrak",
        "WBS",
        "Cost Center"
    ];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (  
        lower(proyek) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(wbs) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    if (!empty($par['combo1'])) {
        $where .= " and costcenter = '$par[combo1]'";
    }

    if (!empty($par['combo2'])) {
        $where .= " and customer = '$par[combo2]'";
    }

    $order = "id DESC";

    $sql = "SELECT
    a.*, b.nama as namaCost, b.cost
    FROM proyek_data AS a
    join costcenter_data AS b ON (b.id = a.costcenter)
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
            $r["proyek"] . "\t left",
            $r["nomor"] . "\t right",
            $r['kontrak'] . "\t right",
            $r['wbs'] . "\t center",
            $r['costcenter'] . "\t center"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}
