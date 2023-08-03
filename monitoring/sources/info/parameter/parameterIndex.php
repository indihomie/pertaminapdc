<?php
global $s,$menuAccess,$par;

switch($par[mode]){
	
	case "add":
	isset($menuAccess[$s]['add']) ? include 'parameterEdit.php' : include 'parameterList.php';
	break;
	
	case "edit":
	isset($menuAccess[$s]['edit']) ? include 'parameterEdit.php' : include 'parameterList.php';
	break;
	
	case 'del':
	isset($menuAccess[$s]["delete"]) ? deleteData() : include 'parameterList.php';
	break;
	
	case "delFoto":
	isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'parameterList.php';
	break;
	
	default:
	include 'parameterList.php';
	break;
	
}


function deleteData(){
	global $s, $par, $arrParameter, $cUsername, $db, $inp;
	repField();

	db("DELETE FROM `tbl_parameter` WHERE `kodeParameter` = '$par[kodeParameter]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?".getPar($par,'mode, kodeParameter')."';
	</script>
	";

}

?>