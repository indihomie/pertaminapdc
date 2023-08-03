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

        case "detailForm":
            $text = detailForm();
            break;
    }

    return $text;
}


function lihat()
{
    global $s, $par, $arrTitle;

    $text = table(4, array(4));

    $combo1 = empty($combo1) ? 1203 : $combo1;

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
				
				" . comboData("select * from mst_data where kodeCategory = 'MDAS' order BY urutanData asc", "kodeData", "namaData", "combo1", "", $combo1, "", "250px", "chosen-select") . "
                &nbsp;
			</div>

			<div id=\"pos_r\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
            </div>

            
            </form>
            <br clear=\"all\" />


		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th width=\"20\">No</th>
                    <th width=\"*\">Cost Center</th>
                    <th width=\"200\">Approval</th>
                    <th width=\"20\">Kontrol</th>
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

    $id_sbu = getField("select sbu from costcenter_data where id = '$par[id_cc]'");

    $r = getRow("select * from approval_setting where id = $par[id]");

    setValidation("is_null", "inp[kategori]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[id_cc]", "anda harus mengisi cc");
    setValidation("is_null", "inp[id_pegawai]", "anda harus mengisi pegawai");
    $text = getValidation();

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . strtoupper($arrTitle[$s]) . "</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
                <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode, id") . "');\"/>
				</p>
			</div>
			
			<fieldset>
            
             <p>
                <label class=\"l-input-small\">Kategori</label>
                <div class=\"field\"> 
                    " . comboData("select * from mst_data where kodeCategory = 'MDAS' order BY urutanData asc", "kodeData", "namaData", "inp[kategori]", "- Pilih Kategori -", $par["kategori"], "", "210px", "chosen-select", "disabled") . "
                </div>
                <style>
                    #inp_kategori__chosen{ min-width:210px; }
                </style>
            </p>
            
			<p>
                <label class=\"l-input-small\">Cost Center</label>
                <div class=\"field\">
                    " . comboData("select * from costcenter_data order BY nama asc", "id", "nama", "inp[id_cc]", "- Pilih CC -", $par["id_cc"], "", "210px", "chosen-select", "disabled") . "
                </div>
                <style>
                    #inp_id_cc__chosen{ min-width:210px; }
                </style>
            </p>
            
            <p>
                <label class=\"l-input-small\">Nama Pegawai</label>
                <div class=\"field\">
                    " . comboData("select * from pegawai_data where unit = $id_sbu order BY id asc", "id", "nama", "inp[id_pegawai]", "- Pilih Pegawai -", $r["id_pegawai"], "", "210px", "chosen-select") . "
                </div>
                <style>
                    #inp_id_pegawai__chosen{ min-width:210px; }
                </style>
            </p>
                    
                    
                
			</fieldset>
            
		</form>
	</div>";

    return $text;
}

function simpan()
{
    global $inp, $par, $cID;

    $setData = "`kategori`  = '" . $par[kategori] . "',
                  `id_cc` = '" . $par[id_cc] . "',
                  `id_pegawai` = '" . $inp["id_pegawai"] . "',";

    if (empty($par["id"])) {

        $sql = "INSERT
                  `approval_setting`
                SET
                  $setData
                  `created_at` = now(),
                  `updated_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `approval_setting`
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

    $where = " WHERE 1 = 1";

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);

    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (  
        lower(nama) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(id) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) {
        $filterApproval = " and kategori = '" . $_GET['combo1'] . "'";

    } else {
        $filterApproval = "";
    }


    $arrOrder = array("", "kategori", "id_cc", "id_pegawai");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id ASC";


    $sql = "SELECT id as id_cc, nama from costcenter_data
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(id) from costcenter_data $where"),
        "aaData" => array()
    );


    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id_cc]=" . $r["id_cc"] ."&par[kategori]=".$_GET['combo1']. getPar($par, "mode, id, kategori") . "\" class=\"edit\"><span>Detail</span></a>";

        $approval = "";
        $getApproval = getRows("select * from approval_setting where id_cc = '" . $r["id_cc"] . "' {$filterApproval} ");
        if ($getApproval) {
            $noApproval = 0;
            foreach ($getApproval as $appr) {
                $noApproval++;
                $nama = getField("select nama from pegawai_data where id = '" . $appr["id_pegawai"] . "'");
                $approval .= "$noApproval - $nama <br>";
            }
        }

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r["nama"] . "</div>",
            "<div align=\"left\">" . $approval . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from approval_setting where id = '" . $par["id"] . "'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])) . ".xls";
    $judul = $arrTitle[$s];

    $field = ["NO",
        "Bisnis Unit",
        "Approval"];

    $where = " WHERE kodeCategory = 'KSBU'";

    if (!empty($par['fSearch'])) {
        $where .= " and (  
        lower(namaData) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(kodeData) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    if (!empty($par['combo1'])) {
        $filterApproval = " and kategori = '" . $par['combo1'] . "'";

    } else {
        $filterApproval = "";
    }

    $order = "urutanData ASC";

    $sql = "SELECT kodeData, namaData from mst_data
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $approval = "";
        $getApproval = getRows("select * from approval_setting where id_sbu = '" . $r["kodeData"] . "' {$filterApproval} ");
        if ($getApproval) {
            $noApproval = 0;
            foreach ($getApproval as $appr) {
                $noApproval++;
                $nama = getField("select nama from pegawai_data where id = '" . $appr["id_pegawai"] . "'");
                $approval .= "$noApproval - $nama \n ";
            }
        }

        $data[] = [
            $no . "\t center",
            $r["namaData"] . "\t left",
            $approval . "\t center"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 3, $field, $data);
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess;

    $sbu = getField("select kodeData from mst_data where kodeData = (select sbu from costcenter_data where id = '" . $par['id_cc'] . "')");
    $cc = getField("select nama from costcenter_data where id = '" . $par['id_cc'] . "'");
    $kategori = getField("select namaData from mst_data where kodeData = '". $par['kategori'] ."'");

    $text .= "
    
    
    <div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . strtoupper($arrTitle[$s]) . "</h1>
	</div>
    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
            <p style=\"position: absolute; right: 20px; top: 10px;\">
		        " . (!$popup ? "<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_cc") . "';\"/>" : "") . "
			</p>
			
			<br>
            <fieldset>
                <p>
                    <label class=\"l-input-small\" >Kategori</label>
                    <span class=\"field\">
                        " . $kategori . "
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\" >Cost Center</label>
                    <span class=\"field\">
                        " . $cc . "
                    </span>
                </p>
            </fieldset>
            <br>
            
             <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>APPROVAL</h3>
                    ";
    if (isset($menuAccess[$s]["add"])) $text .= "<a href=\"#\" style=\"float:right; margin-top:-30px; margin-right:-20px;\" onclick=\"openBox('popup.php?par[mode]=add&par[kodeData]=".$sbu . getPar($par, "mode, kodeData") . "', 650, 250); \" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>";
    $text .= "
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th  style=\"vertical-align: middle; \" width=\"20\">No</th>
                        <th  style=\"vertical-align: middle; \" width=\"*\">Nama</th>
                        <th  style=\"vertical-align: middle; \" width=\"40\">Nik</th>
                        <th  style=\"vertical-align: middle; \" width=\"40\">Jabatan</th>
                        <th  style=\"vertical-align: middle; \" width=\"200\">No Hp</th>
                        <th  style=\"vertical-align: middle; \" width=\"75\">Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    ";
    $getData = getRows("select a.*, b.nama, b.nik, b.jabatan, b.nohp
                    from approval_setting as a
                    join pegawai_data as b on (b.id = a.id_pegawai)
                    where a.id_cc = '" . $par['id_cc'] . "' and a.kategori = '". $par['kategori'] ."'  order by id asc");
    if ($getData) {

        $no = 0;
        foreach ($getData as $data) {

            $no++;

            $kontrol = "";
            if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=".$data["id"]."&par[kodeData]=$sbu" . getPar($par, "mode, id, kodeData") . "', 650, 280);\" class=\"edit\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $data["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

            $text .= "
                            <tr>
                                <td align=\"center\">" . $no . "</td>
                                <td align=\"left\">" . $data['nama'] . "</td>
                                <td align=\"center\">" . $data['nik'] . "</td>
                                <td align=\"center\">" . $data['jabatan'] . "</td>
                                <td align=\"left\">" . $data['nohp'] . "</td>
                                <td align=\"center\">" . $kontrol . "</td>
                            </tr>
                            ";
        }

    } else {

        $text .= "
                        <tr>
                            <td colspan=\"7\"><strong><center>- Data Kosong -</center></strong></td>
                        </tr>
                        ";

    }
    $text .= "
                </tbody>
            </table>
		</form>
		
		
    </div>";

    return $text;
}