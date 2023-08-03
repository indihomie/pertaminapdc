<?php
include "global.php";

//url redirect
if(
	$environment == "production" &&
	empty($_submit) &&
	!isset($_POST["btnSimpan"]) &&
	(preg_match("/&s=/i", $_SERVER['REQUEST_URI']) || preg_match("/par\[/i", $_SERVER['REQUEST_URI']))
){
	$variable = empty($var) ? "" : "?var=".encode($var);
	header('Location: '.APP_URL."/".encode($decode).$variable);
}
//end redirect

if (empty($c)) {
    echo "<script>window.location='main'</script>";
}

if (!getUser()) {
    echo "<script>window.location='logout'</script>";
    exit();
}

$kodeInfo = 1;
$sql = "select * from app_info where kodeInfo='$kodeInfo'";
$res = db($sql);
$r = mysql_fetch_array($res);

if (empty($p)) {
    $p = key($arrSite);
}

if (empty($m)) {
    $arrM = $arrInduk;
    arsort($arrM[$p]);
    reset($arrM[$p]);
    if (is_array($arrM[$p])) {
        arsort($arrM[$p]);
        reset($arrM[$p]);
        while (list($id) = each($arrM[$p])) {
            if ($cntMenu[$id] > 0) {
                $m = $id;
            }

        }
    }
}

if (empty($s)) {
    $arrS = $arrMenu;
    arsort($arrM[$m]);
    reset($arrS[$m]);
    if (is_array($arrS[$m])) {
        arsort($arrS[$m]);
        reset($arrS[$m]);
        while (list($id) = each($arrS[$m])) {
            if ($cntMenu[$id] > 0) {
                $s = $id;
            }

        }
    }
}

if (empty($s)) {
    $s = $m;
}

//if(empty($_GET[p]) && empty($_GET[m]) && empty($_GET[s])) echo "<script>window.location='main'</script>";
//echo "<script>window.location='?".getPar($par)."'</script>";

list($namaUser, $fotoUser) = explode("\t", getField("select concat(namaUser, '\t', fotoUser) from app_user where username='$cUsername'"));
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $r[keteranganInfo]; ?></title>
  <link href="favicon.ico" rel="shortcut icon" />
  <link rel="stylesheet" href="styles/styles.css" type="text/css" />
  <link rel="stylesheet" href="scripts/orgchart/jquery.orgchart.css" type="text/css" />
  <link href="plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" rel="stylesheet">
  <link href="plugins/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" rel="stylesheet">
  <link href="plugins/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" rel="stylesheet">
  <script type="text/javascript" src="scripts/jquery.js"></script>
  <script type="text/javascript" src="scripts/custom.js"></script>
  <script type="text/javascript" src="scripts/cookie.js"></script>
  <script type="text/javascript" src="scripts/data.js"></script>
  <script type="text/javascript" src="scripts/color.js"></script>
  <script type="text/javascript" src="scripts/uniform.js"></script>
  <script type="text/javascript" src="scripts/time.js"></script>
  <script type="text/javascript" src="scripts/chosen.js"></script>
  <script type="text/javascript" src="scripts/general.js"></script>
  <script type="text/javascript" src="scripts/tables.js"></script>
  <script type="text/javascript" src="scripts/tinybox.js"></script>
  <script type="text/javascript" src="scripts/autoNumeric.js"></script>
  <script type="text/javascript" src="scripts/jquery.chained.min.js"></script>
  <script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
  <script type="text/javascript" src="scripts/jquery.redirect.min.js"></script>
  <script type="text/javascript" src="scripts/jquery.validate.min.js"></script>
  <script type="text/javascript" src="scripts/webcamjs/webcam.min.js"></script>
  <script type="text/javascript" src="scripts/datatablesExt.fnReloadAjax.js"></script>
  <script type="text/javascript" src="scripts/basejs.js"></script>
  <script type="text/javascript" src="scripts/orgchart/jquery.orgchart.min.js"></script>
  <script type="text/javascript" src="scripts/jquery.autocomplete.min.js"></script>

  <script src="plugins/fancybox/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>
  <script src="plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
  <script src="plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <script src="plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
  <script src="plugins/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

  <script type="text/javascript">
  window.MAPEXPLORER_MAPPATH = "charts/";
  </script>
  <script type="text/javascript" src="charts/FusionCharts.js"></script>
  <script type="text/javascript" src="charts/helper.js"></script>
  <script type="text/javascript" src="charts/theme.js"></script>
  <script type="text/javascript" src="charts/maplist-core-pack.js"></script>
  <script type="text/javascript">
  var baseUrl = '<?=APP_URL?>';
  var suri = '<?=$_SERVER["REQUEST_URI"]?>';
  var sop = suri.split("index.php").join("");
  sop = sop.substr(0, sop.indexOf("?")) + "popup.php" + sop.substr(sop.indexOf("?"));
  sop = removeParameter(sop, "mode");
  var sajax = suri.split("index.php").join("");
  sajax = sajax.substr(0, sajax.indexOf("?")) + "ajax.php" + sajax.substr(sajax.indexOf("?"));
  var ot;
  </script>
  <script src="amcharts/amcharts.js" type="text/javascript"></script>
  <script src="amcharts/serial.js"></script>
  <script src="amcharts/pie.js" type="text/javascript"></script>
  <script src="amcharts/plugins/export/export.min.js"></script>
  <link rel="stylesheet" href="amcharts/plugins/export/export.css" type="text/css" media="all" />
  <script src="amcharts/themes/light.js"></script>
  <?php
$pgIsView = isset($menuAccess[$s]["view"]) ? "view" : "";
$pgIsAdd = isset($menuAccess[$s]["add"]) ? "add" : "";
$pgIsEdit = isset($menuAccess[$s]["edit"]) ? "edit" : "";
$pgIsDelete = isset($menuAccess[$s]["delete"]) ? "del" : "";
if($arrInfo[$s]=='t'){
    setFullscreen2();
}
echo "<script>
  var pgIsView = '$pgIsView'
  var pgIsAdd = '$pgIsAdd'
  var pgIsEdit = '$pgIsEdit'
  var pgIsDel = '$pgIsDelete';
</script>";
?>
</head>
<!-- <?php echo selisihMenit($cSession, $nSession); ?> -->
<script>
function startTime() {
  var today = new Date();
  var h = today.getHours();
  var m = today.getMinutes();
  var s = today.getSeconds();
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById('jam').innerHTML =
  h + ":" + m + ":" + s;
  var t = setTimeout(startTime, 1000);
}
function checkTime(i) {
  if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
  return i;
}
</script>
<body onload="startTime()"
  class="<?php echo getField("select lower(trim(namaSite)) from app_site where kodeSite='$p'") != "laporan" && getField("select menuStatus from app_site where kodeSite='$p'") != "f" ? "withvernav" : "" ?>">
  <?php
    valPar($par);
    ?>
    
  <div class="bodywrapper">
    <div class="topheader">
      <div class="companyinfof" style="top: 95px !important;">
        <?php
        $arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
        
        $namaKategori = getField("SELECT t1.namaData FROM mst_data t1 JOIN app_modul t2 ON(t1.kodeData=t2.kategoriModul) WHERE t2.kodeModul='$c'");
        
        $namaModul = getField("SELECT t1.keteranganData FROM mst_data t1 JOIN app_modul t2 ON(t1.kodeData=t2.kategoriModul) WHERE t2.kodeModul='$c'");
        
        echo "<table style=\"width:100%;\">
              <tr>
              <td style=\"vertical-align:top; padding-right:10px;\">" . $arrHari[date('w')] . ", " . getTanggal(date('Y-m-d'), "t") . ", <span id=\"jam\"></span></td>
              <td style=\"vertical-align:top;\" width='15'> | </td>
              <td style=\"vertical-align:top; padding-right:10px;\">" . $namaKategori . "</td>
              </tr>
            </table>";

        ?>
      </div>
      
      <div class="userinfof">
        <span><?php
$fotoUser = empty($fotoUser) ? "styles/images/foto.png" : "images/user/" . $fotoUser;
echo "<table style=\"width:100%;\">
          <tr>
           <td align=\"center\" style=\"vertical-align:top; padding-right:5px;\"><img src=\"$fotoUser\" height=\"20\" ></td>
           <td style=\"vertical-align:top; padding-right:10px;\"><a href=\"#\" class=\"menu\" onclick=\"openBox('popup.php?par[mode]=profile',825,390);\">" . $namaUser . "</a></td>
           <td style=\"vertical-align:top;\" width='15'> | </td>
           <td style=\"vertical-align:top; padding-right:10px;\"><a href=\"main\" class=\"menu\"><strong>HOME</strong></a></td>
           <td style=\"vertical-align:top;\" width='15'> | </td>
           <td style=\"vertical-align:top; padding-right:10px;\"><a href=\"#\" class=\"menu\" onclick=\"openBox('view.php?doc=manual&par[kodeMenu]=$s',925,625);\"><strong>HELP</strong></a></td>
           <td style=\"vertical-align:top;\" width='15'> | </td>
           <td style=\"vertical-align:top; padding-left:10px;\"><a href=\"logout\" class=\"menu\" onclick=\"return confirm('are you sure to logout ?');\"><strong>LOGOUT</strong></a></td>
         </tr>
       </table>";
?></span>
      </div>
      <!--userinfo-->
      <div class="left">
        <h1 class="logo" style="background: url(images/info/<?php echo $r[fileInfo] ?>) no-repeat; ">
          <?php
          echo "<span>" .$namaModul. "</span> ";
          ?>&nbsp;
        </h1>
        <br clear="all" />
        <span class="slogan"><?php echo $r[keteranganInfo]; ?></span>
      </div>
      <!--left-->

      <div class="right">
        <ul class="headermenu">
          <?php
if (is_array($arrInduk)) {
    arsort($arrInduk);
    reset($arrInduk);
    while (list($kodeSite) = each($arrInduk)) {
        if (is_array($arrInduk[$kodeSite])) {
            arsort($arrInduk[$kodeSite]);
            reset($arrInduk[$kodeSite]);
            while (list($keyInduk, $valInduk) = each($arrInduk[$kodeSite])) {
                list($urutanInduk, $kodeInduk, $namaInduk, $iconInduk) = explode("\t", $valInduk);
                if ($cntMenu[$kodeInduk] > 0) {
                    $arrDef[$kodeSite][$keyInduk] = $kodeInduk . "\t" . $kodeInduk;
                    $arrKey[$kodeSite] = $kodeInduk . "\t" . $kodeInduk;
                    if (is_array($arrMenu[$kodeInduk])) {
                        arsort($arrMenu[$kodeInduk]);
                        reset($arrMenu[$kodeInduk]);
                        while (list($keyMenu, $valMenu) = each($arrMenu[$kodeInduk])) {
                            list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu) = explode("\t", $valMenu);
                            if ($cntMenu[$kodeMenu] > 0) {
                                $arrDef[$kodeSite][$keyInduk] = $kodeMenu . "\t" . $kodeMenu;
                                $arrKey[$kodeSite] = $kodeInduk . "\t" . $kodeMenu;
                                if (is_array($arrMenu[$kodeMenu])) {
                                    arsort($arrMenu[$kodeMenu]);
                                    reset($arrMenu[$kodeMenu]);
                                    while (list($keyMenu1, $valMenu1) = each($arrMenu[$kodeMenu])) {
                                        list($urutanMenu1, $kodeMenu1, $namaMenu1, $iconMenu1) = explode("\t", $valMenu1);
                                        if ($cntMenu[$kodeMenu1] > 0) {
                                            $arrDef[$kodeSite][$keyInduk] = $kodeMenu . "\t" . $kodeMenu1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

if (empty($m)) {
    list($m, $s) = explode("\t", $arrDef[$p][$m]);
}

if (empty($m)) {
    $m = $s = $p;
}

$sql = "select * from app_site where statusSite='t' order by urutanSite";
$res = db($sql);
$cnt = mysql_fetch_row($res);
while ($r = mysql_fetch_array($res)) {
    if ($cntMenu["s" . "$r[kodeSite]"] > 0) {
        list($kodeMenu, $kodeMenu1) = explode("\t", $arrKey["$r[kodeSite]"]);
        $current = $p == $r[kodeSite] ? "class=\"current\"" : "";
        $iconSite = empty($r[iconSite]) ? "styles/images/paper.png" : "images/menu/" . $r[iconSite];

        if ($cnt > 1) {
			/*
            echo getField("select lower(trim(namaSite)) from app_site where kodeSite='$r[kodeSite]'") != "laporan" && $r[menuStatus] !='f' ?
            "<li " . $current . "><a href=\"index.php?c=" . $c . "&p=" . $r[kodeSite] . "&m=" . $kodeMenu . "&s=" . $kodeMenu1 . "\"><span style=\"height: 32px; display: block; margin-bottom: 8px; background: url(" . $iconSite . ") no-repeat center center; background-size:32px 32px;\"></span>" . $r[namaSite] . "</a></li>" :
            "<li " . $current . "><a href=\"index.php?c=" . $c . "&p=" . $r[kodeSite] . "\"><span style=\"height: 32px; display: block; margin-bottom: 8px; background: url(" . $iconSite . ") no-repeat center center; background-size:32px 32px;\"></span>" . $r[namaSite] . "</a></li>";
            */
			echo getField("select lower(trim(namaSite)) from app_site where kodeSite='$r[kodeSite]'") != "laporan" && $r[menuStatus] !='f' ?
            "<li " . $current . "><a href=\"".encode("$c-$r[kodeSite]-$kodeMenu-$kodeMenu1")."\"><span style=\"height: 32px; display: block; margin-bottom: 8px; background: url(" . $iconSite . ") no-repeat center center; background-size:32px 32px;\"></span>" . $r[namaSite] . "</a></li>" :
            "<li " . $current . "><a href=\"".encode("$c-$r[kodeSite]-0-0")."\"><span style=\"height: 32px; display: block; margin-bottom: 8px; background: url(" . $iconSite . ") no-repeat center center; background-size:32px 32px;\"></span>" . $r[namaSite] . "</a></li>";
        }

    }
}
?>
        </ul>

      </div>
      <!--right-->
    </div>
    <!--topheader-->

    <div class="header">
    </div>
    <!--header-->
    <?php
$mstCategory = arrayQuery("select kodeCategory, kodeMenu from mst_category order by kodeCategory");
$arrMenu = $arrMenu_site[$p];

if (is_array($arrMenu[0]) && getField("select lower(trim(namaSite)) from app_site where kodeSite='$p'") != "laporan" && getField("select menuStatus from app_site where kodeSite='$p'") != "f" ){
    echo "<div class=\"vernav2 iconmenu\">";
    echo "<ul>";
    asort($arrMenu[0]);
    reset($arrMenu[0]);
    while (list($keyMenu, $valMenu) = each($arrMenu[0])) {
        list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu, $targetMenu,,$viewMenu) = explode("\t", $valMenu);
        if ($cntMenu[$kodeMenu] > 0) {
            //$linkMenu = empty($targetMenu) ? "#" . $kodeMenu : "index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu;
			$linkMenu = empty($targetMenu) ? "#" . $kodeMenu : encode("$c-$p-$kodeMenu-$kodeMenu");
            $iconMenu = empty($iconMenu) ? "styles/images/poin.png" : "images/menu/" . $iconMenu;
            $current = $m == $kodeMenu ? "class=\"current\"" : "";
$wew = $viewMenu == 't'? $namaMenu."" : $namaMenu;

            echo "<li " . $current . "><a href=\"" . $linkMenu . "\" " . (strlen($wew) >= 25 ? "title=\"$wew\"" : "") . "><img src=\"" . $iconMenu . "\" style=\"float:left; margin-right:5px;\" width=\"20\" height=\"20\"> <span>" . (strlen($wew) >= 25 ? substr($wew, 0, 25) . "..." : $wew) . "</span></a>";


            if (is_array($arrMenu[$kodeMenu]) && !in_array($targetMenu, array("laporan"))) {
                asort($arrMenu[$kodeMenu]);
                reset($arrMenu[$kodeMenu]);
                echo "<span class=\"arrow\"></span>
              <ul id=\"" . $kodeMenu . "\">";
                while (list($keyMenu1, $valMenu1) = each($arrMenu[$kodeMenu])) {
                    list($urutanMenu1, $kodeMenu1, $namaMenu1, $iconMenu1, $targetMenu1) = explode("\t", $valMenu1);
                    if ($cntMenu[$kodeMenu1] > 0) {
                        //$linkMenu1 = in_array($kodeMenu1, $mstCategory) ? "index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu1 . "&par[mode]=det&par[kodeCategory]=" . array_search($kodeMenu1, $mstCategory) : "index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu1;
						
                        $var = ($environment == 'production') ? "?var=" : "?";
                        
                        $linkMenu1 = in_array($kodeMenu1, $mstCategory) ? encode("$c-$p-$kodeMenu-$kodeMenu1"). "?var=" .encode("par[mode]=det&par[kodeCategory]=" . array_search($kodeMenu1, $mstCategory). "") : encode("$c-$p-$kodeMenu-$kodeMenu1");

                        if ($kodeMenu == 57) {
                            $linkMenu1 = $linkMenu1 . "&empid=" . $_GET[empid];
                        }

                        $iconMenu1 = empty($iconMenu1) ? "" : "<img src=\"images/menu/" . $iconMenu1 . "\" style=\"float:left; margin-left:5px; margin-right:5px;\" width=\"20\" height=\"20\">";
                        $current = $s == $kodeMenu1 ? "class=\"current\"" : "";
                        echo "<li " . $current . "><a href=\"" . $linkMenu1 . "\" " . (strlen($namaMenu1) >= 25 ? "title=\"$namaMenu1\"" : "") . ">" . $iconMenu1 . " " . (strlen($namaMenu1) >= 25 ? substr($namaMenu1, 0, 25) . "..." : $namaMenu1) . "</a>";
                    }
                }
                echo "</ul>";
            }
            echo "</li>";
        }
    }
    echo "<a class=\"togglemenu\"></a>
    <br />
    <br />
  </ul>
</div>";
}

if (is_file($arrScript[$s])) {
    echo "<script type=\"text/javascript\" src=\"" . $arrScript[$s] . "\"></script>";
} else {
    echo "<script type=\"text/javascript\" src=\"sources/js/default.js\"></script>";
}

if(getField("select lower(trim(namaSite)) from app_site where kodeSite='$p'") == "laporan" && empty($_GET[m]) && empty($_GET[s]) ){
    $arrSource[$s] = "sources/laporan.php";
}elseif(getField("select menuStatus from app_site where kodeSite='$p'") == "f"){
        $arrSource[$s] = "sources/laporan1.php";

}else{
    $arrSource[$s];
}





$srcDir = $arrSource[$s];
if (strpos($srcDir, "?") > -1) {
    $srcDir = substr($srcDir, 0, strpos($srcDir, "?"));
    $query_str = parse_url($arrSource[$s], PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    foreach ($query_params as $key => $value) {
        $key = str_replace(".php", "", $value);
        global $$key;
    }
}
$srcDir = str_replace(".php", "", $srcDir);
$srcDirPath = substr($srcDir, (strripos($srcDir, "/") + 1));

echo getField("select lower(trim(namaSite)) from app_site where kodeSite='$p'") != "laporan" && getField("select menuStatus from app_site where kodeSite='$p'") != "f" ? "<div class=\"centercontent contentpopup\">" : "<div class=\"contentfull\">";
if (empty($c)) {
    include "sources/index.php";
} else {
    include is_file($arrSource[$s]) ? $arrSource[$s] : (is_dir($srcDir) ? $srcDir . "/" . $srcDirPath . "_index.php" : "sources/index.php");
}

if (function_exists('getContent')) {
    echo getContent($par);
}

echo "<br clear=\"all\" /></div>";
?>
    <!--leftmenu-->
  </div>
  <!--bodywrapper-->
  

  <script type="text/javascript">
  jQuery(document).ready(function() {

    joinTable();

  });

  function showDoc(elem) {
    openBox('popup.php?par[mode]=showDetailDoc&par[idDoc]=' + elem + '', 800, 520);
  }

  jQuery(".fancybox-effects").fancybox({
    openEffect: 'elastic',
    openSpeed: 150,
    closeEffect: 'elastic',
    closeSpeed: 150,
    helpers: {
      title: {
        type: 'over'
      }
    }
  });
  </script>

<script type="text/javascript" src="plugins/tinymce/jquery.tinymce.js"></script>
<script>
  jQuery(document).ready(function () {
    jQuery('textarea.tinymce').tinymce({
      script_url: 'plugins/tinymce/tiny_mce.js',
      theme: "advanced",
      skin: "themepixels",
      width: "100%",
      plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
      inlinepopups_skin: "themepixels",
      theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent,blockquote,formatselect,fontselect,fontsizeselect",
      theme_advanced_buttons2: "pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,image,help,code,|,preview,|,forecolor,backcolor,removeformat,|,charmap,media,|,fullscreen",
      theme_advanced_buttons3: "table,tablecontrols",
      theme_advanced_toolbar_location: "top",
      theme_advanced_toolbar_align: "left",
      theme_advanced_statusbar_location: "bottom",
      theme_advanced_resizing: true,
      force_br_newlines: true,
      force_p_newlines: false,
      convert_newlines_to_brs: false,
      remove_linebreaks: true,
      forced_root_block: '',
      content_css: "plugins/tinymce/tinymce.css",
      template_external_list_url: "lists/template_list.js",
      external_link_list_url: "lists/link_list.js",
      external_image_list_url: "lists/image_list.js",
      media_external_list_url: "lists/media_list.js",
      table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
      table_cell_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
      table_row_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
      table_cell_limit: 100,
      table_row_limit: 10,
      table_col_limit: 5,
      setup: function (ed) {
        ed.onKeyDown.add(function (ed, evt) {
          if (evt.keyCode === 9) {
            ed.execCommand('mceInsertRawHTML', false, '\x09');
            evt.preventDefault();
            evt.stopPropagation();
            return false;
          }
        });
      }
    });
  });
</script>
</body>

</html>