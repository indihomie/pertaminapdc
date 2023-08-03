<?php

global $p, $s, $m, $menuAccess, $arrTitle, $par;

$fFileE = "files/export/";

function xls()
{
	global $db, $s, $inp, $par, $arrTitle, $arrParameter, $cNama, $fFile, $menuAccess, $arrParam, $fFileE;
	$direktori = $fFileE;
	$namaFile = "REPORT EMERGENCY.xls";
	$judul = "DATA EMERGENCY";
	$field = array("no",  "Judul", "Tanggal", "Ringkasan", "Status");
	// $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");           
	// $sWhere= " where t2.status='".$status."'";
	$filter1 	= !empty($par['filter']) ? "AND `kategoriEmergency` = '$par[filter]'" : "";
	$sql = "SELECT * FROM tbl_emergency WHERE kodeEmergency IS NOT NULL $filter1 ";
	// var_dump($sql);
	// die();
	$res = db($sql);
	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
	$no = 0;
	$arrStatus = array('t' => 'Aktif', 'f' => 'Tidak Aktif');
	while ($r = mysql_fetch_array($res)) {
		$r[tanggalEmergency] = getTanggal($r[tanggalEmergency]);
		$no++;
		$data[] = array(
			$no . "\t center",
			$r[judulEmergency] . "\t left",
			$r[tanggalEmergency] . "\t center",
			$r[ringkasanEmergency] . "\t left",
			$arrStatus[$r[statusEmergency]] . "\t left"
		);
	}
	exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
}

if ($_GET["json"] == 1) {

	header("Content-type: application/json");
	$filter1 	= !empty($par['filter']) ? "AND `kategoriEmergency` = '$par[filter]'" : "";

	$sql = "SELECT * FROM tbl_emergency WHERE kodeEmergency IS NOT NULL $filter1";
	$res = db($sql);
	$ret = array();
	while ($r = mysql_fetch_assoc($res)) {
		$r['tanggalEmergency'] = getTanggal($r['tanggalEmergency']);
		$r["statusEmergency"] = ($r["statusEmergency"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";
		$ret[] = $r;
	}
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}
?>

<?php
$checkKontrolAccess = (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) ? "<th width=\"20\" style=\"vertical-align: middle\">Kontrol</th>" : "");
$checkAddAccess = (isset($menuAccess[$s]["add"]) ? "<a href=\"?par[mode]=add" . getPar($par, "mode,kodeEmergency") . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>" : "");

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
			<?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='KIP'", "kodeData", "namaData", "par[filter]", "All Kategori", $par['filter'], "", "200px", "chosen-select") ?>
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
				<th width="50px" style="vertical-align:middle;">Foto</th>
				<th width="200px" ; style="vertical-align:middle;">Judul</th>
				<th width="100px" ; style="vertical-align:middle;">Tanggal</th>
				<th width="*" ; style="vertical-align:middle;">Ringkasan</th>
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
	var fFile = "files/emergency/";
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
			"aoColumns": [{
					"mData": null,
					"sWidth": "20px",
					"bSortable": false,
					"sClass": "alignCenter"
				},
				{
					"bSortable": false,
					"mData": "fotoEmergency",
					"mRender": function(data) {
						var gambarKecil = data.replace('.', 'thumb.');
						return '<img src="' + fFile + gambarKecil + '" height="42" width="42" />';
					},
					"sClass": "alignCenter"

				},
				{
					"mData": "judulEmergency",
					"bSortable": true
				},

				{
					"mData": "tanggalEmergency",
					"bSortable": true,
					"sClass": "alignCenter"
				},

				{
					"mData": "ringkasanEmergency",
					"bSortable": true
				},

				{
					"mData": "statusEmergency",
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
							kodeEmergency = data.aData['kodeEmergency'];
						<?php if (isset($menuAccess[$s]['edit'])) { ?>
							ret += "<a href=\"?par[mode]=edit&par[kodeEmergency]=" + kodeEmergency + "<?= getPar($par, "mode, kodeEmergency"); ?>\"  title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
						<?php } ?>
						<?php if (isset($menuAccess[$s]['edit'])) { ?>
							ret += "<a href=\"?par[mode]=del&par[kodeEmergency]=" + kodeEmergency + "<?= getPar($par, "mode, kodeEmergency"); ?>\"  onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
<?php
if ($par[mode] == "xls") {
	xls();
	echo "<iframe src=\"download.php?d=exp&f=REPORT EMERGENCY.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}
?>