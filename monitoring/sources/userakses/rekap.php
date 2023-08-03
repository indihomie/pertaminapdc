<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function getContent($par)
{
	global $s,$_submit,$menuAccess;
    
	switch($par[mode])
    {
		case "lst":
		  $text=lData();
		break;	
        
		default:
		  $text = lihat();
		break;
	}
    
	return $text;
}	

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;
    
	$cols=9;
	$text = table($cols);
    
    $combo1 = empty($combo1) ? date("m") : $combo1;
    $combo2 = empty($combo2) ? date("Y") : $combo2;

	$text.="<div class=\"pageheader\">
            	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
            	".getBread()."
            	<span class=\"pagedesc\">&nbsp;</span>
            </div>    

    <div id=\"contentwrapper\" class=\"contentwrapper\">
    
    	<form action=\"\" method=\"post\" id = \"form\" class=\"stdform\" onsubmit=\"return false;\">
    
    		<div id=\"pos_l\" style=\"float:left;\">
    			<p>					
    				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
                    ".comboMonth("combo1", $combo1, "", "", "")."
                    ".comboYear("combo2", $combo2)."
    			</p>
    		</div>	
    		<div id=\"pos_r\" style=\"float:right;\">
    		";
    		if(isset($menuAccess[$s]["add"])) $text.="<a onclick=\"openBox('popup.php?par[mode]=add". getPar($par, "mode") . "',825,500);\"  href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
    		$text.="
    			<a href=\"?par[mode]=xls2" . getPar($par, "mode") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
    		</div>
    	</form>

	   <br clear=\"all\" />

	   <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
		  <thead>
			<tr>
				<th width=\"20\">No</th>
				<th width=\"*\">User</th>
				<th width=\"200\">Group</th>
                <th width=\"100\">Login</th>
				<th width=\"100\">Open Page</th>
				<th width=\"100\">View Detail</th>
				<th width=\"100\">Input Data</th>
				<th width=\"100\">Edit Data</th>
                <th width=\"100\">Delete Data</th>
             </tr>
			</thead>

			<tbody></tbody>
		</table>

	</div>";
    
	$sekarang = date('Y-m-d');
	if($par[mode] == "xls2"){
		xls2();			
		$text.="<iframe src=\"download.php?d=exp&f=exp-".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	return $text;

}

	

function lData()
{

	global $s,$par,$fFoto,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
    
	if($_GET[json]==1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') $sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	
    $filters = "WHERE username IS NOT NULL";

	if (!empty($_GET['fSearch'])) $filters.= " and (
                                                lower(namaUser) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
                                                or
                                                lower(namaGroup) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
                                                )";

    $arrOrder = array(	
            		"namaUser",
            		"namaGroup",
                    "login",
            		"open_page",
            		"view_data",
            		"input_data",
            		"edit_data",
            		"delete_data"
            	);


	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql = "SELECT * FROM
            (
                SELECT 
                    a.username, 
                    a.namaUser, 
                    b.namaGroup,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'login' and month(createTime) = '$_GET[combo1]' and year(createTime) = '$_GET[combo2]') AS login,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'open page' and month(createTime) = '$_GET[combo1]' and year(createTime) = '$_GET[combo2]') AS open_page,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'view detail' and month(createTime) = '$_GET[combo1]' and year(createTime) = '$_GET[combo2]') AS view_data,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'input data' and month(createTime) = '$_GET[combo1][combo1]' and year(createTime) = '$_GET[combo2]') AS input_data,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'edit data' and month(createTime) = '$_GET[combo1]' and year(createTime) = '$_GET[combo2]') AS edit_data,
                    (SELECT COUNT(kodeLog) FROM log_access WHERE createBy = a.username AND aktivitasLog = 'delete data' and month(createTime) = '$_GET[combo1]' and year(createTime) = '$_GET[combo2]') AS delete_data
                FROM app_user AS a
                JOIN app_group AS b ON (b.kodeGroup = a.kodeGroup)
            ) 
            AS X $filters order by $orderBy $sLimit ";

	$res = db($sql);

	$json = array(
        		"iTotalRecords" => mysql_num_rows($res),
        		"iTotalDisplayRecords" => getField("SELECT COUNT(*) from app_user $filters"),
        		"aaData" => array(),
    		);

	$no = intval($_GET['iDisplayStart']);

	while($r=mysql_fetch_array($res))
    {
		$no++;

		$data = array(
        			"<div align=\"center\">".$no.".</div>",	
        			"<div align=\"left\">$r[namaUser]</div>",
        			"<div align=\"left\">$r[namaGroup]</div>",
                    "<div align=\"right\">".getAngka($r[login])."</div>",
        			"<div align=\"right\">".getAngka($r[open_page])."</div>",
        			"<div align=\"right\">".getAngka($r[view_data])."</div>",
        			"<div align=\"right\">".getAngka($r[input_data])."</div>",
        			"<div align=\"right\">".getAngka($r[edit_data])."</div>",
        			"<div align=\"right\">".getAngka($r[delete_data])."</div>",
			     );
		$json['aaData'][]=$data;


	}

	return json_encode($json);

}

?>