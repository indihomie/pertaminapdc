<?php
global $s,$menuAccess,$par;
$fFoto = "files/program/";

switch($par[mode]){
	
	case "add":
	isset($menuAccess[$s]['add']) ? include 'programEdit.php' : include 'programList.php';
	break;
	
	case "edit":
	isset($menuAccess[$s]['edit']) ? include 'programEdit.php' : include 'programList.php';
	break;
	
	case 'del':
	isset($menuAccess[$s]["delete"]) ? deleteData() : include 'programList.php';
	break;
	
	case "delFoto":
	isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'programList.php';
	break;
	
	default:
	include 'programList.php';
	break;
	
}


function deleteData(){
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	$file = getField("SELECT `fotoProgram` FROM `tbl_program` WHERE `kodeProgram` = '$par[kodeProgram]'");

	if (file_exists($fFoto . $file) and $file != "") unlink($fFoto . $file);

	db("DELETE FROM `tbl_program` WHERE `kodeProgram` = '$par[kodeProgram]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?".getPar($par,'mode, kodeProgram')."';
	</script>
	";

}

function hapusFoto() {
	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT `fotoProgram` FROM `tbl_program` WHERE `kodeProgram` = '$par[kodeProgram]'");

	if (file_exists($fFoto . $file) and $file != "")
		unlink($fFoto . $file);

	$sql = "UPDATE `tbl_program` SET `fotoProgram` = '' WHERE `kodeProgram` = '$par[kodeProgram]'";
	db($sql);

	echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeProgram]=$par[kodeProgram]".getPar('kodeProgram')."';
	</script>";		
}

?>