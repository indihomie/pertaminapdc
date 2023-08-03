<?php

global $p, $s, $m, $menuAccess, $arrTitle, $par;

$fFileE = "files/export/";
if (empty($par[kodeProgram]))
	$par[kodeProgram] = getField("select kodeProgram from tbl_program order by urutanProgram limit 1");

function xls()

{

	global $db, $s, $inp, $par, $arrTitle, $arrParameter, $cNama, $fFile, $menuAccess, $arrParam, $fFileE;



	$direktori = $fFileE;

	$namaFile = "REPORT SUB PROGRAM.xls";

	$judul = "DATA SUB PROGRAM";

	$field = array("no",  "Judul", "Ringkasan", "Kategori", "Status");



	// $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");           
	// $sWhere= " where t2.status='".$status."'";

	$filter1 	= !empty($par['filter']) ? " AND  `kodeProgram` = '$par[filter]' " : "";


	$sql = "SELECT tbl_sub.*,tbl_program.judulProgram FROM tbl_sub INNER JOIN tbl_program ON tbl_program.kodeProgram = tbl_sub.kodeProgram where kodeSub IS NOT NULL $filter1 ";

	$res = db($sql);

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	$no = 0;

	$arrStatus = array('t' => 'Aktif', 'f' => 'Tidak Aktif');

	while ($r = mysql_fetch_array($res)) {

		$r[urutanSub] = getAngka($r[urutanSub]);

		$no++;

		$data[] = array(
			$no . "\t center",

			$r[judulSub] . "\t left",

			$r[ringkasanSub] . "\t left",

			$r[judulProgram] . "\t center",

			$arrStatus[$r[statusSub]] . "\t left"
		);
	}

	exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
}

if ($_GET["json"] == 1) {

	header("Content-type: application/json");
	$filter1 	= !empty($par['filter']) ? "AND `kodeProgram` = '$par[filter]'" : "";

	$sql = "SELECT tbl_sub.*,tbl_program.judulProgram FROM tbl_sub INNER JOIN tbl_program ON tbl_program.kodeProgram = tbl_sub.kodeProgram where kodeSub IS NOT NULL $filter1 ";


	$res = db($sql);

	$ret = array();

	while ($r = mysql_fetch_assoc($res)) {

		$r['urutanSub'] = getAngka($r['urutanSub']);

		$r["statusSub"] = ($r["statusSub"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";

		$ret[] = $r;
	}


	echo json_encode(array("sEcho" => 1, "aaData" => $ret));

	exit();
}

?>



<?php
$checkKontrolAccess = (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) ? "<th width=\"20\" style=\"vertical-align: middle\">Kontrol</th>" : "");
$checkAddAccess = (isset($menuAccess[$s]["add"]) ? "<a href=\"?par[mode]=add" . getPar($par, "mode,kodeSub") . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>" : "");

?>
<script src="sources/js/default.js"></script>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<div id="contentwrapper" class="contentwrapper">
	<form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
		<div id="pos_l" style="float:left;">
			<input type="text" id="par[filterSearch]" placeholder="Search.." name="par[filterSearch]" value="<?= $par[filterSearch] ?>" style="width:200px;" />
			<?= comboData("SELECT * from tbl_program where statusProgram='t' order by urutanProgram", "kodeProgram", "judulProgram", "par[filter]", "All Program", $par['filter'], "", "200px", "chosen-select") ?>
		</div>
		<div id="pos_r">
			<a href="?par[mode]=xls<?= getPar($par, "mode") ?>" id="btnExport" class="btn btn1 btn_inboxo">
				<span>Export</span>
			</a>
			<?= $checkAddAccess ?>
		</div>
	</form>
	<br clear="all">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20px" ; style="vertical-align:middle;">No</th>
				<th width="200px" ; style="vertical-align:middle;">Judul</th>
				<th width="*" ; style="vertical-align:middle;">Ringkasan</th>
				<th width="100px" ; style="vertical-align:middle;">Kategori Program</th>
				<th width="50px" ; style="vertical-align:middle;">Status</th>
				<?php
				if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
				?>
					<th width="20" style="vertical-align: middle">Kontrol</th>
				<?php
				}
				?>
			</tr>
		</thead>
	</table>
</div>
<style type="text/css">
	.alignRight {

		text-align: right;

	}

	.alignCenter {

		text-align: center;

	}
</style>



<script type="text/javascript">
	jQuery(document).ready(function($) {

		ot = $('#datatable').dataTable({

			"sScrollY": "100%",

			"aLengthMenu": [
				[20, 35, 70, -1],
				[20, 35, 70, "All"]
			],

			"bSort": true,

			"bFilter": true,

			"iDisplayStart": 0,

			"iDisplayLength": 20,

			"sPaginationType": "full_numbers",

			"sAjaxSource": "ajax.php?json=1<?= getPar($par, "mode, filterGroup"); ?>",

			"aoColumns": [

				{
					"mData": null,
					"sWidth": "20px",
					"bSortable": false,
					"sClass": "alignCenter"
				},

				{
					"mData": "judulSub",
					"bSortable": true
				},

				{
					"mData": "ringkasanSub",
					"bSortable": true
				},

				{
					"mData": "judulProgram",
					"bSortable": true,
					"sClass": "alignCenter"
				},

				{
					"mData": "statusSub",
					"bSortable": false,
					"sClass": "alignCenter"
				},
				{
					"mData": null,
					"sClass": "alignCenter",
					"sWidth": "80px",
					"bSortable": false,
					"fnRender": function(data) {
						var ret = '',
							kodeSub = data.aData['kodeSub'];
						<?php if (isset($menuAccess[$s]['edit'])) { ?>
							ret += "<a href=\"?par[mode]=edit&par[kodeSub]=" + kodeSub + "<?= getPar($par, "mode, kodeSub"); ?>\"  title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
						<?php } ?>
						<?php if (isset($menuAccess[$s]['edit'])) { ?>
							ret += "<a href=\"?par[mode]=del&par[kodeSub]=" + kodeSub + "<?= getPar($par, "mode, kodeSub"); ?>\"  onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						<?php } ?>

						return ret;
					}
				},

			],

			"sDom": "Rfrtlip",
			// "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				return nRow;
			},
		});

		//! Untuk Filter Search
		$("#par\\[filterSearch\\]").on('keyup', function() {
			ot.fnFilter($(this).val()).draw();
		});
		//! Untuk Filter DropDown
		$("#par\\[filter\\]").change(function() {
			var data = $(this).val();
			window.location = '?par[filter]=' + data + '	<?= getPar($par, 'filter,mode') ?>';
		});

		//! Untuk Style MENYAMBUNG TABLE, AWALNYA HEADER DAN BODY KEPOTONG
		$(".dataTables_scrollHeadInner > table").css("border-bottom", "0").css("padding-bottom", "0").css("margin-bottom", "0");
		$(".dataTables_scrollBody > table").css("border-top", "0").css("margin-top", "-5px");
	});
</script>

<script type="text/javascript">
	function joinTable() {
		jQuery(".dataTables_scrollHeadInner > table").css("border-bottom", "0").css("padding-bottom", "0").css("margin-bottom", "0");
		jQuery(".dataTables_scrollBody > table").css("border-top", "0").css("margin-top", "-5px");
	}
</script>
<?php
if ($par[mode] == "xls") {
	xls();
	echo "<iframe src=\"download.php?d=exp&f=REPORT SUB PROGRAM.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iAKTIFITASe>";
}
?>