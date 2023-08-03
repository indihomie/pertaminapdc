<?php
header("Content-type: application/json");
include "global.php";

$output = array();
switch($par[mode]){
	case "login":
	if(!empty($username) && !empty($password)){
		$sql = "select * from app_user where username='$username' and password='".md5($password)."' and statusUser='t'";
		$res=db($sql);
		$r= mysql_fetch_array($res);
		if($r[password]!=""){
			$output = array("statusSession" => "OK", "messageSession" => "success", "userSession" => array("cUsername" => $r[username], "cNama" => $r[namaUser]));

			db("update app_user set loginUser='".date('Y-m-d H:i:s')."' where username='$username'");
		}else{
			$output = array("statusSession" => "FAIL", "messageSession" => "username / password was wrong");
		}
	}else{
		$output = array("statusSession" => "FAIL", "messageSession" => "you must fill username & password");
	}
	break;

	case "visitorList":
	$filter = "WHERE t1.idVisitor IS NOT NULL";
	if(!empty($par[filter]))
		$filter .= " AND (t1.namaVisitor LIKE '%{$par[filter]}%')";
	$sql = "
	SELECT 
	t1.*, t2.namaTenant, t2.lantaiTenant namaLantai,  t2.unitTenant namaUnit
	FROM vis_harian t1 
	JOIN ten_data t2
	ON t2.idTenant = t1.idTenant
	$filter
	ORDER BY t1.tanggalMasuk DESC;
	";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_assoc($res)){
		// unset($r[createBy], $r[createDate], $r[updateBy], $r[updateDate]);
		$ret[] = $r;
	}
	$output = array("visitors" => $ret);
	break;

	case "nomorList":
	$filter = "WHERE t1.idVisitor IS NOT NULL AND t1.tanggalKeluar = '0000-00-00 00:00:00' AND UPPER(t3.namaData) = '$par[nomorVisitor]'";
	$sql = "
	SELECT 
	t1.*, t2.namaTenant, t2.lantaiTenant namaLantai,  t2.unitTenant namaUnit, t3.namaData, t3.keteranganData
	FROM vis_harian t1 
	JOIN ten_data t2
	ON t2.idTenant = t1.idTenant
	JOIN mst_data t3 
	ON t3.kodeData = t1.kodeNomor
	$filter
	ORDER BY t1.tanggalMasuk DESC;
	";
	$res = db($sql);
	$r = mysql_fetch_assoc($res);
	$output = $r;
	break;

	case "kodeNomor":
	$sql = "
	SELECT 
	kodeData kodeNomor, namaData, keteranganData
	FROM mst_data 
	WHERE 
	kodeCategory = 'VS04'
	AND statusData = 't'
	AND UPPER(namaData) = '$par[nomorVisitor]'
	";
	// echo $sql;
	$res = db($sql);
	$r = mysql_fetch_assoc($res);
	$output = $r;
	break;

	case "tenantList":
	$sql = "SELECT idTenant, namaTenant, lantaiTenant, unitTenant FROM ten_data WHERE statusTenant = 't' ORDER BY namaTenant";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_assoc($res)){
		$ret[] = $r;
	}
	$output = array("tenants" => $ret);
	break;

	case "visitorMasuk":
	repField();
	$nextId = getField("SELECT idVisitor FROM vis_harian ORDER BY idVisitor DESC LIMIT 1")+1;
	$foto1 = uploadFileVisitor($nextId, 'foto1');
	$foto2 = uploadFileVisitor($nextId, 'foto2');
	$sql = "INSERT INTO vis_harian (idVisitor, kodeNomor, namaVisitor, keperluanVisitor, idTenant, genderVisitor, tanggalMasuk, foto1, foto2, createBy, createDate) VALUES ('$nextId', '$inp[kodeNomor]', '$inp[namaVisitor]', '$inp[keperluanVisitor]', '$inp[idTenant]', '$inp[genderVisitor]', '$inp[tanggalMasuk]', '$foto1', '$foto2', '$inp[createBy]', '$inp[createDate]');";
	db($sql);
	$output = array("statusProses" => "OK");
	break;

	case "visitorKeluar":
	repField();
	$sql = "UPDATE vis_harian SET tanggalKeluar = '$inp[tanggalKeluar]', updateBy = '$inp[updateBy]', updateDate = '$inp[updateDate]' WHERE idVisitor = '$par[idVisitor]'";
	db($sql);
	$output = array("statusProses" => $sql);
	break;

	case "validateNomor":
	$filter = "WHERE t1.idVisitor IS NOT NULL AND t1.tanggalKeluar = '0000-00-00 00:00:00' AND UPPER(t2.namaData) = '$par[namaData]'";
	$sql = "
	SELECT 
	t1.idVisitor
	FROM vis_harian t1 
	JOIN mst_data t2 
	ON t2.kodeData = t1.kodeNomor
	$filter
	ORDER BY t1.idVisitor DESC LIMIT 1
	";
	$idVisitor = getField($sql);
	$idVisitor = !empty($idVisitor) ? "FAIL" : "OK";
	$output = array("statusProses" => $idVisitor);
	break;

	default:
	$output = array("content" => "VMS RESTful v1.2 by AuliaYF");
	break;
}

function uploadFileVisitor($uniqueValue, $fileKey) {
	global $s, $inp, $par;
	$fFile = "files/visitor/";
	$fileUpload = $_FILES["$fileKey"]["tmp_name"];
	$fileUpload_name = $_FILES["$fileKey"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$fotoVisitor = "visitor-" . $fileKey . "-" . $uniqueValue . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fotoVisitor);
	}

	if (empty($fotoVisitor))
		$fotoVisitor = getField("select $fileKey from vis_harian where idVisitor='$uniqueValue'");
	return $fotoVisitor;
}

echo json_encode($output);
exit();
?>