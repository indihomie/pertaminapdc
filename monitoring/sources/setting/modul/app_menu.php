<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "images/menu/";
	$fManual = "files/menu/";
	//$arrAkses = array("View", "Add", "Edit", "Delete","Appr Lv1","Appr Lv2","Appr Lv3");
	$arrAkses = array("View", "Add", "Edit", "Delete","Level 1","Level 2","Level 3");
	$arrSite = arrayQuery("select kodeSite, namaSite from app_site where statusSite='t' order by urutanSite");
	
	function chk(){
		global $inp,$par;
		if(getField("select kodeMenu from app_menu where kodeInduk='$par[kodeMenu]' and statusMenu='t'") || getField("select kodeMenu from mst_category where kodeMenu='$par[kodeMenu]'"))
		return "sorry, data has been use";
	}
	
	function hapusIcon(){
		global $s,$inp,$par,$fFile,$cUsername;
		$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		if(file_exists($fFile.$iconMenu) and $iconMenu!="")unlink($fFile.$iconMenu);
		
		$sql="update app_menu set iconMenu='' where kodeMenu='$par[kodeMenu]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
	
	function hapusFile(){
		global $s,$inp,$par,$fManual,$cUsername;
		$fileMenu = getField("select fileMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		if(file_exists($fManual.$fileMenu) and $fileMenu!="")unlink($fManual.$fileMenu);
		
		$sql="update app_menu set fileMenu='' where kodeMenu='$par[kodeMenu]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$fFile,$fManual,$cUsername;
		$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		if(file_exists($fFile.$iconMenu) and $iconMenu!="")unlink($fFile.$iconMenu);
		
		$fileMenu = getField("select fileMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		if(file_exists($fManual.$fileMenu) and $fileMenu!="")unlink($fManual.$fileMenu);
		
		$sql="delete from app_menu where kodeMenu='$par[kodeMenu]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,kodeMenu,kodeInduk")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$acc,$arrAkses,$fFile,$fManual,$cUsername;			
		
		$fileIcon = $_FILES["iconMenu"]["tmp_name"];
		$fileIcon_name = $_FILES["iconMenu"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$iconMenu = "ico-".$par[kodeMenu].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $iconMenu);			
		}
		if(empty($iconMenu)) $iconMenu = getField("select iconMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		
		$fileFile = $_FILES["fileMenu"]["tmp_name"];
		$fileFile_name = $_FILES["fileMenu"]["name"];
		if(($fileFile!="") and ($fileFile!="none")){						
			fileUpload($fileFile,$fileFile_name,$fManual);			
			$fileMenu = "manual-".$par[kodeMenu].".".getExtension($fileFile_name);
			fileRename($fManual, $fileFile_name, $fileMenu);			
		}
		if(empty($fileMenu)) $fileMenu = getField("select fileMenu from app_menu where kodeMenu='$par[kodeMenu]'");
		
		repField();
		
		$aksesMenu = "";
		if(is_array($arrAkses)){
			while(list($kodeAkses)=each($arrAkses)){
				$aksesMenu.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
			}
		}					
		
		$sql="update app_menu set namaMenu='$inp[namaMenu]', viewMenu='$inp[viewMenu]',targetMenu='$inp[targetMenu]', parameterMenu='$inp[parameterMenu]', aksesMenu='$aksesMenu', iconMenu='$iconMenu', fileMenu='$fileMenu', urutanMenu='".setAngka($inp[urutanMenu])."', statusMenu='$inp[statusMenu]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."',kode='$inp[kode]' where kodeMenu='$par[kodeMenu]'";
		db($sql);
		
		$sql="update mst_category set namaCategory='$inp[namaMenu]', urutanCategory='".setAngka($inp[urutanMenu])."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$par[kodeMenu]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$acc,$arrAkses,$fFile,$fManual,$cUsername,$kodeModul;		
		$kodeMenu=getField("select kodeMenu from app_menu order by kodeMenu desc")+1;
		$levelMenu=getField("select levelMenu from app_menu where kodeMenu='$par[kodeInduk]'") + 1;
		
		$fileIcon = $_FILES["iconMenu"]["tmp_name"];
		$fileIcon_name = $_FILES["iconMenu"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$iconMenu = "ico-".$kodeMenu.".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $iconMenu);			
		}
			
		$fileFile = $_FILES["fileMenu"]["tmp_name"];
		$fileFile_name = $_FILES["fileMenu"]["name"];
		if(($fileFile!="") and ($fileFile!="none")){						
			fileUpload($fileFile,$fileFile_name,$fManual);			
			$fileMenu = "manual-".$kodeMenu.".".getExtension($fileFile_name);
			fileRename($fManual, $fileFile_name, $fileMenu);			
		}
				
		repField();
		
		$aksesMenu = "";
		if(is_array($arrAkses)){
			while(list($kodeAkses)=each($arrAkses)){
				$aksesMenu.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
			}
		}		
		
		$sql="insert into app_menu (kodeMenu, kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, parameterMenu, aksesMenu, iconMenu, viewMenu, fileMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime, kode) values ('$kodeMenu', '$kodeModul', '$par[kodeSite]', '$par[kodeInduk]', '$inp[namaMenu]', '$inp[targetMenu]', '$inp[parameterMenu]', '$aksesMenu', '$iconMenu', '$inp[viewMenu]','$fileMenu', '".setAngka($inp[urutanMenu])."', '$inp[statusMenu]', '$levelMenu', '$cUsername', '".date('Y-m-d H:i:s')."','$inp[kode]')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $s,$inp,$par,$fFile,$fManual,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$sql="select * from app_menu where kodeMenu='$par[kodeMenu]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);									
				
		if(empty($r[kodeInduk])) $r[kodeInduk] = $par[kodeInduk];
		if(empty($r[urutanMenu])) $r[urutanMenu] = getField("select urutanMenu from app_menu where kodeInduk='$par[kodeInduk]' and kodeSite='$par[kodeSite]' order by urutanMenu desc limit 1") + 1;
				
		$false =  $r[statusMenu] == "f" ? "checked=\"checked\"" : "";
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		
		$true1 =  $r[viewMenu] == "t" ? "checked=\"checked\"" : "";
		$false1 =  empty($true1) ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[namaMenu]","you must fill menu");
		setValidation("is_null","inp[urutanMenu]","you must fill order");
		$text = getValidation();	

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
					<div style=\"top:13px; right:35px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
			</div>
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Sub Modul</label>
						<span class=\"field\">";
				$text.= empty($r[kodeInduk]) ? $arrSite["$par[kodeSite]"] : getField("select namaMenu from app_menu where kodeMenu='$r[kodeInduk]'");
				$text.="</span>
					</p>
					<p>
						<label class=\"l-input-small\">Menu</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaMenu]\" name=\"inp[namaMenu]\"  value=\"$r[namaMenu]\" class=\"mediuminput\" maxlength=\"150\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Target</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[targetMenu]\" name=\"inp[targetMenu]\"  value=\"$r[targetMenu]\" class=\"mediuminput\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Parameter</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[parameterMenu]\" name=\"inp[parameterMenu]\"  value=\"$r[parameterMenu]\" class=\"mediuminput\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Icon</label>
						<div class=\"field\">";
							$text.=empty($r[iconMenu])?
								"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"iconMenu\" name=\"iconMenu\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[fileMenu])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileMenu\" name=\"fileMenu\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<img src=\"".getIcon($r[fileMenu])."\" align=\"left\"style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete file ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Rules</label>
						<div class=\"field\">";
							if(is_array($arrAkses)){
                                $cx=0;
								while(list($kodeAkses,$namaAkses)=each($arrAkses)){
                                  if($cx<4){
									$disabled = empty($kodeAkses) ? "disabled" : "";
									$checked = (substr($r[aksesMenu],$kodeAkses,1) == 1 || empty($kodeAkses)) ? "checked" : "";
									$text.="<input type=\"checkbox\" id=\"acc[$kodeAkses]\" name=\"acc[$kodeAkses]\" value=\"1\" $checked $disabled /> <span style=\"margin-right:30px;\">$namaAkses</span>";	
                                  }
                                    $cx++;
								}
							}	
						$text.="<br clear=\"all\">
						</div>
					</p>
                    <p>
						<label class=\"l-input-small\">Approval</label>
						<div class=\"field\">";
							if(is_array($arrAkses)){
                              reset($arrAkses);
                                $cx=0;
								while(list($kodeAkses,$namaAkses)=each($arrAkses)){
                                  if($cx>3){
									$disabled = empty($kodeAkses) ? "disabled" : "";
									$checked = (substr($r[aksesMenu],$kodeAkses,1) == 1 || empty($kodeAkses)) ? "checked" : "";
									$text.="<input type=\"checkbox\" id=\"acc[$kodeAkses]\" name=\"acc[$kodeAkses]\" value=\"1\" $checked $disabled /> <span style=\"margin-right:30px;\">$namaAkses</span>";	
                                  }
                                  $cx++;
								}
							}	
						$text.="<br clear=\"all\">
						</div>
					</p>					
					<p>
						<table>
							<tr>
								<td style=\"width:50%;\">
									<label class=\"l-input-small\" style=\"width:150px;\">Order</label>
									<input type=\"text\" id=\"inp[urutanMenu]\" name=\"inp[urutanMenu]\"  value=\"".getAngka($r[urutanMenu])."\" class=\"mediuminput\" style=\"width:50px; text-align:right; margin-right:20px;\" onkeyup=\"cekAngka(this);\" />
								</td>
								<td style=\"width:50%;\">
									<label class=\"l-input-small\">Kode</label>
									<input type=\"text\" id=\"inp[kode]\" name=\"inp[kode]\"  value=\"$r[kode]\" class=\"mediuminput\" style=\"width:100px;\"/>
								</td>
							</tr>
						</table>
					</p>						
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusMenu]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusMenu]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
							<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".(getField("select count(*) from app_menu where kodeInduk='$par[kodeMenu]' and statusMenu='t'") + getField("select count(*) from mst_category where kodeMenu='$par[kodeMenu]'"))."\">
						</div>
					</p>
<p>
						<label class=\"l-input-small\">View Menu</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true1\" name=\"inp[viewMenu]\" value=\"t\" $true1 /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false1\" name=\"inp[viewMenu]\" value=\"f\" $false1 /> <span class=\"sradio\">Not Active</span>
						</div>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$kodeModul,$fFile;
		if(empty($par[kodeSite])) $par[kodeSite] = getField("select kodeSite from app_site where statusSite='t' and kodeModul='$kodeModul' order by urutanSite limit 1");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"35\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search..\"/>
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>		
			<div id=\"pos_r\">
			Sub Modul : ".comboData("select * from app_site where statusSite='t' and kodeModul='$kodeModul' order by urutanSite","kodeSite","namaSite","par[kodeSite]","",$par[kodeSite],"onchange=\"document.getElementById('form').submit();\"")."";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" style=\"margin-left:5px;\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeMenu")."',825,575);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan = \"2\" style =\"vertical-align:middle\" width=\"20\">No.</th>
					<th rowspan = \"2\" style =\"vertical-align:middle\" width=\"20\">Icon</th>

					<th rowspan = \"2\" style =\"vertical-align:middle\">Menu</th>
					<th rowspan=\"2\" styl=\"vertical-align:middle\" width=\"100\">Kode</th>
					<th colspan = \"2\" style =\"vertical-align:middle\" width=\"100\">Manual Book</th>
					<th rowspan = \"2\" style =\"vertical-align:middle\" width=\"50\">Order</th>
					<th rowspan = \"2\" style =\"vertical-align:middle\" width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan = \"2\" width=\"75\">Control</th>";
		$text.="</tr>
				<tr>
					<th width=\"50\">VIEW</th>
					<th width=\"50\">DL</th>
				</tr>
			</thead>
			<tbody>";
		
		$levelMax = 2;		
		$filter ="where kodeSite='".$par[kodeSite]."'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(namaMenu) like '%".strtolower($par[filter])."%'				
		)";
	
		$sql="select * from app_menu $filter order by urutanMenu";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r;
		}
		
		if(is_array($arrMenu[0])){
			while(list($kodeMenu,$r)=each($arrMenu[0])){
				$no++;
				if(empty($r[fileMenu])){
					$r[download] = " - ";
					$r[view] = " - ";
					
				}else{
					$r[download] = "<a href=\"download.php?d=fileMenu&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
					$r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileMenu&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					
				}
				$statusMenu = $r[statusMenu] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";
				$paddingMenu = 15 + (($r[levelMenu] - 1) * 15)."px";
				$text.="<tr>
						<td>$no.</td>
							<td align=\"center\"><img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\"></td>
						<td style=\"padding-left:$paddingMenu;\">$r[namaMenu]</td>
						<td align=\"right\">$r[kodeMenu]</td>
						<td align=\"center\">$r[view]</td>
						<td align=\"center\">$r[download]</td>
						<td align=\"right\"><font color=\"".$arrColor["$r[levelMenu]"]."\">".getAngka($r[urutanMenu])."</font></td>					
						<td align=\"center\">$statusMenu</td>";
				if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
					$text.="<td align=\"center\">";					
					if(isset($menuAccess[$s]["add"]) && $r[levelMenu] < $levelMax) $text.="<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeMenu]".getPar($par,"mode,kodeInduk")."',825,575);\"><span>Add</span></a>";
					if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,575);\"><span>Edit</span></a>";
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeMenu]','".getPar($par,"mode,kodeMenu")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
					$text.="</td>";
				}
				$text.="</tr>";
				list($row, $no)=row($arrMenu, $kodeMenu, $no, $levelMax);
				$text.= $row;
			}
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}	
	
	function row($arrMenu, $kodeInduk, $no, $levelMax){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;	
  		if(is_array($arrMenu[$kodeInduk])){
			while(list($kodeMenu,$r)=each($arrMenu[$kodeInduk])){
				$no++;
				if(empty($r[fileMenu])){
					$r[download] = " - ";
					$r[view] = " - ";
					
				}else{
					$r[download] = "<a href=\"download.php?d=fileMenu&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
					$r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileMenu&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					
				}
				$statusMenu = $r[statusMenu] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";
				$paddingMenu = 15 + (($r[levelMenu] - 1) * 15)."px";
				$text.="<tr>
						<td>$no.</td>
							<td align=\"center\"><img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\"></td>
						<td style=\"padding-left:$paddingMenu;\">$r[namaMenu]</td>
						<td align=\"right\">$r[kodeMenu]</td>
						<td align=\"center\">$r[view]</td>
						<td align=\"center\">$r[download]</td>
						<td align=\"right\"><font color=\"".$arrColor["$r[levelMenu]"]."\">".getAngka($r[urutanMenu])."</a></td>					
						<td align=\"center\">$statusMenu</td>";
				if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
					$text.="<td align=\"center\">";
					if(isset($menuAccess[$s]["add"]) && $r[levelMenu] < $levelMax) $text.="<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeMenu]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
					if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,525);\"><span>Edit</span></a>";
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeMenu]','".getPar($par,"mode,kodeMenu")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
					$text.="</td>";
				}
				$text.="</tr>";
				list($row, $no)=row($arrMenu, $kodeMenu, $no, $levelMax);
				$text.= $row;
			}
		}
		return array($text, $no);
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){
			case "chk":
				$text = chk();
			break;
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
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
