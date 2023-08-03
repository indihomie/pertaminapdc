<?php
global $s, $menuAccess, $par;
$fFoto = "files/berita/";

switch ($par[mode]) {

	case "add":
		isset($menuAccess[$s]['add']) ? include 'beritaEdit.php' : include 'beritaList.php';
		break;

	case "edit":
		isset($menuAccess[$s]['edit']) ? include 'beritaEdit.php' : include 'beritaList.php';
		break;

	case 'del':
		isset($menuAccess[$s]["delete"]) ? deleteData() : include 'beritaList.php';
		break;

	case "delFoto":
		isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'beritaList.php';
		break;

	default:
		include 'beritaList.php';
		break;
}


function deleteData()
{
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	$file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");
	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	db("DELETE FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?" . getPar($par, 'mode, kodeBerita') . "';
	</script>
	";
}

function hapusFoto()
{
	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT `fotoBerita` FROM `tbl_berita` WHERE `kodeBerita` = '$par[kodeBerita]'");
	$fileSame = str_replace(".", "same.", $file);
	$fileThumb = str_replace(".", "thumb.", $file);
	if (file_exists($fFoto . $file) and $file != "") {
		unlink($fFoto . $file);
		unlink($fFoto . $fileSame);
		unlink($fFoto . $fileThumb);
	}

	$sql = "UPDATE `tbl_berita` SET `fotoBerita` = '' WHERE `kodeBerita` = '$par[kodeBerita]'";
	db($sql);

	echo "
	<script>
		window.parent.location = 'index.php?par[mode]=edit&par[kodeBerita]=$par[kodeBerita]" . getPar('kodeBerita') . "';
	</script>";
}
