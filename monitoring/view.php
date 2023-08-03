<?php
include "global.php";
$url = APP_URL . "/";
//$url = "http://www.xcloud-sales.com";


//print_r($_GET);
//direct file
if ($doc == "file") {
    $fFile = $val;
    $r[fileData] = "View";
    $eFile = getExtension($val);
    $lFile = $val;
}
//end direct

if ($doc == "srmd") {
    $sql = "select * from armada_surat where id='$par[idf]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/armada/surat/" . $r[fileSurat];
    $eFile = getExtension($r[fileSurat]);
    $lFile = "files/armada/surat/" . $r[fileSurat];
}

if ($doc == "manual") {
    $sql = "select * from app_menu where kodeMenu='$par[kodeMenu]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/FileMenu/" . $r[fileMenu];
    if (is_file($fFile)) {
        $r[fileData] = $r[namaMenu];
        $eFile = getExtension($r[fileMenu]);
        $lFile = "files/FileMenu/" . $r[fileMenu];
    } else {
        echo "maaf, manual book belum diupload.";
    }
}
if ($doc == "customer") {
    $sql = "select * from dta_customer where kodeCustomer='$par[kodeCustomer]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "images/customer/" . $r[logoCustomer];
    $r[fileData] = $r[namaCustomer];
    $eFile = getExtension($r[logoCustomer]);
    $lFile = "images/customer/" . $r[logoCustomer];
}

if ($doc == "supplier") {
    $sql = "select * from dta_supplier_item where kodeSupplier='$par[kodeSupplier]' and kodeItem='$par[kodeItem]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "images/supplier/" . $r[fileItem];
    $r[fileData] = $r[namaItem];
    $eFile = getExtension($r[fileItem]);
    $lFile = "images/supplier/" . $r[fileItem];
}

if ($doc == "item") {
    $sql = "select * from dta_item_detail where kodeItem='$par[kodeItem]' and kodeDetail='$par[kodeDetail]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "images/item/" . $r[fileDetail];
    $r[fileData] = $r[judulDetail];
    $eFile = getExtension($r[fileDetail]);
    $lFile = "images/item/" . $r[fileDetail];
}

if ($doc == "usulan") {
    $sql = "select * from mkt_usulan_detail where kodeUsulan='$par[kodeUsulan]' and kodeDetail='$par[kodeDetail]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/usulan/" . $r[fileDetail];
    $r[fileData] = $r[judulDetail];
    $eFile = getExtension($r[fileDetail]);
    $lFile = "files/usulan/" . $r[fileDetail];
}
if ($doc == "fileSorganisasi") {
    $sql = "select * from struktur_organisasi where idSorganisasi='$par[idSorganisasi]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/fileSorganisasi/" . $r[fileSorganisasi];
    $r[fileSorganisasi] = $r[fileSorganisasi];
    $eFile = getExtension($r[fileSorganisasi]);
    $lFile = "files/fileSorganisasi/" . $r[fileSorganisasi];
}

if ($doc == "fileParea") {
    $sql = "select * from pengawas_area where idParea='$par[idParea]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/fileParea/" . $r[fileParea];
    $r[fileParea] = $r[fileParea];
    $eFile = getExtension($r[fileParea]);
    $lFile = "files/fileParea/" . $r[fileParea];
}

if ($doc == "dokumen") {
    $sql = "select * from dta_dokumen where kodeDokumen='$par[kodeDokumen]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumen/" . $r[fileDokumen];
    $r[fileData] = $r[judulDokumen];
    $eFile = getExtension($r[fileDokumen]);
    $lFile = "files/dokumen/" . $r[fileDokumen];
}

if ($doc == "evidence") {

    $count = getField("SELECT countView FROM dokumen_bukti WHERE idBukti='$par[idBukti]' ") + 1;
    db("UPDATE dokumen_bukti SET countView = '$count' WHERE idBukti='$par[idBukti]' ");

    $sql = "select * from dokumen_bukti where idBukti='$par[idBukti]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/evidence/" . $r[fileBukti];
    // $r[fileData]=$r[judulDokumen];
    $eFile = getExtension($r[fileBukti]);
    $lFile = "files/evidence/" . $r[fileBukti];
}

if ($doc == "fileDoc") {

    $count = getField("SELECT countView FROM dokumen_data WHERE idDoc='$par[idDoc]' ") + 1;
    db("UPDATE dokumen_data SET countView = '$count' WHERE idDoc='$par[idDoc]' ");

    $sql = "select * from dokumen_data where idDoc='$par[idDoc]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumen/" . $r[fileDoc];
    // $r[fileData]=$r[judulDokumen];
    $eFile = getExtension($r[fileDoc]);
    $lFile = "files/dokumen/" . $r[fileDoc];
}

if ($doc == "fileDocMar") {
    $sql = "select * from doc_marketing where id='$par[idDoc]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/marketing/" . $r[file];
    // $r[fileData]=$r[judulDokumen];
    $eFile = getExtension($r[file]);
    $lFile = "files/dokumentasi/marketing/" . $r[file];
}

if ($doc == "fileBerita") {


    $sql = "select * from berita_data where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/customer/" . $r[file];
    // $r[fileData]=$r[judulDokumen];
    $eFile = getExtension($r[file]);
    $lFile = "files/customer/" . $r[file];
}


if ($doc == "fileHis") {

    $count = getField("SELECT viewHistory FROM dokumen_history WHERE idDoc='$par[idDoc]' and idHistory='$par[idHistory]' ") + 1;
    db("UPDATE dokumen_history SET viewHistory = '$count' WHERE idDoc='$par[idDoc]' and idHistory='$par[idHistory]'");

    $sql = "select * from dokumen_history where idDoc='$par[idDoc]' and idHistory='$par[idHistory]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumen/" . $r[fileHistory];
    // $r[fileData]=$r[judulDokumen];
    $eFile = getExtension($r[fileHistory]);
    $lFile = "files/dokumen/" . $r[fileHistory];
}

if ($doc == "fileMesin") {
    $sql = "select * from inventory_cekMesin where idMesin='$par[idMesin]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/mesin/" . $r[filePersetujuan];
    $eFile = getExtension($r[filePersetujuan]);
    $lFile = "files/mesin/" . $r[filePersetujuan];
}

if ($doc == "fileOpname") {
    $sql = "select * from inventory_kontrol_opname where idMesin='$par[idMesin]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/opname/" . $r[file];
    $eFile = getExtension($r[file]);
    $lFile = "files/opname/" . $r[file];
}

if ($doc == "fileFsd") {
    $sql = "select * from app_menu where kodeMenu='$par[kodeMenu]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/fsd/" . $r[fileFsd];
    $eFile = getExtension($r[fileFsd]);
    $lFile = "files/dokumentasi/fsd/" . $r[fileFsd];
}

if ($doc == "fileDokumen") {
    $sql = "select * from document_data where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fFile = "files/dokumen/dokumen/" . $r[filename];
    $eFile = getExtension($r[filename]);
    $lFile = "files/dokumen/dokumen/" . $r[filename];

    $kodeLog = getLastId("log_access", "kodeLog");
    $kodeModul = $c;
    $kodeSite = $p;
    $kodeMenu = $s;
    $aktivitasLog = 'view file';

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $getip = 'http://extreme-ip-lookup.com/json/' . $ip_address;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $getip);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);
    $details = json_decode($content);
    $lokasi = $details->city;
    $createBy = $cUsername;
    $createTime = date('Y-m-d H:i:s');
    $kodeTipe = 1;

    $sql = "INSERT INTO log_access SET kodeLog = '$kodeLog', kodeModul = '$kodeModul', kodeSite = '$kodeSite', kodeMenu = '$kodeMenu', aktivitasLog = '$aktivitasLog',lokasi = '$lokasi',
    ip_address = '$ip_address', createBy = '$createBy', createTime = '$createTime',kodeTipe = '$kodeTipe'";
    db($sql);

}

if ($doc == "fileKontrak") {
    $sql = "select * from dta_kontrak where idKontrak='$par[idKontrak]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/kontrak/" . $r[fileKontrak];
    $eFile = getExtension($r[fileKontrak]);
    $lFile = "files/kontrak/" . $r[fileKontrak];
}

if ($doc == "fileManual") {
    //doc == fileManual <-- parameter doc yang kita lempar tadi dari dta_manual.php
    $sql = "select * from dta_manual where idManual='$par[idManual]'";
    // SELECT TABLE NYA, WHERE par[idManual] <-- parameter idManual yang kita lempar tadi dari dta_manual.php
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/manual/" . $r[fileManual];
    //SELECT FOLDER DAN FILE NYA ^ $r[fileManual] adalah field dari table kita
    $eFile = getExtension($r[fileManual]);
    $lFile = "files/manual/" . $r[fileManual];
    //SELECT FOLDER DAN FILE NYA ^ $r[fileManual] adalah field dari table kita
}

if ($doc == "filePengumuman") {
    $sql = "select * from dta_pengumuman where idPengumuman='$par[idPengumuman]'";
    // SELECT TABLE NYA, WHERE par[idManual] <-- parameter idManual yang kita lempar tadi dari dta_manual.php
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/pengumuman/" . $r[filePengumuman];
    //SELECT FOLDER DAN FILE NYA ^ $r[fileManual] adalah field dari table kita
    $eFile = getExtension($r[filePengumuman]);
    $lFile = "files/pengumuman/" . $r[filePengumuman];
    //SELECT FOLDER DAN FILE NYA ^ $r[fileManual] adalah field dari table kita
}

if ($doc == "fileMenu") {
    $sql = "select * from app_menu where kodeMenu='$par[kodeMenu]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/FileMenu/" . $r[fileMenu];
    $eFile = getExtension($r[fileMenu]);
    $lFile = "files/FileMenu/" . $r[fileMenu];
}

if ($doc == "fileChecklist") {
    $sql = "select * from doc_menuchecklist where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/menu_checklist/" . $r[file];
    $eFile = getExtension($r[file]);
    $lFile = "files/dokumentasi/menu_checklist/" . $r[file];
}

if ($doc == "fileUat") {
    $sql = "select * from doc_uat where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[fileUat];
    $eFile = getExtension($r[fileUat]);
    $lFile = "files/dokumentasi/uat/" . $r[fileUat];
}

if ($doc == "fileBa") {
    $sql = "select * from doc_uat where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[fileBa];
    $eFile = getExtension($r[fileBa]);
    $lFile = "files/dokumentasi/uat/" . $r[fileBa];
}

if ($doc == "filePelatiB") {
    $sql = "select * from doc_pelatihan where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[fileBa];
    $eFile = getExtension($r[fileBa]);
    $lFile = "files/dokumentasi/uat/" . $r[fileBa];
}

if ($doc == "filePlk") {
    $sql = "select * from doc_uat where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[filePlk];
    $eFile = getExtension($r[filePlk]);
    $lFile = "files/dokumentasi/uat/" . $r[filePlk];
}

if ($doc == "filePelati") {
    $sql = "select * from doc_pelatihan where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[filePlk];
    $eFile = getExtension($r[filePlk]);
    $lFile = "files/dokumentasi/uat/" . $r[filePlk];
}

if ($doc == "fileDoc") {
    $sql = "select * from doc_file where id='$par[idDoc]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/rencana/" . $r[file];
    $eFile = getExtension($r[file]);
    $lFile = "files/dokumentasi/rencana/" . $r[file];
}
if ($doc == "fileRencana") {
    $sql = "select * from doc_rencana where id='$par[idRencana]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/rencana/" . $r[file];
    $eFile = getExtension($r[file]);
    $lFile = "files/dokumentasi/rencana/" . $r[file];
}
if ($doc == "fotoTeam") {
    $sql = "select * from doc_team where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/team/" . $r[file];
    $eFile = getExtension($r[file]);
    $lFile = "files/dokumentasi/team/" . $r[file];
}
if ($doc == "fileUatMenu") {
    $sql = "select * from doc_uat_menu where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/dokumentasi/uat/" . $r[file_uat];
    $eFile = getExtension($r[file_uat]);
    $lFile = "files/dokumentasi/uat/" . $r[file_uat];
}

if ($doc == "uploadPreview") {
    $fFile = "files/preview/" . $fileName;
    $eFile = getExtension($fileName);
    $lFile = "files/preview/" . $fileName;
}

if ($doc == "fileSupplier") {
    $sql = "select * from prc_rencana_supplier where kodeRencana='$par[kodeRencana]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/purchasing/pemilihan_supplier/" . $r['fileSupplier'];
    $eFile = getExtension($r['fileSupplier']);
    $lFile = "files/purchasing/pemilihan_supplier/" . $r['fileSupplier'];
}

if ($doc == "fileTagihanSpk") {
    $sql = "select * from tagihan_spk where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/tagihan_spk/" . $r['file_spk'];
    $eFile = getExtension($r['file_spk']);
    $lFile = "files/tagihan_spk/" . $r['file_spk'];
}

if ($doc == "fileTagihanBa") {
    $sql = "select * from tagihan_syarat where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/tagihan_ba/" . $r['ba_file'];
    $eFile = getExtension($r['ba_file']);
    $lFile = "files/tagihan_ba/" . $r['ba_file'];
}

if ($doc == "fileTagihanData") {
    $sql = "select * from tagihan_data where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/tagihan_data/" . $r['file_tagihan'];
    $eFile = getExtension($r['file_tagihan']);
    $lFile = "files/tagihan_data/" . $r['file_tagihan'];
}

if ($doc == "fileTagihanBayar") {
    $sql = "select * from tagihan_bayar where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $fFile = "files/tagihan_bayar/" . $r['bukti_bayar'];
    $eFile = getExtension($r['bukti_bayar']);
    $lFile = "files/tagihan_bayar/" . $r['bukti_bayar'];
}

if (in_array($eFile, ["jpg", "png", "PNG", "gif", "jpeg"])) {
    echo "<img src=\"{$lFile}\"/>";
    exit;
}

if (in_array($eFile, ["mp4", "MP4", "mpeg", "mp3", "mov", "wmv", "3gp", "mpg", "wav"])) {
    echo "<video controls autoplay width=\"887\"  height=\"466\" src=\"{$lFile}\" />";
    exit;
}

if ($eFile == "pdf") {
    echo "<embed src=\"{$lFile}\" width=\"100%\" height=\"475\"/>";
    exit;
}

if (in_array($eFile, ["csv", "xls", "xlsx", "doc", "docx"])) {
    header("location: https://view.officeapps.live.com/op/embed.aspx?src={$url}{$fFile}");
    exit;
}

echo "Format dokumen tidak didukung";