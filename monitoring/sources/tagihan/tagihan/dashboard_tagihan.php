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
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Permohonan</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box allports\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Verifikasi</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT COUNT(*) FROM tagihan_termin AS a
                                                                                JOIN tagihan_data AS b ON (b.id_termin = a.id) WHERE a.`verifikasi_dokumen` = 't' AND YEAR(target) = '".$par[tahun]."'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box dark-orchid\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Approval</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and approval_status = 't'")."</p>
							</div>
						</div>
					</div>
				</td>
				
			</tr>
		</table>
		
		<br>
		
		<table style=\"width:100%\">
			<tr>
			
				<td style=\"width:48%; \">
				    <div class=\"widgetbox\" style=\"position: relative; border-style: solid;
                    border-color: #4D4D4D;\">
                        <div style=\"margin-bottom:-10px;\"><h4>VERIFIKASI</h4></div>
                        <div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
                    </div>
                    <hr style=\"margin-bottom: 15px; \">
                    <div style=\"border:1px #4D4D4D solid ; padding:10px;\">
                    <script>
                        var chart = AmCharts.makeChart( \"chartdiv1\", {
                            \"type\": \"pie\",
                            \"theme\": \"light\",
                            \"dataProvider\": [
                                    {
                                        \"field\": \"Sudah Diverifikasi\",
                                        \"value\": ".getField("SELECT COUNT(*) FROM tagihan_termin AS a
                                                                                JOIN tagihan_data AS b ON (b.id_termin = a.id) WHERE a.`verifikasi_dokumen` = 't' AND YEAR(target) = '".$par[tahun]."'")."
                                    },
                                    {
                                        \"field\": \"Belum Diverifikasi\",
                                        \"value\": ".getField("SELECT COUNT(*) FROM tagihan_termin AS a
                                                                                JOIN tagihan_data AS b ON (b.id_termin = a.id) WHERE a.`verifikasi_dokumen` = '' AND YEAR(target) = '".$par[tahun]."'")."
                                    },
                                    ],
                            \"valueField\": \"value\",
                            \"titleField\": \"field\",
                            \"outlineAlpha\": 0.4,
                            \"depth3D\": 15,
                            \"labelText\": \"[[percents]]%\",
                            \"labelRadius\": 20,
                            \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
                            \"angle\": 30,
                            \"legend\": {
                                \"enabled\": true,
                            },
                            \"export\": {
                                \"enabled\": true
                            }
                        });
                    </script>
                    
                    <div id=\"chartdiv1\" style=\"width: 100%; height: 400px; \"></div>
                    </div>
				</td>
				<td style=\"width:4%\"></td>
				<td style=\"width:48%\">
				    <div class=\"widgetbox\" style=\"position: relative;\">
                        <div style=\"margin-bottom:-10px;\"><h4>APPROVAL</h4></div>
                        <div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
                    </div>
                    <hr style=\"margin-bottom: 15px;\">
                    <div style=\"border:1px #4D4D4D solid ; padding:10px;\">
                    <script>
                        var chart = AmCharts.makeChart( \"chartdiv2\", {
                            \"type\": \"pie\",
                            \"theme\": \"light\",
                            \"dataProvider\": [
                                    {
                                        \"field\": \"Sudah Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and approval_status = 't'")."
                                    },
                                    {
                                        \"field\": \"Belum Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and approval_status = ''")."
                                    },
                                    ],
                            \"valueField\": \"value\",
                            \"titleField\": \"field\",
                            \"outlineAlpha\": 0.4,
                            \"depth3D\": 15,
                            \"labelText\": \"[[percents]]%\",
                            \"labelRadius\": 20,
                            \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
                            \"angle\": 30,
                            \"legend\": {
                                \"enabled\": true,
                            },
                            \"export\": {
                                \"enabled\": true
                            }
                        });
                    </script>
                    <div id=\"chartdiv2\" style=\"width: 100%; height: 400px;\"></div>
                    </div>
				</td>
		
			</tr>
		</table>
		
		<br>
        
        <div class=\"widgetbox\" style=\"position: relative;\">
			<div style=\"margin-bottom:-10px;\"><h4>PENERIMAAN PERMOHONAN</h4></div>
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
                        $nilai = getField("SELECT count(*) from tagihan_data where year(tgl_terima)='".$par[tahun]."' and month(tgl_terima)='".$i."'");
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
			<div style=\"margin-bottom:-10px;\"><h4>PROSES PERMOHONAN</h4></div>
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
                    \"title\": \"Verifikasi\",
                    \"valueField\": \"verif\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Approval\",
                    \"valueField\": \"appr\"
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
                    \"verif\": ".getField("SELECT COUNT(*) FROM tagihan_data AS a
                                                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                            WHERE YEAR(a.tgl_terima) = '".$par[tahun]."' AND month(a.tgl_terima) = '".$i."'").",
                    \"appr\": ".getField("SELECT count(*) from tagihan_data where year(tgl_terima) = '".$par[tahun]."' and month(tgl_terima) = '".$i."' and approval_status = 't'").",
				},
				";
			}
			$text.="
			]
		});
        </script>
        <div id=\"chartdiv5\"></div>
		
        
	</div>";

    return $text;
}