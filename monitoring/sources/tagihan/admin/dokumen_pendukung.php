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

        case "getJenis":
            $text = getJenis();
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "urut":
            $text = urut();
            break;
    }

    return $text;
}

function urut(){
    global $inp,$par;
    $result = getField("select urut from dokumen_pendukung where jenis = ". $inp['jenis'] ." order by urut desc limit 1") + 1;
    return $result;
}

function getJenis()
{
    global $par;

    $getData = getRows("SELECT * FROM mst_data WHERE kodeInduk = '" . $par['kodeInduk'] . "'");
    echo json_encode($getData);
}
function getFilter()
{
    global $par;

    $getData = getRows("SELECT * FROM mst_data WHERE kodeInduk = '" . $par['kodeInduk'] . "'");
    echo json_encode($getData);
}


function lihat()
{
    global $s, $par, $arrTitle;

    $text = table(9, array(9));

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
                <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 370); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>

            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">	
                                
                                <p>
                                    <label class=\"l-input-small\">Kategori</label>
                                    <div class=\"field\">
                         c               " . comboData("select * from mst_data where kodeCategory = 'MDKS' order BY urutanData asc", "kodeData", "namaData", "combo1", "All", $combo1, "onchange=\"getFilter(this.value, '" . getPar($par, "mode") . "')\"", "250px", "chosen-select") . "
                                    </div>
                                    <style>#combo1_chosen{min-width:250px;}</style>
                                </p>
                                <p>
                                <label class=\"l-input-small\">Jenis</label>
                                <div class=\"field\">
                                    " . comboData("select * from mst_data where kodeCategory = 'MDKD' and kodeInduk = '" . $r['kategori'] . "' order BY namaData asc", "kodeData", "namaData", "combo2", "All", $combo2, "", "250px", "chosen-select") . "
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
                    <th rowspan=\"2\" width=\"20\">No</th>
                    <th rowspan=\"2\" width=\"200\">Dokumen</th>
                    <th colspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">Jenis</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"20\">Mandatory</th>
                    <th rowspan=\"2\" width=\"250\">Keterangan</th>
                    <th rowspan=\"2\" width=\"200\">Kategori</th>
                    <th rowspan=\"2\" width=\"200\">Jenis</th>
                    <th rowspan=\"2\" width=\"50\">Kontrol</th>
                </tr>
                <tr>
                    <th width='20'>Asli</th>
                    <th width='20'>Copy</th>
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

    $r = getRow("SELECT * FROM dokumen_pendukung WHERE id = '$par[id]'");

    setValidation("is_null", "inp[dokumen]", "anda harus mengisi nama dokumen");
    setValidation("is_null", "inp[kategori]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[jenis]", "anda harus mengisi jenis");
    $text = getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">DOKUMEN PENDUKUNG</h1>
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
            <legend>Kategori</legend>
                <td style=\"width:50%\">
            <p>
                    <label class=\"l-input-small\">Kategori</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'MDKS' order BY urutanData asc", "kodeData", "namaData", "inp[kategori]", "- Pilih Kategori -", $r["kategori"], "onchange=\"getJenis(this.value, '" . getPar($par, "mode") . "')\"", "610px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_kategori__chosen{ min-width:210px; }
                    </style>
            </p>
            <p>
                    <label class=\"l-input-small\">Jenis</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'MDKD' and kodeInduk = '" . $r['kategori'] . "' order BY namaData asc", "kodeData", "namaData", "inp[jenis]", "- Pilih Jenis -", $r["jenis"], "onchange=\"urut('".getPar($par, "mode")."');\"", "610px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_jenis__chosen{ min-width:210px; }
                    </style>
            </p>    
                </td>
			
            
			</fieldset>
			<fieldset>
            <legend>Dokumen</legend>
            <p>
                <label class=\"l-input-small\" >Dokumen</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[dokumen]\" name=\"inp[dokumen]\"  value=\"" . $r["dokumen"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"100\"/>
                </div>
			</p>
			<p>
				<label class=\"l-input-small\" >Keterangan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"" . $r["keterangan"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"200\"/>
				</div>
			</p>
			<table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\">Lembar</label>
                                <div class=\"fradio\">
                                    <input type=\"radio\" id=\"inp[lembar]\" name=\"inp[lembar]\" value=\"a\" " . (($r["lembar"] == "a" or empty($r["lembar"])) ? "checked" : "") . "/> <span class=\"sradio\">Asli</span>
                                    <input type=\"radio\" id=\"inp[lembar]\" name=\"inp[lembar]\" value=\"c\" " . ($r["lembar"] == "c" ? "checked" : "") . " /> <span class=\"sradio\">Copy</span>
                                </div>
                            </p>
                    </td>
                    <td style='width: 50%;'> 
                            <p>
                                <label class=\"l-input-small2\"\">Mandatory</label>
                                <div class=\"fradio\">  
                                     <input type=\"checkbox\" ".($r[mandatory] == '1' ? "checked=\"checked\"" : "")." onclick=\"checkMandatory();\" id=\"inp[mandatory]\" name=\"inp[mandatory]\" value=\"1\"/> Ya
                                </div>
                            </p>
                    </td>
                </tr>
                </table>
                <p>
                    <label class=\"l-input-small\">Urut</label>
                    <div class=\"field\">                                        
                       <input type=\"text\" id=\"inp[urut]\" name=\"inp[urut]\"  value=\"" . $r["urut"] . "\" class=\"mediuminput\" style=\"width:30px;\" maxlength=\"50\"/>
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
            
		</form>
	</div>";

    return $text;
}

function simpan()
{
    global $inp, $par, $cID;

    $setData = "`kategori`  = '" . $inp["kategori"] . "',
                  `jenis` = '" . $inp["jenis"] . "',
                  `dokumen` = '" . $inp["dokumen"] . "',
                  `keterangan` = '" . $inp["keterangan"] . "',
                  `lembar` = '" . $inp["lembar"] . "',
                  `mandatory` = '" . $inp["mandatory"] . "',
                  `urut` = '" . $inp["urut"] . "',
                  `status` = '" . $inp["status"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `dokumen_pendukung`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `dokumen_pendukung`
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
        lower(dokumen) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(keterangan) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) {
        $where .= " and kategori = '$_GET[combo1]'";

        if (!empty($_GET['combo2'])) {
            $where .= " and jenis = '$_GET[combo2]'";
        }
    }



    $arrOrder = array("", "kategori", "jenis", "dokumen", "keterangan");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "urut asc";


    $sql = "SELECT
    a.*, b.namaData AS namaKategori, c.namaData AS namaJenis
    FROM dokumen_pendukung AS a
    JOIN mst_data AS b ON (b.kodeData = a.kategori)
    JOIN mst_data AS c ON (c.kodeData = a.jenis)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(id) FROM dokumen_pendukung $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        if ($r['lembar'] == 'a'){
            $asli = "<img src=\"styles/images/icons/check.png\" title='Asli'>";
        } else {
            $asli = "";
        }

        if ($r['lembar'] == 'c'){
            $copy = "<img src=\"styles/images/icons/check.png\" title='Copy'>";
        } else {
            $copy = "";
        }

        if ($r['mandatory'] == '1'){
            $mandatory = "<img src=\"styles/images/icons/check.png\" title='Mandatory'>";
        } else {
            $mandatory = "";
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" . $r["id"] . "" . getPar($par, "mode, id") . "',  980, 370);\" class=\"edit\"><span>Edit</span></a>";
        if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r["dokumen"] . "</div>",
            "<div align=\"center\">".$asli."</div>",
            "<div align=\"center\">".$copy."</div>",
            "<div align=\"center\">".$mandatory."</div>",
            "<div align=\"left\">" . $r["keterangan"] . "</div>",
            "<div align=\"left\">" . $r["namaKategori"] . "</div>",
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

    db("delete from dokumen_pendukung where id = '" . $par["id"] . "'");

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
        "Kategori",
        "Jenis",
        "Dokumen",
        "Keterangan"
    ];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (  
            lower(dokumen) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            or
            lower(keterangan) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            )";
    }

    // if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '" . $par['combo1'] . "'";
    // if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '" . $par['combo2'] . "'";

    $order = "id DESC";

    $sql = "SELECT
    a.*, b.namaData AS namaKategori, c.namaData AS namaJenis
    FROM dokumen_pendukung AS a
    JOIN mst_data AS b ON (b.kodeData = a.kategori)
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
            $r["namaKategori"] . "\t left",
            $r["namaJenis"] . "\t left",
            $r['dokumen'] . "\t left",
            $r['keterangan'] . "\t left"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
}
