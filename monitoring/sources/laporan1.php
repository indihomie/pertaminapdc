<?php		
	function lihat(){
		global $c,$p,$s,$inp,$par,$arrTitle,$menuAccess,$arrMenu,$arrMenu_site,$cntMenu;
		
		$namaSite = getField("select namaSite from app_site where kodeSite='$p'");
		if(empty($arrTitle[$s])) $arrTitle[$s] = $namaSite;
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				<div style=\"margin-top: 10px;\">
				".getBread()."					
				</div>
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<br clear=\"all\" />
				<ol style=\"margin-left:20px;\">";
		$no=1;
		
		$arrMenu[$s] = empty($_GET[s]) ? $arrMenu_site[$p][0] : $arrMenu[$s];
		if (is_array($arrMenu[$s])) {
			  asort($arrMenu[$s]);
			  reset($arrMenu[$s]);
			  while (list($keyMenu, $valMenu) = each($arrMenu[$s])) {
				list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu, $targetMenu) = explode("\t", $valMenu);				
				if ($cntMenu[$kodeMenu] > 0){
					
					if(is_array($arrMenu[$kodeMenu])){
						$text.="<li style=\"list-style: number; font-size:14px; font-weight:bold; margin-bottom:10px;\"><a href=\"#\">$namaMenu</a></li>";
						$no++;
						$text.="<ul>";
						asort($arrMenu[$kodeMenu]);
							while(list($keyAnak, $valAnak) = each($arrMenu[$kodeMenu])){
								list($urutanAnak, $kodeAnak, $namaAnak, $iconAnak, $targetAnak) = explode("\t", $valAnak);
								//$text.="<li style=\"list-style: lower-alpha; font-size:14px; margin-left: 20px; font-weight:bold; margin-bottom:10px;\"><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeAnak . "&s=" . $kodeAnak."\">$namaAnak</a></li>";
								$text.="<li style=\"list-style: lower-alpha; font-size:14px; margin-left: 20px; font-weight:bold; margin-bottom:10px;\"><a href=\"".encode("$c-$p-$kodeAnak-$kodeAnak")."\">$namaAnak</a></li>";
						}
						$text.="</ul>";
					}else{
						//$text.="<li style=\"list-style: number; font-size:14px; font-weight:bold; margin-bottom:10px;\"><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu."\">$namaMenu</a></li>";
						$text.="<li style=\"list-style: number; font-size:14px; font-weight:bold; margin-bottom:10px;\"><a href=\"".encode("$c-$p-$kodeMenu-$kodeMenu")."\">$namaMenu</a></li>";
						$no++;
					}
				}
			  }
		}
		$text.="</ol>
			</div>";
		return $text;
	}	
	
	function getContent($par){
		global $s,$_submit,$menuAccess,$cUsername;
		switch($par[mode]){			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>