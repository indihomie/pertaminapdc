<?php
global $s, $menuAccess, $par;

switch ($par[mode]) {

	case "add":
		isset($menuAccess[$s]['add']) ? include 'faqEdit.php' : include 'faqList.php';
		break;

	case "edit":
		isset($menuAccess[$s]['edit']) ? include 'faqEdit.php' : include 'faqList.php';
		break;

	case 'del':
		isset($menuAccess[$s]["delete"]) ? deleteData() : include 'faqList.php';
		break;

		// case "delFoto":
		// isset($menuAccess[$s]['delete']) ? hapusFoto() : include 'faqList.php';
		// break;

	default:
		include 'faqList.php';
		break;
}


function deleteData()
{
	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;
	repField();
	db("DELETE FROM `tbl_faq` WHERE `kodeFaq` = '$par[kodeFaq]'");
	echo "
	<script type=\"text/javascript\">
		window.location.href='?" . getPar($par, 'mode, kodeFaq') . "';
	</script>
	";
}
