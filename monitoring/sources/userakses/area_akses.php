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

        case "detailForm":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detailForm() : simpan();
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

    $text = table(6, array(4, 5, 6));
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
                " . comboData("select * from app_group order BY namaGroup asc", "kodeGroup", "namaGroup", "combo1", "All", $combo1, "", "250px", "chosen-select") . "
                <style>#combo1_chosen{min-width:250px;}</style>
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
                    <th width=\"250\">Nama</th>
                    <th width=\"100\">User</th>
                    <th width=\"150\">Group</th>
                    <th width=\"100\">Area</th>
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

function simpan()
{
    global $inp, $par, $cID, $det_;
    $sql = "delete from app_user_akses where id_user = '" . $par["id"] . "'";
    db($sql);
    foreach ($det_ as $key => $value) {
        $setData = "`id_user`  = '" . $par["id"] . "',
                  `id_cc` = '" . $value . "',";

        $sql = "INSERT
                  `app_user_akses`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";


        db($sql);
    }

    echo "<script>
    alert('Data telah tersimpan'); reloadPage(); </script>";
}



function detailForm()
{
    global $inp, $s, $par, $arrTitle, $menuAccess;
    $par[kodeCategory] = $arrParam[$s];
    $kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='" . $arrParam[$s] . "'");


    $r = getRow("SELECT
    a.id, a.idPegawai, a.namaUser, a.username, b.namaGroup, d.kodeData, d.namaData
    FROM app_user AS a
    JOIN app_group AS b ON (b.kodeGroup = a.kodeGroup)
    LEFT JOIN pegawai_data AS c ON (c.id = a.idPegawai)
    LEFT JOIN mst_data AS d ON (d.kodeData = c.unit)
    
    WHERE a.id = '$par[id]'");

    $costcenter = "";
        $getCc = getRows("select * from costcenter_data where sbu = '" . $r["kodeData"] . "'");
        if ($getCc) {
            $nocc = 0;
            foreach ($getCc as $cc) {
                $nocc++;
                $nama = getField("select nama from costcenter_data where id = '" . $cc["id"] . "'");
                $costcenter .= "$nocc - $nama <br>";
            }
        }

    $arrValue = arrayQuery("SELECT id_cc, id FROM app_user_akses WHERE id_user = '$par[id]' ");

    if (!empty($_GET['fSearch'])) {
        $where .= " and (  
    lower(namaData) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
    or
    lower(keterangan) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
    )";
    }

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread(ucwords(str_replace("Detail", "", $par["mode"]))) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		    <p style=\"position: absolute; right: 20px; top: 10px;\">
		        <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</p>
			
			<br>
			
			<fieldset>
			    <legend>User</legend>
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nama</label>
                                <span class=\"field\">  
                                    " . $r["namaUser"] . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Bisnis Unit</label>
                                <span class=\"field\">
                                    " . $r["namaData"] . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">User</label>
                                <span class=\"field\">  
                                    " . $r["username"] . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                             <p>
                                <label class=\"l-input-small2\">Cost Center</label>
                                <span class=\"field\" style='text-align: right'>  
                                    " . $costcenter . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                 <p>
                    <label class=\"l-input-small\">Group</label>
                    <span class=\"field\">  
                      " . $r["namaGroup"] . " &nbsp;
                    </span>
                </p>
                
               
                
            </fieldset>
            <br>
            
            <div class=\"widgetbox\" style=\"margin-top:-20px; margin-bottom:0px;\">
                <div class=\"title\">
                    <h3>MAPPING</h3>
                </div>
            </div>
            <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
            <div id=\"pos_l\" style=\"float:left;\">
           
        </div>

                <div style=\"float:right; top:20px; right:20px;\">
                    <input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
                </div>
            
            <br clear=\"all\" />

            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:10px;\">
                <thead>
                    <tr>
                        <th width=\"20\">No</th>
                        <th width=\"250\">Cost Center</th>
                        <th width=\"50\">Pilih</th>
                    </tr>
                </thead>
                <tbody>
                    ";


    $getData = getRows("select * from costcenter_data order by id asc");
    if ($getData) {

        $no = 0;
        foreach ($getData as $data) {

            $no++;

            $kontrol = "";

            $checked = $arrValue[$data['id']] ? "checked=\"checked\"" : "";

            $checklist = "
                                            <input type=\"checkbox\" id=\"det_[" . $data['id'] . "]\" name=\"det_[" . $data['id'] . "]\" value=\"" . $data['id'] . "\" $checked />
                                            ";
            $text .= "
                                    <tr>
                                        <td align=\"center\">" . $no . "</td>
                                        <td align=\"left\">" . $data['nama'] . "</td>
                                        <td align=\"center\">" . $checklist . "</td>
                                    </tr>
                                    ";
        }
    } else {

        $text .= "
                                <tr>
                                    <td colspan=\"3\"><strong><center>- Data Kosong -</center></strong></td>
                                </tr>
                                ";
    }
    $text .= "
                </tbody>
            </table>
            </form>
         
		</form>
		
		
    </div>";

    return $text;
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
        lower(namaUser) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        or
        lower(username) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
        )";
    }

    if (!empty($_GET['combo1'])) {
        $where .= " and a.kodeGroup = '$_GET[combo1]'";
    }

    $arrOrder = array("", "namaUser", "username", "jenis", "kodeGroup", "sbu");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";

    $sql = "SELECT
    a.id, a.namaUser, a.username, b.namaGroup
    FROM app_user AS a
    JOIN app_group AS b ON (b.kodeGroup = a.kodeGroup)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(id) FROM app_user $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $statusUser = $r[statusUser] == "t" ?
            "<img src=\"styles/images/t.png\" title='Active'>" :
            "<img src=\"styles/images/f.png\" title='Not Active'>";

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=detailForm&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" class=\"edit\"><span>Detail</span></a>";


        $jmlcc = getField("SELECT count(id) FROM app_user_akses WHERE id_user = $r[id]");

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"left\">" . $r['namaUser'] . "</div>",
            "<div align=\"center\">" . $r["username"] . "</div>",
            "<div align=\"left\">" . $r["namaGroup"] . "</div>",
            "<div align=\"right\">" . getAngka($jmlcc) . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function hapus()
{
    global $par;

    db("delete from app_user_ where id = '" . $par["id"] . "'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function xls()
{
    global $r, $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])) . ".xls";
    $judul = $arrTitle[$s];

    $field = [
        "NO",
        "Nama",
        "Username",
        "Jenis",
        "Group",
        "SBU"
    ];

    $where = " WHERE 1 = 1";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(namaUser) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        or
        lower(username) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
        )";
    }

    $order = "id DESC";


    $sql = "SELECT
    a.*, b.namaGroup, c.jenis, d.namaData AS namaJenis
    FROM app_user AS a
    JOIN app_group AS b ON (b.kodeGroup = a.kodeGroup)
    JOIN pegawai_data AS c ON (c.id = a.idPegawai)
    JOIN mst_data AS d ON (d.kodeData = c.jenis)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {


        $no++;

        $jmlsbu = getField("SELECT count(id) FROM app_user_akses WHERE id_user = $r[id]");

        // $appr = "Menunggu Persetujuan";
        // if ($r["approve_status"] == "t") $appr = "Setuju";
        // if ($r["approve_status"] == "f") $appr = "Tolak";
        // if ($r["approve_status"] == "p") $appr = "Pending";

        $data[] = [
            $no . "\t center",
            $r["namaUser"] . "\t left",
            $r["username"] . "\t center",
            $r['namaJenis'] . "\t left",
            $r['namaGroup'] . "\t left",
            getAngka($jmlsbu) . "\t right"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}
