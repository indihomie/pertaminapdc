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

    $text = table(10, array(10));

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
                 <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode") . "', 980, 430); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>

            
            </form>
            <br clear=\"all\" />


		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th width=\"20\">No</th>
                    <th width=\"80\">Kode</th>
                    <th width=\"100\">Grup</th>
                    <th width=\"250\">Judul</th>
                    <th width=\"70\">Cocd</th>
                    <th width=\"70\">Curr</th>
                    <th width=\"60\">Level</th>
                    <th width=\"60\">Fbi</th>
                    <th width=\"80\">Fst</th>
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
    global $s, $par, $arrTitle;

    $r = getRow("SELECT * FROM account_gl WHERE id = '$par[id]'");

    setValidation("is_null", "inp[judul]", "anda harus mengisi nama dokumen");
    setValidation("is_null", "inp[kode]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[grup]", "anda harus mengisi jenis");
    setValidation("is_null", "inp[currency]", "anda harus mengisi nama dokumen");
    setValidation("is_null", "inp[level]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[fbi]", "anda harus mengisi jenis");
    setValidation("is_null", "inp[fst]", "anda harus mengisi jenis");
    $text = getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".strtoupper($arrTitle[$s])."</h1>
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
            <legend>GL ACCOUNT</legend>
            
			
			<table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\" >Kode</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[kode]\" name=\"inp[kode]\"  value=\"" . $r["kode"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			                </p>
                    </td>
                    <td style='width: 50%;'> 
                            <p>
                                <label class=\"l-input-small2\" >Grup</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[grup]\" name=\"inp[grup]\"  value=\"" . $r["grup"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			               </p>
                    </td>
                </tr>
            </table>
            <p>
                <label class=\"l-input-small\" >Judul</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"" . $r["judul"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
                </div>
            </p>
            <p>
                <label class=\"l-input-small\" >Keterangan</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"" . $r["keterangan"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
                </div>
            </p>
            
            <table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\" >COCD</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[cocd]\" name=\"inp[cocd]\"  value=\"" . $r["cocd"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			                </p>
                    </td>
                    <td style='width: 50%;'> 
                            <p>
                                <label class=\"l-input-small2\" >Currency</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[currency]\" name=\"inp[currency]\"  value=\"" . $r["currency"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			               </p>
                    </td>
                </tr>
            </table>
            
            <table style='width: 100%;'>
                <tr>
                    <td style='width: 50%;'>
                            <p>
                                <label class=\"l-input-small2\" >Level</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[level]\" name=\"inp[level]\"  value=\"" . $r["level"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			                </p>
                    </td>
                    <td style='width: 50%;'> 
                            <p>
                                <label class=\"l-input-small2\" >FBI</label>
                                <div class=\"field\">
                                    <input type=\"text\" id=\"inp[fbi]\" name=\"inp[fbi]\"  value=\"" . $r["fbi"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
                                </div>
			               </p>
                    </td>
                </tr>
            </table>
            <p>
                <label class=\"l-input-small\" >FST Group</label>
                <div class=\"field\">
                    <input type=\"text\" id=\"inp[fst]\" name=\"inp[fst]\"  value=\"" . $r["fst"] . "\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
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

    $setData = "`kode`  = '" . $inp["kode"] . "',
                  `grup` = '" . $inp["grup"] . "',
                  `judul` = '" . $inp["judul"] . "',
                  `keterangan` = '" . $inp["keterangan"] . "',
                  `cocd` = '" . $inp["cocd"] . "',
                  `currency` = '" . $inp["currency"] . "',
                  `level` = '" . $inp["level"] . "',
                  `fbi` = '" . $inp["fbi"] . "',
                  `fst` = '" . $inp["fst"] . "',
                  `status` = '" . $inp["status"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `account_gl`
                SET
                  $setData
                  `created_at` = now(),
                  `updated_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `account_gl`
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
        lower(judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(kode) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }




    $arrOrder = array("", "kategori", "jenis", "dokumen", "keterangan");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";


    $sql = "SELECT * from account_gl
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(id) FROM account_gl $where"),
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
            "<div align=\"center\">" . $r["kode"] . "</div>",
            "<div align=\"center\">" . $r["grup"] . "</div>",
            "<div align=\"left\">" . $r["judul"] . "</div>",
            "<div align=\"left\">" . $r["cocd"] . "</div>",
            "<div align=\"center\">" . $r["currency"] . "</div>",
            "<div align=\"left\">" . $r["level"] . "</div>",
            "<div align=\"left\">" . $r["fbi"] . "</div>",
            "<div align=\"left\">" . $r["fst"] . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from account_gl where id = '" . $par["id"] . "'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
    $judul = $arrTitle[$s];

    $field = ["NO",
        "Kode",
        "Grup",
        "Judul",
        "Cocd",
        "Currency",
        "Level",
        "FBI",
        "FST"];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(a.nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(a.judul) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(a.namaSupplier) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    $order = "id DESC";

    $sql = "SELECT * from account_gl
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $data[]=[
            $no . "\t center",
            $r["kode"] . "\t center",
            $r["grup"] . "\t center",
            $r["judul"]."\t left",
            $r["cocd"] . "\t center",
            $r['currency'] . "\t center",
            $r["level"] . "\t center",
            $r["fbi"] . "\t center",
            $r["fst"]. "\t center"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}
