<?php
include "vendor/autoload.php";
include "scripts/parserphp7.php";
include "global.tagihan.php";
include "global.administrasi.php";

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
//error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 360);
date_default_timezone_set("Asia/Jakarta");
define('DS', DIRECTORY_SEPARATOR);
define('APP_URL', "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('HOME_DIR', dirname(__FILE__));
define('MODEL_DIR', HOME_DIR . DS . "sources/" . DS . "models" . DS);
define('COMMON_DIR', HOME_DIR . DS . "sources" . DS . "common" . DS);
// require_once MODEL_DIR . "/_init.models.php";


$db['host'] = "localhost";            # server database
$db['name'] = "pertaminapdc_sistem";    # nama database
$db['user'] = "pertaminapdc_digital";    # user database
$db['pass'] = "sA+^255FT?";         # password database

require("plugins/support-lib.php");
require("plugins/ui.helper.php");
require("helper.php");

//$_POST = escapeString();
regAccess();
//logAccess();
//logAccess2();
$sUser = "superadmin";
$sGroup = "Super Admin";
$arrColor = array("#ba8b26", "#0020e0", "#e00020", "#5fe000", "#128a92", "#851712", "#394a67", "#a41899");

/*if (getUser()) {
  setcookie("cSession", date('Y-m-d H:i:s'));
  $nSession = date('Y-m-d H:i:s');  
  if (!empty($cSession) && abs(selisihMenit($cSession, $nSession)) > 10) {
    db("update app_user set logoutUser='".date('Y-m-d H:i:s')."' where username='$cUsername'");
    echo "
    <script>
      alert('sesi berakhir, silahkan login kembali');
      parent.window.location='logout';
    </script>";
  }  
}*/

// decode encode url
$environment = "development"; # production | development
list($_, $addr) = preg_match("?var=", $val) ? explode("?var=",decode(str_replace("/crm/","", $_SERVER['REQUEST_URI']))) : explode("?",$_SERVER['REQUEST_URI']);

$decode = decode($c);
$mod = explode("-", $decode);
if(count($mod) > 3){
	if(isset($mod[0])){ $c = $mod[0]; $_GET[c] =  $mod[0];}
	if(isset($mod[1])){ $p = $mod[1]; $_GET[p] =  $mod[1];}
	if(isset($mod[2])){ $m = $mod[2]; $_GET[m] =  $mod[2];}
	if(isset($mod[3])){ $s = $mod[3]; $_GET[s] =  $mod[3];}
}

$var = "";
$set = explode("&", preg_match("?var=", $val) ? decode($addr) : $addr);
if(is_array($set)){
	while(list($id, $val) = each($set)){
		list($key, $value) = explode("=", $val);
		if(preg_match("/par\[/i", $val)){
			$key = str_replace("par[","",$key);
			$key = str_replace("]","",$key);
			if(!empty($value)) $par[$key] = $value;
		}
		if(!empty($val) && !in_array($key, array("var"))){
			$var.="&".$val;
			if($key == "n") $_GET[n] = $value;
			if($key == "tab") $_GET[tab] = $value;
			if($key == "_submit") $_submit = $value;
		}
	}
}

if(empty($var)){
	$set = explode("&", decode(str_replace("var=","",$addr)));
	if(is_array($set)){
		while(list($id, $val) = each($set)){
			list($key, $value) = explode("=", $val);
			if(preg_match("/par\[/i", $val)){
				$key = str_replace("par[","",$key);
				$key = str_replace("]","",$key);
				if(!empty($value)) $par[$key] = $value;
			}
			if(!empty($val) && !in_array($key, array("var"))){
				$var.="&".$val;
				if($key == "n") $_GET[n] = $value;
				if($key == "tab") $_GET[tab] = $value;
			}
		}
	}
}

$decode = "$c-$p-$m-$s";
$var = str_replace("&c=$c&p=$p&m=$m&s=$s","",$var);
// decode encode end

$inp[page] = 5;
$kodeModul = $c;
$kodeSite = $p;
$menuAccess = arrayQuery("select t1.kodeMenu,t1.statusGroup,t2.username from app_group_menu t1 join app_user t2 on (t1.kodeGroup = t2.kodeGroup) where t2.username='$cUsername'");

if (empty($cPerusahaan)) $cPerusahaan = 1200000001;
if (empty($cArea)) $cArea = 970;


$filter = " and t1.kodeModul='" . $kodeModul . "'";

$filter2 = "";
if ($kodeSite == "99")
  $filter2 = "and t1.parameterMenu='$par[layanan]'";

$arrSite = arrayQuery("select t1.kodeSite, t1.namaSite from app_site t1 join app_menu t2 join app_group_menu t3 join app_user t4 on (t1.kodeSite=t2.kodeSite and t2.kodeMenu=t3.kodeMenu and t3.kodeGroup=t4.kodeGroup) where t1.statusSite='t' and t4.username='$cUsername' " . $filter . " order by t1.urutanSite");
$arrParameter = arrayQuery("select kodeParameter, nilaiParameter from app_parameter order by kodeParameter");

$sql = "select * from app_menu t1 join app_modul t2 on (t1.kodeModul=t2.kodeModul) where t1.statusMenu='t' " . $filter . " order by t1.kodeMenu";
$res = db($sql);
while ($r = mysql_fetch_array($res)) {
  if (empty($r[kodeInduk]))
    $arrParent["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

  if (empty($r[kodeInduk]))
    $arrInduk["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

  $arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu] . "\t" . $r[viewMenu];

  $arrMenu_site["$r[kodeSite]"]["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu] . "\t" . $r[viewMenu];

  $arrTarget = explode("/", $r[targetMenu]);
  $script = $arrTarget[count($arrTarget) - 1];
  unset($arrTarget[count($arrTarget) - 1]);
  $folder = implode("/", $arrTarget);

  $arrSource["$r[kodeMenu]"] = "sources/" . $r[targetMenu] . ".php";
  $arrScript["$r[kodeMenu]"] = "sources/" . $folder . "/js/" . $script . ".js";
  $arrTitle["$r[kodeMenu]"] = $r[namaMenu];
  $arrFlag["$r[kodeMenu]"] = $r[kodeSite];
  $arrParam["$r[kodeMenu]"] = $r[parameterMenu];
  $arrTop["$r[kodeMenu]"] = $r[kodeSite] . "\t" . $r[kodeInduk];
  $arrInfo["$r[kodeMenu]"] = $r[viewMenu];

  /*
  $menuAccess["$r[kodeMenu]"]["add"] = true;
  $menuAccess["$r[kodeMenu]"]["view"] = true;
  $menuAccess["$r[kodeMenu]"]["edit"] = true;
  $menuAccess["$r[kodeMenu]"]["delete"] = true;
  */
}

if (is_array($arrInduk)) {
  asort($arrInduk);
  reset($arrInduk);
  while (list($kodeSite) = each($arrInduk)) {
    if (is_array($arrInduk[$kodeSite])) {
      asort($arrInduk[$kodeSite]);
      reset($arrInduk[$kodeSite]);
      while (list($kodeInduk) = each($arrInduk[$kodeSite])) {
        $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeInduk]["view"]) ? 1 : 0;
        $cntMenu[$kodeInduk] += isset($menuAccess[$kodeInduk]["view"]) ? 1 : 0;
        if (is_array($arrMenu[$kodeInduk])) {
          asort($arrMenu[$kodeInduk]);
          reset($arrMenu[$kodeInduk]);
          while (list($kodeMenu) = each($arrMenu[$kodeInduk])) {
            $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
            $cntMenu[$kodeInduk] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
            $cntMenu[$kodeMenu] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
            if (is_array($arrMenu[$kodeMenu])) {
              asort($arrMenu[$kodeMenu]);
              reset($arrMenu[$kodeMenu]);
              while (list($kodeMenu1) = each($arrMenu[$kodeMenu])) {
                $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                $cntMenu[$kodeInduk] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                $cntMenu[$kodeMenu] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                $cntMenu[$kodeMenu1] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
              }
            }
          }
        }
      }
    }
  }
}


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


if (empty($m))
  list($m, $s) = explode("\t", $arrDef[$c][$p]);
if (empty($m))
  $m = $s = $p;

$arrMaps = array(
  "01" => "1", #Aceh
  "02" => "17", #Bali
  "03" => "3", #Bengkulu
  "04" => "13", #DKI Jakarta
  "05" => "4", #Jambi
  "07" => "14", #Jawa Tengah
  "08" => "15", #Jawa Timur
  "10" => "16", #Daerah Istimewa Yogyakarta
  "11" => "20", #Kalimantan Barat
  "12" => "21", #Kalimantan Selatan
  "13" => "22", #Kalimantan Tengah
  "14" => "23", #Kalimantan Timur
  "15" => "8", #Lampung
  "17" => "18", #Nusa Tenggara Barat
  "18" => "19", #Nusa Tenggara Timur
  "21" => "27", #Sulawesi Tengah
  "22" => "26", #Sulawesi Tenggara
  "24" => "6", #Sumatera Barat
  "26" => "2", #Sumatera Utara
  "28" => "30", #Maluku
  "29" => "31", #Maluku Utara
  "30" => "12", #Jawa Barat
  "31" => "28", #Sulawesi Utara
  "32" => "7", #Sumatera Selatan
  "33" => "11", #Banten
  "34" => "24", #Gorontalo
  "35" => "9", #Kepulauan Bangka Belitung
  "36" => "32", #Papua
  "37" => "5", #Riau
  "38" => "25", #Sulawesi Selatan
  "39" => "33", #Papua Barat
  "40" => "10", #Kepulauan Riau
  "41" => "29", #Sulawesi Barat
);

function setFullscreen()
{
  echo '
  <script type="text/javascript">
    jQuery(document).ready(function () {
      fullPage();
      jQuery(window).bind("resize", function () {
        fullPage();
      });
});

function fullPage(){
  jQuery(".togglemenu").click();
  jQuery(".vernav2").css("display", "none");
  jQuery("body").css("background-image", "none");
  jQuery("div.centercontent.contentpopup").css("margin-left", "0");
}
</script>
';
}

function setFullscreen2()
{
  echo '
  <script type="text/javascript">
    jQuery(document).ready(function () {
      fullPage();
      jQuery(window).bind("resize", function () {
        fullPage();
      });
    });

    function fullPage(){
      jQuery(".togglemenu").click();
      jQuery(".vernav2").css("display", "block");
      jQuery("body").css("background-image", "block");
    }
  </script>
  ';
}
function getSizeFile($file)
{
  $bytes = filesize($file);
  if ($bytes >= 1073741824) {
    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
  } elseif ($bytes >= 1048576) {
    $bytes = number_format($bytes / 1048576, 2) . ' MB';
  } elseif ($bytes >= 1024) {
    $bytes = number_format($bytes / 1024, 2) . ' KB';
  } elseif ($bytes > 1) {
    $bytes = $bytes . ' bytes';
  } elseif ($bytes == 1) {
    $bytes = $bytes . ' byte';
  } else {
    $bytes = '0 bytes';
  }

  return $bytes;
}

function getBread($mode = "")
{
  global $c, $p, $s, $m, $par, $arrTitle;
  if (!empty($par[kodeCategory]))
    $cat = "&par[mode]=det&par[kodeCategory]=$par[kodeCategory]";

  $text .= "<ul class=\"breadcrumbs\">
  <li><a href=\"main\" target=\"_top\">Home</a></li>";
  $text .= getTopBread($s);
  $text .= empty($mode) ? "<li>" . $arrTitle[$s] . "</li>" :
    "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $m . "&s=" . $s . $cat . "\" target=\"_top\">" . $arrTitle[$s] . "</a></li>
  <li>" . $mode . "</li>";
  $text .= "</ul>";
  return $text;
}

function labelTitle($title, $color)
{
  $text .= "<table style=\"width:100%;\">
  <tr>
    <td nowrap=\"nowrap\" style=\"font-weight:bold; text-transform:uppercase; font-size:15px; border-top:solid 1px $color; padding:5px 10px 10px 5px;\">$title</td>
    <td style=\"width:100%; border-top:solid 1px #ececec;\">&nbsp;</td>
  </tr>
</table>";
  return $text;
}

function systemInfo($ipAddress)
{
  $systemInfo = array();
  $ipDetails = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ipAddress), true);
  $systemInfo['city'] = $ipDetails['geoplugin_city'];




  return $systemInfo;
}
function informasiUser()
{
  global $cUsername;

  $sql = "select * from app_user where username='$cUsername'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  list($tanggalLogin, $waktuLogin) = explode(" ", $r[loginUser]);

  list($tanggalPosting, $waktuPosting) = explode(" ", getField("select max(cre_date) from berita_data where status='t' and tipe='forum' and cre_by='$cUsername'"));

  list($tanggalUpload, $waktuUpload) = explode(" ", getField("select max(createDate) from dokumen_data where createBy='$cUsername'"));

  list($tanggalPrivate, $waktuPrivate) = explode(" ", getField("select max(createDate) from dokumen_data where statusDoc='x' and createBy='$cUsername'"));

  list($tanggalSharing, $waktuSharing) = explode(" ", getField("select max(tanggalSharing) from dokumen_sharing where userSharing='$cUsername'"));

  list($tanggalApproval, $waktuApproval) = explode(" ", getField("select max(tanggalApproval) from dokumen_approval where userApproval='$cUsername'"));

  $text = "<table style=\"width:100%;\">
  <tr>
    <td nowrap=\"nowrap\" style=\"font-weight:bold; text-align:left; color:#fff; text-transform:uppercase; font-size:12px; border-bottom:solid 2px #2dd56a; background:#239cc7; padding:10px;\">Informasi</td>
  </tr>
  <tr>
    <td nowrap=\"nowrap\" style=\"background:#f9f9f9; text-align:left; padding-top:10px;\">
      " . labelTitle("<a href=\"" . APP_URL . "/profile\" style=\"font-size:12px;padding:10px;\">Profile</a>", "#ececec") . "
      " . labelTitle("<a href=\"" . APP_URL . "/posting\" style=\"font-size:12px;padding:10px;\">Posting</a>", "#ececec") . "
      " . labelTitle("<a href=\"" . APP_URL . "/upload\" style=\"font-size:12px;padding:10px;\">Upload File</a>", "#ececec") . "
      ";

  if (!empty($tanggalPrivate) && $tanggalPrivate != "0000-00-00")
    $text .= "" . labelTitle("<a href=\"" . APP_URL . "/private\" style=\"font-size:12px;padding:10px;\">Private File</a>", "#ececec") . "
      ";

  if (!empty($tanggalSharing) && $tanggalSharing != "0000-00-00")
    $text .= "" . labelTitle("<a href=\"" . APP_URL . "/sharing\" style=\"font-size:12px;padding:10px;\">File Sharing</a>", "#ececec") . "
      ";

  if (!empty($tanggalApproval) && $tanggalApproval != "0000-00-00")
    $text .= "" . labelTitle("<a href=\"" . APP_URL . "/approve\" style=\"font-size:12px;padding:10px;\">Approval</a>", "#ececec") . "
      ";
  $text .= "</td>
    </tr>
  </table>";

  return $text;
}


function convertMinsToHours($time, $format = '%02d:%02d')
{
  if ($time < 1) {
    return;
  }
  $hours = floor($time / 60);
  $minutes = ($time % 60);
  return sprintf($format, $hours, $minutes);
}

function getTopBread($kodeMenu)
{
  global $c, $p, $m, $par, $arrTitle, $arrSite, $arrTop, $arrKey, $arrDef;
  list($kodeSite, $kodeInduk) = explode("\t", $arrTop[$kodeMenu]);

  if (empty($arrTitle[$kodeInduk]))
    list($kodeMenu, $kodeMenu1) = explode("\t", $arrKey[$kodeSite]);
  else
    list($kodeMenu, $kodeMenu1) = explode("\t", $arrDef[$kodeSite][$kodeInduk]);

  if (!empty($kodeInduk))
    $result = getTopBread($kodeInduk);

  if (in_array($m, array(5, 18, 39, 71, 88)) && !empty($arrTitle[$kodeInduk])) # hack master data
    $kodeMenu = $kodeInduk;

  if (empty($arrSite[$kodeSite]))
    $result .= empty($arrTitle[$kodeInduk]) ? "" : "<li><a href=\"index.php?c=" . $kodeSite . "&p=" . $p . "&m=" . $kodeInduk . "&s=" . $kodeMenu . "\" target=\"_top\">" . $arrTitle[$kodeInduk] . "</a></li>";
  else
    $result .= empty($arrTitle[$kodeInduk]) ? "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu1 . "\" target=\"_top\">" . $arrSite[$kodeSite] . "</a></li>" : "<li><a href=\"index.php?c=" . $kodeSite . "&p=" . $p . "&m=" . $kodeInduk . "&s=" . $kodeMenu . "\" target=\"_top\">" . $arrTitle[$kodeInduk] . "</a></li>";

  return $result;
}

function logAccess2()
{
  global $c, $p, $s, $par, $_submit, $cUsername;
  $aktivitasLog = isset($par[mode]) ? $par[mode] : "open page";
  $aktivitasLog = $par[mode] == "det" ? "view detail" : $aktivitasLog;
  $aktivitasLog = $par[mode] == "del" ? "delete data" : $aktivitasLog;
  $aktivitasLog = ($par[mode] == "add" && !empty($_submit)) ? "input data" : $aktivitasLog;
  $aktivitasLog = ($par[mode] == "edit" && !empty($_submit)) ? "edit data" : $aktivitasLog;
  $aktivitasLog = ($par[mode] == "det" && !empty($_submit)) ? "update data" : $aktivitasLog;

  if (!empty($cUsername) && in_array($aktivitasLog, array("open page", "view detail", "input data", "edit data", "delete data", "update data"))) {
    $kodeLog = getField("select kodeLog from log_access order by kodeLog desc limit 1") + 1;
    $kodeModul = empty($c) ? 0 : $c;
    $kodeSite = empty($p) ? 0 : $p;
    $kodeMenu = empty($s) ? 0 : $s;
    $ip = $_SERVER['REMOTE_ADDR'];
    $getip = 'http://extreme-ip-lookup.com/json/' . $ip;
    $curl  = curl_init();
    curl_setopt($curl, CURLOPT_URL, $getip);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);
    $details       = json_decode($content);
    $country_code  = $details->countryCode;
    $nama_kota     = $details->city;
    $nama_provinsi = $details->region;
    if ($c == '8') {
      $sql = "insert into log_access (kodeLog, kodeModul, kodeSite, kodeMenu, aktivitasLog,lokasi,ip_address, createBy, createTime,kodeTipe) values ('$kodeLog', '$kodeModul', '$kodeSite', '$kodeMenu', '$aktivitasLog','" . $nama_kota . "','" . $ip . "', '$cUsername', '" . date('Y-m-d H:i:s') . "','1')";
      db($sql);
    } else {
      $sql = "insert into log_access (kodeLog, kodeModul, kodeSite, kodeMenu, aktivitasLog,lokasi,ip_address, createBy, createTime,kodeTipe) values ('$kodeLog', '$kodeModul', '$kodeSite', '$kodeMenu', '$aktivitasLog','" . $nama_kota . "','" . $ip . "', '$cUsername', '" . date('Y-m-d H:i:s') . "','0')";
      db($sql, "false");
    }
  }

  $createDate = date("Y-m-d", dateMin("d", 10, mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"))));
  if (getField("select count(*) from log_access where date(createTime) < '" . $createDate . "'")) {
    $sql = "delete from log_access where date(createTime) < '" . $createDate . "'";
    db($sql);
  }
}


// }
// function logAccess() {
//   global $c, $p, $s, $par, $_submit, $cUsername;
//   $aktivitasLog = isset($par[mode]) ? $par[mode] : "open page";
//   $aktivitasLog = $par[mode] == "det" ? "view detail" : $aktivitasLog;
//   $aktivitasLog = $par[mode] == "del" ? "delete data" : $aktivitasLog;
//   $aktivitasLog = ($par[mode] == "add" && !empty($_submit)) ? "input data" : $aktivitasLog;
//   $aktivitasLog = ($par[mode] == "edit" && !empty($_submit)) ? "edit data" : $aktivitasLog;
//   $aktivitasLog = ($par[mode] == "det" && !empty($_submit)) ? "update data" : $aktivitasLog;

//   if (!empty($cUsername) && in_array($aktivitasLog, array("open page", "view detail", "input data", "edit data", "delete data", "update data"))) {
//     $kodeLog = getField("select kodeLog from log_access order by kodeLog desc limit 1") + 1;
//     $kodeModul = empty($c) ? 0 : $c;
//     $kodeSite = empty($p) ? 0 : $p;
//     $kodeMenu = empty($s) ? 0 : $s;
//     $ip = $_SERVER['REMOTE_ADDR'];
// $systemInfo = systemInfo($_SERVER['REMOTE_ADDR']);
//     $sql = "insert into log_access (kodeLog, kodeModul, kodeSite, kodeMenu, aktivitasLog,lokasi,ip_address, createBy, createTime) values ('$kodeLog', '$kodeModul', '$kodeSite', '$kodeMenu', '$aktivitasLog','".$systemInfo['city']."','".$ip."', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
//     db($sql);
//   }

//   $createDate = date("Y-m-d", dateMin("d", 10, mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"))));
//   if(getField("select count(*) from log_access where date(createTime) < '".$createDate."'")){
//    $sql="delete from log_access where date(createTime) < '".$createDate."'";
//    db($sql);
//  }
// }

function regAccess($order = 'egpcs')
{
  if (!function_exists('register_global_array')) {

    function register_global_array(array $superglobal)
    {
      foreach ($superglobal as $varname => $value) {
        global $$varname;
        $$varname = $value;
      }
    }
  }

  $order = explode("\r\n", trim(chunk_split($order, 1)));
  foreach ($order as $k) {
    switch (strtolower($k)) {
      case 'e':
        register_global_array($_ENV);
        break;
      case 'g':
        register_global_array($_GET);
        break;
      case 'p':
        register_global_array($_POST);
        break;
      case 'c':
        register_global_array($_COOKIE);
        break;
      case 's':
        register_global_array($_SERVER);
        break;
    }
  }
}
function db($sql, $flag = 'true')
{
  global $conn, $db;
  if (!isset($conn)) {
    $conn = mysql_connect("$db[host]", "$db[user]", "$db[pass]") or die("server is currently offline");
    mysql_select_db("$db[name]");
  }
  if (!$result = mysql_query($sql, $conn)) {
    //echo "$sql<br>Proses ke database gagal<br>";
    //mysql_error($conn);
  } else {
    if ($flag == 'true' && substr(strtolower($sql), 0, 6) != "select") {
      $kodeLog = getField("select kodeLog from log_access order by kodeLog desc limit 1");
      $sql =  "INSERT into log_access_detail (kodeLog,sqlstat,cre_time) values ('$kodeLog','" . mysql_real_escape_string($sql) . "',now())";
      db($sql, 'false');
    }
  }
  return $result;
}



function encode($value) {
	global $environment;
	
	if($environment != "production")
		return $value;
		
	if (!$value)
		return false;

	$key = sha1('EnCRypT10nK#Y!RiSRNn');
	$strLen = strlen($value);
	$keyLen = strlen($key);
	$j = 0;
	$crypttext = '';

	for ($i = 0; $i < $strLen; $i++) {
		$ordStr = ord(substr($value, $i, 1));
		if ($j == $keyLen) {
			$j = 0;
		}
		$ordKey = ord(substr($key, $j, 1));
		$j++;
		$crypttext .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
	}

	return $crypttext;
}


function decode($value) {
	global $environment;
	if($environment != "production")
		return $value;
		
	if (!$value)
		return false;

	$key = sha1('EnCRypT10nK#Y!RiSRNn');
	$strLen = strlen($value);
	$keyLen = strlen($key);
	$j = 0;
	$decrypttext = '';

	for ($i = 0; $i < $strLen; $i += 2) {
		$ordStr = hexdec(base_convert(strrev(substr($value, $i, 2)), 36, 16));
		if ($j == $keyLen) {
			$j = 0;
		}
		$ordKey = ord(substr($key, $j, 1));
		$j++;
		$decrypttext .= chr($ordStr - $ordKey);
	}

	return $decrypttext;
}


function sendSMS($to, $message)
{
  if (!empty($to) && !empty($message)) {
    $userkey = "ymkg64";
    $passkey = "sinergics";
    $xmlResponse = simplexml_load_file("https://reguler.zenziva.net/apps/smsapi.php?userkey=" . $userkey . "&passkey=" . $passkey . "&nohp=" . $to . "&pesan=" . urlencode($message));
    $jsonResponse = json_encode($xmlResponse);
    $response = json_decode($jsonResponse);

    return $response->message;
  }
}

function sendMail($address, $subject, $message)
{
  include "plugins/PHPMailer/PHPMailerAutoload.php";

  $mail = new PHPMailer;

  $mail->SMTPDebug = 0;

  $mail->Debugoutput = 'html';
  $mail->Host = 'cloud1.hrcules.co.id';

  $mail->Port = 465;
  $mail->SMTPSecure = 'ssl';

  $mail->SMTPAuth = true;
  $mail->Username = "reset-password@pegadaian-loyalti.com";
  $mail->Password = "R3s3tP4ssword";

  $mail->setFrom('reset-password@pegadaian-loyalti.com', 'noreply');
  //$mail->addReplyTo('noreply@mdspustaka.com', 'noreply');

  $mail->addAddress($address);
  $mail->Subject = $subject;
  $mail->msgHTML($message);

  if (!$mail->send()) {
    return "Mailer Error: " . $mail->ErrorInfo;
  } else {
    return "DONE";
  }
}
function repField($ex = array())
{
  global $inp;
  if (is_array($inp)) {
    while (list($id, $nilai) = each($inp)) {
      $inp[$id] = in_array($id, $ex) ? $nilai : mysql_real_escape_string($nilai);
      // $inp[$id] = in_array($id, $ex) ? $nilai : $nilai;
    }
  }
  return $inp;
}

function getUser()
{
  global $cUsername, $cPassword;
  if ($cUsername == "" or $cPassword == "") {
    return false;
  } else {
    if (!getField("select username from app_user where username='$cUsername' and password='$cPassword' and statusUser='t'")) {
      return false;
    }
  }
  return true;
}

function getField($sql)
{
  $res = db($sql);
  $r = mysql_fetch_row($res);
  return $r[0];
}

function dbpage($sql)
{
  global $inp, $par;
  if ($par[hal] == "") {
    $par[hal] = 0;
    $next = $par[hal] + 1;
  } else {
    $prev = $par[hal] - 1;
    $next = $par[hal] + 1;
  }
  $hal_sql = $par[hal] * $inp[page];
  $res = db($sql);
  $jumlah = mysql_num_rows($res);
  $jml = ceil($jumlah / $inp[page]);
  $jmlakhir = $jml - 1;
  $str_list .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr>";
  if ($jumlah > $inp[page]) {
    if ($par[hal] == 0) {
      $str_list .= "<td style=\"padding:3px;\" align=center>" . comboPage($jml, getPar($par, "hal")) . "</td>";
      $str_list .= "<td style=\"padding:3px;\" width=20 align=right nowrap><a href=\"?par[hal]=$next" . getPar($par, "hal") . "\" title=\"Next Page\"  class=\"tmb\"><span>&rsaquo;</span></a></td><td width=20 align=right nowrap><a href=\"?par[hal]=$jmlakhir" . getPar($par, "hal") . "\" title=\"Last Page\"  class=\"tmb\"><span>&raquo;</span></a></td>";
    } else {
      $str_list .= "<td style=\"padding:3px;\" width=20 align=left nowrap><a href=\"?par[hal]=0" . getPar($par, "hal") . "\" title=\"First Page\"  class=\"tmb\"><span>&laquo;</span></a></td>
        <td style=\"padding:3px;\" width=20 align=left nowrap><a href=\"?par[hal]=$prev" . getPar($par, "hal") . "\" title=\"Previous Page\"  class=\"tmb\"><span>&lsaquo;</span></a></td>";
      $str_list .= "<td style=\"padding:3px;\" align=center>" . comboPage($jml, getPar($par, "hal")) . "</td>";
      if ($par[hal] < ($jml - 1)) {
        $str_list .= "<td style=\"padding:3px;\" width=20 align=right nowrap><a href=\"?par[hal]=$next" . getPar($par, "hal") . "\" title=\"Next Page\"  class=\"tmb\"><span>&rsaquo;</span></a></td>
          <td style=\"padding:3px;\" width=20 align=right nowra><a href=\"?par[hal]=$jmlakhir" . getPar($par, "hal") . "\" title=\"Last Page\"  class=\"tmb\"><span>&raquo;</span></td>";
      }
    }
  }
  $str_list .= "</tr></table>";
  $res = db($sql . " limit " . $inp[page] . " offset " . $hal_sql);
  if (mysql_num_rows($res) == 0 && $par[hal] > 0)
    echo "<script>window.location='?par[hal]=" . ($par[hal] - 1) . "" . getPar($par, "hal") . "';</script>";

  return array("view" => "<form>
     <table align=\"center\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">           
      <tr>    
       <td>$str_list</td>
     </tr>
   </table>
 </form>", "result" => $res);
}

function comboPage($jml, $filter)
{
  global $inp, $par;
  $txt .= "<select name=par[hal] onChange=\"javascript:page('mainFrame',this,0)\">";
  for ($i = 1; $i <= $jml; $i++) {
    if ($par[hal] == ($i - 1)) {
      $txt .= "<option value=\"?par[hal]=" . ($i - 1) . "" . getPar($par, "hal") . "\" selected>Page $i</option>";
    } else {
      $txt .= "<option value=\"?par[hal]=" . ($i - 1) . "" . getPar($par, "hal") . "\">Page $i</option>";
    }
  }
  $txt .= "</select>";
  return $txt;
}

function setValidation($param, $nm_obj, $message = "", $rg_awal = "0", $rg_akhir = "0")
{
  global $rule;
  switch ($param) {
    case "is_null":
      $rule .= "t10_checkisi(document.getElementById('$nm_obj'),\"$message\");\n";
      break;
    case "is_mail":
      $rule .= "t10_checkmail(document.getElementById('$nm_obj'),\"$message\");\n";
      break;
    case "is_date":
      $rule .= "t10_checkvaliddate(document.getElementById('$nm_obj[0]'),document.getElementById('$nm_obj[1]'),document.getElementById('$nm_obj[2]'));\n";
      break;
    case "is_num":
      $rule .= "t10_checknum(document.getElementById('$nm_obj'),\"$message\");\n";
      break;
    case "is_range":
      $rule .= "t10_checkrange(document.getElementById('$nm_obj'),\"$message\",$rg_awal,$rg_akhir);\n";
      break;
  }
}

function getValidation()
{
  global $rule;
  $text = "<script language=javascript src=\"" . https_converter(APP_URL . "/scripts/validation.js") . "\"></script>
    <script language=\"javascript\">
      var valid;
      function validation(var_nama_form) {
       var nm_form=var_nama_form;
       valid=true;
       " . $rule . "
       if (valid){
         return true;
       }else{
         return false;
       }
     }
   </script>";
  return $text;
}

// by tenno
function https_converter($url)
{
  return str_replace('http', 'https', $url);
}

function getTable($table)
{
  $sql = "show columns from $table";
  $res = db($sql);
  $field = array();
  while ($r = mysql_fetch_array($res)) {
    $field["$r[0]"] = "";
  }
  return $field;
}

function table2($cols = 0, $nosort = array(), $mode = "lst", $paging = "false", $scroll = "", $tabel = "dataList", $param = "")
{
  global $par;
  $result = "<script type=\"text/javascript\">   
 jQuery(document).ready(function() {                        
   var oTable = jQuery('#" . $tabel . "').dataTable( {
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': 'ajax.php?par[mode]=" . $mode . "" . $param . "" . getPar($par, "mode") . "',
    'sPaginationType': 'full_numbers',
    'bFilter': false,";

  if ($scroll == "hv") {
    $result .= "'sScrollY': '225px',   
     'sScrollX': '100%',";
  }

  if ($scroll == "h") {
    $result .= "'sScrollX': '100%',";
  }

  if ($scroll == "v") {
    $result .= "'sScrollY': '300px',";
  }

  if ($paging == "false") {
    $result .= "'bPaginate': false,
     'bInfo': false,";
  }

  if (!empty($cols)) {
    $result .= "'aoColumns': [";
    for ($i = 1; $i <= $cols; $i++)
      $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
    $result .= "],";
  } else {
    $result .= "'bSort': false,";
  }

  $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
   'iDisplayLength':parseInt(jQuery('#_len').val()),
   'sDom': 'frtlip',
   'oLanguage':{            
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {        
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));
   },

   'fnServerParams': function (aoData) {                      
     aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
     aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
     aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
     aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
     aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
     aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
     aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
   },     
 });

jQuery('#fSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#sSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#bSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#tSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#pSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#mSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#aSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#fSearch').attr('placeholder', 'search ...');
jQuery('#fSearch').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-left:30px;');
";

  $result .= "});           
</script>";
  return $result;
}

function data2($cols = 0, $nosort = array(), $mode = "lst", $paging = "false", $scroll = "", $tabel = "dataList", $param = "", $length = 5)
{
  global $par;
  $result = valPar($par) . "<script type=\"text/javascript\">    
 jQuery(document).ready(function() {                        
   var oTable = jQuery('#" . $tabel . "').dataTable( {
    'bProcessing': true,
    'bLengthChange':false,
    'bServerSide': true,
    'sAjaxSource': '" . APP_URL . "/get.php?p=" . $_GET[p] . "&m=" . $mode . "" . $param . "',
    'sPaginationType': 'full_numbers',
    'bFilter': false,
    'bInfo': false,";

  if ($scroll == "hv") {
    $result .= "'sScrollY': '225px',   
     'sScrollX': '100%',";
  }

  if ($scroll == "h") {
    $result .= "'sScrollX': '100%',";
  }

  if ($scroll == "v") {
    $result .= "'sScrollY': '300px',";
  }

  if ($paging == "false") {
    $result .= "'bPaginate': false,";
  }

  if (!empty($cols)) {
    $result .= "'aoColumns': [";
    for ($i = 1; $i <= $cols; $i++)
      $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
    $result .= "],";
  } else {
    $result .= "'bSort': false,";
  }

  $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
   'iDisplayLength':parseInt(" . $length . "),
   'sDom': 'frtlip',
   'oLanguage':{            
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"" . APP_URL . "/cms/styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {        
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));
   },

   'fnServerParams': function (aoData) {                      
     aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
     aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
     aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
     aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
     aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
     aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
     aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
   },     
 });

jQuery('#fSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#sSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#bSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#tSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#pSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#mSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#aSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#fSearch').attr('placeholder', 'search ...');
jQuery('#fSearch').attr('style', 'background: #fff url(\"" . APP_URL . "/cms/styles/images/filter.png\") no-repeat; width:250px; padding-left:30px;');
";

  $result .= "});           
</script>";
  return $result;
}

/*function table($cols=0, $nosort=array(), $mode="lst", $paging="true", $scroll="", $tabel="dataList", $param=""){
 global $par;
 $result="<script type=\"text/javascript\">   
 jQuery(document).ready(function() {                        
   var oTable = jQuery('#".$tabel."').dataTable( {
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': 'ajax.php?par[mode]=".$mode."".$param."".getPar($par,"mode")."',
    'sPaginationType': 'full_numbers',
    'bFilter': false,";

    if($scroll == "hv"){
     $result.="'sScrollY': '225px',   
     'sScrollX': '100%',";
   }

   if($scroll == "h"){
     $result.="'sScrollX': '100%',";
   }

   if($scroll == "v"){
     $result.="'sScrollY': '300px',";
   }

   if($paging == "false"){
     $result.="'bPaginate': false,
     'bInfo': false,";
   }

   if(!empty($cols)){
     $result.="'aoColumns': [";
     for($i=1; $i<=$cols; $i++)
       $result.= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
     $result.="],";
   }else{
     $result.="'bSort': false,";
   }

   $result.="'iDisplayStart':parseInt(jQuery('#_page').val()),
   'iDisplayLength':parseInt(jQuery('#_len').val()),
   'sDom': 'frtlip',
   'oLanguage':{            
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {        
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));
   },

   'fnServerParams': function (aoData) {                      
     aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
     aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
     aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
     aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
     aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
     aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
     aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
   },     
 });

jQuery('#fSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#sSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#bSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#tSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#pSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#mSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#aSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#fSearch').attr('placeholder', 'search ...');
jQuery('#fSearch').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-left:30px;');
";    

$result.="});           
</script>";
return $result;
}*/

function table($cols = 0, $nosort = array(), $mode = "lst", $paging = "true", $scroll = "", $tabel = "dataList", $param = "", $get = "", $height = "")
{
  global $par;
  if (empty($height)) $height = 500;
  $result = "<script type=\"text/javascript\">    
    jQuery(document).ready(function() {                       
        var oTable = jQuery('#" . $tabel . "').dataTable( {
          'bProcessing': true,
          'bServerSide': true,
          'sAjaxSource': 'ajax.php?par[mode]=" . $mode . "" . $param . "" . getPar($par, "mode") . "',
          'sPaginationType': 'full_numbers',
          'bFilter': false,
          'aLengthMenu': [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, \"All\"]],";

  if ($scroll == "hv") {
    $result .= "'sScrollY': '500px',   
          'sScrollX': '100%',";
  }

  if ($scroll == "h") {
    $result .= "'sScrollX': '100%',";
  }

  if ($scroll == "v") {
    $result .= "'sScrollY': '" . $height . "px',";
  }

  if ($paging == "false") {
    $result .= "'bPaginate': false,
              'bInfo': false,";
  }

  if (!empty($cols)) {
    $result .= "'aoColumns': [";
    for ($i = 1; $i <= $cols; $i++)
      $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
    $result .= "],";
  } else {
    $result .= "'bSort': false,";
  }

  $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
            'iDisplayLength':parseInt(jQuery('#_len').val()),
            'sDom': 'frtlip',
            'oLanguage':{           
              'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
              'sInfoFiltered': '<span></span>',
              'sProcessing': '<img src=\"styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
            },

            'fnDrawCallback': function () {       
              jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
              jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));";

  if (!empty($get))
    $result .= "arr = jQuery('#chk').val().split('\t');         
                  for(i=0; i< arr.length; i++){           
                    jQuery('#id_' + arr[i] + '').prop('checked', true );
                    }
              ";

  $result .= "},
                'fnServerData': function (sSource, aoData, fnCallback ) {
                  aoData.push({'name': 'more_data', 'value': 'my_value' } );
                  jQuery.getJSON(sSource, aoData, function (json) {
                      jQuery('#tRecord').html(json.iTotalDisplayRecords + ' Record');
                      fnCallback(json);
                      });
                },

                'fnServerParams': function (aoData) {                     
                  aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
                  aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
                  aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
                  aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
                  aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
                  aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
                  aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
                  aoData.push({ 'name': 'cSearch', 'value': jQuery('#cSearch').val() });
                  aoData.push({ 'name': 'dSearch', 'value': jQuery('#dSearch').val() });
                  aoData.push({ 'name': 'eSearch', 'value': jQuery('#eSearch').val() });
                  aoData.push({ 'name': 'gSearch', 'value': jQuery('#gSearch').val() });
                  aoData.push({ 'name': 'nSearch', 'value': jQuery('#nSearch').val() });
                  aoData.push({ 'name': 'oSearch', 'value': jQuery('#oSearch').val() });
                  aoData.push({ 'name': 'xSearch', 'value': jQuery('#xSearch').val() });
                  aoData.push({ 'name': 'ySearch', 'value': jQuery('#ySearch').val() });
                  
                  aoData.push({ 'name': 'combo1', 'value': jQuery('#combo1').val() });
                  aoData.push({ 'name': 'combo2', 'value': jQuery('#combo2').val() });
                  aoData.push({ 'name': 'combo3', 'value': jQuery('#combo3').val() });
                  aoData.push({ 'name': 'combo4', 'value': jQuery('#combo4').val() });
                  aoData.push({ 'name': 'combo5', 'value': jQuery('#combo5').val() });
                  aoData.push({ 'name': 'combo6', 'value': jQuery('#combo6').val() });
                  aoData.push({ 'name': 'combo7', 'value': jQuery('#combo7').val() });
                  aoData.push({ 'name': 'combo8', 'value': jQuery('#combo8').val() });
                  aoData.push({ 'name': 'combo9', 'value': jQuery('#combo9').val() });
                  aoData.push({ 'name': 'combo10', 'value': jQuery('#combo10').val() });
                },      
        });

        jQuery('#fSearch').keyup(function(){
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#sSearch').keyup(function(){
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#bSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#cSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#dSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#eSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#gSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });


        jQuery('#tSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });           

        jQuery('#pSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            }); 

        jQuery('#mSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });           

        jQuery('#aSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            }); 

        jQuery('#nSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#oSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#xSearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });

        jQuery('#ySearch').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
            });
            
        jQuery('#combo1').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo2').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo3').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo4').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo5').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo6').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo7').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo8').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo9').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
        
        jQuery('#combo10').change(function(){ 
            oTable.fnPageChange(0);
            oTable.fnReloadAjax();
        });
            
            


        jQuery('#fSearch').attr('placeholder', 'Cari..');
        jQuery('#fSearch').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-top:-100px;padding-left:30px;');
        ";

  $result .= "});           
          </script>";
  return $result;
}

function data($cols = 0, $nosort = array(), $mode = "lst", $paging = "true", $scroll = "", $tabel = "dataList", $param = "", $length = 5)
{
  global $par;
  $result = valPar($par) . "<script type=\"text/javascript\">    
 jQuery(document).ready(function() {                        
   var oTable = jQuery('#" . $tabel . "').dataTable( {
    'bProcessing': true,
    'bLengthChange':false,
    'bServerSide': true,
    'sAjaxSource': '" . APP_URL . "/get.php?p=" . $_GET[p] . "&m=" . $mode . "" . $param . "',
    'sPaginationType': 'full_numbers',
    'bFilter': false,
    'bInfo': false,";

  if ($scroll == "hv") {
    $result .= "'sScrollY': '225px',   
     'sScrollX': '100%',";
  }

  if ($scroll == "h") {
    $result .= "'sScrollX': '100%',";
  }

  if ($scroll == "v") {
    $result .= "'sScrollY': '300px',";
  }

  if ($paging == "false") {
    $result .= "'bPaginate': false,";
  }

  if (!empty($cols)) {
    $result .= "'aoColumns': [";
    for ($i = 1; $i <= $cols; $i++)
      $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
    $result .= "],";
  } else {
    $result .= "'bSort': false,";
  }

  $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
   'iDisplayLength':parseInt(" . $length . "),
   'sDom': 'frtlip',
   'oLanguage':{            
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"" . APP_URL . "/cms/styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {        
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));
   },

   'fnServerParams': function (aoData) {                      
     aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
     aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
     aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
     aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
     aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
     aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
     aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
   },     
 });

jQuery('#fSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#sSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#bSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#tSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#pSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#mSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});           

jQuery('#aSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#fSearch').attr('placeholder', 'search ...');
jQuery('#fSearch').attr('style', 'background: #fff url(\"" . APP_URL . "/cms/styles/images/filter.png\") no-repeat; width:250px; padding-left:30px;');
";

  $result .= "});           
</script>";
  return $result;
}

function getPar($par, $ha_nama = "")
{
  global $c, $p, $m, $s, $_p, $_l;
  if (strlen($c))
    $var .= "&c=$c";
  if (strlen($p))
    $var .= "&p=$p";
  if (strlen($m))
    $var .= "&m=$m";
  if (strlen($s))
    $var .= "&s=$s";
  if (strlen($_p))
    $var .= "&_p=$_p";
  if (strlen($_l))
    $var .= "&_l=$_l";
  if (is_array($par)) {
    while (list($nama, $nilai) = each($par)) {
      if (strpos(",$ha_nama,", "$nama,") == 0) {
        if (!empty($nilai))
          $var .= "&par[$nama]=$nilai";
      }
    }
  }
  return $var;
}
function getLink($par, $fil = 0, $ha_nama = "")
{
  global $c, $p, $m, $s, $_p, $_l;
  $var .= "&c=$c";
  $var .= "&p=$p";
  $var .= "&m=$m";
  $var .= "&s=$s";
  $var .= "&_p=' + jQuery('#_page').val() + '";
  $var .= "&_l=' + jQuery('#_len').val() + '";

  if ($fil > 0) $var .= "&fs=' + (jQuery('#fSearch').val() == undefined ? '' : jQuery('#fSearch').val())+ '";
  if ($fil > 1) $var .= "&ps=' + (jQuery('#pSearch').val() == undefined ? '' : jQuery('#pSearch').val()) + '";
  if ($fil > 2) $var .= "&bs=' + (jQuery('#bSearch').val() == undefined ? '' : jQuery('#bSearch').val()) + '";
  if ($fil > 3) $var .= "&ts=' + (jQuery('#tSearch').val() == undefined ? '' : jQuery('#tSearch').val()) + '";
  if ($fil > 4) $var .= "&ss=' + (jQuery('#sSearch').val() == undefined ? '' : jQuery('#sSearch').val()) + '";
  if ($fil > 5) $var .= "&os=' + (jQuery('#oSearch').val() == undefined ? '' : jQuery('#oSearch').val()) + '";
  if ($fil > 6) $var .= "&ns=' + (jQuery('#nSearch').val() == undefined ? '' : jQuery('#nSearch').val()) + '";
  if ($fil > 7) $var .= "&xs=' + (jQuery('#xSearch').val() == undefined ? '' : jQuery('#xSearch').val()) + '";
  if ($fil > 8) $var .= "&ys=' + (jQuery('#ySearch').val() == undefined ? '' : jQuery('#ySearch').val()) + '";
  if ($fil > 9) $var .= "&yc=' + (jQuery('#cSearch').val() == undefined ? '' : jQuery('#cSearch').val()) + '";
  if ($fil > 10) $var .= "&yd=' + (jQuery('#dSearch').val() == undefined ? '' : jQuery('#dSearch').val()) + '";
  if ($fil > 11) $var .= "&ye=' + (jQuery('#eSearch').val() == undefined ? '' : jQuery('#eSearch').val()) + '";
  if ($fil > 12) $var .= "&ye=' + (jQuery('#gSearch').val() == undefined ? '' : jQuery('#gSearch').val()) + '";
  if ($fil > 13) $var .= "&ms=' + (jQuery('#mSearch').val() == undefined ? '' : jQuery('#mSearch').val()) + '";

  if (is_array($par)) {
    while (list($nama, $nilai) = each($par)) {
      if (strpos(",$ha_nama,", "$nama,") == 0) {
        if (!empty($nilai))
          $var .= "&par[$nama]=$nilai";
      }
    }
  }
  return $var;
}
function notifikasi($status, $message = "")
{
  if (!empty($status)) {
    if ($status == "a") $message = "Data berhasil <strong>ditambahkan</strong>.";
    if ($status == "e") $message = "Data berhasil <strong>diupdate</strong>.";
    if ($status == "d") $message = "Data berhasil <strong>dihapus</strong>.";
    if ($status == "p") $message = "Data berhasil <strong>diproses</strong>.";

    return "<div class=\"notibar msginfo\">       
      <a class=\"close\"></a>
      <p>" . $message . "</p>
      </div>
      <script type=\"text/javascript\">
      jQuery(document).ready(function() {
          function hidePanel() {     
          jQuery(\"a.close\").click();
          }           
          setTimeout(hidePanel, 3000);
          });
    </script>";
  }
}
function radioData($sql, $key, $val, $nama, $nilai = "", $java = "", $width = "", $class = "", $disabled = "")
{
  $width = $width == "" ? "" : "width:$width;";
  $disabled = $disabled == "" ? "" : "disabled";
  $result = db("$sql");
  $jml = mysql_num_rows($result);

  for ($i = 0; $i < $jml; $i++) {
    $r = mysql_fetch_array($result);
    if (empty($nilai))
      $checked = $i == 0 ? "checked=\"checked\"" : "";
    else
      $checked = trim($r[$key]) == trim($nilai) ? "checked=\"checked\"" : "";
    $txt .= "<input type=\"radio\" id=\"$key_" . $i . "\" name=\"" . $nama . "\" value=\"" . $r[$key] . "\" " . $checked . " /> <span class=\"sradio\">" . $r[$val] . "</span>";
  }

  return $txt;
}
function setPar($par, $ha_nama = "")
{
  if (is_array($par)) {
    while (list($nama, $nilai) = each($par)) {
      if (strpos(",$ha_nama,", "$nama,") == 0) {
        if (!empty($nilai))
          $var .= "<input type=\"hidden\" id=\"par[$nama]\" name=\"par[$nama]\" value=\"$nilai\"/>";
      }
    }
  }
  return $var;
}

function valPar($par, $ha_nama = "", $length = "")
{
  global $c, $p, $m, $s, $_p, $_l, $arrParameter;
  if (strlen($c))
    $var .= "&c=$c";
  if (strlen($p))
    $var .= "&p=$p";
  if (strlen($m))
    $var .= "&m=$m";
  if (strlen($s))
    $var .= "&s=$s";
  if (is_array($par)) {
    while (list($nama, $nilai) = each($par)) {
      if (strpos(",$ha_nama,", "$nama,") == 0) {
        if (!empty($nilai))
          $var .= "&par[$nama]=$nilai";
      }
    }
  }

  if (empty($_p))
    $_p = 0;
  if (empty($_l))
    $_l = $arrParameter[1];
  if (!empty($length))
    $_l = $length;
  echo "<input type=\"hidden\" id=\"_par\" name=\"_par\" value=\"$var\"/>
  <input type=\"hidden\" id=\"_page\" name=\"_page\" value=\"$_p\" />
  <input type=\"hidden\" id=\"_len\" name=\"_len\" value=\"$_l\" />";
}
function queryAssoc($sql)
{
  $getResult = db($sql);

  if (!$getResult) {
    echo $sql;
    echo "<br />";
    echo "This query is fail to connect database!";
    echo "<br />";
    die;
  } else {
    if (!empty($getResult)) {
      while ($result = mysql_fetch_assoc($getResult)) {
        $arr[] = $result;
      }
      return $arr;
    }
  }
}

function debugVar($arr)
{
    if (is_array($arr)) {
        echo '<pre>';
        print_r($arr);
        echo '<pre>';
    } else {

        if (!empty($arr)) {
            echo $arr;
        } else {
            echo "<strong>-- Empty Variable --</strong>";
        }
    }
}

//$sql = "select * from acc_tutup_bulan where kodePerusahaan='$kodePerusahaan' order by periodeTutup";
//$res = db($sql);
//while ($r = mysql_fetch_array($res)) {
//  $tutupBuku["$r[periodeTutup]"] = $r[statusTutup];
//  if ($r[statusTutup] == "t") {
//    $tutupTahun = substr($r[periodeTutup], 0, 4);
//    $tutupBulan = substr($r[periodeTutup], 4, 2);
//  }
//}
//$periodeAktif = $tutupBulan == 12 ? getBulan(1) . " " . ($tutupTahun + 1) : getBulan($tutupBulan + 1) . " " . $tutupTahun;
//
function tutupBuku($tanggalJurnal)
{
  global $tutupBuku;
  list($tahun, $bulan) = explode("-", $tanggalJurnal);
  return $tutupBuku[$tahun . $bulan] == "t" ? "periode " . getBulan($bulan) . " " . $tahun . " sudah tutup buku" : false;
}

function getEmail($arrKode)
{

  $emailUser = array();
  /*
	$sql="select t2.*, t3.email, t3.name from app_group_menu t1 join app_user t2 join emp t3 on (t1.kodeGroup=t2.kodeGroup and t2.idPegawai=t3.id) where t1.kodeMenu='".implode("','", $arrKode)."' and t1.statusGroup in ('apprlv1', 'apprlv2', 'apprlv3') and t3.email!='' and t3.email is not null";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$emailUser[]=$r[email]."\t".$r[name]."\t".$r[username]."\t".$r[password];
	}
	*/
  return $emailUser;
}


function arrayQuery($sql)
{
  $arr_y = array();
  $res_item = db($sql);
  while ($r = mysql_fetch_row($res_item)) {
    $jumlah = count($r);
    if ($jumlah == 1) {
      $arr_y[] = $r[0];
    } elseif ($jumlah == 2) {
      $arr_y["$r[0]"] = $r[1];
    } elseif ($jumlah == 3) {
      $arr_y["$r[0]"]["$r[1]"] = $r[2];
    } elseif ($jumlah == 4) {
      $arr_y["$r[0]"]["$r[1]"]["$r[2]"] = $r[3];
    } elseif ($jumlah == 5) {
      $arr_y["$r[0]"]["$r[1]"]["$r[2]"]["$r[3]"] = $r[4];
    } elseif ($jumlah == 6) {
      $arr_y["$r[0]"]["$r[1]"]["$r[2]"]["$r[3]"]["$r[4]"] = $r[5];
    }
  }
  return $arr_y;
}

function comboLantai($nama, $sel, $java = "", $width = "", $all = "", $totlantai)
{
  $style = $width == "" ? "" : "style=\"width:$width\"";
  $text = "<select id=\"$nama\" name=\"$nama\" $java $style class=\"chosen-select\">";
  if (!empty($all))
    $text .= empty($sel) ? "<option value=\"\" selected>All Lantai</option>" : "<option value=\"\">All Lantai</option>";
  for ($nilai = 1; $nilai <= $totlantai; $nilai++) {
    $lantai = $nilai;
    if ($nilai == $sel) {
      $text .= "<option value=\"$lantai\" selected>" . $lantai . "</option>";
    } else {
      $text .= "<option value=\"$lantai\">" . $lantai . "</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

function comboArray($nama, $arr_nilai, $sel, $java = "", $width = "", $class)
{
  $style = $width == "" ? "" : "style=\"width:$width\"";
  $text = "<select id=\"$nama\" name=\"$nama\" $java $style class=\"$class\">";
  ksort($arr_nilai);
  reset($arr_nilai);
  while (list($key, $nilai) = each($arr_nilai)) {
    if ($nilai == $sel) {
      $text .= "<option value=\"$nilai\" selected>$nilai</option>";
    } else {
      $text .= "<option value=\"$nilai\">$nilai</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

function comboKey($nama, $arr_nilai, $sel, $java = "", $width = "", $all = "")
{
  $style = $width == "" ? "" : "style=\"width:$width\"";
  $text = "<select id=\"$nama\" name=\"$nama\" $java $style class=\"\">";
  #ksort($arr_nilai);
  #reset($arr_nilai); 
  if (!empty($all))
    $text .= empty($sel) ? "<option value=\"\" selected>All</option>" : "<option value=\"\">All</option>";
  while (list($key, $nilai) = each($arr_nilai)) {
    if ($key == $sel) {
      $text .= "<option value=\"$key\" selected>$nilai</option>";
    } else {
      $text .= "<option value=\"$key\">$nilai</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

function comboYear($nama, $sel, $range = "", $java = "", $width = "", $all = "", $awal = "", $akhir = "")
{
  $style = $width == "" ? "" : "style=\"width:$width\"";
  $text = "<style>
	#" . str_replace("]", "_", str_replace("[", "_", $nama)) . "_chosen {min-width: 80px}
</style>
<select id=\"$nama\" name=\"$nama\" $java $style class=\"chosen-select\">";
  $range = $range == "" ? 5 : $range;
  if (empty($awal))
    $awal = empty($sel) ? date('Y') - $range : $sel - $range;
  if (empty($akhir))
    $akhir = empty($sel) ? date('Y') + $range : $sel + $range;
  if (!empty($all))
    $text .= empty($sel) ? "<option value=\"\" selected>All</option>" : "<option value=\"\">All</option>";
  for ($nilai = $awal; $nilai <= $akhir; $nilai++) {
    if ($nilai == $sel) {
      $text .= "<option value=\"$nilai\" selected>$nilai</option>";
    } else {
      $text .= "<option value=\"$nilai\">$nilai</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

function comboMonth($nama, $sel, $java = "", $width = "", $all = "")
{
  $style = $width == "" ? "" : "style=\"width:$width\"";
  $text = "<select id=\"$nama\" name=\"$nama\" $java $style class=\"chosen-select\">";
  if (!empty($all))
    $text .= empty($sel) ? "<option value=\"\" selected>All</option>" : "<option value=\"\">All</option>";
  for ($nilai = 1; $nilai <= 12; $nilai++) {
    $bulan = str_pad($nilai, 2, "0", STR_PAD_LEFT);
    if ($nilai == $sel) {
      $text .= "<option value=\"$bulan\" selected>" . getBulan($bulan) . "</option>";
    } else {
      $text .= "<option value=\"$bulan\">" . getBulan($bulan) . "</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

function comboData($sql, $key, $val, $nama, $option = "All", $nilai = "", $java = "", $width = "", $class = "", $disabled = "")
{
  $width = $width == "" ? "" : "width:$width;";
  $disabled = $disabled == "" ? "" : "disabled";
  $txt = "<select id=\"$nama\" name=\"$nama\" class=\"$class\" style=\"height:32px; $width\" $java $disabled>";

  $result = db("$sql");
  $jml = mysql_num_rows($result);

  if ($option == " ") {
    $txt .= "<option value=\"\">$option</option>";
  }

  if (strlen($option) > 2) {
    $txt .= "<option value=\"\">$option</option>";
  }

  for ($i = 0; $i < $jml; $i++) {
    $r = mysql_fetch_array($result);
    if (trim($r[$key]) == trim($nilai)) {
      $txt .= "<option value=\"$r[$key]\" selected>$r[$val]</option>";
    } else {
      $txt .= "<option value=\"$r[$key]\">$r[$val]</option>";
    }
  }
  $txt .= "</select>";
  return $txt;
}


function comboStatus($nama, $array, $sel, $java = "", $style = "")
{
  $style = $width == "" ? "" : "style=\"$style\"";

  $text = "<select id=\"$nama\" name=\"$nama\" $java $style>";

  foreach ($array as $key => $value) {

    if ($key == $sel)
      $text .= "<option value=\"$key\" selected>$value</option>";
    else
      $text .= "<option value=\"$key\">$value</option>";
  }

  $text .= "</select>";

  return $text;
}


function floorDec($input, $decimals)
{
  return round($input - (5 / pow(10, $decimals + 1)), $decimals);
}

function setAngka($nilai)
{
  $nilai = $nilai == "" ? "0" : $nilai;
  $hasil = substr($nilai, 0, 1) == "." ? "0" . $nilai : $nilai;
  $hasil = str_replace(",", "", $hasil);
  return $hasil;
}

function getAngka($nilai = 0, $digit = 0)
{
    if (strpos( $nilai, "." ) == true) return number_format($nilai, $digit);
    else return number_format($nilai);
}

function ceilAngka($number, $significance = 1)
{
  return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
}


function getAccounting($nilai = 0, $digit = 0)
{
  $nilai = floor($nilai);
  $digit = 0;
  if ($nilai < 0)
    return "(" . number_format(($nilai * -1), $digit) . ")";
  else
    return number_format($nilai, $digit);
}

function setAccounting($nilai = 0)
{
  return setAngka(getAccounting($nilai));
}


function namaData($kodeData)
{
  $text = getField("select namaData from mst_data where kodeData = '$kodeData'");
  return $text;
}

function numToAlpha($data)
{
  $data = $data - 1;
  $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
  $alpha_flip = array_flip($alphabet);
  if ($data <= 25) {
    return strtoupper($alphabet[$data]);
  } elseif ($data > 25) {
    $dividend = ($data + 1);
    $alpha = '';
    $modulo;
    while ($dividend > 0) {
      $modulo = ($dividend - 1) % 26;
      $alpha = $alphabet[$modulo] . $alpha;
      $dividend = floor((($dividend - $modulo) / 26));
    }
    return strtoupper($alpha);
  }
}

function setTanggal($tanggal)
{
  if (empty($tanggal))
    $hasil = "0000-00-00";
  $arr = explode("/", $tanggal);
  $hasil = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
  return $hasil;
}

function getBulan($bulan, $str = "")
{
  $arr = array(
    "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April", "5" => "Mei", "6" => "Juni", "7" => "Juli",
    "8" => "Agustus", "9" => "September", "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli",
    "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
  );
  $sub = array(
    "1" => "Jan", "2" => "Feb", "3" => "Mar", "4" => "Apr", "5" => "May", "6" => "Jun", "7" => "Jul",
    "8" => "Aug", "9" => "Sep", "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec"
  );
  $hasil = $str ? $sub["$bulan"] : $arr["$bulan"];
  return $hasil;
}

function getRomawi($val)
{
  $arr = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
  $value = intval($val);
  $hasil = $arr[$value];
  return $hasil;
}
function getTanggal2($date, $format)
{
  $middle = strtotime($date);
  $new_date = date($format, $middle);

  return $new_date;
}

function getTanggal($tanggal, $format = "")
{
  $arr = explode("-", $tanggal);
  if ($format == "") {
    $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : $arr[2] . "/" . $arr[1] . "/" . $arr[0];
  } else {
    $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : "$arr[2] " . getBulan($arr[1]) . " $arr[0]";
  }
  return $hasil;
}

function getWaktu($waktu, $format = "")
{
  $arr_ = explode(" ", $waktu);
  $tanggal = $arr_[0];
  $jam = $arr_[1];
  $arr = explode("-", $tanggal);
  if ($format == "") {
    $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : $arr[2] . "/" . $arr[1] . "/" . $arr[0];
  } else {
    $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : "$arr[2] " . getBulan($arr[1]) . " $arr[0]";
  }
  return $hasil ? $hasil . " @ " . substr($jam, 0, 5) : "";
}

function dateDiff($per, $d1, $d2)
{
  $d = $d2 - $d1;
  switch ($per) {
    case "yyyy":
      $d /= 12;
    case "m":
      $d *= 12 * 7 / 365.25;
    case "ww":
      $d /= 7;
    case "d":
      $d /= 24;
    case "h":
      $d /= 60;
    case "n":
      $d /= 60;
  }
  return round($d) > 0 ? round($d) : round($d) * -1;
}

function dateAdd($per, $n, $d)
{
  switch ($per) {
    case "yyyy":
      $n *= 12;
    case "m":
      $d = mktime(
        date("H", $d),
        date("i", $d),
        date("s", $d),
        date("n", $d) + $n,
        date("j", $d),
        date("Y", $d)
      );
      $n = 0;
      break;
    case "ww":
      $n *= 7;
    case "d":
      $n *= 24;
    case "h":
      $n *= 60;
    case "n":
      $n *= 60;
  }
  return $d + $n;
}

function dateMin($per, $n, $d)
{
  switch ($per) {
    case "yyyy":
      $n *= 12;
    case "m":
      $d = mktime(
        date("H", $d),
        date("i", $d),
        date("s", $d),
        date("n", $d) - $n,
        date("j", $d),
        date("Y", $d)
      );
      $n = 0;
      break;
    case "ww":
      $n *= 7;
    case "d":
      $n *= 24;
    case "h":
      $n *= 60;
    case "n":
      $n *= 60;
  }
  return $d - $n;
}

function sumTime($times)
{

  // loop throught all the times
  foreach ($times as $time) {
    list($hour, $minute) = explode(':', $time);
    $minutes += $hour * 60;
    $minutes += $minute;
  }

  $hours = floor($minutes / 60);
  $minutes -= $hours * 60;

  // returns the time already formatted
  $result = sprintf('%02d:%02d', $hours, $minutes);
  if ($result == "00:00") $result = "";
  return $result;
}

function selisihTahun($d1, $d2)
{
  list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
  list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
  list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

  list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
  list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
  list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

  if (empty($jamAwal)) $jamAwal = 0;
  if (empty($menitAwal)) $menitAwal = 0;
  if (empty($detikAwal)) $detikAwal = 0;

  if (empty($jamAkhir)) $jamAkhir = 0;
  if (empty($menitAkhir)) $menitAkhir = 0;
  if (empty($detikAkhir)) $detikAkhir = 0;

  $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
  $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
  return dateDiff("yyyy", $dAwal, $dAkhir);
}

function selisihBulan($d1, $d2)
{
  list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
  list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
  list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

  list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
  list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
  list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

  if (empty($jamAwal)) $jamAwal = 0;
  if (empty($menitAwal)) $menitAwal = 0;
  if (empty($detikAwal)) $detikAwal = 0;

  if (empty($jamAkhir)) $jamAkhir = 0;
  if (empty($menitAkhir)) $menitAkhir = 0;
  if (empty($detikAkhir)) $detikAkhir = 0;

  $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
  $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
  return dateDiff("m", $dAwal, $dAkhir);
}

function selisihJam($d1, $d2)
{
  list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
  list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
  list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

  list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
  list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
  list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

  $dAwal = mktime($jamAwal, $menitAwal, $detikAwal,  $bulanAwal, $hariAwal, $tahunAwal);
  $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir,  $bulanAkhir, $hariAkhir, $tahunAkhir);
  return dateDiff("h", $dAwal, $dAkhir);
}

function selisihMenit($d1, $d2)
{
  list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
  list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
  list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

  list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
  list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
  list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

  $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
  $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
  return dateDiff("n", $dAwal, $dAkhir);
}
function lastUpdate($id_field, $id_value, $created_by, $created_datetime, $update_by, $update_datetime, $table)
{

  $text = "
  <fieldset style=\"margin-bottom:20px;\">
    <legend>Update</legend>
    <table width=\"100%\">
      <tr>
        <td width=\"50%\" style=\"vertical-align: top\">
          <p>
            <label class=\"l-input-small2\">Created Date</label>
            <span class=\"field\">" . getField("SELECT $created_datetime FROM $table WHERE $id_field = $id_value") . "&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small2\">Update Date</label>
            <span class=\"field\">" . getField("SELECT $update_datetime FROM $table WHERE $id_field = $id_value") . "&nbsp;</span>
          </p>
        </td>             
        <td width=\"50%\" style=\"vertical-align: top\">
          <p>
            <label class=\"l-input-small3\">Created By</label>
            <span class=\"field\">" . getField("SELECT namaUser FROM app_user WHERE idPegawai='" . getField("SELECT $created_by FROM $table WHERE $id_field = $id_value") . "'") . "&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small3\">Update By</label>
            <span class=\"field\">" . getField("SELECT namaUser FROM app_user WHERE idPegawai='" . getField("SELECT $update_by FROM $table WHERE $id_field = $id_value") . "'") . "&nbsp;</span>
          </p>  
        </td>
      </tr>
    </table>
  </fieldset>";

  return $text;
}

function uploadImages($id, $name, $folder_target, $format, $sizeThumb, $sizeSame)
{
  uploadFilesThumb("$id", "$name", "$folder_target", "$format", "$sizeThumb");
  uploadFilesSame("$id", "$name", "$folder_target", "$format", "$sizeSame");
  $year = date("Y");
  $month = date("m");
  $day = date("d");

  $cFile = "$folder_target/$year/$month/$day/";

  if (!is_dir("$folder_target/$year/")) {
    mkdir("$folder_target/$year/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/")) {
    mkdir("$folder_target/$year/$month/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/$day/")) {
    mkdir("$folder_target/$year/$month/$day/", 0755, true);
  }

  $fileUpload = $_FILES["$name"]["tmp_name"];
  $fileUpload_name = $_FILES["$name"]["name"];

  if (($fileUpload != "") and ($fileUpload != "none")) {
    fileUpload($fileUpload, $fileUpload_name, $cFile);
    $file = $id . $format . "." . getExtension($fileUpload_name);
    fileRename($cFile, $fileUpload_name, $file);
    $file = "$year/$month/$day/" . $file;
  }

  return $file;
}

function uploadFilesThumb($id, $name, $folder_target, $format, $size)
{
  $year = date("Y");
  $month = date("m");
  $day = date("d");
  if ($size == '') {
    $size = "260px";
  }
  $cFile = "$folder_target/$year/$month/$day/";

  if (!is_dir("$folder_target/$year/")) {
    mkdir("$folder_target/$year/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/")) {
    mkdir("$folder_target/$year/$month/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/$day/")) {
    mkdir("$folder_target/$year/$month/$day/", 0755, true);
  }

  $fileUpload = $_FILES["$name"]["tmp_name"];
  $fileUpload_name = $_FILES["$name"]["name"];

  if (($fileUpload != "") and ($fileUpload != "none")) {

    //thumbnail image
    fileUpload($fileUpload, $fileUpload_name, $cFile);
    $File = $id . $format . "thumb" . "." . getExtension($fileUpload_name);
    $newFile = $cFile . $File;
    $ext = getExtension($fileUpload_name);
    if ($ext == "jpg" || $ext == "jpeg") $src = imagecreatefromjpeg($fileUpload);
    if ($ext == "png") $src = imagecreatefrompng($fileUpload);
    if ($ext == "gif") $src = imagecreatefromgif($fileUpload);
    $maxWidth = $size;
    $maxHeight = $size;
    list($width, $height) = getimagesize($fileUpload);
    /*$ratioH = $maxHeight/$height;
    $ratioW = $maxWidth/$width;
    $ratio = min($ratioH, $ratioW);
    $newWidth = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$width) : $width;
    $newHeight = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$height) : $height;*/

    $newWidth = $size;
    $newHeight = $size;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    $filename = $cFile . $fileUpload_name;
    imagejpeg($tmp, $filename, 100);
    imagedestroy($src);
    imagedestroy($tmp);
    fileRename($cFile, $fileUpload_name, $File);
    // end thumbnail image
    $file = "$year/$month/$day/" . $File;
  }

  return $file;
}

function uploadFilesSame($id, $name, $folder_target, $format, $size)
{
  $year = date("Y");
  $month = date("m");
  $day = date("d");
  if ($size == '') {
    $size = "180px";
  }
  $cFile = "$folder_target/$year/$month/$day/";

  if (!is_dir("$folder_target/$year/")) {
    mkdir("$folder_target/$year/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/")) {
    mkdir("$folder_target/$year/$month/", 0755, true);
  }

  if (!is_dir("$folder_target/$year/$month/$day/")) {
    mkdir("$folder_target/$year/$month/$day/", 0755, true);
  }

  $fileUpload = $_FILES["$name"]["tmp_name"];
  $fileUpload_name = $_FILES["$name"]["name"];

  if (($fileUpload != "") and ($fileUpload != "none")) {

    //thumbnail image
    fileUpload($fileUpload, $fileUpload_name, $cFile);
    $File = $id . $format . "same" . "." . getExtension($fileUpload_name);
    $newFile = $cFile . $File;
    $ext = getExtension($fileUpload_name);
    if ($ext == "jpg" || $ext == "jpeg") $src = imagecreatefromjpeg($fileUpload);
    if ($ext == "png") $src = imagecreatefrompng($fileUpload);
    if ($ext == "gif") $src = imagecreatefromgif($fileUpload);
    $maxWidth = $size;
    $maxHeight = $size;
    list($width, $height) = getimagesize($fileUpload);
    /*$ratioH = $maxHeight/$height;
    $ratioW = $maxWidth/$width;
    $ratio = min($ratioH, $ratioW);
    $newWidth = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$width) : $width;
    $newHeight = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$height) : $height;*/

    $newWidth = $size;
    $newHeight = $size;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    $filename = $cFile . $fileUpload_name;
    imagejpeg($tmp, $filename, 100);
    imagedestroy($src);
    imagedestroy($tmp);
    fileRename($cFile, $fileUpload_name, $File);
    // end thumbnail image
    $file = "$year/$month/$day/" . $File;
  }

  return $file;
}

function uploadFiles($id, $name, $folder_target, $format)
{
  $fileUpload = $_FILES["$name"]["tmp_name"];
  $fileUpload_name = $_FILES["$name"]["name"];
  if (($fileUpload != "") and ($fileUpload != "none")) {
    fileUpload($fileUpload, $fileUpload_name, $folder_target);
    $file = $format . $id . "." . getExtension($fileUpload_name);
    fileRename($folder_target, $fileUpload_name, $file);
  }
  return $file;
}

function fileUpload($userfile, $userfile_name, $dir)
{
  if ($userfile != "") {
    if (!is_dir("$dir/")) {
      mkdir("$dir", 0755);
    }

    if (!copy($userfile, "$dir/$userfile_name")) {
      echo "error tuh";
    }
  }
}

function getNoPr($tipe)
{
  $getlastNumber = getField("SELECT nomorPr FROM wrh_pr WHERE tipe = '$tipe' AND SUBSTR(nomorPr,3,2) = '" . date('y') . "' AND SUBSTR(nomorPr,6,2) = '" . date('m') . "'  ORDER BY SUBSTR(nomorPr,9,4) DESC LIMIT 1");
  $str = (empty($getlastNumber)) ? "0000" : substr($getlastNumber, 8, 10);
  $incNum = str_pad($str + 1, 4, "0", STR_PAD_LEFT);
  $year = substr(date("y"), 0, 2);
  $month = date('m');
  return $tipe . $year . '/' . $month . '/' . $incNum;
}

function deletePr($kodePr)
{
  db("delete from wrh_pr where kodePr = $kodePr");
  db("delete from wrh_pr_detail where kodePr = $kodePr");
  db("delete from prc_rencana_supplier where kodePr = $kodePr");
}

function nilaiPr()
{
  return 50000000;
}

function updateStok($tipe = '', $kodeProduk = '', $kodeKategori = '', $kodeGudang = '', $jumlahDetail = '', $status = '', $jumlahDetailAwal = '')
{
  $freeAwal = getField("select jumlahStok from inv_produk_stok where kodeStatus = 'KS01' and kodeProduk = $kodeProduk and kodeKategori = $kodeKategori and kodeGudang = $kodeGudang");
  $markingAwal = getField("select jumlahStok from inv_produk_stok where kodeStatus = 'KS02' and kodeProduk = $kodeProduk and kodeKategori = $kodeKategori and kodeGudang = $kodeGudang");

  if ($status == 'create') {
    $freeAkhir = ($tipe == "IR") ? $freeAwal - $jumlahDetail : $freeAwal + $jumlahDetail;
    $markingAkhir = ($tipe == "IR") ? $markingAwal + $jumlahDetail : $markingAwal;
  }

  if ($status == 'update') {
    if ($jumlahDetailAwal == $jumlahDetail) // same
    {
      $freeAkhir = $freeAwal;
      $markingAkhir = $markingAwal;
    }
    if ($jumlahDetailAwal > $jumlahDetail) // plus
    {
      if ($tipe == "IR") {
        $diff = $jumlahDetail - $jumlahDetailAwal;
        $freeAkhir = $freeAwal - $diff;
        $markingAkhir = $markingAwal + $diff;
      }
      if ($tipe == "PR") {
        $diff = $jumlahDetail - $jumlahDetailAwal;
        $freeAkhir = $freeAwal + $diff;
        $markingAkhir = $markingAwal;
      }
    }
    if ($jumlahDetailAwal < $jumlahDetail) // minus
    {
      if ($tipe == "IR") {
        $diff = $jumlahDetailAwal - $jumlahDetail;
        $freeAkhir = $freeAwal + $diff;
        $markingAkhir = $markingAwal - $diff;
      }
      if ($tipe == "PR") {
        $diff = $jumlahDetailAwal - $jumlahDetail;
        $freeAkhir = $freeAwal - $diff;
        $markingAkhir = $markingAwal;
      }
    }
  }

  if ($status == 'delete') {
    $freeAkhir = ($tipe == "IR") ? $freeAwal + $jumlahDetail : $freeAwal - $jumlahDetail;
    $markingAkhir = ($tipe == "IR") ? $markingAwal - $jumlahDetail : $markingAwal;
  }

  if ($status == 'confirm') {
    if ($tipe == "IR") {
      if (empty($jumlahDetailAwal)) {
        $freeAkhir = $freeAwal;
        $markingAkhir = $markingAwal - $jumlahDetail;
      } else {
        if ($jumlahDetail == $jumlahDetailAwal) {
          $freeAkhir = $freeAwal;
          $markingAkhir = $markingAwal;
        }

        if ($jumlahDetail > $jumlahDetailAwal) {
          $diff = $jumlahDetail - $jumlahDetailAwal;
          $freeAkhir = $freeAwal;
          $markingAkhir = $markingAwal - $diff;
        }

        if ($jumlahDetail < $jumlahDetailAwal) {
          $diff = $jumlahDetailAwal - $jumlahDetail;
          $freeAkhir = $freeAwal;
          $markingAkhir = $markingAwal + $diff;
        }
      }
    }
  }

  db("update inv_produk_stok set jumlahStok = $freeAkhir where kodeStatus = 'KS01' and kodeProduk = $kodeProduk and kodeKategori = $kodeKategori and kodeGudang = $kodeGudang");
  db("update inv_produk_stok set jumlahStok = $markingAkhir where kodeStatus = 'KS02' and kodeProduk = $kodeProduk and kodeKategori = $kodeKategori and kodeGudang = $kodeGudang");
  // echo "update inv_produk_stok set jumlahStok = $freeAkhir where kodeStatus = 'KS01' and kodeProduk = $kodeProduk and kodeKategori = $kodeKategori and kodeGudang = $kodeGudang";
}

function updateStokMutasi($status, $kodeProduk, $kodeKategori, $gudangAsal, $gudangTujuan, $jumlahTerima, $jumlahTerimaAwal)
{

  $cekStokGudangAsal = queryAssoc("SELECT * FROM inv_produk_stok  WHERE kodeGudang = $gudangAsal AND  kodeProduk = $kodeProduk AND kodeKategori = $kodeKategori");
  $cekStokGudangTujuan = queryAssoc("SELECT * FROM inv_produk_stok  WHERE kodeGudang = $gudangTujuan AND  kodeProduk = $kodeProduk AND kodeKategori = $kodeKategori");

  if (empty($cekStokGudangAsal)) {
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangAsal, $kodeKategori, 'KS01', 0)");
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangAsal, $kodeKategori, 'KS02', 0)");
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangAsal, $kodeKategori, 'KS03', 0)");
  }

  if (empty($cekStokGudangTujuan)) {
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangTujuan, $kodeKategori, 'KS01', 0)");
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangTujuan, $kodeKategori, 'KS02', 0)");
    db("insert into inv_produk_stok (idStok, kodeProduk, kodeGudang, kodeKategori, kodeStatus, jumlahStok) values ('', $kodeProduk, $gudangTujuan, $kodeKategori, 'KS03', 0)");
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $stokGudangAsal = getField("select jumlahStok from inv_produk_stok
                where kodeGudang = $gudangAsal and
                kodeProduk = $kodeProduk and
                kodeKategori = $kodeKategori and
                kodeStatus = 'KS01'");

  $stokGudangTujuan = getField("select jumlahStok from inv_produk_stok
                where kodeGudang = $gudangTujuan and
                kodeProduk = $kodeProduk and
                kodeKategori = $kodeKategori and
                kodeStatus = 'KS01'");

  if ($status == 'tambah') {
    $stokGudangAsal = $stokGudangAsal - $jumlahTerima;
    $stokGudangTujuan = $stokGudangTujuan + $jumlahTerima;
  }

  if ($status == 'kurang') {
    $stokGudangAsal = $stokGudangAsal + ($jumlahTerimaAwal - $jumlahTerima);
    $stokGudangTujuan = $stokGudangTujuan - ($jumlahTerimaAwal - $jumlahTerima);
  }

  if ($status == 'reset') {
    $stokGudangAsal = $stokGudangAsal + $jumlahTerimaAwal;
    $stokGudangTujuan = $stokGudangTujuan - $jumlahTerimaAwal;
  }

  db("update inv_produk_stok set jumlahStok = '$stokGudangAsal'
                where kodeGudang = $gudangAsal and
                kodeProduk = $kodeProduk and
                kodeKategori = $kodeKategori and
                kodeStatus = 'KS01'");

  db("update inv_produk_stok set jumlahStok = '$stokGudangTujuan'
                where kodeGudang = $gudangTujuan and
                kodeProduk = $kodeProduk and
                kodeKategori = $kodeKategori and
                kodeStatus = 'KS01'");
}

function getNoGd()
{
  $getlastNumber = getField("SELECT nomorMutasi FROM mutasi_gudang WHERE SUBSTR(nomorMutasi,3,2) = '" . date("y") . "' AND SUBSTR(nomorMutasi,6,2) = '" . date('m') . "' order by SUBSTR(nomorMutasi,9,4) desc limit 1");
  $str = (empty($getlastNumber)) ? "0000" : substr($getlastNumber, 8, 10);
  $incNum = str_pad($str + 1, 4, "0", STR_PAD_LEFT);
  $year = substr(date("y"), 0, 2);
  $month = date('m');
  return "GD" . $year . '/' . $month . '/' . $incNum;
}

function getExtension($str)
{
  $i = strrpos($str, ".");
  if (!$i) {
    return "";
  }

  $l = strlen($str) - $i;
  $ext = substr($str, $i + 1, $l);
  return strtolower($ext);
}

function getIcon($file, $folder = "")
{
  $ext = getExtension($file);
  $file = "styles/images/extensions/" . $ext . ".png";
  $icon = is_file($folder . $file) ? $file : "styles/images/extensions/file.png";
  return $icon;
}

function fileRename($folder, $oldfile, $newfile)
{
  if (!rename($folder . $oldfile, $folder . $newfile)) {
    if (copy($folder . $oldfile, $folder . $newfile)) {
      unlink($folder . $oldfile);
    }
  }
}

function fileMove($nfile, $ofolder, $nfolder)
{
  if (!is_dir("$nfolder/")) {
    mkdir("$nfolder", 0755);
  }
  if (!rename($ofolder . $nfile, $nfolder . $nfile)) {
    if (copy($ofolder . $nfile, $nfolder . $nfile)) {
      unlink($ofolder . $nfile);
    }
  }
}

function arraySuffle($list)
{
  if (!is_array($list))
    return $list;

  $keys = array_keys($list);
  shuffle($keys);
  $random = array();
  foreach ($keys as $key)
    $random = $list[$key];

  return $random;
}

function terbilang($x)
{
  $t = explode(".", $x);
  return $t[1] > 0 ? numToString($t[0]) . " Koma" . numToString($t[1]) : numToString($t[0]);
}

function numToString($x)
{
  $abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
  if ($x < 12)
    return " " . $abil[$x];
  elseif ($x < 20)
    return numToString($x - 10) . " Belas";
  elseif ($x < 100)
    return numToString($x / 10) . " Puluh" . numToString($x % 10);
  elseif ($x < 200)
    return " Seratus" . numToString($x - 100);
  elseif ($x < 1000)
    return numToString($x / 100) . " Ratus" . numToString($x % 100);
  elseif ($x < 2000)
    return " Seribu" . numToString($x - 1000);
  elseif ($x < 1000000)
    return numToString($x / 1000) . " Ribu" . numToString($x % 1000);
  elseif ($x < 1000000000)
    return numToString($x / 1000000) . " Juta" . numToString($x % 1000000);
}

function formatBytes($bytes, $precision = 2)
{
  $units = array('B', 'KB', 'MB', 'GB', 'TB');

  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);

  // Uncomment one of the following alternatives
  $bytes /= pow(1024, $pow);
  // $bytes /= (1 << (10 * $pow)); 

  return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatKode($kodeParameter, $fieldNama, $tableNama, $tanggalDefault = "")
{
  global $inp, $par;

  list($tanggal, $bulan, $tahun) = explode("/", $tanggalDefault);
  if (empty($bulan))
    $bulan = date('m');
  if (empty($tahun))
    $tahun = date('Y');

  $param = getField("select nilaiParameter from app_parameter where kodeParameter='$kodeParameter'");
  $param = str_replace("YYYY", $tahun, $param);
  $param = str_replace("YY", substr($tahun, 2, 2), $param);
  $param = str_replace("MM", $bulan, $param);


  if (substr($param, 0, 1) != "C" && substr($param, -1) != "C") {
    $count = "C";
    $no = 1;
  }

  $arr = explode("C", $param);
  if (is_array($arr)) {
    while (list($k, $cnt) = each($arr)) {
      if (empty($cnt)) {
        $count .= "C";
        $no++;
      }
    }
  }

  list($prefix, $sufix) = explode($count, $param);

  if (is_array($fieldNama) && is_array($tableNama)) {
    $sql = "select replace(replace(fldNama, '" . $sufix . "', ''), '" . $prefix . "', '') from (";
    while (list($k, $tblNama) = each($tableNama)) {
      if ($k > 0)
        $sql .= " union ";
      $sql .= "select " . $fieldNama[$k] . " as fldNama from " . $tblNama . " where " . $fieldNama[$k] . " like '" . str_replace($count, "%", $param) . "'";
    }
    $sql .= ") as t order by 1 desc limit 1";
    $counter = getField($sql);
  } else {
    $counter = getField("select replace(replace(" . $fieldNama . ", '" . $sufix . "', ''), '" . $prefix . "', '') from " . $tableNama . " where " . $fieldNama . " like '" . str_replace($count, "%", $param) . "' order by 1 desc limit 1");
  }

  $counter = str_pad($counter + 1, $no, "0", STR_PAD_LEFT);

  $param = str_replace($count, $counter, $param);
  return $param;
}


function kodeGenerate($kodeJenis, $fieldNama, $tableNama, $tanggalDefault = "", $kodeRekening = "", $kodeDepartemen = "", $kodeArea = "")
{
  global $cPerusahaan;
  $paramPerusahaan = !empty($cPerusahaan) ? getField("select paramPerusahaan from mst_perusahaan where kodePerusahaan='$cPerusahaan'") :
    getField("select paramPerusahaan from mst_perusahaan where statusPerusahaan='t' order by kodePerusahaan  limit 1");

  list($tanggal, $bulan, $tahun) = explode("/", $tanggalDefault);
  if (empty($bulan)) $bulan = date('m');
  if (empty($tahun)) $tahun = date('Y');

  $param = getField("select namaJenis from mst_jenis where kodeJenis='$kodeJenis'");
  $param = str_replace("REK", getField("select nomorRekening from mst_rekening where kodeRekening='$kodeRekening'"), $param);
  $param = empty($kodeDepartemen) ? str_replace("DIV/", "", $param) :
    str_replace("DIV", getField("select kodeMaster from mst_data where kodeData='$kodeDepartemen'"), $param);
  $param = str_replace("CAB", getField("select kodeMaster from mst_data where kodeData='$kodeArea'"), $param);
  $param = str_replace("YYYY", $tahun, $param);
  $param = str_replace("YY", substr($tahun, 2, 2), $param);
  $param = str_replace("MMM", getRomawi($bulan), $param);
  $param = str_replace("[MM]", getBulan($bulan, "t") . " ", $param);
  $param = str_replace("MM", $bulan, $param);
  $param = str_replace("RR", getBulan($bulan, "", "t"), $param);

  if (substr($param, 0, 1) != "C" && substr($param, -1) != "C") {
    $count = "C";
    $no = 1;
  }
  $arr = explode("C", $param);
  if (is_array($arr)) {
    while (list($k, $cnt) = each($arr)) {
      if (empty($cnt)) {
        $count .= "C";
        $no++;
      }
    }
  }

  $param = str_replace("PER", $paramPerusahaan, $param);
  list($prefix, $sufix) = explode($count, $param);

  $counter = str_pad(getField("select CAST(replace(replace(" . $fieldNama . ", '" . $sufix . "', ''), '" . $prefix . "', '') AS UNSIGNED) from " . $tableNama . " where " . $fieldNama . " like '" . str_replace($count, "%", $param) . "' order by 1 desc limit 1") + 1, $no, "0", STR_PAD_LEFT);

  $param = str_replace($count, $counter, $param);
  return $param;
}

function invoicePengadaan($arr)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodePengadaan) = each($arr)) {
      $sql = "select sum(t2.nilaiDetail) as invoicePengadaan, sum(t2.nilaiDetail_asing) as invoicePengadaan_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and t1.approveBy is not null and t2.kodeTransaksi_='$kodePengadaan'";
      $res = db($sql);
      $r = mysql_fetch_array($res);

      $sql = "update prc_pengadaan set invoicePengadaan='" . $r[invoicePengadaan] . "', invoicePengadaan_asing='" . $r[invoicePengadaan_asing] . "' where kodePengadaan='$kodePengadaan'";
      db($sql);
    }
  }
}


function returTransaksi($arr)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodeTransaksi) = each($arr)) {
      $sql = "select sum(nilaiTransaksi) as returTransaksi, sum(nilaiTransaksi_asing) as returTransaksi_asing from fin_transaksi where approveBy!='' and approveBy is not null and kodeTransaksi_='$kodeTransaksi'";
      $res = db($sql);
      $r = mysql_fetch_array($res);

      $sql = "update fin_transaksi set returTransaksi='" . $r[returTransaksi] . "', returTransaksi_asing='" . $r[returTransaksi_asing] . "' where kodeTransaksi='$kodeTransaksi'";
      db($sql);
    }
  }
}

function penghapusanTransaksi($arr, $statusTransaksi)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodeTransaksi) = each($arr)) {
      $sql = "update fin_transaksi set statusTransaksi='$statusTransaksi' where kodeTransaksi='$kodeTransaksi'";
      db($sql);
    }
  }
}


function penambahanTransaksi($arr)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodeTransaksi) = each($arr)) {
      $sql = "select sum(t2.nilaiDetail) as penambahanTransaksi, sum(t2.nilaiDetail_asing) as penambahanTransaksi_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and approveBy is not null and t2.kodeTransaksi_='$kodeTransaksi' and t1.kodeJenis in ('BKK')";
      $res = db($sql);
      $r = mysql_fetch_array($res);

      $sql = "update fin_transaksi set penambahanTransaksi='" . $r[penambahanTransaksi] . "', penambahanTransaksi_asing='" . $r[penambahanTransaksi_asing] . "' where kodeTransaksi='$kodeTransaksi'";
      db($sql);
    }
  }
}

function pembayaranTransaksi($arr)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodeTransaksi) = each($arr)) {
      $pembayaranTransaksi = 0;
      $sql = "select sum(t2.nilaiDetail) as pembayaranTransaksi, sum(t2.nilaiDetail_asing) as pembayaranTransaksi_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and approveBy is not null and t2.kodeTransaksi_='$kodeTransaksi'";
      $res = db($sql);
      $r = mysql_fetch_array($res);
      $pembayaranTransaksi += $r[pembayaranTransaksi];

      //$sql="select sum(nilaiJurnal) as pembayaranTransaksi from acc_jurnal where approveBy!='' and approveBy is not null and kodeTransaksi='$kodeTransaksi' and kodeJenis='JUM'";
      $sql = "select sum(case when t2.statusDetail='d' then t2.nilaiDetail * -1 else t2.nilaiDetail end) as pembayaranTransaksi from acc_jurnal t1 join acc_jurnal_detail t2 join acc_account t3 on (t1.kodeJurnal=t2.kodeJurnal and t2.kodeAccount=t3.kodeAccount) where t1.approveBy!='' and t1.approveBy is not null and t1.kodeTransaksi='" . $kodeTransaksi . "' and t1.kodeJenis='JUM' and t3.nomorAccount like '114.%' and t3.nomorAccount not in ('114.06.04')";
      $res = db($sql);
      $r = mysql_fetch_array($res);
      $pembayaranTransaksi += $r[pembayaranTransaksi];

      $sql = "update fin_transaksi set pembayaranTransaksi='" . $pembayaranTransaksi . "', pembayaranTransaksi_asing='" . $pembayaranTransaksi . "' where kodeTransaksi='$kodeTransaksi'";
      db($sql);
    }
  }
}

function realisasiBon($arr)
{
  if (is_array($arr)) {
    ksort($arr);
    reset($arr);
    while (list($id, $kodeTransaksi) = each($arr)) {
      $sql = "select sum(t2.nilaiDetail) as realisasiTransaksi, sum(t2.nilaiDetail_asing) as realisasiTransaksi_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and approveBy is not null and t2.kodeTransaksi_='$kodeTransaksi' and t1.kodeJenis not in ('BKK')";
      $res = db($sql);
      $r = mysql_fetch_array($res);

      $sql = "update fin_transaksi set realisasiTransaksi='" . $r[realisasiTransaksi] . "', realisasiTransaksi_asing='" . $r[realisasiTransaksi_asing] . "' where kodeTransaksi='$kodeTransaksi'";
      db($sql);
    }
  }
}

function mutasiTransaksi($kodeRekening, $tanggalMutasi, $kodeJenis)
{
  global $cUsername;
  if (!in_array($kodeJenis, array("BBM", "BBK", "BKM", "BKK", "BKB", "BBG", "BPB"))) return false;
  list($tahunMutasi, $bulanMutasi) = explode("-", $tanggalMutasi);

  //Pindah Buku
  $nilaiPindah = 0;
  $nilaiPindah_asing = 0;
  if (in_array($kodeJenis, array("BBM", "BKM"))) {
    $jenisMutasi = "'BPB'";
    $kodeRekening_ = $kodeRekening;
    $arrBPB = arrayQuery("select t1.kodeTransaksi from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and t1.approveBy is not null and t2.kodeRekening='$kodeRekening' and month(t1.tanggalTransaksi)='$bulanMutasi' and year(t1.tanggalTransaksi)='$tahunMutasi' and t2.nilaiDetail > 0 and t1.kodeJenis in (" . $jenisMutasi . ")");
    $sql = "select sum(nilaiTransaksi) as nilaiPindah, sum(nilaiTransaksi_asing) as nilaiPindah_asing from fin_transaksi where approveBy!='' and approveBy is not null and kodeRekening_='$kodeRekening_' and kodeJenis in (" . $jenisMutasi . ") and month(tanggalTransaksi)='$bulanMutasi' and year(tanggalTransaksi)='$tahunMutasi' and kodeTransaksi not in ('" . implode("', '", $arrBPB) . "')";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $nilaiPindah = $r[nilaiPindah];
    $nilaiPindah_asing = $r[nilaiPindah_asing];
  }

  $statusDetail = in_array($kodeJenis, array("BBM", "BKM")) ? "d" : "k";
  $fieldMutasi = in_array($kodeJenis, array("BBM", "BKM")) ? "debet" : "kredit";
  $jenisMutasi = in_array($kodeJenis, array("BBK", "BKK", "BPB", "BKB", "BBG")) ? "'BBK', 'BKK', 'BPB', 'BKB', 'BBG'" : "'" . $kodeJenis . "'";

  //Detail Jurnal
  $nilaiJurnal = 0;
  $nilaiJurnal_asing = 0;
  $sql = "select t1.tanggalJurnal, t1.kodeJurnal, t2.* from acc_jurnal t1 join acc_jurnal_detail t2 on (t1.kodeJurnal=t2.kodeJurnal) where t1.kodeJenis in ('JUM', 'JPY') and t2.kodeAccount in (select kodeAccount from mst_rekening where kodeRekening='$kodeRekening') and t2.statusDetail='$statusDetail' and month(t1.tanggalJurnal)='$bulanMutasi' and year(t1.tanggalJurnal)='$tahunMutasi'and t1.approveBy!='' ";
  $res = db($sql);
  while ($r = mysql_fetch_array($res)) {
    $nilaiJurnal += $r[nilaiDetail];
    $nilaiJurnal_asing += $r[nilaiDetail];
  }

  //Detail Transaksi
  $nilaiDetail = 0;
  $nilaiDetail_asing = 0;
  if (in_array($kodeJenis, array("BBK", "BKK", "BPB", "BKB", "BBG"))) {
    $sql = "select sum(t2.nilaiDetail) as nilaiDetail, sum(t2.nilaiDetail_asing) as nilaiDetail_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and t1.approveBy is not null and t2.kodeRekening='$kodeRekening' and month(t1.tanggalTransaksi)='$bulanMutasi' and year(t1.tanggalTransaksi)='$tahunMutasi' and t2.nilaiDetail < 0";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $nilaiDetail = $r[nilaiDetail] * -1;
    $nilaiDetail_asing = $r[nilaiDetail_asing] * -1;
  } else {
    $sql = "select sum(t2.nilaiDetail) as nilaiDetail, sum(t2.nilaiDetail_asing) as nilaiDetail_asing from fin_transaksi t1 join fin_transaksi_detail t2 on (t1.kodeTransaksi=t2.kodeTransaksi) where t1.approveBy!='' and t1.approveBy is not null and t2.kodeRekening='$kodeRekening' and month(t1.tanggalTransaksi)='$bulanMutasi' and year(t1.tanggalTransaksi)='$tahunMutasi' and t2.nilaiDetail > 0";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $nilaiDetail = $r[nilaiDetail];
    $nilaiDetail_asing = $r[nilaiDetail_asing];
  }

  //Transaksi
  $sql = "select sum(nilaiTransaksi) as nilaiMutasi, sum(nilaiTransaksi_asing) as nilaiMutasi_asing from fin_transaksi where  approveBy!='' and approveBy is not null and kodeRekening='$kodeRekening' and kodeJenis in (" . $jenisMutasi . ") and month(tanggalTransaksi)='$bulanMutasi' and year(tanggalTransaksi)='$tahunMutasi'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $nilaiMutasi = $r[nilaiMutasi] + $nilaiPindah + $nilaiDetail + $nilaiJurnal;
  $nilaiMutasi_asing = $r[nilaiMutasi_asing] + $nilaiPindah_asing + $nilaiDetail_asing + $nilaiJurnal_asing;

  $sql = getField("select kodeRekening from mst_rekening_mutasi where kodeRekening='$kodeRekening' and tahunMutasi='$tahunMutasi'") ?
    "update mst_rekening_mutasi set " . $fieldMutasi . $bulanMutasi . "='" . setAngka($nilaiMutasi) . "', " . $fieldMutasi . $bulanMutasi . "_asing='" . setAngka($nilaiMutasi_asing) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeRekening='$kodeRekening' and tahunMutasi='$tahunMutasi'" :
    "insert into mst_rekening_mutasi (kodeRekening, tahunMutasi, " . $fieldMutasi . $bulanMutasi . ", " . $fieldMutasi . $bulanMutasi . "_asing, createBy, createTime) values ('$kodeRekening', '$tahunMutasi', '" . setAngka($nilaiMutasi) . "', '" . setAngka($nilaiMutasi_asing) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  #start : saldo awal tahun
  $tahunDepan = $tahunMutasi + 1;
  $sql = "select (" . $fieldMutasi . "00 + " . $fieldMutasi . "01 + " . $fieldMutasi . "02 + " . $fieldMutasi . "03 + " . $fieldMutasi . "04 + " . $fieldMutasi . "05 + " . $fieldMutasi . "06 + " . $fieldMutasi . "07 + " . $fieldMutasi . "08 + " . $fieldMutasi . "09 + " . $fieldMutasi . "10 + " . $fieldMutasi . "11 + " . $fieldMutasi . "12) as nilaiDepan, (" . $fieldMutasi . "00_asing + " . $fieldMutasi . "01_asing + " . $fieldMutasi . "02_asing + " . $fieldMutasi . "03_asing + " . $fieldMutasi . "04_asing + " . $fieldMutasi . "05_asing + " . $fieldMutasi . "06_asing + " . $fieldMutasi . "07_asing + " . $fieldMutasi . "08_asing + " . $fieldMutasi . "09_asing + " . $fieldMutasi . "10_asing + " . $fieldMutasi . "11_asing + " . $fieldMutasi . "12_asing) as nilaiDepan_asing from mst_rekening_mutasi where kodeRekening='$kodeRekening' and tahunMutasi='$tahunMutasi'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $sql = getField("select kodeRekening from mst_rekening_mutasi where kodeRekening='$kodeRekening' and tahunMutasi='$tahunDepan'") ?
    "update mst_rekening_mutasi set " . $fieldMutasi . "00='" . setAngka($r[nilaiDepan]) . "', " . $fieldMutasi . "00_asing='" . setAngka($r[nilaiDepan_asing]) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeRekening='$kodeRekening' and tahunMutasi='$tahunDepan'" :
    "insert into mst_rekening_mutasi (kodeRekening, tahunMutasi, " . $fieldMutasi . "00, " . $fieldMutasi . "00_asing, createBy, createTime) values ('$kodeRekening', '$tahunDepan', '" . setAngka($r[nilaiDepan]) . "', '" . setAngka($r[nilaiDepan_asing]) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);
  #end : saldo awal tahun		
}

function awalAccount($kodeAccount)
{
  global $cUsername;
  /*
	if($indukAccount=getField("select indukAccount from acc_account where kodeAccount='$kodeAccount'")){			
		
		$debetAccount= getField("select sum(awalAccount) as awalAccount from acc_account where tipeAccount='d' and indukAccount='$indukAccount'");
		$kreditAccount= getField("select sum(awalAccount) as awalAccount from acc_account where tipeAccount='k' and indukAccount='$indukAccount'");			
		$awalAccount= getField("select tipeAccount from acc_account where kodeAccount='$indukAccount'") == "d" ? $debetAccount-$kreditAccount : $kreditAccount - $debetAccount;
		
		$sql="update acc_account set awalAccount='$awalAccount' where kodeAccount='$indukAccount'";
		db($sql);
		
		awalAccount($indukAccount);		
	}
	*/
}


function mutasiAccount($kodeAccount, $tanggalMutasi, $statusDetail, $kodeArea = 0)
{
  global $cUsername;
  list($tahunMutasi, $bulanMutasi) = explode("-", $tanggalMutasi);

  $fieldMutasi = $statusDetail == "d" ? "debet" : "kredit";

  $nilaiMutasi = getField("select sum(nilaiDetail) as nilaiMutasi from acc_jurnal t1 join acc_jurnal_detail t2 on (t1.kodeJurnal=t2.kodeJurnal) where t2.kodeAccount='$kodeAccount' and t1.kodeArea='$kodeArea' and month(t1.tanggalJurnal)='$bulanMutasi' and year(t1.tanggalJurnal)='$tahunMutasi' and t2.statusDetail='$statusDetail' and t1.approveBy!=''");

  $sql = getField("select kodeAccount from acc_account_mutasi where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'") ?
    "update acc_account_mutasi set " . $fieldMutasi . $bulanMutasi . "='" . setAngka($nilaiMutasi) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'" :
    "insert ignore into acc_account_mutasi (kodeAccount, kodeArea, tahunMutasi, " . $fieldMutasi . $bulanMutasi . ", createBy, createTime) values ('$kodeAccount', '$kodeArea', '$tahunMutasi', '" . setAngka($nilaiMutasi) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  #set saldo awal tahun
  $tahunDepan = $tahunMutasi + 1;
  $nilaiDepan = getField("select (" . $fieldMutasi . "00 + " . $fieldMutasi . "01 + " . $fieldMutasi . "02 + " . $fieldMutasi . "03 + " . $fieldMutasi . "04 + " . $fieldMutasi . "05 + " . $fieldMutasi . "06 + " . $fieldMutasi . "07 + " . $fieldMutasi . "08 + " . $fieldMutasi . "09 + " . $fieldMutasi . "10 + " . $fieldMutasi . "11 + " . $fieldMutasi . "12) as nilaiDepan from acc_account_mutasi where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'");
  if (getField("select kodeAccount from acc_account where kodeAccount='$kodeAccount' and left(nomorAccount,1) > 3"))
    $nilaiDepan = 0;

  $sql = getField("select kodeAccount from acc_account_mutasi where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunDepan'") ?
    "update acc_account_mutasi set " . $fieldMutasi . "00='" . setAngka($nilaiDepan) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunDepan'" :
    "insert ignore into acc_account_mutasi (kodeAccount, kodeArea, tahunMutasi, " . $fieldMutasi . "00, createBy, createTime) values ('$kodeAccount', '$kodeArea', '$tahunDepan', '" . setAngka($nilaiDepan) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  #echo $kodeAccount ." -- ". $nilaiDepan."<br><br>";


  #set laba rugi account
  updateAccount($tanggalMutasi, $statusDetail, $kodeArea);
}

function updateAccount($tanggalMutasi, $statusDetail, $kodeArea)
{
  global $cUsername;
  $kodeAccount = getField("select nilaiParameter from app_parameter where kodeParameter='89'");

  list($tahunMutasi, $bulanMutasi) = explode("-", $tanggalMutasi);
  $fieldMutasi = $statusDetail == "d" ? "debet" : "kredit";

  $sql = "select * from acc_account_jenis t1 join acc_account t2 on (t1.kodeJenis=t2.kodeJenis) where t1.kelompokJenis='l'";
  $res = db($sql);
  while ($r = mysql_fetch_array($res)) {
    $arrFilter["$r[kodeAccount]"] = $r[kodeAccount];
    $sql_ = "select * from acc_account where indukAccount='" . $r[kodeAccount] . "'";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_array($res_)) {
      $arrFilter["$r_[kodeAccount]"] = $r_[kodeAccount];
    }
  }

  if (is_array($arrFilter)) $filter = "and kodeAccount in ('" . implode("','", $arrFilter) . "')";
  $nilaiMutasi = getField("select sum(" . $fieldMutasi . $bulanMutasi . ") as nilaiMutasi from acc_account_mutasi where  tahunMutasi='$tahunMutasi' and kodeArea='$kodeArea' $filter");

  $sql = getField("select kodeAccount from  acc_account_mutasi where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'") ?
    "update acc_account_mutasi set " . $fieldMutasi . $bulanMutasi . "='" . setAngka($nilaiMutasi) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'" :
    "insert ignore into acc_account_mutasi (kodeAccount, kodeArea, tahunMutasi, " . $fieldMutasi . $bulanMutasi . ", createBy, createTime) values ('$kodeAccount', '$kodeArea', '$tahunMutasi', '" . setAngka($nilaiMutasi) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  #set saldo awal tahun
  $tahunDepan = $tahunMutasi + 1;
  $nilaiDepan = getField("select (" . $fieldMutasi . "00 + " . $fieldMutasi . "01 + " . $fieldMutasi . "02 + " . $fieldMutasi . "03 + " . $fieldMutasi . "04 + " . $fieldMutasi . "05 + " . $fieldMutasi . "06 + " . $fieldMutasi . "07 + " . $fieldMutasi . "08 + " . $fieldMutasi . "09 + " . $fieldMutasi . "10 + " . $fieldMutasi . "11 + " . $fieldMutasi . "12) as nilaiDepan from acc_account_mutasi where  kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'");

  $kodeAccount = getField("select nilaiParameter from app_parameter where kodeParameter='88'");
  $nilaiDepan += getField("select (" . $fieldMutasi . "00 + " . $fieldMutasi . "01 + " . $fieldMutasi . "02 + " . $fieldMutasi . "03 + " . $fieldMutasi . "04 + " . $fieldMutasi . "05 + " . $fieldMutasi . "06 + " . $fieldMutasi . "07 + " . $fieldMutasi . "08 + " . $fieldMutasi . "09 + " . $fieldMutasi . "10 + " . $fieldMutasi . "11 + " . $fieldMutasi . "12) as nilaiDepan from acc_account_mutasi where  kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunMutasi'");

  $sql = getField("select kodeAccount from acc_account_mutasi where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunDepan'") ?
    "update acc_account_mutasi set " . $fieldMutasi . "00='" . setAngka($nilaiDepan) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeAccount='$kodeAccount' and kodeArea='$kodeArea' and tahunMutasi='$tahunDepan'" :
    "insert ignore into acc_account_mutasi (kodeAccount, kodeArea, tahunMutasi, " . $fieldMutasi . "00, createBy, createTime) values ('$kodeAccount', '$kodeArea', '$tahunDepan', '" . setAngka($nilaiDepan) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  /*
	#set tahun lalu	
	$tahunLalu = $tahunMutasi-1;
	$nilaiLalu=getField("select (".$fieldMutasi."00 + ".$fieldMutasi."01 + ".$fieldMutasi."02 + ".$fieldMutasi."03 + ".$fieldMutasi."04 + ".$fieldMutasi."05 + ".$fieldMutasi."06 + ".$fieldMutasi."07 + ".$fieldMutasi."08 + ".$fieldMutasi."09 + ".$fieldMutasi."10 + ".$fieldMutasi."11 + ".$fieldMutasi."12) as nilaiLalu from acc_account_mutasi where  kodeAccount='$kodeAccount' and tahunMutasi='$tahunLalu'");

	$kodeAccount = getField("select nilaiParameter from app_parameter where kodeParameter='88'");
	$sql=getField("select kodeAccount from acc_account_mutasi where kodeAccount='$kodeAccount' and tahunMutasi='$tahunDepan'")?
	"update acc_account_mutasi set ".$fieldMutasi."00='".setAngka($nilaiLalu)."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeAccount='$kodeAccount' and tahunMutasi='$tahunMutasi'":
	"insert ignore into acc_account_mutasi (kodeAccount, tahunMutasi, ".$fieldMutasi."00, createBy, createTime) values ('$kodeAccount', '$tahunMutasi', '".setAngka($nilaiLalu)."', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);				

	$sql=getField("select kodeAccount from acc_account_mutasi where kodeAccount='$kodeAccount' and tahunMutasi='$tahunDepan'")?
	"update acc_account_mutasi set ".$fieldMutasi."00='".setAngka($nilaiDepan)."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeAccount='$kodeAccount' and tahunMutasi='$tahunDepan'":
	"insert ignore into acc_account_mutasi (kodeAccount, tahunMutasi, ".$fieldMutasi."00, createBy, createTime) values ('$kodeAccount', '$tahunDepan', '".setAngka($nilaiDepan)."', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);
	*/
}

function detailPegawai($idPegawai)
{
  global $p, $s, $m, $menuAccess, $arrTitle, $par;

  $arrMaster = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");

  $dpFile = "images/pegawai/";

  $res = db("SELECT * FROM `dta_pegawai` WHERE `idPegawai` = '$idPegawai'");

  $row = mysql_fetch_assoc($res);

  $row[namaPegawai] = empty($row[namaPegawai]) ? "-" : $row[namaPegawai];
  $row[nikPegawai] = empty($row[nikPegawai]) ? "-" : $row[nikPegawai];
  $row[idDepartemen] = empty($row[idDepartemen]) ? "-" : $arrMaster[$row[idDepartemen]];
  $row[idJabatan] = empty($row[idJabatan]) ? "-" : $arrMaster[$row[idJabatan]];
  $row[posisiPegawai] = empty($row[posisiPegawai]) ? "-" : $row[posisiPegawai];
  $row[telpPegawai] = empty($row[telpPegawai]) ? "-" : $row[telpPegawai];
  $row[emailPegawai] = empty($row[emailPegawai]) ? "-" : $row[emailPegawai];
  $row[agamaPegawai] = empty($row[agamaPegawai]) ? "-" : $arrMaster[$row[agamaPegawai]];
  $row[masaKerja] = (getTanggal($row[tanggalmasukKerja]) ? $row[tanggalmasukKerja] . " (" . getAngka(selisihTahun($row[tanggalmasukKerja], date("Y-m-d"))) . " thn)" : $row[tanggalmasukKerja] . " (" . "0" . " thn)");
  $row[idPendTerakhir] = empty($row[idPendTerakhir]) ? "-" : $arrMaster[$row[idPendTerakhir]];

  $row[foto] = "" . APP_URL . "/files/nophoto.jpg";

  if ($row[fotoPegawai] != "") {
    $row[foto] = "" . $dpFile . "" . $row[fotoPegawai] . "";
  }

  $text = "
  <div class=\"contentwrapper\" id=\"contentwrapper\">
  <form action=\"\" id=\"form\" class=\"stdform\">
  <fieldset>
   <style>
    td{
      vertical-align:middle;
    }
  </style>
  <legend style=\"padding:10px; margin-left:20px;\">VIEW PEGAWAI</legend> 
  <table width=\"100%\">
    <tr>

      <p>

          <center><img src=\"$row[foto]\" width=\"200px\" height=\"200px\"></center>

      </p>
      <br />
      <td width=\"50%\">
        <p>

          <label class=\"l-input-small2\"><b>Nama</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[namaPegawai]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>NIK</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[nikPegawai]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Departemen</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[idDepartemen]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Jabatan</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[idJabatan]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Posisi</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[posisiPegawai]</span>    

        </p>
      </td>
      <td width=\"50%\">
        <p>

          <label class=\"l-input-small2\"><b>No HP</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[telpPegawai]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Email</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[emailPegawai]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Agama</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[agamaPegawai]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Masa Kerja</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[masaKerja]</span>    

        </p>
        <p>

          <label class=\"l-input-small2\"><b>Pendidikan</b></label>

          <span class=\"field\" style=\"width: 300px;\">$row[idPendTerakhir]</span>    

        </p>
      </td>
      
    </tr>
  </table>

</fieldset>
</form>
</div>
";

  return $text;
}
function detailArea($id)
{
  global $p, $s, $m, $menuAccess, $arrTitle, $par;
  $path = "files/area/";

  $arrData = arrayQuery("SELECT kodeData , namaData from mst_data ");
  $res = db("SELECT * FROM `area_kerja` WHERE `id_area` = '$par[id]'");

  $row = mysql_fetch_assoc($res);

  $text = "
  <fieldset>
   <style>
    td{
      vertical-align:middle;
    }
  </style>
  <legend style=\"padding:10px; margin-left:20px;\">AREA KERJA</legend> 
  <table width=\"100%\">
    <tr>
      <td width=\"80%\">
        ";
  if (!empty($par[idRuang])) {
    $text .= "
          <p>

            <label class=\"l-input-small\"><b>Area</b></label>

            <span class=\"field\" style=\"width: 500px;\">" . getField("SELECT `area` FROM `area_ruang` WHERE `id_ruang`  = '$par[idRuang]'") . "  &nbsp;</span>    

          </p>
          ";
  }
  $text .= "
        <p>

          <label class=\"l-input-small\"><b>Lokasi Kerja</b></label>

          <span class=\"field\" style=\"width: 500px;\">" . $row['area'] . " &nbsp;</span>

        </p>
        <p>

          <label class=\"l-input-small\"><b>Tipe</b></label>

          <span class=\"field\" style=\"width: 500px;\">" . getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData`  = '$row[tipe]'") . "  &nbsp;</span>    

        </p>
        

        <p>

          <label class=\"l-input-small\"><b>Wilayah</b></label>

          <span class=\"field\" style=\"width: 500px;\">" . getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData`  = '$row[wilayah]'") . " &nbsp;</span>    

        </p>

        <p>

          <label class=\"l-input-small\"><b>Alamat</b></label>

          <span class=\"field\" style=\"width: 500px;\">" . $row['alamat'] . "<br />" . $arrData[$row['kabupaten']] . ", " . $arrData[$row['propinsi']] . "&nbsp;</span>    

        </p>
      </td>
      <td width=\"20%\" style=\"padding:0px 20px 10px 00px;\">
        <center>
          <a class=\"fancybox-effects\" href=\"$path$row[foto]\" rel=\"gallery\" style=\"cursor:pointer;text-align:center;border:none;\" title=\"\">
            <img src=\"$path$row[foto]\"  height=\"150px\">
          </a>
        </center>
      </td>
    </tr>
  </table>

</fieldset>
";

  return $text;
}

function columnXLS($col)
{

  $arr = array(
    1 => "A",
    2 => "B",
    3 => "C",
    4 => "D",
    5 => "E",
    6 => "F",
    7 => "G",
    8 => "H",
    9 => "I",
    10 => "J",
    11 => "K",
    12 => "L",
    13 => "M",
    14 => "N",
    15 => "O",
    16 => "P",
    17 => "Q",
    18 => "R",
    19 => "S",
    20 => "T",
    21 => "U",
    22 => "V",
    23 => "W",
    24 => "X",
    25 => "Y",
    26 => "Z",
    27 => "AA",
    28 => "AB",
    29 => "AC",
    30 => "AD",
    31 => "AE",
    32 => "AF",
    33 => "AG",
    34 => "AH",
    35 => "AI",
    36 => "AJ",
    37 => "AK",
    38 => "AL",
    39 => "AM",
    40 => "AN",
    41 => "AO",
    42 => "AP",
    43 => "AQ",
    44 => "AR",
    45 => "AS",
    46 => "AT",
    47 => "AU",
    48 => "AV",
    49 => "AW",
    50 => "AX",
    51 => "AY",
    52 => "AZ"
  );

  return $arr[$col];
}

function exportXLS($direktori = "files/export/", $namaFile, $judul, $totalField, $field = array(), $data = array(), $fieldTotal = false, $mergeFieldTotal = 1, $dataTotal = array(), $width = array())
{
  global $db, $s, $inp, $par, $arrTitle, $arrParameter, $cNama, $fFile, $menuAccess, $areaCheck;

  require_once 'plugins/PHPExcel.php';

  $objPHPExcel = new PHPExcel();
  $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

  $objPHPExcel->getActiveSheet()->setCellValue('A1', $judul);
  $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
  $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->mergeCells('A1:' . columnXLS($totalField) . '1');

  $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


  $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:' . columnXLS($totalField) . '4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->getStyle('A3:A4')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


  $col = 0;
  foreach ($field as $f1 => $f2) {
    $col++;

    if (empty($width)) {
      if ($col == 1) $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col))->setWidth(5);
      else $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col))->setWidth(40);
    } else {
      $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col))->setWidth($width[$col - 1]);
    }

    if (!is_array($f2)) {
      $x = explode("+", $f2);

      if (count($x) > 1) {
        $colspan = $x[1] - 1;
        $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($col) . '3', strtoupper($x[0]));
        $objPHPExcel->getActiveSheet()->mergeCells(columnXLS($col) . '3:' . columnXLS($col + $colspan) . '4');

        for ($i = 1; $i <= $x[1]; $i++) {
          $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col + $i))->setWidth(40);
        }

        $col = $col + $colspan;
      } else {
        $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($col) . '3', strtoupper($f2));
        $objPHPExcel->getActiveSheet()->mergeCells(columnXLS($col) . '3:' . columnXLS($col) . '4');
      }
    } else {
      $plus = count($f2) - 1;

      $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($col) . '3', strtoupper($f1));
      $objPHPExcel->getActiveSheet()->mergeCells(columnXLS($col) . '3:' . columnXLS($col + $plus) . '3');
      $objPHPExcel->getActiveSheet()->getStyle(columnXLS($col) . '3:' . columnXLS($col + $plus) . '3')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

      $noCld = $col - 1;
      foreach ($f2 as $f3) {
        $noCld++;
        $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($noCld) . '4', strtoupper($f3));
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($noCld))->setWidth(40);
      }

      $col = $col + $plus;
    }


    $objPHPExcel->getActiveSheet()->getStyle(columnXLS($col) . '3:' . columnXLS($col) . '4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  }

  $rows = 4;

  foreach ($data as $key) {
    $rows++;

    $objPHPExcel->getActiveSheet()->getStyle('A' . $rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':' . columnXLS($totalField) . $rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle(columnXLS($totalField) . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $no = 0;
    foreach ($key as $val) {
      $dt = explode("\t", $val);

      $val = $dt[0];
      $align = str_replace(" ", "", $dt[1]);

      $no++;
      $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($no) . $rows, $val);

      if ($align == "left") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
      if ($align == "center") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      if ($align == "right") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

      $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }
  }

  if ($fieldTotal == true) {
    $rowsTotal = $rows + 1;

    $objPHPExcel->getActiveSheet()->getStyle('A' . $rowsTotal)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $rowsTotal . ':' . columnXLS($totalField) . $rowsTotal)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowsTotal . ':' . columnXLS($mergeFieldTotal) . $rowsTotal);

    $noTotal = 0;
    foreach ($dataTotal as $dtt) {
      $ddd = explode("\t", $dtt);

      $dtt = $ddd[0];
      $alg = str_replace(" ", "", $ddd[1]);

      $noTotal++;

      if ($noTotal == 1) {
        $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue("A" . $rowsTotal, $dtt);
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        if ($alg == "left") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        if ($alg == "center") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($alg == "right") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
      } else {
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($mergeFieldTotal) . $rowsTotal, $dtt);
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        if ($alg == "left") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        if ($alg == "center") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if ($alg == "right") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
      }

      $mergeFieldTotal++;
    }
  }


  $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

  $objPHPExcel->getActiveSheet()->setTitle($judul);
  $objPHPExcel->setActiveSheetIndex(0);

  // Save Excel file
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save($direktori . $namaFile);
}

function generateRandomString($length = 6, $tipe = "char")
{
    if($tipe == "char") $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if($tipe == "num") $characters = "0123456789";
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

include 'global.custome.php';
