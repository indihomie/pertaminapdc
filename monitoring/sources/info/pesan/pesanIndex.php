<?php
global $s,$menuAccess,$par;
$fFoto = "files/pesan/";

switch($par[mode]){
	
	case 'del':
	isset($menuAccess[$s]["delete"]) ? deleteData() : include 'pesanList.php';
	break;
	
	default:
	include 'pesanList.php';
	break;
	
}


function deleteData(){
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();

	db("DELETE FROM `tbl_kontak` WHERE `kodeKontak` = '$par[kodeKontak]'");

	echo "
	<script type=\"text/javascript\">
		window.location.href='?".getPar($par,'mode, kodeKontak')."';
	</script>
	";

}


?>