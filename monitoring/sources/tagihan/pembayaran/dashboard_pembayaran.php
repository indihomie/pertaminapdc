<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $arrTitle;

    switch ($par[mode]) {

        default:
            $text = lihat();
            break;

    }

    return $text;
}

function lihat()
{
    global $s, $arrTitle, $par;
    $par[tahun] = empty($par[tahun]) ? date("Y") : $par[tahun];

    $yearStart = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) asc limit 1");
    $yearEnd = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) desc limit 1");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"POST\" class=\"stdform\" autocomplete=\"off\">
			<p style=\"position: absolute; right: 20px; top: 4px;\">
				".comboYear("par[tahun]", $par[tahun], "5", "onchange=\"document.getElementById('form').submit();\"","","", $yearStart, $yearEnd)."
			</p>
		</form>
		
		<table style=\"width:100%\">
			<tr>
			    <td style=\"width:25%\">
					<div class=\"dashboard-box murky-green\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Jml Tiket</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and tiket_nomor != ''")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Belum</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and tiket_nomor != '' and status_pelunasan = 'proses'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box allports\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Sebagian</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and tiket_nomor != '' and status_pelunasan = 'sebagian'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box dark-orchid\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Lunas</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and tiket_nomor != '' and status_pelunasan = 'lunas'")."</p>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		
		<br>
        
        <div class=\"widgetbox\" style=\"position: relative;\">
			<div style=\"margin-bottom:-10px;\"><h4>JADWAL PEMBAYARAN</h4></div>
			<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
		</div>
		<hr style=\"margin-bottom: 15px;\">
		<style>
            #chartdiv4{
            width	: 100%;
            height	: 400px;
        }										
        </style>
        <script type=\"text/javascript\">
            var chart2 = AmCharts.makeChart(\"chartdiv4\",
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
                        \"title\": \"Nilai\",
                        \"type\": \"column\",
                        \"valueField\": \"colom1\"
                    },
                ],
                \"guides\": [],
                \"valueAxes\": [
                {
                    \"id\": \"ValueAxis-1\",
                    \"stackType\": \"regular\",
                    \"title\": \"Total\"
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
                for ($i=1; $i <= 12 ; $i++) {
                    $text.="
                    {
                        \"category\": \"".getBulan($i)."\",
                        ";
                        $nilai = getField("SELECT count(*) from tagihan_data where tiket_nomor != '' and year(rencana_bayar)='".$par[tahun]."' and month(rencana_bayar)='".$i."'");
                        $text.="
                        \"colom1\": $nilai,
                        ";
                        $text.="
                    },
                    ";
                }
                $text.="
                ]
            });
        </script>
        <div id=\"chartdiv4\"></div>
        
        <br>
        
        <div class=\"widgetbox\" style=\"position: relative;\">
			<div style=\"margin-bottom:-10px;\"><h4>PROSES TAGIHAN</h4></div>
			<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
		</div>
		<hr style=\"margin-bottom: 15px;\">
		<style>
            #chartdiv5{
            width	: 100%;
            height	: 400px;
        }										
        </style>
        <script type=\"text/javascript\">
            var chart2 = AmCharts.makeChart(\"chartdiv5\",
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
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-1\",
                    \"title\": \"Belum\",
                    \"valueField\": \"belum\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Sebagian\",
                    \"valueField\": \"sebagian\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-3\",
                    \"title\": \"Lunas\",
                    \"valueField\": \"lunas\"
                },
			],
			\"guides\": [],
			\"valueAxes\": [
			{
				\"title\": \"Total\"
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
			for ($i=1; $i <= 12 ; $i++) {
				$text.="
				{
					\"category\": \"".getBulan($i)."\",
                    \"belum\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and month(tgl_terima) = '".$i."' and tiket_nomor != '' and status_pelunasan = 'proses'").",
                    \"sebagian\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and month(tgl_terima) = '".$i."' and tiket_nomor != '' and status_pelunasan = 'sebagian'").",
                    \"lunas\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and month(tgl_terima) = '".$i."' and tiket_nomor != '' and status_pelunasan = 'lunas'").",
				},
				";
			}
			$text.="
			]
		});
        </script>
        <div id=\"chartdiv5\"></div>
		
        <br>
        
        <div class=\"widgetbox\" style=\"position: relative;\">
			<div style=\"margin-bottom:-10px;\"><h4>TAGIHAN (TOP 10 NILAI)</h4></div>
			<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
		</div>
		<hr style=\"margin-bottom: 15px;\">
        <script type=\"text/javascript\">
			AmCharts.makeChart(\"chartdiv6\",
				{
					\"type\": \"serial\",
					\"categoryField\": \"category\",
					\"rotate\": true,
					\"autoMarginOffset\": 40,
					\"marginRight\": 60,
					\"marginTop\": 60,
					\"startDuration\": 1,
					\"fontSize\": 13,
					\"theme\": \"light\",
					\"categoryAxis\": {
						\"gridPosition\": \"start\"
					},
					\"trendLines\": [],
					\"graphs\": [
						{
							\"balloonText\": \"[[category]]:[[value]]\",
							\"fillAlphas\": 1,
							\"id\": \"AmGraph-1\",
							\"labelText\": \"\",
							\"type\": \"column\",
							\"valueField\": \"column-1\"
						}
					],
					\"guides\": [],
					\"valueAxes\": [
						{
							\"id\": \"ValueAxis-1\",
							\"title\": \"\"
						}
					],
					\"allLabels\": [],
					\"balloon\": {},
					\"titles\": [],
					\"dataProvider\": [
					    ";
					    $getData = getRows("SELECT 
                                            a.namaSupplier, 
                                            (
                                            SELECT IFNULL(SUM(c.total), 0) FROM tagihan_data AS b
                                            JOIN tagihan_spk AS c ON (c.id = b.id_spk)
                                            WHERE b.tiket_nomor != '' AND b.id_supplier = a.kodeSupplier AND YEAR(b.tgl_terima) = '$par[tahun]'
                                            ) AS total_nilai 
                                            FROM dta_supplier AS a ORDER BY total_nilai DESC LIMIT 10");
                        foreach ($getData as $data) {

                            $text.="
                            {
                                \"category\": \"$data[namaSupplier]\",
                                \"column-1\": $data[total_nilai]
                            },
                            ";

                        }
					    $text.="
					]
				}
			);
		</script>
        
        <div id=\"chartdiv6\" style=\"width: 100%; height: 400px;\" ></div>
        
	</div>";

    return $text;
}