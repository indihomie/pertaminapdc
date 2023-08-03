<?php
global $s, $menuAccess, $par;
$fFoto = "files/event/";

switch ($par[mode]) {

	case "add":
		isset($menuAccess[$s]['add']) ? include 'eventEdit.php' : include 'eventList.php';
		break;

	case "edit":
		isset($menuAccess[$s]['edit']) ? include 'eventEdit.php' : include 'eventList.php';
		break;

	case 'del':
		isset($menuAccess[$s]["delete"]) ? deleteData() : include 'eventList.php';
		break;

	case "delFoto":
		isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'eventList.php';
		break;

	default:
		include 'eventList.php';
		break;
}


function deleteData()
{
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	$file = getField("SELECT `fotoEvent` FROM `tbl_event` WHERE `kodeEvent` = '$par[kodeEvent]'");

	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	db("DELETE FROM `tbl_event` WHERE `kodeEvent` = '$par[kodeEvent]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?" . getPar($par, 'mode, kodeEvent') . "';
	</script>
	";
}

function hapusFoto()
{
	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT `fotoEvent` FROM `tbl_event` WHERE `kodeEvent` = '$par[kodeEvent]'");
	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	$sql = "UPDATE `tbl_event` SET `fotoEvent` = '' WHERE `kodeEvent` = '$par[kodeEvent]'";
	db($sql);

	echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeEvent]=$par[kodeEvent]" . getPar('kodeEvent') . "';
	</script>";
}
