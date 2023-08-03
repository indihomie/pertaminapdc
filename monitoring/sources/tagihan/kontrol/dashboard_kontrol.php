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
				<td style=\"width:33%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Permohonan</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE c.approval_status = 't'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:33%\">
					<div class=\"dashboard-box allports\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Control</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE a.nilai_biaya_total = a.nilai_plus_ppn")."</p>
							</div>
						</div>
					</div>
				</td>
                
				<td style=\"width:33%\">
					<div class=\"dashboard-box murky-green\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Jurnal</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE a.pengajuan_approve_status = 't'")."</p>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
		
		<br>
		
		<table style=\"width:100%\">
			<tr>
			
				<td style=\"width:49%; \">
				    <div class=\"widgetbox\" style=\"position: relative; border-style: solid;
                    border-color: #4D4D4D;\">
                        <div style=\"margin-bottom:-10px;\"><h4>KONTROL</h4></div>
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
                                        \"field\": \"Sudah Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) FROM tagihan_termin AS a JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100') JOIN tagihan_data AS c ON (c.id_termin = a.id) JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier) WHERE b.status_serah_terima = 't' AND YEAR(c.tgl_terima) = '".$par[tahun]."'")."
                                    },
                                    {
                                        \"field\": \"Belum Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) FROM tagihan_termin AS a JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100') JOIN tagihan_data AS c ON (c.id_termin = a.id) JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier) WHERE b.status_serah_terima != 't' AND YEAR(c.tgl_terima) = '".$par[tahun]."'")."
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
				
				<td style=\"width:2%\"></td>
				
				<td style=\"width:49%\">
				    <div class=\"widgetbox\" style=\"position: relative;\">
                        <div style=\"margin-bottom:-10px;\"><h4>JURNAL</h4></div>
                        <div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>
                    </div>
                    <hr style=\"margin-bottom: 15px;\">
                    <div style=\"border:1px #4D4D4D solid ; padding:10px;\">
                    <script>
                        var chart = AmCharts.makeChart( \"chartdiv3\", {
                            \"type\": \"pie\",
                            \"theme\": \"light\",
                            \"dataProvider\": [
                                    {
                                        \"field\": \"Sudah Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) FROM tagihan_termin AS a
                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id) WHERE c.id in (SELECT DISTINCT(id_tagihan) FROM tagihan_pajak) and a.pengajuan_approve_status = 't' and year(c.tgl_terima) = '".$par[tahun]."' and approval_status = 't'")."
                                    },
                                    {
                                        \"field\": \"Belum Di-Approve\",
                                        \"value\": ".getField("SELECT count(*) FROM tagihan_termin AS a
                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id) WHERE c.id in (SELECT DISTINCT(id_tagihan) FROM tagihan_pajak) and a.pengajuan_approve_status != 't' and year(c.tgl_terima) = '".$par[tahun]."' and approval_status != 't'")."
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
                    <div id=\"chartdiv3\" style=\"width: 100%; height: 400px;\"></div>
                    </div>
				</td>
		
			</tr>
		</table>
		
		<br>
		
		<table style=\"width:100%\">
			<tr>
				<td style=\"width:25%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Regular Vendor</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE c.approval_status = 't' AND year(a.target)='".$par[tahun]."' and b.id_jenis = '1048'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box allports\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Reimbursement</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE c.approval_status = 't' AND year(a.target)='".$par[tahun]."' and b.id_jenis = '1049'")."</p>
							</div>
						</div>
					</div>
				</td>
				<td style=\"width:25%\">
					<div class=\"dashboard-box dark-orchid\">
						<div class=\"dashboard-box-content\">
							<div class=\"dashboard-box-header\">
								<p class=\"dashboard-box-title\">Deklarasi Dinas</p>
							</div>
							<div class=\"dashboard-box-content\">
								<p class=\"dashboard-box-number\">".getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE c.approval_status = 't' AND year(a.target)='".$par[tahun]."' and b.id_jenis = '1050'")."</p>
							</div>
						</div>
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
                        $nilai = getField("SELECT count(*) FROM tagihan_termin  AS a
                                                                                JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                                                                JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                                                                WHERE c.approval_status = 't' AND year(a.target)='".$par[tahun]."' and month(a.target)='".$i."'");
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
                    \"title\": \"Kontrol\",
                    \"valueField\": \"kontrol\"
                },
                {
                    \"balloonText\": \"[[title]] of [[category]] : [[value]]\",
                    \"bullet\": \"round\",
                    \"id\": \"AmGraph-2\",
                    \"title\": \"Jurnal\",
                    \"valueField\": \"jurnal\"
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
					
                    \"kontrol\": ".getField("SELECT count(*) FROM tagihan_termin  AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                            WHERE a.nilai_biaya_total = a.nilai_plus_ppn 
                                            AND YEAR(a.target) = '".$par[tahun]."' AND month(a.target) = '".$i."'").",
                                            
                    \"jurnal\": ".getField("SELECT count(*) FROM tagihan_termin  AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id)
                                            WHERE a.pengajuan_approve_status = 't'
                                            AND YEAR(a.target) = '".$par[tahun]."' AND month(a.target) = '".$i."'").",
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