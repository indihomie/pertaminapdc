<?php

global $p, $s, $m, $menuAccess, $arrTitle, $par;
$fFileE = "files/export/";
if ($_GET["json"] == 1) {
	header("Content-type: application/json");
	$sql = "SELECT t1.*, t2.namaUser, t2.email FROM tbl_kontak t1 join app_user t2 on (t1.username=t2.username)";
	$res = db($sql);
	$ret = array();
	while ($r = mysql_fetch_assoc($res)) {
		list($tanggalKontak, $waktuKontak) = explode(" ", $r[createTime]);
		$r["waktuKontak"] = getTanggal($tanggalKontak) . " @ " . substr($waktuKontak, 0, 5);
		$r["statusKontak"] = ($r["statusKontak"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";
		$ret[] = $r;
	}
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}
?>
<script src="sources/js/default.js"></script>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>
	<span class="pagedesc">&nbsp;</span>
</div>

<div id="contentwrapper" class="contentwrapper">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20px" ; style="vertical-align:middle;">No</th>
				<th width="200px" ; style="vertical-align:middle;">Dikirim</th>
				<th width="200px" ; style="vertical-align:middle;">Oleh</th>
				<th width="150px" ; style="vertical-align:middle;">Email</th>
				<th width="*" ; style="vertical-align:middle;">Pesan</th>
				<th width="50px" ; style="vertical-align:middle;">Status</th>
				<?php
				if (isset($menuAccess[$s]['delete'])) {
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
	jQuery(document).ready(function() {
		ot = jQuery('#datatable').dataTable({
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
					"mData": "waktuKontak",
					"bSortable": true
				},
				{
					"mData": "namaUser",
					"bSortable": true
				},

				{
					"mData": "email",
					"bSortable": true
				},

				{
					"mData": "isiKontak",
					"bSortable": true
				},

				{
					"mData": "statusKontak",
					"bSortable": false,
					"sClass": "alignCenter"
				},



				<?php if (isset($menuAccess[$s]['delete'])) { ?>

					{
						"mData": null,
						"sWidth": "80px",
						"bSortable": false,
						"sClass": "alignCenter",
						"fnRender": function(o) {

							var ret = "";

							<?php if (isset($menuAccess[$s]['delete'])) { ?>

								ret += "<a href=\"?par[mode]=del&par[kodeKontak]=" + o.aData['kodeKontak'] + "<?= getPar($par, "mode, kodeKontak"); ?>\" class=\"delete\" title=\"Delete Data\" onclick=\"return confirm('Apakah anda ingin menghapus data ini?');\"><span>Delete Data</span></a>";

							<?php } ?>

							return ret;

						}
					}

				<?php } ?>

			],

			"aaSorting": [
				[1, "asc"]
			],

			"fnInitComplete": function(oSettings) {

				oSettings.oLanguage.sZeroRecords = "No data available";

			},
			"sDom": "<'top'f>rt<'bottom'lip><'clear'>",

			"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");

				return nRow;

			},

			"bProcessing": true,

			"oLanguage": {

				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"

			}

		});



		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");

		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

		// jQuery("#datatable_wrapper #right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");

		//jQuery("#datatable_wrapper #right_panel").append("<a href=\"?par[mode]=xls<?= getPar($par, "mode") ?>\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");

		//jQuery("#datatable_wrapper #right_panel").append('&nbsp;&nbsp;<a href="?par[mode]=add<?= getPar($par, "mode") ?>" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>');


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
	echo "<iframe src=\"download.php?d=exp&f=REPORT PESAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iAKTIFITASe>";
}
?>