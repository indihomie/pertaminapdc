<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fExport = "files/export/";

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$fFile, $cGroup;
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
                <input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" value=\"".$par[filter]."\" style=\"width:200px;\" placeholder=\"Search..\"/>
                ".comboData("select * from mst_data where kodeCategory='BE' order by namaData","kodeData","namaData","par[kategoriModul]","All Kategori",$par[kategoriModul],"onchange=\"getSub('".getPar($par,"mode,kategoriModul")."');\"", "190px","chosen-select")."
                ".comboData("select * from app_modul where  kategoriModul='$par[kategoriModul]' and statusLink !='p' and namaModul !='Setting' order by urutanModul","kodeModul","namaModul","par[kodeModul]","All Modul",$par[kodeModul],"","190px;","chosen-select")."
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>		
			<div id=\"pos_r\">
			 <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>";
		$text.="</div>

			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
                    <th width=\"20\">Status</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter ="where kodeModul is not null and namaModul !='Setting'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(namaModul) like '%".strtolower($par[filter])."%'				
		)";
		if(!empty($par[kategoriModul]))
			$filter.= " AND kategoriModul = '$par[kategoriModul]'";
		  
        if(!empty($par[kodeModul]))
              $filter.= " AND kodeModul = '$par[kodeModul]'";
	
	
		$sql="select * from app_modul $filter order by urutanModul";
		$res=db($sql);
		$noModul = 0;
		while($r=mysql_fetch_array($res)){			
			$no++;
			$noModul++;
			$r[statusModul] = $r[statusModul] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";	
			$text.="<tr>
					<td>$no.</td>
					<td>$noModul. ".strtoupper($r[namaModul])."</td>
					<td align=\"center\">$r[statusModul]</td>
			</tr>";
			$sql_ = "select * from app_site where kodeModul = '$r[kodeModul]' and namaSite != 'Setting' order by urutanSite";
			$res_=db($sql_);
			while($r_=mysql_fetch_array($res_)){	
				$no++;
				$r_[statusSite] = $r_[statusSite] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";	
				$text.="
				<tr>
					<td>$no.</td>
					<td style=\"padding-left:40px;\">$r_[namaSite]</td>
					<td align=\"center\">$r_[statusSite]</td>
				</tr>";
				$sql__ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r_[kodeMenu]' and statusMenu='t' order by urutanMenu";
				$res__=db($sql__);
				$noMenu = 0;
				while($r__=mysql_fetch_array($res__)){	
					$no++;
					$noMenu++;
					$r__[statusMenu] = $r__[statusMenu] == "t"?
					"<img src=\"styles/images/t.png\" title='Active'>":
					"<img src=\"styles/images/f.png\" title='Not Active'>";	
					$text.="
					<tr>
    					<td>$no.</td>
    					<td style=\"padding-left:60px;\">".numtoAlpha($noMenu)." .$r__[namaMenu]</td>
    					<td align=\"center\">$r__[statusMenu]</td>
					</tr>";
					$sql___ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r__[kodeMenu]' and statusMenu='t' order by urutanMenu";
					$res___=db($sql___);
					$subMenu = 0;
					while($r___=mysql_fetch_array($res___)){	
						$no++;
						$subMenu++;
						$r___[statusMenu] = $r___[statusMenu] == "t"?
						"<img src=\"styles/images/t.png\" title='Active'>":
						"<img src=\"styles/images/f.png\" title='Not Active'>";	
						$text.="
						<tr>
						<td>$no.</td>
						<td style=\"padding-left:80px;\">".strtolower(numtoAlpha($subMenu))." . ". $r___[namaMenu]."</td>

						</tr>";
					}
				}
			}
		}	
		
		$text.="</tbody>
			</table>
			</div>";
			if ($par[mode] == "xls") {
    		xls();
    		$text.= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . date('Y-m-d H:i:s') . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    	}
		return $text;
	}

	function xls()
{
	global $s, $arrTitle, $cNama, $fExport, $par;

	require_once 'plugins/PHPExcel.php';

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');


	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA SITEMAP');


	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'MODUL');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'SUB MODUL');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'MENU');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'STATUS');

	$rows = 5;

	$filter = "WHERE namaModul !='Setting'";
	if (!empty($par[kodeKategori]))
		$filter .= " AND kategoriModul = '$par[kodeKategori]'";
	if (!empty($par[kodeModul]))
		$filter .= " AND kodeModul = '$par[kodeModul]'";
	$sql = "SELECT kodeModul, namaModul FROM app_modul $filter order by urutanModul";
	// echo $sql;
	$res = db($sql);
	while ($r = mysql_fetch_assoc($res)) {
		$no++;

		$objPHPExcel->getActiveSheet()->getStyle('A' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $rows, $r[namaModul]);

		$sql_ = "SELECT kodeSite, namaSite FROM app_site WHERE kodeModul = '$r[kodeModul]' and namaSite !='Setting' order by urutanSite";
		$res_ = db($sql_);
		while ($r_ = mysql_fetch_assoc($res_)) {
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $rows,  $r_[namaSite]);
			$sql__ = "SELECT kodeMenu, namaMenu, statusMenu FROM app_menu WHERE kodeSite = '$r_[kodeSite]' AND kodeInduk = '0' order by urutanMenu";
			$res__ = db($sql__);
			while ($r__ = mysql_fetch_assoc($res__)) {
				$noMenu++;
				$objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$cekFile = $r__[statusMenu] == "f" ? "Tidak Aktif" : "Aktif";
				// if ($noMenu == 1) {
				// 	$rows--;
				// }
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $rows,  $r__[namaMenu]);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $rows,  $cekFile);
				$rows++;

				$sql___ = "SELECT namaMenu, statusMenu FROM app_menu WHERE kodeInduk = '$r__[kodeMenu]' AND kodeInduk != '0' order by urutanMenu";
				$res___ = db($sql___);
				while ($r___ = mysql_fetch_assoc($res___)) {
					$cekFile = $r___[statusMenu] == "f" ? "Tidak Aktif" : "Aktif";

					$objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->setCellValue('D' . $rows,  $r___[namaMenu]);
					$objPHPExcel->getActiveSheet()->setCellValue('E' . $rows,  $cekFile);
					$rows++;
				}
			}
			// $rows--;
		}
	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A4:A' . $rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:C' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:D' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:E' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:E' . $rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:E' . $rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:E' . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

	$objPHPExcel->getActiveSheet()->setTitle("DATA SITEMAP");
	$objPHPExcel->setActiveSheetIndex(0);

	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport . "exp-" . $arrTitle[$s] . date('Y-m-d H:i:s') . ".xls");
}

function submodul(){
  global $db,$s,$id,$inp,$par,$arrParameter;        
  $data = arrayQuery("select concat(kodeModul, '\t', namaModul) from app_modul where kategoriModul='$par[kategoriModul]' and namaModul !='Setting' and statusLink ='s' order by namaModul");  

  return implode("\n", $data);
}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){
      case "submod":
      $text = submodul();
      break;

			case "chk":
				$text = chk();
			break;
			case "delIco":
				if(isset($menuAccess[$s]["edit"])) $text = hapusIcon(); else $text = lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>