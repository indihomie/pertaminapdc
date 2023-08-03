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
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin where pengajuan_approve_status = 't' and YEAR(target) = '$par[tahun]'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box allports\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Setuju</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 't' and YEAR(target) = '$par[tahun]'")."</p>
							</div>
						</div>
					</div>
				</td>
                <td style=\"width:25%\">
					<div class=\"dashboard-box dark-orchid\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Pending</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 'p' and YEAR(target) = '$par[tahun]'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box murky-green\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Tolak</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 'f' and YEAR(target) = '$par[tahun]'")."</p>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		
		<br>
		
        <div class=\"widgetbox\" style=\"position: relative;\">
			<div style=\"margin-bottom:-10px;\"><h4>JUMLAH PERMOHONAN</h4></div>
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
                        $nilai = getField("SELECT count(*) FROM tagihan_termin where pengajuan_approve_status = 't' and year(target)='".$par[tahun]."' and month(target)='".$i."'");
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
			<div style=\"margin-bottom:-10px;\"><h4>STATUS PERMOHONAN</h4></div>
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
                    \"title\": \"Setuju\",
                    \"valueField\": \"setuju\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Pending\",
                    \"valueField\": \"pending\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Tolak\",
                    \"valueField\": \"tolak\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Belum\",
                    \"valueField\": \"belum\"
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
                    \"setuju\": ".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 't' and year(target)='".$par[tahun]."' and month(target)='".$i."'").",
                    \"pending\": ".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 'p' and year(target)='".$par[tahun]."' and month(target)='".$i."'").",
                    \"tolak\": ".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = 'f' and year(target)='".$par[tahun]."' and month(target)='".$i."'").",
                    \"belum\": ".getField("SELECT count(*) FROM tagihan_termin where tax_approval_status = '' and year(target)='".$par[tahun]."' and month(target)='".$i."'")."
				},
				";
			}
			$text.="
			]
		});
        </script>
        <div id=\"chartdiv5\"></div>
		
        <br>
        
        <table style=\"width:100%\">
			<tr>
			    <td width='49%'>
			    
                    <div class=\"widgetbox\" style=\"position: relative; border-style: solid; border-color: #4D4D4D;\">
                        <div style=\"margin-bottom:-10px;\"><h4>PERMHONAN + PPN</h4></div>
                        <div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
                    </div>
                    <hr style=\"margin-bottom: 15px; \">
                    
                    <style>
                        #chartdiv6{
                        width	: 100%;
                        height	: 400px;
                    }										
                    </style>
                    
                    <script type=\"text/javascript\">
                        AmCharts.makeChart(\"chartdiv6\",
                            {
                                \"type\": \"pie\",
                                \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
                                \"titleField\": \"category\",
                                \"valueField\": \"column-1\",
                                \"theme\": \"default\",
                                \"allLabels\": [],
                                \"balloon\": {},
                                \"legend\": {
                                    \"enabled\": true,
                                    \"align\": \"center\",
                                    \"markerType\": \"circle\"
                                },
                                \"titles\": [],
                                \"dataProvider\": [
                                    ";
                                    $nilai = getField("SELECT sum(nilai) FROM tagihan_termin where pengajuan_approve_status = 't' and YEAR(target) = '$par[tahun]'");
                                    $ppn = getField("SELECT sum(nilai_ppn) FROM tagihan_termin where pengajuan_approve_status = 't' and YEAR(target) = '$par[tahun]'");
                                    $text.="
                                    {
                                        \"category\": \"Permohonan\",
                                        \"column-1\": $nilai
                                    },
                                    {
                                        \"category\": \"PPN\",
                                        \"column-1\": $ppn
                                    }
                                ]
                            }
                        );
                    </script>
                    <div style=\"border:1px #4D4D4D solid ; padding:10px;\">
                        <div id=\"chartdiv6\"></div>
                    </div>
			            
                </td>
			    <td width='2%'></td>
			    <td width='49%'>
			    
			        <div class=\"widgetbox\" style=\"position: relative; border-style: solid; border-color: #4D4D4D;\">
                        <div style=\"margin-bottom:-10px;\"><h4>PAJAK PPH</h4></div>
                        <div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
                    </div>
                    <hr style=\"margin-bottom: 15px; \">
                    
                    <style>
                        #chartdiv7{
                        width	: 100%;
                        height	: 400px;
                    }										
                    </style>
                    
                    <script type=\"text/javascript\">
                    AmCharts.makeChart(\"chartdiv7\",
                        {
                            \"type\": \"pie\",
                            \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
                            \"titleField\": \"category\",
                            \"valueField\": \"column-1\",
                            \"theme\": \"light\",
                            \"allLabels\": [],
                            \"balloon\": {},
                            \"legend\": {
                                \"enabled\": true,
                                \"align\": \"center\",
                                \"markerType\": \"circle\"
                            },
                            \"titles\": [],
                            \"dataProvider\": [
                            ";
                            $getPajak = queryAssoc("select * from mst_data where kodeCategory = 'MDPJ' order by urutanData asc");
                            foreach ($getPajak as $pjk) {
                                $nilai = getField("select ifnull(sum(nilai), 0) from tagihan_pajak where id_pajak = '$pjk[kodeData]' and YEAR(created_at) = '$par[tahun]'");
                                $text.="
                                {
                                    \"category\": \"".$pjk[namaData]."\",
                                    \"column-1\": $nilai
                                },
                                ";
                            }
                            $text.="
                            ]
                        }
                    );
                    </script>
                    <div style=\"border:1px #4D4D4D solid ; padding:10px;\">
                        <div id=\"chartdiv7\"></div>
                    </div>
                </td>
			</tr>
        </table>
        
        
	</div>";

    return $text;
}