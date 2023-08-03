<?php

include "global.php";
###############################################################
# File Download 1.31
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
###############################################################
# Sample call:
#    download.php?f=phptutorial.zip
#
# Sample call (browser will try to save with new file name):
#    download.php?f=phptutorial.zip&fc=php123tutorial.zip
###############################################################
// Allow direct file download (hotlinking)?
// Empty - allow hotlinking
// If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
define('ALLOWED_REFERRER', '');

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_DIR', 'files');

// log downloads?  true/false
define('LOG_DOWNLOADS', true);

// log file name
define('LOG_FILE', 'downloads.log');

// Allowed extensions list in format 'extension' => 'mime type'
// If myme type is set to empty string then script will try to detect mime type 
// itself, which would only work if you have Mimetype or Fileinfo extensions
// installed on server.
$allowed_ext = array(
    // archives
  'zip' => 'application/zip',
    // documents
  'pdf' => 'application/pdf',
  'doc' => 'application/msword',
  'xls' => 'application/vnd.ms-excel',
  'ppt' => 'application/vnd.ms-powerpoint',
  'csv' => 'application/vnd.ms-excel',
  'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    // text
  'txt' => 'text/plain',
  'log' => 'text/x-log',
    // executables
  'exe' => 'application/octet-stream',
    // images
  'gif' => 'image/gif',
  'png' => 'image/png',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg',
  'ico' => 'image/x-icon',
    // audio
  'mp3' => 'audio/mpeg',
  'wav' => 'audio/x-wav',
    // video
  'mpeg' => 'video/mpeg',
  'mpg' => 'video/mpeg',
  'mp4' => 'video/mpeg',
  'mpe' => 'video/mpeg',
  'mov' => 'video/quicktime',
  'avi' => 'video/x-msvideo'
  );


####################################################################
###  DO NOT CHANGE BELOW
####################################################################
// If hotlinking not allowed then make hackers think there are some server problems
if (ALLOWED_REFERRER !== '' && (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper(ALLOWED_REFERRER)) === false)
  ) {
  die("Internal server error. Please contact system administrator.");
}

// Make sure program execution doesn't time out
// Set maximum script execution time in seconds (0 means no limit)
set_time_limit(0);

if (!isset($_GET['d']) || empty($_GET['d'])) {
  die("Please specify file name for download.");
}


// Nullbyte hack fix
if (strpos($_GET['d'], "\0") !== FALSE)
  die('');
if (strpos($_GET['f'], "\0") !== FALSE)
  die('');

// Get real file name.
// Remove any path info to avoid hacking by adding relative path, etc.

if ($_GET['d'] == "exp") {
  $fname = basename($_GET['f']);
  $dfile = $_GET['f'];
} else if ($_GET['d'] == "jwlPegawai") {
  $fname = basename("Jadwal Pegawai.xlsx");
  $dfile = "template.xls";
} else if ($_GET['d'] == "logJadwal") {
  $fname = basename("Jadwal Pegawai.log");
  $dfile = date('Y-m-d H:i:s').".log"; 
} else if ($_GET['d'] == "rncBulanan") {
  $fname = basename("Rencana Kerja Bulanan.xlsx");
  $dfile = "template.xls";
} else if ($_GET['d'] == "logBulanan") {
  $fname = basename("Rencana Bulanan.log");
  $dfile = date('Y-m-d H:i:s').".log"; 
} else if ($_GET['d'] == "rncTahunan") {
  $fname = basename("Rencana Kerja Tahunan.xlsx");
  $dfile = "template.xls";
} else if ($_GET['d'] == "logTahunan") {
  $fname = basename("Rencana Tahunan.log");
  $dfile = date('Y-m-d H:i:s').".log";    
} else if ($_GET['d'] == "logAbsensi") {
  $fname = basename("Absensi Harian.log");
  $dfile = date('Y-m-d H:i:s').".log";  
} else if ($_GET['d'] == "fmtAbsen") {
  $fname = basename("Absensi Harian.xlsx");
  $dfile = "template.xls";
} else if ($_GET['d'] == "fmtJadwal") {
  $fname = basename("Format Jadwal.xlsx");
  $dfile = "template.xlsx";
} else if ($_GET['d'] == "komponen") {
  $sql = "select * from dta_komponen where idKomponen='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileKomponen']);
  $dfile = $r['fileKomponen'];
} else if ($_GET['d'] == "pokok") {
  $sql = "select * from pay_pokok where idPokok='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filePokok']);
  $dfile = $r['filePokok'];
} else if ($_GET['d'] == "fileFsd") {
  $sql = "select * from app_menu where kodeMenu='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileFsd']);
  $dfile = $r['fileFsd'];
}else if ($_GET['d'] == "fileMenu") {
  $sql = "select * from app_menu where kodeMenu='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileMenu']);
  $dfile = $r['fileMenu'];
} else if ($_GET['d'] == "fileChecklist") {
  $sql = "select * from doc_menuchecklist where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
} else if ($_GET['d'] == "melekat") {
  $sql = "select * from pay_melekat where idMelekat='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileMelekat']);
  $dfile = $r['fileMelekat'];
}else if ($_GET['d'] == "helpMenu") {
  $sql = "select * from app_menu where kodeMenu='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileMenu']);
  $dfile = $r['fileMenu'];
} else if ($_GET['d'] == "koreksi") {
  $sql = "select * from pay_koreksi where idKoreksi='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileKoreksi']);
  $dfile = $r['fileKoreksi'];
} else if ($_GET['d'] == "sakit") {
  $sql = "select * from att_sakit where idSakit='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileSakit']);
  $dfile = $r['fileSakit'];
} else if ($_GET['d'] == "hadir") {
  $sql = "select * from att_hadir where idHadir='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileHadir']);
  $dfile = $r['fileHadir'];
} else if ($_GET['d'] == "kas") {
  $sql = "select * from ess_kas where idKas='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileKas']);
  $dfile = $r['fileKas'];
} else if ($_GET['d'] == "tiket") {
  $sql = "select * from ess_tiket where idTiket='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileTiket']);
  $dfile = $r['fileTiket'];
} else if ($_GET['d'] == "dinas") {
  $sql = "select * from ess_dinas where idDinas='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileDinas']);
  $dfile = $r['fileDinas'];
} else if ($_GET['d'] == "kacamata") {
  $sql = "select * from emp_rmb_files where parent_id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "kesehatan") {
  $sql = "select * from emp_rmb_files where parent_id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "pinjaman") {
  $sql = "select * from ess_pinjaman where idPinjaman='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filePinjaman']);
  $dfile = $r['filePinjaman'];
} else if ($_GET['d'] == "empktp") {
  $sql = "select ktp_filename from emp where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['ktp_filename']);
  $dfile = $r['ktp_filename'];
} else if ($_GET['d'] == "emprel") {
  $sql = "select rel_filename from emp_family where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['rel_filename']);
  $dfile = $r['rel_filename'];
} else if ($_GET['d'] == "empcareer") {
  $sql = "select sk_filename from emp_career where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['sk_filename']);
  $dfile = $r['sk_filename'];
} else if ($_GET['d'] == "emptrn") {
  $sql = "select trn_filename filename from emp_training where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "emprwd") {
  $sql = "select rwd_filename filename from emp_reward where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "emppnh") {
  $sql = "select pnh_filename filename from emp_punish where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "emphlt") {
  $sql = "select hlt_filename filename from emp_health where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "empedu") {
  $sql = "select edu_filename filename from emp_edu where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "empast") {
  $sql = "select ast_filename filename from emp_asset where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "emprmb") {
  $sql = "select filename filename from emp_rmb_files where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "emppw") {
  $sql = "select filename filename from emp_pwork where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "empdoc") {
  $sql = "select file_document filename from emp_info_doc where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "empnews") {
  $sql = "select file_news filename from emp_info_news where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "vacancy") {
  $sql = "select file_lowongan filename from rec_vacancy where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "dokumen_penilaian") {
  $sql = "select fileDokumen filename from pen_dokumen where kodeDokumen='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "pen_setting_kode") {
  $sql = "select skKode filename from pen_setting_kode where idKode='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "pen_setting_penilaian") {
  $sql = "select skSetting filename from pen_setting_penilaian where idSetting='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} else if ($_GET['d'] == "tenant") {
  $sql = "select logoTenant filename from ten_data where idTenant='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
}  else if ($_GET['d'] == "pengumuman") {
  $sql = "select filePengumuman filename from inf_pengumuman where idPengumuman='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filename']);
  $dfile = $r['filename'];
} 
else if ($_GET['d'] == "catatanFile") {
  $sql = "select * from catatan_sistem where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
} else if ($_GET['d'] == "catatanDokumen") {
  $sql = "select * from catatan_dokumen where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
} else if ($_GET['d'] == "catatanDiskusi") {
  $sql = "select * from catatan_diskusi where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
}  else if ($_GET['d'] == "fileEvidence") {

  $count = getField("SELECT countDownload FROM dokumen_bukti WHERE idBukti = '".$_GET['f']."' ")+1;
  db("UPDATE dokumen_bukti SET countDownload = '$count' WHERE idBukti = '".$_GET['f']."' ");
  
  $sql = "select * from dokumen_bukti where idBukti='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileBukti']);
  $dfile = $r['fileBukti'];
}  else if ($_GET['d'] == "fileHis") {
  list($idDoc, $idHistory) = explode("-", $_GET["f"]);
  $count = getField("select downloadHistory from dokumen_history WHERE idDoc = '".$idDoc."' and idHistory='".$idHistory."' ")+1;
  db("update dokumen_history set downloadHistory = '$count' where idDoc = '".$idDoc."' and idHistory='".$idHistory."' ");

  $sql = "select * from dokumen_history where idDoc = '".$idDoc."' and idHistory='".$idHistory."'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileHistory']);
  $dfile = $r['fileHistory'];
}  else if ($_GET['d'] == "fileDoc") {

  $count = getField("SELECT countDownload FROM dokumen_data WHERE idDoc = '".$_GET['f']."' ")+1;
  db("UPDATE dokumen_data SET countDownload = '$count' WHERE idDoc = '".$_GET['f']."' ");

  $sql = "select * from dokumen_data where idDoc='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileDoc']);
  $dfile = $r['judulDoc'].".".getExtension($r['fileDoc']);
}  else if ($_GET['d'] == "fileUat") {
  $sql = "select * from doc_uat where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileUat']);
  $dfile = $r['fileUat'];
}  else if ($_GET['d'] == "fileBa") {
  $sql = "select * from doc_uat where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['fileBa']);
  $dfile = $r['fileBa'];
}  else if ($_GET['d'] == "filePlk") {
  $sql = "select * from doc_uat where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['filePlk']);
  $dfile = $r['filePlk'];
} 

else if ($_GET['d'] == "fileDocRencana") {
  $sql = "select * from doc_file where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
} 
else if ($_GET['d'] == "fileRencana") {
  $sql = "select * from doc_rencana where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
} 
else if ($_GET['d'] == "file_uat_menu") {
  $sql = "select * from doc_uat_menu where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file_uat']);
  $dfile = $r['file_uat'];
} 
else if ($_GET['d'] == "fileberita") {
  $sql = "select * from berita_data where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file']);
  $dfile = $r['file'];
}
else if ($_GET['d'] == "fileTagihanSpk") {
  $sql = "select * from tagihan_spk where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file_spk']);
  $dfile = $r['file_spk'];
}
else if ($_GET['d'] == "fileTagihanBa") {
  $sql = "select * from tagihan_syarat where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['ba_file']);
  $dfile = $r['ba_file'];
}
else if ($_GET['d'] == "fileTagihanData") {
  $sql = "select * from tagihan_data where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['file_tagihan']);
  $dfile = $r['file_tagihan'];
}
else if ($_GET['d'] == "fileTagihanBayar") {
  $sql = "select * from tagihan_bayar where id='" . $_GET['f'] . "'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  $fname = basename($r['bukti_bayar']);
  $dfile = $r['bukti_bayar'];
}
else {
  die('');
}

// Check if the file exists
// Check in subfolders too
function find_file($dirname, $fname, &$file_path) {
  $dir = opendir($dirname);

  while ($file = readdir($dir)) {
    if (empty($file_path) && $file != '.' && $file != '..') {
      if (is_dir($dirname . '/' . $file)) {
        find_file($dirname . '/' . $file, $fname, $file_path);
      } else {
        if (file_exists($dirname . '/' . $fname)) {
          $file_path = $dirname . '/' . $fname;
          return;
        }
      }
    }
  }
}

// find_file
// get full file path (including subfolders)
$file_path = '';
find_file(BASE_DIR, $fname, $file_path);

if (!is_file($file_path)) {
  die("File does not exist. Make sure you specified correct file name.");
}

// file size in bytes
$fsize = filesize($file_path);

// file extension
$fext = strtolower(substr(strrchr($fname, "."), 1));

// check if allowed extension
if (!array_key_exists($fext, $allowed_ext)) {
  die("Not allowed file type.");
}

// get mime type
if ($allowed_ext[$fext] == '') {
  $mtype = '';
  // mime type is not set, get from server settings
  if (function_exists('mime_content_type')) {
    $mtype = mime_content_type($file_path);
  } else if (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME); // return mime type
    $mtype = finfo_file($finfo, $file_path);
    finfo_close($finfo);
  }
  if ($mtype == '') {
    $mtype = "application/force-download";
  }
} else {
  // get mime type defined by admin
  $mtype = $allowed_ext[$fext];
}

// Browser will try to save file with this filename, regardless original filename.
// You can override it if needed.

if (!isset($_GET['fc']) || empty($_GET['fc'])) {
  $asfname = $fname;
} else {
  // remove some bad chars
  $asfname = str_replace(array('"', "'", '\\', '/'), '', $_GET['fc']);
  if ($asfname === '')
    $asfname = 'NoName';
}

$downloadFile = empty($dfile) ? "unknown" : $dfile;

// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: $mtype");
header("Content-Disposition: attachment; filename=\"$downloadFile\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);

// download
// @readfile($file_path);
$file = @fopen($file_path, "rb");
if ($file) {
  while (!feof($file)) {
    print(fread($file, 1024 * 8));
    flush();
    if (connection_status() != 0) {
      @fclose($file);
      die();
    }
  }
  @fclose($file);
}

// log downloads
if (!LOG_DOWNLOADS)
  die();
