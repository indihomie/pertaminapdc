<?php
global $s, $menuAccess, $par;
$fFoto = "files/emergency/";

switch ($par[mode]) {

	case "add":
		isset($menuAccess[$s]['add']) ? include 'emergencyEdit.php' : include 'emergencyList.php';
		break;

	case "edit":
		isset($menuAccess[$s]['edit']) ? include 'emergencyEdit.php' : include 'emergencyList.php';
		break;

	case 'del':
		isset($menuAccess[$s]["delete"]) ? deleteData() : include 'emergencyList.php';
		break;

	case "delFoto":
		isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'emergencyList.php';
		break;

	default:
		include 'emergencyList.php';
		break;
}


function deleteData()
{
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	$file = getField("SELECT `fotoEmergency` FROM `tbl_emergency` WHERE `kodeEmergency` = '$par[kodeEmergency]'");

	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	db("DELETE FROM `tbl_emergency` WHERE `kodeEmergency` = '$par[kodeEmergency]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?" . getPar($par, 'mode, kodeEmergency') . "';
	</script>
	";
}

function hapusFoto()
{
	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT `fotoEmergency` FROM `tbl_emergency` WHERE `kodeEmergency` = '$par[kodeEmergency]'");
	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	$sql = "UPDATE `tbl_emergency` SET `fotoEmergency` = '' WHERE `kodeEmergency` = '$par[kodeEmergency]'";
	db($sql);

	echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeEmergency]=$par[kodeEmergency]" . getPar('kodeEmergency') . "';
	</script>";
}
