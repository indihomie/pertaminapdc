<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFoto = "images/pegawai/";
$fMesin = "images/mesin/";

function getContent($par)
{
    global $s, $arrTitle;

    switch($par[mode]){

        case "lst":
            $text=lData();
            break;

        case "cal":
            $text = cData();
        break;

        case "det":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], false, $par['id_termin'], true);
        break;

        case "lihat_table":
            $text = lihat_table();
        break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up'], true, $par['id_termin'], true);
            break;

        case "detailTagihan":
            $text = view_tagihan($arrTitle[$s], $par['id_tagihan'], $par['pop_up']);
            break;

        case "getFilter":
            $text = getFilter();
            break;

        case "detailForm":
            $text = detailForm();
            break;

        default:
            $text = lihat();
        break;
    }
    return $text;
}

function detailForm()
{
    global $s, $par, $arrTitle, $menuAccess, $getPajak;

    $tagihan = getRow("select * from tagihan_data where id = '".$par['id_tagihan']."'");

    $text.="
          
            ".view_permohonan($arrTitle[$s], $tagihan['id_spk'], '', false, $tagihan['id_termin'])."  
            
            <br />
            <br />
            
            <div id=\"contentwrapper\" class=\"contentwrapper\">
                
                <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
                
                    <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                        <div class=\"title\">
                            <h3>PPH PASAL (%)</h3>
                        </div>
                    </div>
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                        <thead>
                            <tr>
                                <th width=\"20\" style=\"vertical-align: middle\">No</th>
                                <th width=\"*\" style=\"vertical-align: middle\">Pajak</th>
                                <th width=\"150\" style=\"vertical-align: middle\">Tarif</th>
                                <th width=\"150\" style=\"vertical-align: middle\">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            ";

                            $no = 0;
                            foreach ($getPajak as $pjk) {

                                $no++;

                                $data = getRow("select * from tagihan_pajak where id_tagihan = '$par[id_tagihan]' and id_pajak = '$pjk[kodeData]'");


                                $text.="
                                <tr>
                                    <td align=\"center\">".$no."</td>
                                    <td align=\"left\">".$pjk['namaData']."</td>
                                    <td align=\"center\">".$data['tarif']."</td>
                                    <td align=\"right\">".(!empty($data['nilai']) ? getAngka($data['nilai']) : "")."</td>
                                </tr>
                                ";

                                $total += $data['nilai'];
                            }

                            $text.="
                            <tr>
                                <td colspan=\"3\" align=\"right\"><strong>Total</strong></td>
                                <td align=\"right\"><strong>".getAngka($total)."</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>";

    return $text;
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function lData()
{
    global $par;

    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
        $where = " WHERE a.pengajuan_approve_status = 't'";
    }

    if (!empty($_GET['fSearch'])) {
        $where .= " and (     
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(c.no_permohonan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(d.namaSupplier) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(e.nama) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(a.pengajuan_no_tiket) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(c.tiket_nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        )";
    }


    if (!empty($_GET['combo1'])) $where .= " and month(a.target) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(a.target) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and b.id_jenis = '".$_GET['combo3']."'";
    if (!empty($_GET['combo4'])) $where .= " and b.id_supplier = '".$_GET['combo4']."'";
    if (!empty($_GET['combo5'])) {
        $where .= " and b.id_sbu = '".$_GET['combo5']."'";
        if (!empty($_GET['combo6'])) $where .= " and b.id_proyek = '".$_GET['combo6']."'";
    }

    $arrOrder = array("", "a.target");

    if(!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
    else $order = "c.id DESC";

    $sql = "SELECT 
            a.id,
            a.target,
            a.termin, 
            a.nilai, 
            a.nilai_total, 
            a.persen,
            a.id_spk,
            a.pengajuan_no_tiket,
            c.id as id_tagihan,
            b.id_jenis,
            b.judul,
            b.nomor,
            b.tanggal, 
            b.id_supplier,
            c.tgl_terima, 
            c.file_tagihan,
            c.no_invoice,
            c.no_permohonan,
            c.tiket_tanggal,
            c.tiket_status,
            c.tiket_nomor,
            d.namaSupplier,
            e.nama
            FROM tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
            $where order by $order $limit";
    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM tagihan_termin AS a
                                            JOIN tagihan_spk AS b ON (b.id = a.id_spk and approve_status = 't' and persen_termin = '100')
                                            JOIN tagihan_data AS c ON (c.id_termin = a.id) 
                                            LEFT JOIN dta_supplier AS d ON (d.kodeSupplier = b.id_supplier)
                                            LEFT JOIN pegawai_data AS e ON (e.id = b.id_supplier)
                                            $where"),
        "aaData" => array()
        );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $pemohon = ($r[id_jenis] == '1048') ? $r[namaSupplier] : $r[nama];

        $data = array(
            "<div align=\"center\">".$no."</div>",
            "<div align=\"center\">".getTanggal($r["target"])."</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&&par[id_termin]=" . $r["id"]."&par[id_spk]=".$r["id_spk"]."" . getPar($par, "mode, id_spk, id_termin") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">".$pemohon."</div>",
            "<div align=\"right\">".getAngka($r["nilai_total"])."</div>",
            "<div align=\"center\">".$r["pengajuan_no_tiket"]."</div>",
            "<div align=\"center\">".$r["tiket_nomor"]."</div>",
            "<div align=\"center\">
                <a href=\"?par[mode]=detailForm&par[id_tagihan]=".$r["id_tagihan"].getPar($par, "mode, id_tagihan")."\" class=\"detail\"><span>Detail</span></a>
            </div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function lihat_table()
{
	global $s, $arrTitle, $par;

	$text = table(8, array(3, 4, 5, 6, 7, 8));

    $combo1 = empty($combo1) ? date("m") : $combo1;
    $combo2 = empty($combo2) ? date("Y") : $combo2;

    $yearStart = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) asc limit 1");
    $yearEnd = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) desc limit 1");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\">
		
		    <p style=\"position:absolute;top:10px;right:20px;\"> 
                <input type=\"button\" class=\"cancel radius2\" value=\"VIEW CALENDAR\" onclick=\"window.location='?" . getPar($par, "mode") . "';\"/>
            </p>
		    
			<div id=\"pos_l\" style=\"float:left; display: flex;\">
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$fSearch."\" style=\"width:250px;\"/>
				&nbsp;
                ".comboMonth("combo1", $combo1, "", "100px", "ALL")." &nbsp;
                ".comboYear("combo2", $combo2, "", "", "80px", "ALL", $yearStart, $yearEnd)."
                
                <span id=\"bView\">
                    <input type=\"button\" value=\"+\" style=\"font-size:12px;\" class=\"btn btn_search btn-small\" onclick=\"
                        document.getElementById('bView').style.display = 'none';
                        document.getElementById('bHide').style.display = 'block';
                        document.getElementById('dFilter').style.display = 'block';							
                        document.getElementById('fSet').style.height = 'auto';
                        \" />
                </span>
                    
                <span id=\"bHide\" style=\"display:none\">
                    <input type=\"button\" value=\"-\" style=\"font-size:12px;\" class=\"btn btn_search btn-small\" onclick=\"
                        document.getElementById('bView').style.display = 'block';
                        document.getElementById('bHide').style.display = 'none';
                        document.getElementById('dFilter').style.display = 'none';							
                        document.getElementById('fSet').style.height = '0px';
                        \" />	
                </span>
                
			</div>
			
			<div id=\"pos_r\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
            </div>
            
            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Kategori SP3</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'MDKS' order BY urutanData asc", "kodeData", "namaData", "combo3", "Semua Kategori", $combo3, "", "250px", "chosen-select") . "
                                    </div>
                                    <style>#combo3_chosen{min-width:210px;}</style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Pemohon</label>
                                    <div class=\"field\">
                                        " . comboData("select * from dta_supplier where kodeSupplier in (SELECT DISTINCT(id_supplier) from tagihan_spk) order by namaSupplier asc", "kodeSupplier", "namaSupplier", "combo4", "Semua Pemohon", $combo4, "", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo4_chosen{ min-width:210px; }
                                    </style>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Bisnis Unit</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'KSBU' order by urutanData", "kodeData", "namaData", "combo5", "Semua SBU", $combo5, "onchange=\"getFilter(this.value, '" . getPar($par, "mode") . "')\"", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo5_chosen{ min-width:210px; }
                                    </style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Proyek</label>
                                    <div class=\"field\">
                                        " . comboData("select * from proyek_data where sbu = '" . $r['id_sbu'] . "' order by proyek asc", "id", "proyek", "combo6", "Semua Proyek", $combo6, "", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo6_chosen{ min-width:210px; }
                                    </style>
                                </p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            
		</form>
		
		<br clear=\"all\" />

		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th style=\"vertical-align: middle; min-width: 20px;\">No</th>
                    <th style=\"vertical-align: middle; min-width: 70px;\">Tanggal</th>
                    <th style=\"vertical-align: middle; min-width: 250px;\">Judul - Nomor</th>
                    <th style=\"vertical-align: middle; min-width: 150px;\">Pemohon</th>
                    <th style=\"vertical-align: middle; min-width: 80px;\">Nilai</th>
                    <th style=\"vertical-align: middle; min-width: 80px;\">No Tiket</th>
                    <th style=\"vertical-align: middle; min-width: 80px;\">No Pembayaran</th>
                    <th style=\"vertical-align: middle; min-width: 50px;\">Detil</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
	</div>
	";

	if ($par[export] == "xls"){
        xls();
        $text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    $text.="
    <script>
    	jQuery(\"#btnExport\").live('click', function(e){
    		e.preventDefault();
    		window.location.href=\"?par[export]=xls".getPar($par,"export, fSearch, combo1, combo2, combo3, combo4, combo5, combo6")."&par[fSearch]=\"+jQuery(\"#fSearch\").val() + \"&par[combo1]=\"+jQuery(\"#combo1\").val() + \"&par[combo2]=\"+jQuery(\"#combo2\").val() + \"&par[combo3]=\"+jQuery(\"#combo3\").val() + \"&par[combo4]=\"+jQuery(\"#combo4\").val() + \"&par[combo5]=\"+jQuery(\"#combo5\").val() + \"&par[combo6]=\"+jQuery(\"#combo6\").val() ;
    	});
    </script>
    ";

    return $text;
}

function cData()
{

    $color = ["#fa5050", "#f75ead", "#ef75ff", "#bb73fa", "#716afc", "#5b9bfc", "#4cccff", "#4de2f0", "#51e096", "#50d161", "#abe356", "#d1e860", "#f0a857"];
    shuffle($arr_color);

    $data = array();
    $sql="SELECT  *, a.id AS id_termin FROM 
            tagihan_termin AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk AND approve_status = 't' AND persen_termin = '100')
            JOIN tagihan_data AS c ON (c.id_termin = a.id) WHERE c.tiket_nomor != ''";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        $c++;
        $data[] = array(
            "id_spk" => $r[id_spk],
            "id_termin" => $r[id_termin],
            "title" => $r[nomor] ." \n ". $r[termin] ." \n ". $r[tiket_nomor],
            "start" => $r[rencana_bayar],
            "end" => $r[rencana_bayar],
            "color" => $color[$c],
            "tmp" => $r[nomor] ." \n ". $r[termin] ." \n ". $r[tiket_nomor],
        );
    }
    return json_encode($data);
}

function lihat()
{
    global $s,$par,$arrTitle;

    $text.="<div class=\"pageheader\">
            <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
            ".getBread()."				
        </div>    
        <div id=\"contentwrapper\" class=\"contentwrapper\">
            <div id=\"calendar\"></div>
        </div>
        <script type=\"text/javascript\" src=\"scripts/calendar.js\"></script>
        <script type=\"text/javascript\">
            jQuery(function () {
                jQuery('#calendar').fullCalendar({
                    year: '".date('Y')."',
                    month: '".(date('m') - 1)."',
                    date: '".date('d')."',
                    header: {
                        left: 'month',
                        center: 'title',
                        right: 'prev, next'
                    },
                    buttonText: {
                        prev: '&laquo;',
                        next: '&raquo;',
                        prevYear: '&nbsp;&lt;&lt;&nbsp;',
                        nextYear: '&nbsp;&gt;&gt;&nbsp;',
                        today: 'today',
                        month: 'month',
                        week: 'week',
                        day: 'day'
                    },
                    events: {
                        url: 'void.php?".getPar($par, 'mode')."&par[mode]=cal',
                        cache: true
                    },
                    eventMouseover: function (calEvent, jsEvent) {
                        arr = calEvent.tmp.split('\\n');
                        let html = '<div class=\"tooltipevent\" style=\"background: ' + calEvent.color + '; color: #fff; padding: 10px 20px; position: absolute; z-index: 10000; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;\"><strong>' + arr[0] + '</strong><br>' + arr[1] + '<br></div>';
                        jQuery('body').append(html);

                        jQuery(this).mouseover(function (e) {
                            jQuery(this).css('z-index', 10000);
                            jQuery('.tooltipevent')
                                .fadeIn('500')
                                .fadeTo('10', 1.9);
                        }).mousemove(function (e) {
                            jQuery('.tooltipevent')
                                .css('top', e.pageY + 10)
                                .css('left', e.pageX + 20);
                        });
                        
                    },
                    eventMouseout: function (calEvent, jsEvent) {
                        jQuery(this).css('z-index', 8);
                        jQuery('.tooltipevent').remove();
                    },
                    eventClick: function (calEvent, jsEvent, view) {
                        openBox('popup.php?".getPar($par, 'mode, id')."&par[mode]=det&par[id_termin]='+ calEvent.id_termin +'&par[id_spk]=' + calEvent.id_spk, '950', '550');
                    },
                })
            })
        </script>
        
        <p style=\"position:absolute;top:30px;right:20px;\"> 
			<input type=\"button\" class=\"Batal radius2\" value=\"VIEW TABLE\" onclick=\"window.location='?par[mode]=lihat_table".getPar($par,"mode")."';\"  style=\"float:right; margin-top:-15px;\"/>
		</p>
        
        ";



    return $text;
}

function detail()
{
    global $s,$par,$arrTitle;

    $tagihan = getRow("select * from tagihan_data where id = '".$par[id]."'");
    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '".$tagihan['id_spk']."'");

    $text.="
    <div class=\"pageheader\">
        <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
        &nbsp;
    </div>
    <div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">
        
            <fieldset>
			    <legend>SPK</legend>
			    <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal</label>
                                <span class=\"field\">  
                                    " . getTanggal($r["tanggal"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Nomor</label>
                                <span class=\"field\">
                                    ".$r["nomor"]." &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Supplier</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getField("select namaSupplier from dta_supplier where kodeSupplier = '".$r["id_supplier"]."'")." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Judul</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["judul"]." &nbsp;
                    </span>
                </p>
                
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["catatan"]." &nbsp;
                    </span>
                </p>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">Tanggal Realisasi</label>
                                <span class=\"field\">  
                                    " . getTanggal($r["target_realisasi"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:50%\">
                             <p>
                                <label class=\"l-input-small2\">Total Nilai</label>
                                <span class=\"field\">  
                                    " . getAngka($r["nilai"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\" >Alamat Tujuan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$r["alamat"]." &nbsp;
                    </span>
                </p>
                
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width:50%\">
                            <p>
                                <label class=\"l-input-small2\">PPN</label>
                                <span class=\"field\">  
                                    " . getAngka($r["ppn"]) . "% &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:25%\">
                            <p>
                                <label class=\"l-input-small2\">PPH</label>
                                <span class=\"field\">  
                                    " . getAngka($r["pph"]) . "% &nbsp;
                                </span>
                            </p>
                        </td>
                        <td style=\"width:25%\">
                            <p>
                                <label class=\"l-input-small2\">Diskon</label>
                                <span class=\"field\">  
                                    " . getAngka($r["diskon"]) . " &nbsp;
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p>
                    <label class=\"l-input-small\">Grand Total</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getAngka($r["total"])." &nbsp;
                    </span>
                </p>
                
            </fieldset>
            
            <br />
            ";
            $termin = getRow("select * from tagihan_termin where id = '".$tagihan['id_termin']."'");
            $text .= "
            
            <fieldset>
                <legend>Termin Pembayaran</legend>
                <p>
                    <label class=\"l-input-small\">Termin</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$termin['termin']." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Besaran</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getAngka($termin['persen'])."% &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Senilai</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getAngka($termin['nilai'])." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Target</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".getTanggal($termin['target'])." &nbsp;
                    </span>
                </p>
                <p>
                    <label class=\"l-input-small\">Catatan</label>
                    <span class=\"field\" style=\"margin-left: -10px\">
                        ".$termin['catatan']." &nbsp;
                    </span>
                </p>
            </fieldset>
            
            <br />
            
            <div class=\"widgetbox\" style=\"margin-top:-20px;\">
                <div class=\"title\">
                    <h3>SYARAT & KETENTUAN</h3>
                </div>
            </div>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:-30px;\">
                <thead>
                    <tr>
                        <th width=\"20\" rowspan=\"2\" style=\"vertical-align: middle\">No</th>
                        <th width=\"300\" rowspan=\"2\" style=\"vertical-align: middle\">Syarat</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Last Update</th>
                        <th width=\"*\" colspan=\"2\">File</th>
                        <th width=\"100\" rowspan=\"2\" style=\"vertical-align: middle\">Verifikasi</th>
                    </tr>
                    <tr>
                        <th width=\"75\">View</th>
                        <th width=\"75\">D / L</th>
                    </tr>
                </thead>
                <tbody>
                    ";
                    $getData = getRows("select * from tagihan_syarat where id_termin = '".$tagihan['id_termin']."' order by id asc");
                    if ($getData) {

                        $no = 0;
                        foreach ($getData as $data) {

                            $no++;

                            $view = $data['ba_file'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanBa&par[id]=$data[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
                            $download = $data['ba_file'] ? "<a href=\"download.php?d=fileTagihanBa&f=$data[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($data['ba_file'])."\" height=\"20\"></a>" : "";

                            if ($data['ba_verifikasi'] == 't') $data['ba_verifikasi'] = "Diterima";
                            if ($data['ba_verifikasi'] == 'f') $data['ba_verifikasi'] = "Ditolak";

                            $text.="
                            <tr>
                                <td align=\"center\">".$no."</td>
                                <td align=\"left\">".$data['judul']."</td>
                                <td align=\"center\">".$data['updated_at']."</td>
                                <td align=\"center\">".$view."</td>
                                <td align=\"center\">".$download."</td>
                                <td align=\"center\">".$data['ba_verifikasi_date']."</td>
                            </tr>
                            ";
                        }

                    } else {

                        $text.="
                        <tr>
                            <td colspan=\"6\"><strong><center>- Data Kosong -</center></strong></td>
                        </tr>
                        ";

                    }
                    $text.="
                </tbody>
            </table>
            
            <br>
            
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" width=\"100%\">
                <thead>
                    <tr>
                        <th colspan=\"3\">APPROVAL</th>
                    </tr>
                    <tr>
                        <th width=\"33%\">1</th>
                        <th width=\"33%\">2</th>
                        <th width=\"33%\">3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        ";
                        for ($i = 1; $i <= 3; $i++) {

                            $text.="
                            <td align=\"center\">
                                <br>                        
                                ";
                                $dtAppr = getRow("select * from tagihan_approval where id_termin = '".$tagihan['id_termin']."' and approve_lvl = '$i'");

                                $appr = "<img src=\"assets/images/p.png\" title='Menunggu Persetujuan'>";
                                if ($dtAppr["approve_status"] == "t") $appr = "<img src=\"assets/images/t.png\" title='Setuju'>";
                                if ($dtAppr["approve_status"] == "f") $appr = "<img src=\"assets/images/f.png\" title='Tolak'>";
                                if ($dtAppr["approve_status"] == "p") $appr = "<img src=\"assets/images/o.png\" title='Pending'>";

                                if (isset($menuAccess[$s]["apprlv$i"])) {
                                    $approval = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[lvl]=$i". getPar($par, "mode") . "', 600, 300);\">$appr</a>";
                                } else {
                                    $approval = "$appr";
                                }

                                $text.="
                                $approval
                                ";
                                 if (!empty($dtAppr)) {
                                    $text.="
                                        <br><br>
                                        ".getField("select namaUser from app_user where id = '".$dtAppr['approve_by']."'")." &nbsp;
                                        <br>
                                        ".getTanggal($dtAppr['approve_date'])." &nbsp;
                                        <br>
                                        ".$dtAppr['approve_desc']."
                                    ";
                                }
                                $text.="
                            </td>
                            ";
                        }
                        $text.="
                        
                    </tr>
                </tbody>
            </table>
        
        </form>    
    </div>";

    return $text;
}

function xls()
{
    global $par, $arrTitle, $s;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])).".xls";
	$judul = $arrTitle[$s];

	$field = ["NO",
              "Tanggal Rencana",
              "No. Invoice",
              "No. SPK",
              "Supplier",
              "Tahap",
              "Nilai"];

    $where = " WHERE a.tiket_status = 't'";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
        lower(a.no_invoice) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(b.nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(c.termin) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(a.tiket_nomor) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        or
        lower(d.namaSupplier) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
        )";
    }

    if (!empty($par['combo1'])) $where .= " and month(a.rencana_bayar) = '".$par['combo1']."'";
    if (!empty($par['combo2'])) $where .= " and year(a.rencana_bayar) = '".$par['combo2']."'";
    if (!empty($par['combo3'])) $where .= " and d.kodeSupplier = '".$par['combo3']."'";
    if (!empty($par['combo4'])) $where .= " and a.status_pelunasan = '".$par['combo4']."'";

    $order = "a.id DESC";

    $sql = "SELECT 
            a.*, 
            b.nomor AS no_spk, 
            c.termin, 
            c.nilai, 
            d.namaSupplier 
            FROM tagihan_data AS a
            JOIN tagihan_spk AS b ON (b.id = a.id_spk)
            JOIN tagihan_termin AS c ON (c.id = a.id_termin)
            JOIN dta_supplier AS d ON (d.kodeSupplier = a.id_supplier) $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $data[]=[
			$no . "\t center",
			getTanggal($r["rencana_bayar"]) . "\t center",
			$r["no_invoice"] . "\t center",
			$r["no_spk"] . "\t center",
			$r["namaSupplier"]."\t left",
			$r['termin'] . "\t left",
			getAngka($r["nilai"]) . "\t right",
		];
    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}
?>