<?php
global $s,$menuAccess,$par;
$fFoto = "files/sub/";

switch($par[mode]){
	
	case "add":
	isset($menuAccess[$s]['add']) ? include 'subEdit.php' : include 'subList.php';
	break;
	
	case "edit":
	isset($menuAccess[$s]['edit']) ? include 'subEdit.php' : include 'subList.php';
	break;
	
	case 'del':
	isset($menuAccess[$s]["delete"]) ? deleteData() : include 'subList.php';
	break;
	
	case "delFoto":
	isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'subList.php';
	break;
	
	default:
	include 'subList.php';
	break;
	
}


function deleteData(){
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	$file = getField("SELECT `fotoSub` FROM `tbl_sub` WHERE `kodeSub` = '$par[kodeSub]'");

	if (file_exists($fFoto . $file) and $file != "") unlink($fFoto . $file);

	db("DELETE FROM `tbl_sub` WHERE `kodeSub` = '$par[kodeSub]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?".getPar($par,'mode, kodeSub')."';
	</script>
	";

}

function hapusFoto() {
	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT `fotoSub` FROM `tbl_sub` WHERE `kodeSub` = '$par[kodeSub]'");

	if (file_exists($fFoto . $file) and $file != "")
		unlink($fFoto . $file);

	$sql = "UPDATE `tbl_sub` SET `fotoSub` = '' WHERE `kodeSub` = '$par[kodeSub]'";
	db($sql);

	echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeSub]=$par[kodeSub]".getPar('kodeSub')."';
	</script>";		
}

?>