<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/menu/";
$fManual = "files/menu";
$arrAkses = array("View", "Add", "Edit", "Delete","Appr Lv1","Appr Lv2","Appr Lv3");
$arrSite = arrayQuery("select kodeSite, namaSite from app_site where statusSite='t' order by urutanSite");

function getContent($par){
  global $s,$_submit,$menuAccess;
  switch($par[mode]){
    case "id":
    $text = id();
    break;

    case "id2":
    $text = id2();
    break;

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

function id() {
  session_start();
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(kodeModul, '\t', namaModul) from app_modul where statusModul = 't' and kategoriModul = '$par[kategoriModul]' order by urutanModul asc");
  $_SESSION[kategoriModul] = $par[kategoriModul];
  
  return implode("\n", $data);
}

function id2() {
  session_start();
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(kodeSite, '\t', namaSite) from app_site where statusSite = 't' and kodeModul = '$par[kodeModul]' order by urutanSite asc");
  $_SESSION[kodeModul] = $par[kodeModul];
  return implode("\n", $data);
}

function lihat(){
  session_start();
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$fManual;
  if(!empty($inp[kategoriModul])){
    $par[kategoriModul] =  $inp[kategoriModul];
  }

  if(!empty($inp[kodeModul])){
    $par[kodeModul] = $inp[kodeModul];
  }

  $cols = 6;  
  $text = table($cols, array($cols,($cols-1)));
  if(empty($par[kodeSite])) $par[kodeSite] = getField("select kodeSite from app_site where statusSite='t' and kodeModul='$kodeModul' order by urutanSite limit 1");
  $text.="
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    ".getBread()."
    <span class=\"pagedesc\">&nbsp;</span>
  </div> 

  <p style=\"position: absolute; right: 20px; top: 10px;\">

  </p>   

  <div id=\"contentwrapper\" class=\"contentwrapper\">
   <form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
     <div id=\"pos_l\" style=\"float:left;\">
       <p>					
        <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$_GET['fSearch']."\" style=\"width:200px;\"/>
        ".comboData("SELECT kodeData, namaData from mst_data where kodeCategory= 'BE' order by urutanData asc","kodeData","namaData","inp[kategoriModul]","Kategori Modul","$par[kategoriModul]","onchange=\"getSubModul('" . getPar($par, "mode,kategoriModul") . "');\"","220px","chosen-select","")."

        ".comboData("SELECT kodeModul, namaModul from app_modul where statusModul = 't' and kategoriModul='$par[kategoriModul]' order by urutanModul asc","kodeModul","namaModul","inp[kodeModul]","Sub Modul","$par[kodeModul]","onchange=\"getKodeSite('" . getPar($par, "mode,kodeModul") . "');\"","220px","chosen-select","")."

        ".comboData("select * from app_site where statusSite='t' and kodeModul='$par[kodeModul]' order by urutanSite","kodeSite","namaSite","par[kodeSite]","All Menu",$par[kodeSite],"onchange=\"document.getElementById('form').submit();\"","220px","chosen-select","")."

      </p>
    </div>

    <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">";
      if(isset($menuAccess[$s]["add"])) {
       $text.="
       <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
       ";
     }
     $text.="
   </div>	
 </form>
 <br clear=\"all\" />
 <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
   <thead>
    <tr>
     <th width=\"20\">No</th>
     <th width=\"*\">Sub Modul - Menu</th>
     <th width=\"70\">View</th>
     <th width=\"70\">D/L</th>
     <th width=\"70\">Size</th>
     <th width=\"100\">Kontrol</th>
   </tr>
 </thead>
 <tbody>";
  $filter ="where statusSite = 't'";

  if(!empty($par[kodeModul])){
    $filter .=" and kodeModul='".$par[kodeModul]."'";
  }

  $sql=db("select * from app_site $filter order by urutanSite asc");
  $no=-1;
  while($r = mysql_fetch_assoc($sql)){
    $no++;
    $levelMax = 2;    
    $filter ="where kodeSite='".$par[kodeSite]."'";

    $sql="select * from app_menu $filter order by urutanMenu";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
      $arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r;
    }

    if(is_array($arrMenu[0])){
      while(list($kodeMenu,$r)=each($arrMenu[0])){
        $no++;
        $fileManual = "$fManual/$r[fileMenu]";
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
        $iconMenu = empty($r[iconMenu]) ? " " : "<img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
        $text.="
        <tr>
          <td>$no.</td>
          <td style=\"padding-left:$paddingMenu;\">$r[namaMenu]</td>
          <td align=\"center\">$r[view]</td>
          <td align=\"center\">$r[download]</td>
          <td align=\"right\">".getSizeFile($fileManual)."</td>";
          if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
            $text.="<td align=\"center\">";         
            if(isset($menuAccess[$s]["add"]) && $r[levelMenu] < $levelMax) $text.="<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeMenu]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
            if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,525);\"><span>Edit</span></a>";
            if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeMenu]','".getPar($par,"mode,kodeMenu")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text.="
          </td>";
        }
        $text.="</tr>";
        list($row, $no)=row($arrMenu, $kodeMenu, $no, $levelMax);
        $text.= $row;
      }
    } 
  }
  $text.="
</tbody>
</table>
";
if($par[mode] == "xls"){			
 xls();			
 $text.="<iframe src=\"download.php?d=exp&f=exp-".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

$text.="
<script>
 jQuery(\"#btnExport\").live('click', function(e){
  e.preventDefault();
  window.location.href=\"?par[mode]=xls\"+\"".getPar($par,"mode")."\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
});
</script>
";
return $text;
}

function row($arrMenu, $kodeInduk, $no, $levelMax){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$fFile; 
  if(is_array($arrMenu[$kodeInduk])){
    while(list($kodeMenu,$r)=each($arrMenu[$kodeInduk])){
      $no++;
      if(empty($r[fileMenu])){
        $r[download] = " - ";
        $r[view] = " - ";

      $fileManual = "$fManual"."$r[fileMenu]";
      
      }else{
        $r[download] = "<a href=\"download.php?d=fileMenu&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
        $r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileMenu&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";

      }
      $statusMenu = $r[statusMenu] == "t"?
      "<img src=\"styles/images/t.png\" title='Active'>":
      "<img src=\"styles/images/f.png\" title='Not Active'>";
      $paddingMenu = 15 + (($r[levelMenu] - 1) * 15)."px";
      $iconMenu = empty($r[iconMenu]) ? " " : "<img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
      $text.="<tr>
      <td>$no.</td>
      <td style=\"padding-left:$paddingMenu;\">$r[namaMenu]</td>

      <td align=\"center\">$r[view]</td>
      <td align=\"center\">$r[download]</td>
      <td align=\"right\">".getSizeFile($fileManual)."</td>";
      if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
        $text.="
        <td align=\"center\">";
          if(isset($menuAccess[$s]["add"]) && $r[levelMenu] < $levelMax) $text.="<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeMenu]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
          if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,525);\"><span>Edit</span></a>";
          if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeMenu]','".getPar($par,"mode,kodeMenu")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
          $text.="
        </td>";
      }
      $text.="</tr>";
      list($row, $no)=row($arrMenu, $kodeMenu, $no, $levelMax);
      $text.= $row;
    }
  }
  return array($text, $no);
}

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
  
  $sql="update app_menu set namaMenu='$inp[namaMenu]', targetMenu='$inp[targetMenu]', parameterMenu='$inp[parameterMenu]', aksesMenu='$aksesMenu', iconMenu='$iconMenu', fileMenu='$fileMenu', urutanMenu='".setAngka($inp[urutanMenu])."', statusMenu='$inp[statusMenu]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$par[kodeMenu]'";
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
  
  $sql="insert into app_menu (kodeMenu, kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, parameterMenu, aksesMenu, iconMenu, fileMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime) values ('$kodeMenu', '$kodeModul', '$par[kodeSite]', '$par[kodeInduk]', '$inp[namaMenu]', '$inp[targetMenu]', '$inp[parameterMenu]', '$aksesMenu', '$iconMenu', '$fileMenu', '".setAngka($inp[urutanMenu])."', '$inp[statusMenu]', '$levelMenu', '$cUsername', '".date('Y-m-d H:i:s')."')";
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
                <label class=\"l-input-small\">Order</label>
                <div class=\"field\">
                  <input type=\"text\" id=\"inp[urutanMenu]\" name=\"inp[urutanMenu]\"  value=\"".getAngka($r[urutanMenu])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
                </div>
              </p>          
              <p>
                <label class=\"l-input-small\">Status</label>
                <div class=\"fradio\">
                  <input type=\"radio\" id=\"true\" name=\"inp[statusMenu]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
                  <input type=\"radio\" id=\"false\" name=\"inp[statusMenu]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
                  <input type=\"hidden\" id=\"count\" name=\"count\" value=\"".(getField("select count(*) from app_menu where kodeInduk='$par[kodeMenu]' and statusMenu='t'") + getField("select count(*) from mst_category where kodeMenu='$par[kodeMenu]'"))."\">
                </div>
              </p>
              
            </div>
            <p style=\"position:absolute; right:20px; top:14px;\">
              <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
              <input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
            </p>
          </form> 
        </div>";
        return $text;
      }

      ?>