<?php
global $s, $par, $menuAccess, $arrTitle,$cGroup;

$arrbox = array("1"=> "goldenrod","2"=> " murky-green","3"=> "allports","4"=> "chocolate","5"=> "dark-orchid","6"=> "camarone","7"=> "light-blue","8"=> "orange","9"=> "pink","10"=> "sky-blue");

// .dark-orchid{ background-color: #9A00AD; }
// .camarone{ background-color: #297140; }
// .goldenrod{ background-color: #E1A300; }
// .citrus{ background-color: #9CB500; }
// .caper{ background-color: #C2D69B; }
// .moon-raker{ background-color: #B2A1C7; }
// .allports{ background-color: #31849B; }
// .chocolate{ background-color: #E36C0A; }
// .purple-orchid{ background-color: #CBC0D9; }
// .light-blue { background-color: #B7DDE7; }
// .murky-green { background-color: #D6E3BC; }
// .orange{ background-color: #FBD4B4; }
// .purple-orchid{ background-color: #E5B9FF; }
// .purple-orchid2{ background-color: #ECE8FF; }
// .light-blue { background-color: #3DD2F9; }
// .murky-green { background-color: #9BCA3E; }
// .pale-green { background-color: #EAFFF1; }
// .sky-blue{ background-color: #E8F2FF; }
// .orange { background-color: #FF9945; }
// .pink { background-color: #FFB9B7;}
// .pink2 { background-color: #FFEDF5; };

// $jumlahArrayPegawai = count($arrAngkaPegawai) + 1;
// $jumlahArrayPegawai = 100/$jumlahArrayPegawai;
// $jumlahArrayPegawai = str_replace(",", ".", $jumlahArrayPegawai);
// $jumlahArrayPegawai = substr($jumlahArrayPegawai, 0,5);
// var_dump($jumlahArrayPegawai);
if(empty($par[bulanProses])) $par[bulanProses] = date('m');
	if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		

$filter = "";
if(!empty($par[bulanProses]))
	$filter.= " AND MONTH(t1.create_time)='$par[bulanProses]'";

if(!empty($par[tahunProses]))
	$filter.= " AND YEAR(t1.create_time)='$par[tahunProses]'";	

$filter2 = "";
if(!empty($par[bulanProses]))
	$filter2.= " AND MONTH(t1.createTime)='$par[bulanProses]'";

if(!empty($par[tahunProses]))
	$filter2.= " AND YEAR(t1.createTime)='$par[tahunProses]'";	

$no = 1;
?>
<style type="text/css">
	h3{
		margin-bottom:-4px;
	}
	.scrollsIMG {
		overflow-x: scroll;
		overflow-y: hidden;
		height: auto;
		padding-bottom: 20px;
		white-space:nowrap
	}
</style>
<div class="pageheader">
	<h1 class="pagetitle">Dashboard</h1>
	<?= getBread() ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<form id="form" name="form" action="" method="post" class="stdform">
	<p style="position: absolute; right: 20px; top: 4px;">
		<?=  comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")."&nbsp;".comboYear("par[tahunProses]", $par[tahunProses], "5", "onchange=\"document.getElementById('form').submit();\"")."&nbsp;"; ?>
	</p>
</form>
<div id="contentwrapper" class="contentwrapper">
	<table style="width:100%">
		<tr style="width:100%">
			<td style="width:33.33%;">
				<div class="dashboard-box light-blue" style="padding-bottom: 10px;margin-bottom: 10px">
					<div class="dashboard-box-header">
						<p class="dashboard-box-title">KEJADIAN</p>
					</div>
					<div class="dashboard-box-content">
						<p class="dashboard-box-number"><?= getField("SELECT count(*) FROM `data_kejadian` t1 where t1.id_kejadian is not null $filter"); ?></p>
					</div>
				</div>
			</td>


			<td style="width: 33.33%;">
				<div class="dashboard-box <?= $arrbox[$no] ?>" style="padding-bottom: 10px;margin-bottom: 10px">
					<div class="dashboard-box-header">
						<p class="dashboard-box-title">BERITA</p>
					</div>
					<div class="dashboard-box-content">
						<p class="dashboard-box-number"><?= getField("SELECT count(*) FROM `tbl_berita` t1 where t1.kodeBerita is not null $filter2"); ?></p>
					</div>
				</div>
			</td>

			<td style="width: 33.33%;">
				<div class="dashboard-box <?= $arrbox[$no] ?>" style="padding-bottom: 10px;margin-bottom: 10px">
					<div class="dashboard-box-header">
						<p class="dashboard-box-title">INFO</p>
					</div>
					<div class="dashboard-box-content">
						<p class="dashboard-box-number"><?= getField("SELECT count(*) FROM `dta_pengumuman` t1 where t1.idPengumuman is not null $filter2"); ?></p>
					</div>
				</div>
			</td>
			
			
	
		
			


		
	</tr>
</table>
<?php
echo "
<table style=\"width:100%; margin-top:20px; margin-bottom:10px; margin-left:-15px;\">
	<tr>
		<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>LOKASI</h3></div>
			</div>
			<div id=\"divJabatan\" align=\"center\" ></div>
			<script type=\"text/javascript\">
				var jabatanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";

				
				$sql="SELECT * FROM `area_ruang`";
				$res=db($sql);
				while ($r=mysql_fetch_array($res)) {
					$getJumlah=getField("SELECT count(*) from data_kejadian t1 where t1.id_lokasi='$r[id_ruang]' $filter");
					echo "<set value=\"".setAngka($getJumlah)."\" label=\"".$r[area]."\" showValue=\"".setAngka($getJumlah)."\" />";
				}			

				echo "</chart>';
				var chart = new FusionCharts(\"Pie2D\", \"chartJabatan\", \"100%\", 350);
				chart.setXMLData( jabatanChart );
				chart.render(\"divJabatan\");
			</script>
		</td>
		<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>KATEGORI</h3></div>
			</div>
			<div id=\"divPendidikan\" align=\"center\" ></div>
			<script type=\"text/javascript\">
				var pendidikanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";

				$sql="SELECT * FROM `mst_data` WHERE  `kodeCategory` = 'KEJADIAN' AND `statusData` = 't' ORDER BY `urutanData`";
				$res=db($sql);
				while ($r=mysql_fetch_array($res)) {
					$getJumlah=getField("SELECT count(*) from data_kejadian t1 where t1.kategori='$r[kodeData]' $filter");
					echo "<set value=\"".setAngka($getJumlah)."\" label=\"".$r[namaData]."\" showValue=\"".setAngka($getJumlah)."\" />";
				}				

				echo "</chart>';
				var chart = new FusionCharts(\"Pie2D\", \"chartPendidikan\", \"100%\", 350);
				chart.setXMLData( pendidikanChart );
				chart.render(\"divPendidikan\");
			</script>
		</td>
	</tr>
</table>
<br />
";

?>
<?php
// onclick=\"openBox('popup.php?par[mode]=detPegawai".getPar($par,"mode")."',875,450);\"
echo "
<div class=\"widgetbox\" style=\"position: relative;\">

	<div style=\"margin-bottom:-10px;\"><h3>TOTAL KEJADIAN PER AREA</h3></div>

	<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>

</div>

<hr style=\"margin-bottom: 15px;\">

<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">

	<thead>

		<tr>

			<th width=\"20\" style=\"vertical-align:middle;\">No</th>

			<th width=\"200\" style=\"vertical-align:middle;\">AREA </th>

			<th  style=\"vertical-align:middle;\">WILAYAH</th>


			<th width=\"60\" style=\"vertical-align:middle;\">TOTAL</th>

		</tr>

	</thead>

	<tbody>
	";
	$sql = "SELECT *,count(id_lokasi) as total FROM `data_kejadian` t1  where id_kejadian is not null $filter group by id_lokasi";

	$res = db($sql);

	$ret = array(); 
	$no=0;
	while ($r = mysql_fetch_assoc($res)) {
		$r['id_wilayah']    =getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$r[id_wilayah]'");
		$r['id_lokasi'] = getField("SELECT `area` FROM `area_ruang` WHERE `id_ruang` = '$r[id_lokasi]'");
		//$total=getField("SELECT count(*) FROM `data_kejadian` ");
	$no++;
	echo"
	<tr>
		<td align=\"center\">$no</td>
		<td>$r[id_lokasi]</td>
		<td>$r[id_wilayah]</td>
		<td align=\"center\">$r[total]</td>
	</tr>
	";
	}
	echo "
	</tbody>
</table>
<br />

";
?>
<?php
echo "

<div class=\"widgetbox\" style=\"position: relative;\">

	<div style=\"margin-bottom:-10px;\"><h3>KEJADIAN PER TAHUN</h3></div>

	<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>

</div>

<hr style=\"margin-bottom: 15px;\">
<table width=\"100%\">
	<tr>
		<td width=\"100%\">
			<style>
					#chartdiv2 {
				width	: 100%;
				height	: 400px;
			}										
		</style>
		<script type=\"text/javascript\">
			var chart2 = AmCharts.makeChart(\"chartdiv2\",
		{
				\"type\": \"serial\",
				\"categoryField\": \"category\",
				\"startDuration\": 1,
				\"categoryAxis\": {
					\"gridPosition\": \"start\"
				},
				\"trendLines\": [],
				\"graphs\": [
				{
					\"balloonText\": \"[[title]] of [[category]]:[[value]]\",
					\"fillAlphas\": 1,
					\"id\": \"AmGraph-1\",
					\"title\": \"KEJADIAN\",
					\"type\": \"column\",
					\"valueField\": \"column-1\"
				}
				],
				\"guides\": [],
				\"valueAxes\": [
				{
					\"id\": \"ValueAxis-1\",
					\"stackType\": \"regular\"
				}
				],
				\"allLabels\": [],
				\"balloon\": {},
				\"legend\": {
					\"enabled\": true,
					\"useGraphSettings\": true
				},
				\"dataProvider\": [
				";
				$namaBulan = array("Bulan","Januari","Februaru","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
				for ($i=1; $i <= 11 ; $i++) { 
					$getKejadian= getField("SELECT count(*) from data_kejadian t1 where MONTH(t1.tanggal)='$i' $filter");
					
					echo "
					{
						\"category\": \"$namaBulan[$i]\",
						\"column-1\": $getKejadian
					},
					";
				}
				echo "
				]
			}
			);
</script>
<div id=\"chartdiv2\"></div>

</td>
</tr>

</table>
<br />
";
?>






</div>
