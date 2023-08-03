<?php

global $p,$s,$m,$menuAccess,$arrTitle,$par,$arrParam;

$fFileE = "files/export/";

function xls()

{        

    global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrParam,$fFileE;

    

    $direktori = $fFileE;

    $namaFile = "REPORT PARAMETER.xls";

    $judul = "DATA PARAMETER";

    $field = array("no",  "Judul", "Tanggal", "Sumber","Status");

    

    // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");           
    // $sWhere= " where t2.status='".$status."'";

    

    $sql = "SELECT * FROM tbl_parameter";
    
    $res=db($sql);

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $no = 0;

    $arrStatus =array('t' =>'Aktif' , 'f' => 'Tidak Aktif');

    while($r=mysql_fetch_array($res))

    {

       $r[tanggalParameter] = getTanggal($r[tanggalParameter]);

        $no++;

        $data[] = array($no ."\t center" , 

            $r[judulParameter] ."\t left", 

            $r[tanggalParameter] ."\t center", 

            $r[waktuParameter]."<br>".$r[lokasiParameter] ."\t left", 


            $arrStatus[$r[statusParameter]] ."\t left");

    }
    
    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);

}

if ($_GET["json"] == 1) {

	header("Content-type: application/json");

	$sql = "SELECT * FROM tbl_parameter where tipeParameter='".$arrParam[$s]."' order by urutanParameter";

	$res = db($sql);

	$ret = array();

	while ($r = mysql_fetch_assoc($res)) {

		
		$r["statusParameter"] = ($r["statusParameter"] == "t") ? "<img src=\"styles/images/t.png\" title='Tampil'>" : "<img src=\"styles/images/f.png\" title='Tidak Tampil'>";

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

				<th width="20px"; style="vertical-align:middle;">No</th>

				<th width="100px"; style="vertical-align:middle;">Urutan</th>
				<th width="*"; style="vertical-align:middle;">Keterangan</th>
				
				<th width="50px"; style="vertical-align:middle;">Status</th>

				<?php

				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){

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

	.alignRight{

		text-align: right;

	}	

	.alignCenter{

		text-align: center;

	}

</style>



<script type="text/javascript">

	jQuery(document).ready(function () {

		ot = jQuery('#datatable').dataTable({

			"sScrollY": "100%",

			"aLengthMenu": [[20, 35, 70, -1], [20, 35, 70, "All"]],

			"bSort": true,

			"bFilter": true,

			"iDisplayStart": 0,

			"iDisplayLength": 20,

			"sPaginationType": "full_numbers",

			"sAjaxSource": "ajax.php?json=1<?= getPar($par, "mode, filterGroup"); ?>",

			"aoColumns": [

			{"mData": null, "sWidth": "20px", "bSortable": false, "sClass": "alignCenter"},			

			{"mData": "urutanParameter", "bSortable": true, "sClass": "alignCenter"},
			{"mData": "isiParameter", "bSortable": true},
			
			{"mData": "statusParameter", "bSortable": false, "sClass": "alignCenter"},



			<?php if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){ ?>

				{"mData": null,"sWidth": "80px", "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){

				var ret = "";

					<?php if(isset($menuAccess[$s]['edit'])){ ?>

						ret += "<a href=\"?par[mode]=edit&par[kodeParameter]=" + o.aData['kodeParameter'] + "<?= getPar($par, "mode, kodeParameter"); ?>\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";

					<?php } ?>

					<?php if(isset($menuAccess[$s]['delete'])){ ?>

							ret += "<a href=\"?par[mode]=del&par[kodeParameter]=" + o.aData['kodeParameter'] + "<?= getPar($par, "mode, kodeParameter"); ?>\" class=\"delete\" title=\"Delete Data\" onclick=\"return confirm('Apakah anda ingin menghapus data ini?');\"><span>Delete Data</span></a>";

					<?php } ?>

				return ret;

				}}

			<?php } ?>

				],

				"aaSorting": [[1, "asc"]],

				"fnInitComplete": function (oSettings) {

					oSettings.oLanguage.sZeroRecords = "No data available";

				}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",

				"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

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

		<?php
			if(isset($menuAccess[$s]['add'])){
		?>
		jQuery("#datatable_wrapper #right_panel").append('&nbsp;&nbsp;<a href="?par[mode]=add<?= getPar($par, "mode") ?>" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>');
		<?php
			}
		?>

	});

</script>

<script type="text/javascript">

    function joinTable(){
        jQuery(".dataTables_scrollHeadInner > table").css("border-bottom","0").css("padding-bottom","0").css("margin-bottom","0");
        jQuery(".dataTables_scrollBody > table").css("border-top","0").css("margin-top","-5px");
    }

</script>
<?php
	if($par[mode] == "xls"){            
	    xls();          
	    echo "<iframe src=\"download.php?d=exp&f=REPORT PARAMETER.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iAKTIFITASe>";
	}
?>