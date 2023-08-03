<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/purchasing/";
$dFile = "files/purchasing/";
$fExport = "files/export/";

$maps = "<script defer src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBio4DnANXPdAt6inLdithPKwX64Doj7uc\"></script>";

function xls()
{
    global $s, $arrTitle, $fExport, $par, $arrParam;
    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";
    $field = array("no", "No Akun", "Nama supplier", "Tipe", "Kategori");
    $filters = " where t1.tipe = '$arrParam[$s]'";
    if (!empty($_GET['fSearch'])) {
        $filters .= " AND t1.namaSupplier LIKE '%" . $_GET['fSearch'] . "%'";
    }
    $sql = "SELECT t1.*,
	t2.kodeData,
	t2.namaData AS namaKota,
	t3.kodeProduk,
	t3.namaProduk AS namaKategori 
	FROM 
	dta_supplier t1 
	LEFT JOIN mst_data t2 ON (t1.kodeKota = t2.kodeData) 
	LEFT JOIN dta_produk t3 ON (t1.kodeKategori=t3.kodeProduk)
	$filters 
	";
    $res = db($sql);
    $no = 0;
    $data = [];
    while ($r = mysql_fetch_assoc($res)) {
        $arrTipe = array("j" => "Jasa", "b" => "Barang");
        $no++;
        $tmp = array(
            $no . "\t center",
            $r[nomorSupplier] . "\t center",
            $r[namaSupplier] . "\t left",
            $arrTipe[$r[tipeSupplier]] . "\t center",
            $r[namaKategori] . "\t left",
        );
        array_push($data, $tmp);
    }
    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
}

function cek()
{
    global $db, $inp, $par;
    if (getField("select nomorSupplier from dta_supplier where nomorSupplier='$inp[nomorSupplier]' and kodeSupplier!='$par[kodeSupplier]'"))
        return "sorry, account no. \" $inp[nomorSupplier] \" already exist";
}

/*function kota(){
	global $db,$s,$id,$inp,$par,$arrParameter;
	$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodePropinsi]' and kodeCategory='S03' order by namaData");
	return implode("\n", $data);
}*/

function kota()
{
    global $par;

    $getData = getRows("SELECT * from mst_data where statusData='t' and kodeInduk='$par[kodePropinsi]' and kodeCategory='S03' order by namaData asc");
    echo json_encode($getData);
}

function subkat()
{
    global $db, $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeKategori,'\t', namaKategori, ' - ' ,tipeKategori) from dta_produk_kategori where kodeProduk='$par[kodeProduk]'  order by namaKategori ,tipeKategori");
    return implode("\n", $data);
}

function hapusNote()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_note where kodeSupplier='$par[kodeSupplier]' and kodeNote='$par[kodeNote]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=7" . getPar($par, "mode,kodeNote") . "';</script>";
}

function ubahNote()
{
    global $db, $s, $inp, $par, $cID;
    repField();
    $sql = "update dta_supplier_note set namaNote='$inp[namaNote]', keteranganNote='$inp[keteranganNote]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeNote='$par[kodeNote]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=7" . getPar($par, "mode,kodeNote") . "';</script>";
}

function tambahNote()
{
    global $db, $s, $inp, $par, $cID;
    $kodeNote = getField("select kodeNote from dta_supplier_note where kodeSupplier='$par[kodeSupplier]' order by kodeNote desc limit 1") + 1;
    repField();
    $sql = "insert into dta_supplier_note (kodeSupplier, kodeNote, namaNote, keteranganNote, createBy, createTime) values ('$par[kodeSupplier]', '$kodeNote', '$inp[namaNote]', '$inp[keteranganNote]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=7" . getPar($par, "mode,kodeNote") . "';</script>";
}

function hapusBank()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_bank where kodeSupplier='$par[kodeSupplier]' and kodeBank='$par[kodeBank]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=6" . getPar($par, "mode,kodeBank") . "';</script>";
}

function ubahBank()
{
    global $db, $s, $inp, $par, $cID;
    repField();
    $sql = "update dta_supplier_bank set namaBank='$inp[namaBank]', rekeningBank='$inp[rekeningBank]', pemilikBank='$inp[pemilikBank]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeBank='$par[kodeBank]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=6" . getPar($par, "mode,kodeBank") . "';</script>";
}

function tambahBank()
{
    global $db, $s, $inp, $par, $cID;
    $kodeBank = getField("select kodeBank from dta_supplier_bank where kodeSupplier='$par[kodeSupplier]' order by kodeBank desc limit 1") + 1;
    repField();
    $sql = "insert into dta_supplier_bank (kodeSupplier, kodeBank, namaBank, rekeningBank, pemilikBank, createBy, createTime) values ('$par[kodeSupplier]', '$kodeBank', '$inp[namaBank]', '$inp[rekeningBank]', '$inp[pemilikBank]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=6" . getPar($par, "mode,kodeBank") . "';</script>";
}

function hapusContact()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]' and kodeContact='$par[kodeContact]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=5" . getPar($par, "mode,kodeContact") . "';</script>";
}

function ubahContact()
{
    global $db, $s, $inp, $par, $cID;
    repField();
    $sql = "update dta_supplier_contact set namaContact='$inp[namaContact]', jabatanContact='$inp[jabatanContact]', emailContact='$inp[emailContact]', teleponContact='$inp[teleponContact]', kantorContact='$inp[kantorContact]', faxContact='$inp[faxContact]', keteranganContact='$inp[keteranganContact]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeContact='$par[kodeContact]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=5" . getPar($par, "mode,kodeContact") . "';</script>";
}

function tambahContact()
{
    global $db, $s, $inp, $par, $cID;
    $kodeContact = getField("select kodeContact from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]' order by kodeContact desc limit 1") + 1;
    repField();
    $sql = "insert into dta_supplier_contact (kodeSupplier, kodeContact, namaContact, jabatanContact, emailContact, teleponContact, kantorContact, faxContact, keteranganContact, createBy, createTime) values ('$par[kodeSupplier]', '$kodeContact', '$inp[namaContact]', '$inp[jabatanContact]', '$inp[emailContact]', '$inp[teleponContact]', '$inp[kantorContact]', '$inp[faxContact]', '$inp[keteranganContact]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=5" . getPar($par, "mode,kodeContact") . "';</script>";
}

function setProduk()
{
    global $db, $s, $id, $inp, $par, $arrParameter;
    return getField("select concat(tipeKategori,' -- ',namaKategori) from dta_produk_kategori where kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'");
}

function setJasa()
{
    global $db, $s, $id, $inp, $par, $arrParameter;
    return getField("select concat(tipeKategori,' -- ',namaKategori) from dta_produk_kategori where kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'");
}

function hapusFProduct()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $fileProduk = getField("select fileProduk from dta_supplier_produk where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'");
    if (file_exists($dFile . $fileProduk) and $fileProduk != "") unlink($dFile . $fileProduk);
    $sql = "update dta_supplier_produk set fileProduk='' where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.location='?par[mode]=editProduct" . getPar($par, "mode") . "';</script>";
}

function hapusFJasa()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $fileProduk = getField("select fileProduk from dta_supplier_jasa where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'");
    if (file_exists($dFile . $fileProduk) and $fileProduk != "") unlink($dFile . $fileProduk);
    $sql = "update dta_supplier_jasa set fileProduk='' where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.location='?par[mode]=editProduct" . getPar($par, "mode") . "';</script>";
}

function hapusProduct()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_produk where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function hapusJasa()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_jasa where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function ubahProduct()
{
    global $db, $s, $inp, $par, $cID;
    $fileProduk = uploadProduct($par[kodeSupplier], $inp[kodeProduk], $inp[kodeKategori]);
    repField();
    $sql = "update dta_supplier_produk set kodeProduk='$inp[kodeProduk]', kodeKategori='$inp[kodeKategori]',namaProduk='$inp[namaProduk]',subkodeProduk='$inp[subkodeProduk]', keteranganProduk='$inp[keteranganProduk]', hargaProduk='" . setAngka($inp[hargaProduk]) . "',LastUpdate='" . setTanggal($inp[LastUpdate]) . "', fileProduk='$fileProduk', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function ubahJasa()
{
    global $db, $s, $inp, $par, $cID;
    $fileProduk = uploadProduct($par[kodeSupplier], $inp[kodeProduk], $inp[kodeKategori]);
    repField();
    $sql = "update dta_supplier_jasa set kodeProduk='$inp[kodeProduk]', kodeKategori='$inp[kodeKategori]',namaProduk='$inp[namaProduk]',subkodeProduk='$inp[subkodeProduk]', keteranganProduk='$inp[keteranganProduk]', hargaProduk='" . setAngka($inp[hargaProduk]) . "',LastUpdate='" . setTanggal($inp[LastUpdate]) . "', fileProduk='$fileProduk', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function tambahProduct()
{
    global $db, $s, $inp, $par, $cID;
    $fileProduk = uploadProduct($par[kodeSupplier], $inp[kodeProduk], $inp[kodeKategori]);
    repField();
    $sql = "insert into dta_supplier_produk (kodeSupplier, kodeProduk, namaProduk,kodeKategori,subkodeProduk, keteranganProduk, hargaProduk,LastUpdate ,fileProduk, createBy, createTime) values ('$par[kodeSupplier]', '$inp[kodeProduk]',  '$inp[namaProduk]', '$inp[kodeKategori]', '$inp[subkodeProduk]','$inp[keteranganProduk]', '" . setAngka($inp[hargaProduk]) . "','" . setTanggal($inp[LastUpdate]) . "','$fileProduk', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    // var_dump($sql);
    // die();
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function tambahJasa()
{
    global $db, $s, $inp, $par, $cID;
    $fileProduk = uploadProduct($par[kodeSupplier], $inp[kodeProduk], $inp[kodeKategori]);
    repField();
    $sql = "insert into dta_supplier_jasa (kodeSupplier, kodeProduk, namaProduk,kodeKategori,subkodeProduk, keteranganProduk, hargaProduk,LastUpdate ,fileProduk, createBy, createTime) values ('$par[kodeSupplier]', '$inp[kodeProduk]',  '$inp[namaProduk]', '$inp[kodeKategori]', '$inp[subkodeProduk]','$inp[keteranganProduk]', '" . setAngka($inp[hargaProduk]) . "','" . setTanggal($inp[LastUpdate]) . "','$fileProduk', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    // var_dump($sql);
    // die();
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=2" . getPar($par, "mode,kodeProduk,kodeKategori") . "';</script>";
}

function hapusAddress()
{
    global $db, $s, $inp, $par, $cID;
    $sql = "delete from dta_supplier_address where kodeSupplier='$par[kodeSupplier]' and kodeAddress='$par[kodeAddress]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function ubahAddress()
{
    global $db, $s, $inp, $par, $cID;
    repField();
    $sql = "update dta_supplier_address set kodePropinsi='$inp[kodePropinsi]', kodeKota='$inp[kodeKota]', kategoriAddress='$inp[kategoriAddress]', alamatAddress='$inp[alamatAddress]', teleponAddress='$inp[teleponAddress]', faxAddress='$inp[faxAddress]', latitudeAddress='$inp[latitudeAddress]', longitudeAddress='$inp[longitudeAddress]', keteranganAddress='$inp[keteranganAddress]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]' and kodeAddress='$par[kodeAddress]'";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function tambahAddress()
{
    global $db, $s, $inp, $par, $cID;
    $kodeAddress = getField("select kodeAddress from dta_supplier_address where kodeSupplier='$par[kodeSupplier]' order by kodeAddress desc limit 1") + 1;
    repField();
    $sql = "insert into dta_supplier_address (kodeSupplier, kodeAddress, kodePropinsi, kodeKota, kategoriAddress, alamatAddress, teleponAddress, faxAddress, latitudeAddress, longitudeAddress, keteranganAddress, createBy, createTime) values ('$par[kodeSupplier]', '$kodeAddress', '$inp[kodePropinsi]', '$inp[kodeKota]', '$inp[kategoriAddress]', '$inp[alamatAddress]', '$inp[teleponAddress]', '$inp[faxAddress]', '$inp[latitudeAddress]', '$inp[longitudeAddress]', '$inp[keteranganAddress]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.parent.location='index.php?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function uploadProduct()
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["fileProduk"]["tmp_name"];
    $fileUpload_name = $_FILES["fileProduk"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $fileProduk = "produk-" . $par[kodeSupplier] . "." . $inp[kodeProduk] . "." . $inp[kodeKategori] . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $fileProduk);
    }
    if (empty($fileProduk)) $fileProduk = getField("select fileProduk from dta_supplier_produk where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'");
    return $fileProduk;
}

function uploadNpwp($kodeSupplier)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["npwpIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["npwpIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $npwpIdentity_file = "npwp-" . $kodeSupplier . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $npwpIdentity_file);
    }
    if (empty($npwpIdentity_file)) $npwpIdentity_file = getField("select npwpIdentity_file from dta_supplier_identity where kodeSupplier='$kodeSupplier'");
    return $npwpIdentity_file;
}

function uploadId($kodeSupplier)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["idIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["idIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $idIdentity_file = "id-" . $kodeSupplier . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $idIdentity_file);
    }
    if (empty($idIdentity_file)) $idIdentity_file = getField("select idIdentity_file from dta_supplier_identity where kodeSupplier='$kodeSupplier'");
    return $idIdentity_file;
}

function uploadTdp($kodeSupplier)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["tdpIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["tdpIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $tdpIdentity_file = "tdp-" . $kodeSupplier . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $tdpIdentity_file);
    }
    if (empty($tdpIdentity_file)) $tdpIdentity_file = getField("select tdpIdentity_file from dta_supplier_identity where kodeSupplier='$kodeSupplier'");
    return $tdpIdentity_file;
}

function uploadSiup($kodeSupplier)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["siupIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["siupIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $siupIdentity_file = "siup-" . $kodeSupplier . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $siupIdentity_file);
    }
    if (empty($siupIdentity_file)) $siupIdentity_file = getField("select siupIdentity_file from dta_supplier_identity where kodeSupplier='$kodeSupplier'");
    return $siupIdentity_file;
}

function uploadLogo($kodeSupplier)
{
    global $db, $s, $inp, $par, $fFile;
    $fileUpload = $_FILES["logoSupplier"]["tmp_name"];
    $fileUpload_name = $_FILES["logoSupplier"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $logoSupplier = "logo-" . $kodeSupplier . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $logoSupplier);
    }
    if (empty($logoSupplier)) $logoSupplier = getField("select logoSupplier from dta_supplier where kodeSupplier='$kodeSupplier'");
    return $logoSupplier;
}

function hapusNpwp()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $npwpIdentity_file = getField("select npwpIdentity_file from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($dFile . $npwpIdentity_file) and $npwpIdentity_file != "") unlink($dFile . $npwpIdentity_file);
    $sql = "update dta_supplier_identity set npwpIdentity_file='' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusId()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $idIdentity_file = getField("select idIdentity_file from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($dFile . $idIdentity_file) and $idIdentity_file != "") unlink($dFile . $idIdentity_file);
    $sql = "update dta_supplier_identity set idIdentity_file='' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusTdp()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $tdpIdentity_file = getField("select tdpIdentity_file from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($dFile . $tdpIdentity_file) and $tdpIdentity_file != "") unlink($dFile . $tdpIdentity_file);
    $sql = "update dta_supplier_identity set tdpIdentity_file='' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusSiup()
{
    global $db, $s, $inp, $par, $dFile, $cID;
    $siupIdentity_file = getField("select siupIdentity_file from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($dFile . $siupIdentity_file) and $siupIdentity_file != "") unlink($dFile . $siupIdentity_file);
    $sql = "update dta_supplier_identity set siupIdentity_file='' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusLogo()
{
    global $db, $s, $inp, $par, $fFile, $cID;
    $logoSupplier = getField("select logoSupplier from dta_supplier where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($fFile . $logoSupplier) and $logoSupplier != "") unlink($fFile . $logoSupplier);
    $sql = "update dta_supplier set logoSupplier='' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $db, $s, $inp, $par, $fFile, $dFile, $cID;
    $logoSupplier = getField("select logoSupplier from dta_supplier where kodeSupplier='$par[kodeSupplier]'");
    if (file_exists($fFile . $logoSupplier) and $logoSupplier != "") unlink($fFile . $logoSupplier);
    $sql = "select * from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (file_exists($dFile . $r[siupIdentity_file]) and $r[siupIdentity_file] != "") unlink($dFile . $r[siupIdentity_file]);
    if (file_exists($dFile . $r[tdpIdentity_file]) and $r[tdpIdentity_file] != "") unlink($dFile . $r[tdpIdentity_file]);
    if (file_exists($dFile . $r[idIdentity_file]) and $r[idIdentity_file] != "") unlink($dFile . $r[idIdentity_file]);
    if (file_exists($dFile . $r[npwpIdentity_file]) and $r[npwpIdentity_file] != "") unlink($dFile . $r[npwpIdentity_file]);
    $sql = "delete from dta_supplier where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = "delete from dta_supplier_address where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = "delete from dta_supplier_produk where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = "delete from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = "delete from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = "delete from dta_supplier_bank where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,kodeSupplier") . "';</script>";
}

function ubah($update = "")
{
    global $db, $s, $inp, $par, $cID;
    $logoSupplier = uploadLogo($par[kodeSupplier]);
    $siupIdentity_file = uploadSiup($par[kodeSupplier]);
    $tdpIdentity_file = uploadTdp($par[kodeSupplier]);
    $idIdentity_file = uploadId($par[kodeSupplier]);
    $npwpIdentity_file = uploadNpwp($par[kodeSupplier]);
    //repField();
    $inp['tipeSupplier'] = implode(",", $inp['tipeSupplier']);

    $sql = "update dta_supplier set tgl_register='" . setTanggal($inp[tgl_register]) . "', kodePropinsi='$inp[kodePropinsi]', kodeKota='$inp[kodeKota]', nomorSupplier='$inp[nomorSupplier]', namaSupplier='$inp[namaSupplier]', tipeSupplier='$inp[tipeSupplier]', aliasSupplier='$inp[aliasSupplier]', alamatSupplier='$inp[alamatSupplier]', teleponSupplier='$inp[teleponSupplier]', instagramSupplier='$inp[instagramSupplier]', emailSupplier='$inp[emailSupplier]', webSupplier='$inp[webSupplier]', logoSupplier='$logoSupplier', statusSupplier='$inp[statusSupplier]', kodeKategori='$inp[kodeKategori]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]'";
    db($sql);
    $sql = getField("select kodeIdentity from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'") ?
        "update dta_supplier_identity set siupIdentity='$inp[siupIdentity]', siupIdentity_file='$siupIdentity_file', tdpIdentity='$inp[tdpIdentity]', tdpIdentity_file='$tdpIdentity_file', idIdentity='$inp[idIdentity]', idIdentity_file='$idIdentity_file', npwpIdentity='$inp[npwpIdentity]', npwpIdentity_file='$npwpIdentity_file', alamatIdentity='$inp[alamatIdentity]', updateBy='$cID', updateTime='" . date('Y-m-d H:i:s') . "' where kodeSupplier='$par[kodeSupplier]'" :
        "insert into dta_supplier_identity (kodeSupplier, kodeIdentity, siupIdentity, siupIdentity_file, tdpIdentity, tdpIdentity_file, idIdentity, idIdentity_file, npwpIdentity, npwpIdentity_file, alamatIdentity, createBy, createTime) values ('$par[kodeSupplier]', '$par[kodeSupplier]', '$inp[siupIdentity]', '$siupIdentity_file', '$inp[tdpIdentity]', '$tdpIdentity_file', '$inp[idIdentity]', '$idIdentity_file', '$inp[npwpIdentity]', '$npwpIdentity_file', '$inp[alamatIdentity]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    if (empty($update)) {
        echo "<script>
		alert('Data telah tersimpan');
		window.location='?par[mode]=edit" . getPar($par, "mode") . "';
	</script>";
    }
}

function tambah()
{
    global $db, $s, $inp, $par, $cID, $arrParam;
    $kodeMenu = $s;
    $kodeSupplier = getField("select kodeSupplier from dta_supplier order by kodeSupplier desc limit 1") + 1;
    $nomorSupplier = "SPL" . str_pad($kodeSupplier, 3, "0", STR_PAD_LEFT);
    $logoSupplier = uploadLogo($kodeSupplier);
    repField();

    $inp['tipeSupplier'] = implode(",", $inp['tipeSupplier']);

    $sql = "insert into dta_supplier (tipe, kodeSupplier, kodeMenu, kodePropinsi, kodeKota, kodeKategori, nomorSupplier, namaSupplier, aliasSupplier, alamatSupplier, teleponSupplier, instagramSupplier, emailSupplier, webSupplier, tgl_register, logoSupplier, statusSupplier, statusSuspen, createBy, createTime, tipeSupplier) values ('$arrParam[$s]', '$kodeSupplier', '$kodeMenu', '$inp[kodePropinsi]', '$inp[kodeKota]', '$inp[kodeKategori]', '$nomorSupplier', '$inp[namaSupplier]','$inp[aliasSupplier]', '$inp[alamatSupplier]', '$inp[teleponSupplier]', '$inp[instagramSupplier]', '$inp[emailSupplier]', '$inp[webSupplier]', '" . setTanggal($inp[tgl_register]) . "', '$logoSupplier', '$inp[statusSupplier]', '$inp[statusSuspen]', '$cID', '" . date('Y-m-d H:i:s') . "', '$inp[tipeSupplier]')";
    db($sql);
    $kodeIdentity = $kodeSupplier;
    $sql = "insert into dta_supplier_identity (kodeSupplier, kodeIdentity, siupIdentity, tdpIdentity, idIdentity, npwpIdentity, alamatIdentity, createBy, createTime) values ('$kodeSupplier', '$kodeIdentity', '$inp[siupIdentity]', '$inp[tdpIdentity]', '$inp[idIdentity]', '$inp[npwpIdentity]', '$inp[alamatIdentity]', '$cID', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&par[kodeSupplier]=$kodeSupplier" . getPar($par, "mode,kodeSupplier") . "';</script>";
}

function formNote()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier_note where kodeSupplier='$par[kodeSupplier]' and kodeNote='$par[kodeNote]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    setValidation("is_null", "inp[namaNote]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[keteranganNote]", "anda harus mengisi catatan");
    $text = getValidation();
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Note</h1>
		" . getBread(ucwords(str_replace("Note", "", $par[mode]) . " note")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaNote]\" name=\"inp[namaNote]\"  size=\"50\" value=\"$r[namaNote]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Catatan</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganNote]\" name=\"inp[keteranganNote]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganNote]</textarea>
					</div>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formBank()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier_bank where kodeSupplier='$par[kodeSupplier]' and kodeBank='$par[kodeBank]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    setValidation("is_null", "inp[namaBank]", "anda harus mengisi bank name");
    setValidation("is_null", "inp[rekeningBank]", "anda harus mengisi account no.");
    setValidation("is_null", "inp[pemilikBank]", "anda harus mengisi account name");
    $text = getValidation();
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Banking</h1>
		" . getBread(ucwords(str_replace("Bank", "", $par[mode]) . " banking")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Bank Name</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaBank]\" name=\"inp[namaBank]\"  size=\"50\" value=\"$r[namaBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">No Akun</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[rekeningBank]\" name=\"inp[rekeningBank]\"  size=\"50\" value=\"$r[rekeningBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"50\"/>
					</div>	
				</p>								
				<p>
					<label class=\"l-input-small\">Account Name</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[pemilikBank]\" name=\"inp[pemilikBank]\"  size=\"50\" value=\"$r[pemilikBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formContact()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]' and kodeContact='$par[kodeContact]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    setValidation("is_null", "inp[jabatanContact]", "anda harus mengisi jabatan");
    setValidation("is_null", "inp[namaContact]", "anda harus mengisi nama");
    $text = getValidation();
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Contact</h1>
		" . getBread(ucwords(str_replace("Contact", "", $par[mode]) . " contact")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">												
				<p>
					<label class=\"l-input-small\">Jabatan</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[jabatanContact]\" name=\"inp[jabatanContact]\"  size=\"50\" value=\"$r[jabatanContact]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Nama</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaContact]\" name=\"inp[namaContact]\"  size=\"50\" value=\"$r[namaContact]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Email</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[emailContact]\" name=\"inp[emailContact]\"  value=\"$r[emailContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Handphone</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[teleponContact]\" name=\"inp[teleponContact]\"  value=\"$r[teleponContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>					
				<p>
					<label class=\"l-input-small\">Tlp. Kantor</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[kantorContact]\" name=\"inp[kantorContact]\"  value=\"$r[kantorContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Fax</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[faxContact]\" name=\"inp[faxContact]\"  value=\"$r[faxContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganContact]\" name=\"inp[keteranganContact]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganContact]</textarea>
					</div>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formProduct()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier_produk where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    setValidation("is_null", "inp[kodeProduk]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[kodeKategori]", "anda harus mengisi produk");
    $text = getValidation();
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Produk</h1>
		" . getBread(ucwords(str_replace("Product", "", $par[mode]) . " product")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">										
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						" . comboData("select * from dta_produk where statusProduk='t' order by nomorProduk", "kodeProduk", "namaProduk", "inp[kodeProduk]", " ", $r[kodeProduk], "onchange=\"getProd('" . getPar($par, "mode,kodeProduk") . "');\"", "210px") . "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Sub Kategori</label>
					<div class=\"field\">
						" . comboData("select * from dta_produk_kategori where kodeProduk='$r[kodeProduk]'", "kodeKategori", "namaKategori", "inp[kodeKategori]", " ", $r[kodeKategori], "onchange=\"setGeocode('" . getPar($par, "mode,kodeKategori") . "')\"", "210px") . "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Kode Produk</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[subkodeProduk]\" name=\"inp[subkodeProduk]\"  size=\"50\" value=\"$r[subkodeProduk]\" class=\"smallinput\"  width:200px;\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Nama Produk</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaProduk]\" name=\"inp[namaProduk]\"  size=\"50\" value=\"$r[namaProduk]\" class=\"smallinput\"  width:200px;\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganProduk]\" name=\"inp[keteranganProduk]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganProduk]</textarea>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"fieldB\">";
    $text .= empty($r[fileProduk]) ?
        "<input type=\"text\" id=\"tempProduk\" name=\"tempProduk\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:300px;\">
							<input type=\"file\" id=\"fileProduk\" name=\"fileProduk\" class=\"realupload\" size=\"50\" onchange=\"this.form.tempProduk.value = this.value;\" />
						</div>" :
        "<a href=\"download.php?d=supp&f=" . $r[kodeSupplier] . "." . $r[kodeProduk] . "." . $r[kodeKategori] . "\"><img src=\"" . getIcon($dFile . "/" . $r[fileProduk]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						<input type=\"file\" id=\"fileProduk\" name=\"fileProduk\" style=\"display:none;\" />
						<a href=\"?par[mode]=delFProduct" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
    $text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Harga</label>
						<div class=\"field\">						
							<input type=\"text\" id=\"inp[hargaProduk]\" name=\"inp[hargaProduk]\"  size=\"50\" value=\"" . getAngka($r[hargaProduk]) . "\" class=\"mediuminput\" style=\"text-align:right; width:150px;\" onkeyup=\"cekAngka(this);\"/>
						</div>
					</p>
				</p>
				<p>
					<label class=\"l-input-small\">Last Update</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[LastUpdate]\" name=\"inp[LastUpdate]\"  size=\"50\" value=\"" . getTanggal(($r[LastUpdate] == '') ? date("Y-m-d") : $r[LastUpdate]) . "\" class=\"hasDatePicker\"  width:200px;\"/>
					</div>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formJasa()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier_jasa where kodeSupplier='$par[kodeSupplier]' and kodeProduk='$par[kodeProduk]' and kodeKategori='$par[kodeKategori]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    setValidation("is_null", "inp[kodeProduk]", "anda harus mengisi kategori");
    setValidation("is_null", "inp[kodeKategori]", "anda harus mengisi produk");
    $text = getValidation();
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Jasa</h1>
		" . getBread(ucwords(str_replace("Product", "", $par[mode]) . " jasa")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">										
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						" . comboData("select * from dta_jasa where statusProduk='t' order by nomorProduk", "kodeProduk", "namaProduk", "inp[kodeProduk]", " ", $r[kodeProduk], "onchange=\"getProd('" . getPar($par, "mode,kodeProduk") . "');\"", "210px") . "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Sub Kategori</label>
					<div class=\"field\">
						" . comboData("select * from dta_jasa_kategori where kodeProduk='$r[kodeProduk]'", "kodeKategori", "namaKategori", "inp[kodeKategori]", " ", $r[kodeKategori], "onchange=\"setGeocode('" . getPar($par, "mode,kodeKategori") . "')\"", "210px") . "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Kode Jasa</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[subkodeProduk]\" name=\"inp[subkodeProduk]\"  size=\"50\" value=\"$r[subkodeProduk]\" class=\"smallinput\"  width:200px;\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Nama Jasa</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaProduk]\" name=\"inp[namaProduk]\"  size=\"50\" value=\"$r[namaProduk]\" class=\"smallinput\"  width:200px;\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganProduk]\" name=\"inp[keteranganProduk]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganProduk]</textarea>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"fieldB\">";
    $text .= empty($r[fileProduk]) ?
        "<input type=\"text\" id=\"tempProduk\" name=\"tempProduk\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:300px;\">
							<input type=\"file\" id=\"fileProduk\" name=\"fileProduk\" class=\"realupload\" size=\"50\" onchange=\"this.form.tempProduk.value = this.value;\" />
						</div>" :
        "<a href=\"download.php?d=supp&f=" . $r[kodeSupplier] . "." . $r[kodeProduk] . "." . $r[kodeKategori] . "\"><img src=\"" . getIcon($dFile . "/" . $r[fileProduk]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						<input type=\"file\" id=\"fileProduk\" name=\"fileProduk\" style=\"display:none;\" />
						<a href=\"?par[mode]=delFProduct" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
    $text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Harga</label>
						<div class=\"field\">						
							<input type=\"text\" id=\"inp[hargaProduk]\" name=\"inp[hargaProduk]\"  size=\"50\" value=\"" . getAngka($r[hargaProduk]) . "\" class=\"mediuminput\" style=\"text-align:right; width:150px;\" onkeyup=\"cekAngka(this);\"/>
						</div>
					</p>
				</p>
				<p>
					<label class=\"l-input-small\">Last Update</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[LastUpdate]\" name=\"inp[LastUpdate]\"  size=\"50\" value=\"" . getTanggal(($r[LastUpdate] == '') ? date("Y-m-d") : $r[LastUpdate]) . "\" class=\"hasDatePicker\"  width:200px;\"/>
					</div>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formAddress()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $maps;
    $sql = "select * from dta_supplier_address where kodeSupplier='$par[kodeSupplier]' and kodeAddress='$par[kodeAddress]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (empty($r[latitudeAddress])) $r[latitudeAddress] = "-6.264563";
    if (empty($r[longitudeAddress])) $r[longitudeAddress] = "106.766342";
    setValidation("is_null", "inp[alamatAddress]", "anda harus mengisi alamat");
    $text = getValidation();
    //$text.="<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>
    $text .= "<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">Alamat</h1>
			" . getBread(ucwords(str_replace("Address", "", $par[mode]) . " address")) . "
			<ul class=\"hornav\">
				<li class=\"current\"><a href=\"#detail\" onclick=\"document.getElementById('dMap').style.visibility = 'collapse';\">Detail</a></li>
				<li><a href=\"#tabMap\" onclick=\"document.getElementById('dMap').style.visibility = 'visible';\">Map</a></li>
			</ul>
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"SIMPAN\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"BATAL\" onclick=\"closeBox();\"/>
				</div>
				<div id=\"detail\" class=\"subcontent\">									
					<p>
						<label class=\"l-input-small\">Kategori</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[kategoriAddress]\" name=\"inp[kategoriAddress]\"  size=\"50\" value=\"$r[kategoriAddress]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>	
					</p>								
					<p>
						<label class=\"l-input-small\">Alamat</label>
						<div class=\"field\">
							<textarea id=\"inp[alamatAddress]\" name=\"inp[alamatAddress]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[alamatAddress]</textarea>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Propinsi</label>
						<div class=\"field\">
							" . comboData("select * from mst_data where statusData='t' and kodeCategory='S02' order by namaData", "kodeData", "namaData", "inp[kodePropinsi]", " ", $r[kodePropinsi], "onchange=\"getKota('" . getPar($par, "mode,kodePropinsi") . "');\"", "180px") . "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kota</label>
						<div class=\"field\">
							" . comboData("select * from mst_data where statusData='t' and kodeCategory='S03' and kodeInduk='$r[kodePropinsi]' order by namaData", "kodeData", "namaData", "inp[kodeKota]", " ", $r[kodeKota], "onchange=\"setGeocode('" . getPar($par, "mode,kodeKota") . "')\"", "180px") . "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Telepon</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[teleponAddress]\" name=\"inp[teleponAddress]\"  value=\"$r[teleponAddress]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Fax</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[faxAddress]\" name=\"inp[faxAddress]\"  value=\"$r[faxAddress]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Diskripsi</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganAddress]\" name=\"inp[keteranganAddress]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganAddress]</textarea>
						</div>
					</p>					
				</div>
				<!--
				<div id=\"maps\" class=\"subcontent\" style=\"display:none;\">					
					<iframe style=\"width: 100%; height: 250px;\" src=\"http://maps.google.com/maps?q=$r[latitudeAddress],$r[longitudeAddress]&z=15&output=embed\"></iframe>
				</div>								
				<table width=\"100%\" id=\"dMap\" style=\"visibility:collapse;\">
					<tr>
						<td>
							<p>
							</p>
							<p>
								<label>Latitude</label>
								<input type=\"text\" id=\"inp[latitudeAddress]\"  name=\"inp[latitudeAddress]\" class=\"smallinput\" value=\"$r[latitudeAddress]\" />
							</p>
							<p>
								<label>Longitude</label>
								<input type=\"text\" id=\"inp[longitudeAddress]\" name=\"inp[longitudeAddress]\" class=\"smallinput\" value=\"$r[longitudeAddress]\" />
							</p>
							<script>initialize();</script>
						</td>
					</tr>
				</table>
				-->
				<div id=\"tabMap\" class=\"subcontent\" style=\"display:none;\">	
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small2\">Cari Lokasi</label>
                                    <div class=\"fieldC\">
                                        <input type=\"text\" id=\"inp[search]\" name=\"inp[search]\"  value=\"\" style=\"width:60%;\"  maxlength=\"45\"/>
                                    </div>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                
                            </td>
                        </tr>
                        <tr>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small2\">Latitude</label>
                                    <div class=\"fieldC\">
                                        <input type=\"text\" id=\"inp[latitudeAddress]\" name=\"inp[latitudeAddress]\"  value=\"$r[latitudeAddress]\" style=\"width:60%;\"  maxlength=\"45\"/>
                                    </div>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small2\">Longitude</label>
                                    <div class=\"fieldC\">
                                        <input type=\"text\" id=\"inp[longitudeAddress]\" name=\"inp[longitudeAddress]\"  value=\"$r[longitudeAddress]\" style=\"width:60%;\"  maxlength=\"45\"/>
                                    </div>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <div id=\"map\" style=\"width:100%; height:600px\"></div>
				</div>
				
				<script async defer src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBio4DnANXPdAt6inLdithPKwX64Doj7uc&v=3.exp&sensor=false&libraries=places&callback=initMap\"></script>
                <script type=\"text/javascript\">
                ";
    $latitude = (empty($r["latitudeAddress"])) ? "-6.2808625242603595" : $r["latitudeAddress"];
    $longitude = (empty($r["longitudeAddress"])) ? "106.82830000000001" : $r["longitudeAddress"];
    $latLng = $latitude . "," . $longitude;
    $text .= "
                function initMap() {

                    position = {lat: $latitude, lng: $longitude}
        
                    // The map, centered at position
                    var map = new google.maps.Map(document.getElementById(\"map\"), {zoom: 15, center: position})
        
                    // The marker, positioned at position
                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        draggable: true
                    })
        
                    google.maps.event.addListener(marker, 'dragend', function (event) {
        
                        jQuery('#inp\\\[latitudeAddress\\\]').val(event.latLng.lat());
                        jQuery('#inp\\\[longitudeAddress\\\]').val(event.latLng.lng());
        
                    });
        
                    var search_input = document.getElementById(\"inp[search]\")
                    var search_box = new google.maps.places.SearchBox(search_input);
        
                    google.maps.event.addListener(search_box, 'places_changed', function () {
        
                        places = search_box.getPlaces();
        
                        marker.setMap(null);
        
                        var temp = new google.maps.Marker({
                            position: places[0].geometry.location,
                            title: places[0].name,
                            map: map,
                            draggable: true
                        })
        
                        jQuery('#inp\\\[latitudeAddress\\\]').val(places[0].geometry.location.lat());
                        jQuery('#inp\\\[longitudeAddress\\\]').val(places[0].geometry.location.lng());
        
                        map.setCenter(places[0].geometry.location);
        
                        setTimeout(() => {
                            map.setZoom(17);
                        }, 1300);
        
                        google.maps.event.addListener(temp, 'dragend', function (event) {
        
                            jQuery('#inp\\\[latitudeAddress\\\]').val(event.latLng.lat());
                            jQuery('#inp\\\[longitudeAddress\\\]').val(event.latLng.lng());
        
                        });
        
                    })
        
                }
                
                
                </script>
				
			</div>
		</form>	
	</div>";
    return $text;
}

function lihat()
{

    global $s, $inp, $par, $arrTitle, $menuAccess, $arrColor, $cVac, $cyear, $m, $lang;
    $sekarang = date("Y-m-d");
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
        $cols = 6;
    } else {
        $cols = 7;
    }
    $arrNilai = array("" => "Semua Tipe", "barang" => "Barang", "jasa" => "Jasa", "barang,jasa" => "Barang, Jasa");
    $text = table($cols, array(4, 5, $cols));
    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
	
        <form action=\"\" method=\"post\" id=\"form\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left; display: flex;\">

				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"" . $fSearch . "\" style=\"width:250px;\"/>
               
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
                <a href=\"?par[mode]=xls" . getPar($par, "mode, kodeSupplier") . "\" class=\"btn btn1 btn_document\"><span>Export</span></a>
                <a href=\"?par[mode]=add" . getPar($par, "mode, kodeSupplier") . "\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
            </div>
		

            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">	
                                <p>
                                    <label class=\"l-input-small\">Tipe</label>
                                    <div class=\"field\">
                                        " . comboKey("mSearch", $arrNilai, $par[mSearch], "Semua Tipe", "200px") . "
                                    </div>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Industri</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'IDS' order by namaData", "kodeData", "namaData", "tSearch", "Semua Kategori", $par[tSearch], "", "200px") . "
                                    </div>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Propinsi</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where statusData='t' and kodeCategory='S02' order by namaData", "kodeData", "namaData", "aSearch", "Semua Propinsi", $par[aSearch], "onchange=\"getKota2('" . getPar($par, "mode,kodePropinsi") . "');\"", "200px") . "
                                    </div>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Kota</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where statusData='t' and kodeCategory='S03' and kodeInduk='$par[aSearch]' order by namaData", "kodeData", "namaData", "bSearch", "Semua Kota", $par[fSearch], "", "200px") . "
                                    </div>
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
                    <th width=\"20\">No.</th>
                    <th width=\"100\">No Akun</th>
                    <th width=\"*\">Nama supplier</th>
                    <th width=\"150\">Tipe</th>
                    <th width=\"150\">Industri</th>
                    ";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "
                </tr>
            </thead>
            <tbody></tbody>
        </table>
		</div>";
    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    return $text;
}

function lData()
{
    global $s, $par, $fFile, $menuAccess, $cID, $sUser, $sGroup, $arrTitle, $arrParam, $m;
    if ($_GET[json] == 1) {
        header("Content-type: application/json");
    }
    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
        $sLimit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
    $filters = " where t1.tipe = '$arrParam[$s]'";
    $arrOrder = array(
        "t1.createTime",
        "t1.nomorSupplier",
        "t1.namaSupplier",
    );
    $orderBy = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    if (!empty($_GET['fSearch'])) {
        $filters .= " AND (t1.namaSupplier LIKE '%" . $_GET['fSearch'] . "%' or t1.nomorSupplier LIKE '%" . $_GET['fSearch'] . "%')";
    }
    if (!empty($_GET['aSearch'])) {
        $filters .= " AND t1.kodePropinsi = '" . $_GET['aSearch'] . "'";
    }
    if (!empty($_GET['bSearch'])) {
        $filters .= " AND t1.kodeKota = '" . $_GET['bSearch'] . "'";
    }
    if (!empty($_GET['mSearch'])) {
        $filters .= " AND t1.tipeSupplier = '" . $_GET['mSearch'] . "'";
    }
    if (!empty($_GET['tSearch'])) {
        $filters .= " AND t1.kodeKategori = '" . $_GET['tSearch'] . "'";
    }
    $sql = "SELECT t1.*
		FROM 
		dta_supplier t1 
		$filters order by $orderBy
		$sLimit
		";
    $res = db($sql);
    $arrIndustri = arrayQuery("select kodeData, namaData from mst_data where kodeCategory = 'IDS'");
    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(*) FROM dta_supplier t1 $filters"),
        "aaData" => array(),
    );
    $no = intval($_GET['iDisplayStart']);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $controlKebutuhan = "";
            if (isset($menuAccess[$s]["edit"]))
                $controlKebutuhan .= "<a href=\"?par[mode]=edit&par[kodeSupplier]=$r[kodeSupplier]" . getPar($par, "mode,kodeSupplier") . "\" title=\"Ubah Data\" class=\"edit\" ><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"]))
                $controlKebutuhan .= "<a href=\"?par[mode]=del&par[kodeSupplier]=$r[kodeSupplier]" . getPar($par, "mode,kodeSupplier") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Hapus Data\" class=\"delete\"><span>Delete</span></a>";
        }
        if ($r[aliasSupplier] != "") $r[brandName] = "(" . $r[aliasSupplier] . ")";
        if ($r[statusSupplier] == "s") $r[statusSupplier] = "<img src=\"styles/images/o.png\" title='Suspend'>";
        elseif ($r[statusSupplier] == "t") $r[statusSupplier] = "<img src=\"styles/images/t.png\" title='Active'>";
        else $r[statusSupplier] = "<img src=\"styles/images/f.png\" title='Not Active'>";
        $data = array(
            "<div align=\"center\">" . $no . ".</div>",
            "<div align=\"center\">$r[nomorSupplier]</div>",
            "<div align=\"left\">$r[namaSupplier]</div>",
            "<div align=\"left\">" . $r['tipeSupplier'] . "</div>",
            "<div align=\"left\">" . $arrIndustri[$r[kodeKategori]] . "</div>",
            "<div align=\"center\">$controlKebutuhan</div>",
        );
        $json['aaData'][] = $data;
    }
    return json_encode($json);
}

function form()
{
    global $db, $s, $inp, $par, $tab, $arrTitle, $fFile, $dFile, $arrParameter, $menuAccess, $lang;
    $sql = "select * from dta_supplier where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (empty($r[kodeTipe])) $r[kodeTipe] = $par[kodeTipe];
    $false = $r[statusSupplier] == "f" ? "checked=\"checked\"" : "";
    $sus = $r[statusSupplier] == "s" ? "checked=\"checked\"" : "";
    $true = empty($false) && empty($sus) ? "checked=\"checked\"" : "";
    setValidation("is_null", "inp[nomorSupplier]", "kamu harus mengisi No. Akun");
    setValidation("is_null", "inp[namaSupplier]", "kamu harus mengisi Nama supplier");
    setValidation("is_null", "inp[alamatSupplier]", "kamu harus mengisi Alamat");
    $text = getValidation();
    $dAddress = " style=\"display: none;\"";
    $dProduct = " style=\"display: none;\"";
    $dJasa = " style=\"display: none;\"";
    $dIdentity = " style=\"display: none;\"";
    $dContact = " style=\"display: none;\"";
    $dBanking = " style=\"display: none;\"";
    $dNote = " style=\"display: none;\"";
    $dGeneral = " style=\"display: none;\"";
    if ($tab == 1) {
        $tAddress = "class=\"current\"";
        $dAddress = " style=\"display: block;\"";
    } else if ($tab == 2) {
        $tProduct = "class=\"current\"";
        $dProduct = " style=\"display: block;\"";
    } else if ($tab == 3) {
        $tJasa = "class=\"current\"";
        $dJasa = " style=\"display: block;\"";
    } else if ($tab == 4) {
        $tIdentity = "class=\"current\"";
        $dIdentity = " style=\"display: block;\"";
    } else if ($tab == 5) {
        $tContact = "class=\"current\"";
        $dContact = " style=\"display: block;\"";
    } else if ($tab == 6) {
        $tBanking = "class=\"current\"";
        $dBanking = " style=\"display: block;\"";
    } else if ($tab == 7) {
        $tNote = "class=\"current\"";
        $dNote = " style=\"display: block;\"";
    } else {
        $tGeneral = "class=\"current\"";
        $dGeneral = " style=\"display: block;\"";
    }
    $mode = empty($r[nomorSupplier]) ? "add" : "edit";
    $text .= "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread(ucwords($mode . " data")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			
			";
    if (!empty($r[logoSupplier])) {

        $text .= "
                        <fieldset>
                        <div style='margin: auto; text-align: center'>
                            <img src=\"" . $fFile . "/" . $r[logoSupplier] . "\" style=\"height:120px;\">
                            <input type=\"file\" id=\"logoSupplier\" name=\"logoSupplier\" style=\"display:none;\" />
                            <br>
                            <a href=\"?par[mode]=delLogo" . getPar($par, "mode") . "\" onclick=\"return confirm('Hapus logo?')\" class=\"action delete\"><span>Hapus</span></a>
                            <br clear=\"all\">
                        </div>
                        </fieldset>      
                        ";

    }
    $text .= "
			
			<br>
			
			<ul class=\"hornav\">
                <li $tGeneral><a href=\"#general\">Umum</a></li>
                <li $tAddress><a href=\"#address\">Alamat</a></li>
                <li $tProduct><a href=\"#product\">Produk</a></li>
                <li $tIdentity><a href=\"#identity\">Identitas</a></li>
                <li $tContact><a href=\"#contact\">Kontak</a></li>
                <li $tBanking><a href=\"#banking\">Bank</a></li>
                <li $tNote><a href=\"#note\">Note</a></li>
            </ul>
			
			<div style=\"top:70px; right:20px; margin-top:-60px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Simpan\" onclick=\"return chk('" . getPar($par, "mode") . "');\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, kodeSupplier") . "';\"/>
			</div>";
    $text .= "<div id=\"general\" class=\"subcontent\" $dGeneral >					
			<p>
				<label class=\"l-input-small\">No Akun</label>
				<div class=\"fieldB\">
					<input type=\"text\" id=\"inp[nomorSupplier]\" name=\"inp[nomorSupplier]\"  value=\"$r[nomorSupplier]\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"30\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Nama supplier</label>
				<div class=\"fieldB\">
					<input type=\"text\" id=\"inp[namaSupplier]\" name=\"inp[namaSupplier]\"  value=\"$r[namaSupplier]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Alias</label>
				<div class=\"fieldB\">
					<input type=\"text\" id=\"inp[aliasSupplier]\" name=\"inp[aliasSupplier]\"  value=\"$r[aliasSupplier]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"150\"/>
				</div>
			</p>
			";
    $text .= empty($r[logoSupplier]) ?
        "
            <p>
                <label class=\"l-input-small\">Logo</label>
                <div class=\"fieldB\">
                    <input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:250px;\" maxlength=\"100\" />
                    <div class=\"fakeupload\" style=\"width:300px;\">
                        <input type=\"file\" id=\"logoSupplier\" name=\"logoSupplier\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
                    </div>
                </div>
            </p>"
        :
        "";
    $text .= "
			<p>
				<label class=\"l-input-small\">Alamat</label>
				<div class=\"fieldB\">
					<textarea id=\"inp[alamatSupplier]\" name=\"inp[alamatSupplier]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[alamatSupplier]</textarea>
				</div>
			</p>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">										
						<p>
							<label class=\"l-input-small2\">Propinsi</label>
							<div class=\"fieldA\">
								" . comboData("select * from mst_data where statusData='t' and kodeCategory='S02' order by namaData", "kodeData", "namaData", "inp[kodePropinsi]", "- Pilih Propinsi -", $r[kodePropinsi], "onchange=\"getKota(this.value, '" . getPar($par, "mode") . "');\"", "200px", "chosen-select") . "
							</div>
						</p>						
						<p>
							<label class=\"l-input-small2\">Telepon</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[teleponSupplier]\" name=\"inp[teleponSupplier]\"  value=\"$r[teleponSupplier]\" class=\"mediuminput\"  maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Email</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[emailSupplier]\" name=\"inp[emailSupplier]\"  value=\"$r[emailSupplier]\" class=\"mediuminput\"  maxlength=\"50\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Status</label>
							<div class=\"fieldA\" style='width:100%;'>											
								<input type=\"radio\" id=\"true\" name=\"inp[statusSupplier]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
								<input type=\"radio\" id=\"false\" name=\"inp[statusSupplier]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
								<input type=\"radio\" id=\"sus\" name=\"inp[statusSupplier]\" value=\"s\" $sus /> <span class=\"sradio\">Calon</span>					
							</div>
						</p>
					</td>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small3\">Kota</label>
							<div class=\"fieldC\">
								" . comboData("select * from mst_data where statusData='t' and kodeCategory='S03' and kodeInduk='$r[kodePropinsi]' order by namaData", "kodeData", "namaData", "inp[kodeKota]", "- Pilih Kota -", $r[kodeKota], "", "200px", "chosen-select") . "
							</div>
						</p>
						<p>
							<label class=\"l-input-small3\">Instagram</label>
							<div class=\"fieldC\">
								<input type=\"text\" id=\"inp[instagramSupplier]\" name=\"inp[instagramSupplier]\"  value=\"$r[instagramSupplier]\" class=\"mediuminput\"  maxlength=\"50\"/>
							</div>
						</p>							
						<p>
							<label class=\"l-input-small3\">Website</label>
							<div class=\"fieldC\">
								<input type=\"text\" id=\"inp[webSupplier]\" name=\"inp[webSupplier]\" value=\"$r[webSupplier]\" class=\"mediuminput\"  maxlength=\"50\"/>
							</div>
						</p>
						<p>
                            <label class=\"l-input-small3\">Tgl. Register</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[tgl_register]\" name=\"inp[tgl_register]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[tgl_register]) . "\" class=\"vsmallinput hasDatePicker\"/>
                            </div>
                        </p>
					</td>
				</tr>
			</table>
		</div>
		";
    # TAB ADDRESS
    $text .= "
		<div id=\"address\" class=\"subcontent\" $dAddress >
			<div class=\"widgetbox\" style=\"margin:0;\">
				<div style=\"float:left; width:50%; padding-top:15px;\">
					<h4>Alamat</h4>
				</div>
				<div style=\"float:left; width:50%;\">
					<div id=\"pos_r\" style=\"\">
						";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addAddress" . getPar($par, "mode,kodeAddress") . "',825,500);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Alamat</span></a>";
    $text .= "
					</div>
				</div>
			</div>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th width=\"200\">Kategori</th>	
						<th>Alamat</th>
						<th width=\"200\">Kota</th>	
						<th width=\"150\">Telepon</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "
					</tr>
				</thead>
				<tbody>";
    $sql = "select * from dta_supplier_address t1 join mst_data t2 on (t1.kodeKota=t2.kodeData) where t1.kodeSupplier='$par[kodeSupplier]' order by t1.kodeAddress";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "
						<tr>
							<td>$no.</td>
							<td>$r[kategoriAddress]</td>
							<td>$r[alamatAddress]</td>
							<td>$r[namaData]</td>
							<td>$r[teleponAddress]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editAddress&par[kodeAddress]=$r[kodeAddress]" . getPar($par, "mode,kodeAddress") . "',825,550);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delAddress&par[kodeAddress]=$r[kodeAddress]" . getPar($par, "mode,kodeAddress") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "
							</td>";
        }
        $text .= "
					</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "
					</tr>";
    }
    $text .= "
			</tbody>
		</table>
	</div>";
    # TAB PRODUCT
    $text .= "<div id=\"product\" class=\"subcontent\" $dProduct >
	<div class=\"widgetbox\" style=\"margin:0;\">
		<div style=\"float:left; width:50%; padding-top:15px;\">
			<h4>Produk</h4>
		</div>
		<div style=\"float:left; width:50%;\">
			<div id=\"pos_r\" style=\"\">
				";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addProduct" . getPar($par, "mode,kodeProduk") . "',900,500);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Produk</span></a>";
    $text .= "
			</div>
		</div>
	</div>
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>
				<th>Product</th>
				<th width=\"125\">Harga</th>
				<th width=\"50\">File</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "</tr>
			</thead>
			<tbody>";
    $sql = "select * from dta_supplier_produk t1 join dta_produk_kategori t2 on (t1.kodeProduk=t2.kodeProduk and t1.kodeKategori=t2.kodeKategori) where t1.kodeSupplier='$par[kodeSupplier]' order by t1.kodeProduk, t1.kodeKategori";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
					<td>$no.</td>
					<td> $r[namaKategori] - $r[tipeKategori]</td>
					<td align=\"right\">" . getAngka($r[hargaProduk]) . "</td>
					<td align=\"center\">";
        if (!empty($r[fileProduk]))
            $text .= "<a href=\"download.php?d=supp&f=" . $r[kodeSupplier] . "." . $r[kodeProduk] . "." . $r[kodeKategori] . "\"><img src=\"" . getIcon($dFile . "/" . $r[fileProduk]) . "\" style=\"padding-right:5px; padding-bottom:5px;\"></a>";
        $text .= "</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editProduct&par[kodeProduk]=$r[kodeProduk]&par[kodeKategori]=$r[kodeKategori]" . getPar($par, "mode,kodeProduk,kodeKategori") . "',900,500);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delProduct&par[kodeProduk]=$r[kodeProduk]&par[kodeKategori]=$r[kodeKategori]" . getPar($par, "mode,kodeProduk,kodeKategori") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "
						</td>";
        }
        $text .= "
				</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
				<tr>
					<td>&nbsp;</td>								
					<td>&nbsp;</td>
					<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "
				</tr>";
    }
    $text .= "
		</tbody>
	</table>
</div>";

# TAB IDENTITY
    $sql = "select * from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $text .= "<div id=\"identity\" class=\"subcontent\" $dIdentity >
<table width=\"100%\">
	<tr>
		<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:middle\">
			<p>
				<label class=\"l-input-small\">SIUP</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[siupIdentity]\" name=\"inp[siupIdentity]\"  value=\"$r[siupIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">TDP</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[tdpIdentity]\" name=\"inp[tdpIdentity]\"  value=\"$r[tdpIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">ID</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[idIdentity]\" name=\"inp[idIdentity]\"  value=\"$r[idIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">NPWP</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[npwpIdentity]\" name=\"inp[npwpIdentity]\"  value=\"$r[npwpIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Alamat</label>
				<div class=\"field\">
					<textarea id=\"inp[alamatIdentity]\" name=\"inp[alamatIdentity]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[alamatIdentity]</textarea>
				</div>
			</p>
		</td>
		<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
			<p>
				<label class=\"l-input-small\">File</label>
				<div class=\"fieldB\">";
    $text .= empty($r[siupIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_siup\" name=\"fileTemp_siup\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:300px;\">
						<input type=\"file\" id=\"siupIdentity_file\" name=\"siupIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_siup.value = this.value;\" />
					</div>" :
        "<a href=\"download.php?d=sup&f=siup.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[siupIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
					<input type=\"file\" id=\"siupIdentity_file\" name=\"siupIdentity_file\" style=\"display:none;\" />
					<a href=\"?par[mode]=delSiup" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";
    $text .= "
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">File</label>
				<div class=\"fieldB\">";
    $text .= empty($r[tdpIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_tdp\" name=\"fileTemp_tdp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:300px;\">
						<input type=\"file\" id=\"tdpIdentity_file\" name=\"tdpIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_tdp.value = this.value;\" />
					</div>" :
        "<a href=\"download.php?d=sup&f=tdp.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[tdpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
					<input type=\"file\" id=\"tdpIdentity_file\" name=\"tdpIdentity_file\" style=\"display:none;\" />
					<a href=\"?par[mode]=delTdp" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";
    $text .= "
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">File</label>
				<div class=\"fieldB\">";
    $text .= empty($r[idIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_id\" name=\"fileTemp_id\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:300px;\">
						<input type=\"file\" id=\"idIdentity_file\" name=\"idIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_id.value = this.value;\" />
					</div>" :
        "<a href=\"download.php?d=sup&f=id.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[idIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
					<input type=\"file\" id=\"idIdentity_file\" name=\"idIdentity_file\" style=\"display:none;\" />
					<a href=\"?par[mode]=delId" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";
    $text .= "
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">File</label>
				<div class=\"fieldB\">";
    $text .= empty($r[npwpIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_npwp\" name=\"fileTemp_npwp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:300px;\">
						<input type=\"file\" id=\"npwpIdentity_file\" name=\"npwpIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_npwp.value = this.value;\" />
					</div>" :
        "<a href=\"download.php?d=sup&f=npwp.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[npwpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
					<input type=\"file\" id=\"npwpIdentity_file\" name=\"npwpIdentity_file\" style=\"display:none;\" />
					<a href=\"?par[mode]=delNpwp" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";
    $text .= "
				</div>
			</p>
		</td>
	</tr>
</table>
</div>";
# TAB CONTACT
    $text .= "<div id=\"contact\" class=\"subcontent\" $dContact >
<div class=\"widgetbox\" style=\"margin:0;\">
	<div style=\"float:left; width:50%; padding-top:15px;\">
		<h4>Kontak</h4>
	</div>
	<div style=\"float:left; width:50%;\">
		<div id=\"pos_r\" style=\"\">
			";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addContact" . getPar($par, "mode,kodeCatatan") . "',725,480);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Kontak</span></a>";
    $text .= "
		</div>
	</div>
</div>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th style=\"min-width:175px;\">Jabatan</th>
			<th style=\"min-width:175px;\">Nama</th>
			<th width=\"150\">Email</th>
			<th width=\"100\">Handphone</th>
			<th width=\"100\">Tlp. Kantor</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "
		</tr>
	</thead>
	<tbody>";
    $sql = "select * from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]' order by kodeContact";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
			<td>$no.</td>
			<td>$r[jabatanContact]</td>
			<td>$r[namaContact]</td>
			<td>$r[emailContact]</td>
			<td>$r[teleponContact]</td>
			<td>$r[kantorContact]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editContact&par[kodeContact]=$r[kodeContact]" . getPar($par, "mode,kodeContact") . "',725,500);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delContact&par[kodeContact]=$r[kodeContact]" . getPar($par, "mode,kodeContact") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "
			</td>";
        }
        $text .= "
	</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "<tr>
	<td>&nbsp;</td>								
	<td>&nbsp;</td>
	<td>&nbsp;</td>								
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "
</tr>";
    }
    $text .= "
</tbody>
</table>
</div>";
    # TAB BANKING
    $text .= "<div id=\"banking\" class=\"subcontent\" $dBanking >
<div class=\"widgetbox\" style=\"margin:0;\">
	<div style=\"float:left; width:50%; padding-top:15px;\">
		<h4>Bank</h4>
	</div>
	<div style=\"float:left; width:50%;\">
		<div id=\"pos_r\" style=\"\">
			";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addBank" . getPar($par, "mode,kodeBank") . "',725,300);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Bank</span></a>";
    $text .= "
		</div>
	</div>
</div>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th>Nama Bank</th>
			<th width=\"150\">No Akun</th>							
			<th>Nama Akun</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "
		</tr>
	</thead>
	<tbody>";
    $sql = "select * from dta_supplier_bank where kodeSupplier='$par[kodeSupplier]' order by kodeBank";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
			<td>$no.</td>
			<td>$r[namaBank]</td>
			<td>$r[rekeningBank]</td>
			<td>$r[pemilikBank]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editBank&par[kodeBank]=$r[kodeBank]" . getPar($par, "mode,kodeBank") . "',725,300);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delBank&par[kodeBank]=$r[kodeBank]" . getPar($par, "mode,kodeBank") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }
        $text .= "
		</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "
		</tr>";
    }
    $text .= "
</tbody>
</table>
</div>";
    # TAB NOTE
    $text .= "<div id=\"note\" class=\"subcontent\" $dNote >
<div class=\"widgetbox\" style=\"margin:0;\">
	<div style=\"float:left; width:50%; padding-top:15px;\">
		<h4>Note</h4>
	</div>
	<div style=\"float:left; width:50%;\">
		<div id=\"pos_r\" style=\"\">
			";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addNote" . getPar($par, "mode,kodeNote") . "',725,300);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Note</span></a>";
    $text .= "
		</div>
	</div>
</div>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th>Kategori</th>
			<th>Catatan</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "
		</tr>
	</thead>
	<tbody>";
    $sql = "select * from dta_supplier_note where kodeSupplier='$par[kodeSupplier]' order by kodeNote";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
			<td>$no.</td>
			<td>$r[namaNote]</td>
			<td>" . nl2br($r[keteranganNote]) . "</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editNote&par[kodeNote]=$r[kodeNote]" . getPar($par, "mode,kodeNote") . "',725,300);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delNote&par[kodeNote]=$r[kodeNote]" . getPar($par, "mode,kodeNote") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "
			</td>";
        }
        $text .= "
	</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
	<tr>
		<td>&nbsp;</td>								
		<td>&nbsp;</td>
		<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "
	</tr>";
    }

    $sql = "select * from dta_supplier where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "
</tbody>
</table>
</div>
<table style=\"width:100%;\">
	<tr>
		<td >															
			<fieldset>
				<legend>Kategori</legend>
				<table style=\"width:100%;\">
					<tr>
						<td style=\"width:50%;\">
							<p>
								<label class=\"l-input-small2\">Tipe</label>
								<div class=\"fieldA\" style='width:100%;'>	
								    ";
    $r['tipeSupplier'] = explode(",", $r['tipeSupplier']);
    $arrTipe = ['barang' => 'Barang', 'jasa' => 'Jasa'];

    foreach ($arrTipe as $key => $val) {

        $checked = in_array($key, $r['tipeSupplier']) ? "checked=\"checked\"" : "";

        $text .= "
								        <input type=\"checkbox\" id=\"inp[tipeSupplier][]\" name=\"inp[tipeSupplier][]\" value=\"" . $key . "\" $checked /> $val &nbsp;
								        ";

    }
    $text .= "	
								    <!--									
									<input type=\"radio\" name=\"inp[tipeSupplier]\" value=\"b\" " . (($r['tipeSupplier'] == '' or $r['tipeSupplier'] == 'b') ? "checked" : "") . "/> <span class=\"sradio\">Barang</span>
									<input type=\"radio\"  name=\"inp[tipeSupplier]\" value=\"j\" " . (($r['tipeSupplier'] == 'j') ? "checked" : "") . "/> <span class=\"sradio\">Jasa</span>
								    -->
								</div>
							</p>
						</td>
						<td style=\"width:50%;\">
							<p>
								<label class=\"l-input-small3\">Industri</label>
								<div class=\"fieldC\">
									" . comboData("select kodeData, namaData from mst_data where kodeCategory = 'IDS' order by namaData asc", "kodeData", "namaData", "inp[kodeKategori]", "- Pilih Industri -", $r[kodeKategori], "", "200px", "chosen-select") . "
								</div>
							</p>
						</td>
					</tr>
				</table>
			</fieldset>	
		</td >
	</tr>
</table>
";
    $crBy = getField("select namaUser from app_user where id = '$r[createBy]' ");
    $upBy = getField("select namaUser from app_user where id = '$r[updateBy]' ");
    $text .= "
" . show_history($r['createTime'], $r['updateTime'], $crBy, $upBy) . "
</form>";
    return $text;
}

function show_history($createDate, $updateDate, $createBy, $updateBy)
{
    global $par;
    $result = "";
    if ($par['mode'] == "edit") {
        $result = "
		<form class=\"stdform\">
			<fieldset>
				<legend>History</legend>
				<table width=\"100%\">
					<tr>
						<td width=\"50%\" >
							<p>
								<label class=\"l-input-small2\" >Created Date</label>
								<span class=\"field\" id=\"created_date\">
									" . $createDate . "
								</span>
							</p>    
						</td> 
						<td >
							<p>
								<label class=\"l-input-small2\" >Update Date</label>
								<span class=\"field\" id=\"update_date\">
									" . $updateDate . "
								</span>
							</p>    
						</td> 
					</tr>
					<tr>
						<td width=\"50%\">
							<p>
								<label class=\"l-input-small2\" >Created By</label>
								<span class=\"field\" id=\"Created_By\">
									" . $createBy . "
								</span>
							</p>    
						</td> 
						<td>
							<p>
								<label class=\"l-input-small2\" >Update By</label>
								<span class=\"field\" id=\"update_by\">
									" . $updateBy . "
								</span>
							</p>    
						</td> 
					</tr>
				</table>
			</fieldset>
		</form>";
    } else {
        $result = "";
    }
    return $result;
}

function detail()
{
    global $db, $s, $inp, $par, $tab, $arrTitle, $fFile, $dFile, $arrParameter, $menuAccess;
    $sql = "select * from dta_supplier where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (empty($r[kodeTipe])) $r[kodeTipe] = $par[kodeTipe];
    if ($r[statusSupplier] == "p")
        $statusSupplier = "Prospect";
    else if ($r[statusSupplier] == "t")
        $statusSupplier = "Active";
    else
        $statusSupplier = "Not Active";
    $dAddress = " style=\"display: none;\"";
    $dProduct = " style=\"display: none;\"";
    $dJasa = " style=\"display: none;\"";
    $dIdentity = " style=\"display: none;\"";
    $dContact = " style=\"display: none;\"";
    $dBanking = " style=\"display: none;\"";
    $dGeneral = " style=\"display: none;\"";
    if ($tab == 1) {
        $tAddress = "class=\"current\"";
        $dAddress = " style=\"display: block;\"";
    } else if ($tab == 2) {
        $tProduct = "class=\"current\"";
        $dProduct = " style=\"display: block;\"";
    } else if ($tab == 3) {
        $tJasa = "class=\"current\"";
        $dJasa = " style=\"display: block;\"";
    } else if ($tab == 4) {
        $tIdentity = "class=\"current\"";
        $dIdentity = " style=\"display: block;\"";
    } else if ($tab == 5) {
        $tContact = "class=\"current\"";
        $dContact = " style=\"display: block;\"";
    } else if ($tab == 6) {
        $tBanking = "class=\"current\"";
        $dBanking = " style=\"display: block;\"";
    } else {
        $tGeneral = "class=\"current\"";
        $dGeneral = " style=\"display: block;\"";
    }
    $text .= "<div class=\"pageheader\">
	<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
	" . getBread(ucwords("detail data")) . "
	<ul class=\"hornav\">
		<li $tGeneral><a href=\"#general\">General</a></li>
		<li $tAddress><a href=\"#address\">Address</a></li>
		<li $tProduct><a href=\"#product\">Product</a></li>
		<li $tProduct><a href=\"#jasa\">Jasa</a></li>
		<li $tIdentity><a href=\"#identity\">Identity</a></li>
		<li $tContact><a href=\"#contact\">Contact</a></li>
		<li $tBanking><a href=\"#banking\">Banking</a></li>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"top:70px; right:35px; position:absolute\">
				<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, kodeSupplier") . "';\"/>
			</div>";
    # TAB GENERAL
    $text .= "<div id=\"general\" class=\"subcontent\" $dGeneral >					
			<p>
				<label class=\"l-input-small\">No Akun</label>
				<span class=\"field\">$r[nomorSupplier]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Nama supplier</label>
				<span class=\"field\">$r[namaSupplier]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Alias</label>
				<span class=\"field\">$r[aliasSupplier]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Logo</label>
				<div class=\"field\">";
    $text .= empty($r[logoSupplier]) ? "" :
        "<img src=\"" . $fFile . "/" . $r[logoSupplier] . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\">
					<br clear=\"all\">";
    $text .= "</div>
				</p>
				<p>
					<label class=\"l-input-small\">Alamat</label>
					<span class=\"field\">" . nl2br($r[alamatSupplier]) . "&nbsp;</span>
				</p>
				<table style=\"width:100%\">
					<tr>
						<td style=\"width:50%\">										
							<p>
								<label class=\"l-input-small2\">Propinsi</label>
								<span class=\"fieldA\">" . getField("select namaData from mst_data where kodeData='$r[kodePropinsi]'") . "&nbsp;</span>
							</p>						
							<p>
								<label class=\"l-input-small2\">Telepon</label>
								<span class=\"fieldA\">$r[teleponSupplier]&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">Email</label>
								<span class=\"fieldA\">$r[emailSupplier]&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">Status</label>
								<span class=\"fieldA\">$statusSupplier&nbsp;</span>
							</p>
						</td>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Kota</label>
								<span class=\"fieldA\">" . getField("select namaData from mst_data where kodeData='$r[kodeKota]'") . "&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">Instagram</label>
								<span class=\"fieldA\">$r[instagramSupplier]&nbsp;</span>
							</p>							
							<p>
								<label class=\"l-input-small2\">Website</label>
								<span class=\"fieldA\">$r[webSupplier]&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">Kategori</label>
								<span class=\"fieldA\">" . getField("select namaData from mst_data where kodeData='$r[kodeKategori]'") . "&nbsp;</span>
							</p>							
						</td>
					</tr>
				</table>														
			</div>";
    # TAB ADDRESS
    $text .= "
			<div id=\"address\" class=\"subcontent\" $dAddress >
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th width=\"200\">Kategori</th>	<th>Alamat</th>
							<th width=\"200\">Kota</th>	<th width=\"150\">Telepon</th>
						</tr>
					</thead>
					<tbody>";
    $sql = "select * from dta_supplier_address t1 join mst_data t2 on (t1.kodeKota=t2.kodeData) where t1.kodeSupplier='$par[kodeSupplier]' order by t1.kodeAddress";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
							<td>$no.</td>
							<td>$r[kategoriAddress]</td>
							<td>$r[alamatAddress]</td>
							<td>$r[namaData]</td>
							<td>$r[teleponAddress]</td>
						</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>";
    }
    $text .= "
				</tbody>
			</table>
		</div>";
    # TAB PRODUCT
    $text .= "<div id=\"product\" class=\"subcontent\" $dProduct >
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Product</th>
					<th width=\"125\">Harga</th>
					<th width=\"50\">File</th>
				</tr>
			</thead>
			<tbody>";
    $sql = "select * from dta_supplier_produk t1 join dta_produk_kategori t2 on (t1.kodeProduk=t2.kodeProduk and t1.kodeKategori=t2.kodeKategori) where t1.kodeSupplier='$par[kodeSupplier]' order by t1.kodeProduk, t1.kodeKategori";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
					<td>$no.</td>
					<td>$r[tipeKategori] -- $r[namaKategori]</td>
					<td align=\"right\">" . getAngka($r[hargaProduk]) . "</td>
					<td align=\"center\">";
        if (!empty($r[fileProduk]))
            $text .= "<a href=\"download.php?d=supp&f=" . $r[kodeSupplier] . "." . $r[kodeProduk] . "." . $r[kodeKategori] . "\"><img src=\"" . getIcon($dFile . "/" . $r[fileProduk]) . "\" style=\"padding-right:5px; padding-bottom:5px;\"></a>";
        $text .= "
					</td>
				</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
				<tr>
					<td>&nbsp;</td>								
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";
    }
    $text .= "
		</tbody>
	</table>
</div>";
    # TAB IDENTITY
    $sql = "select * from dta_supplier_identity where kodeSupplier='$par[kodeSupplier]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $text .= "<div id=\"identity\" class=\"subcontent\" $dIdentity >
<table width=\"100%\">
	<tr>
		<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
			<p>
				<label class=\"l-input-small\">SIUP</label>
				<span class=\"field\">$r[siupIdentity]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">TDP</label>
				<span class=\"field\">$r[tdpIdentity]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">ID</label>
				<span class=\"field\">$r[idIdentity]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">NPWP</label>
				<span class=\"field\">$r[npwpIdentity]&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Alamat</label>
				<span class=\"field\">" . nl2br($r[alamatIdentity]) . "&nbsp;</span>
			</p>
		</td>
		<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
			<p>
				<label class=\"l-input-small\">File</label>
				<div class=\"field\">";
    $text .= empty($r[siupIdentity_file]) ? "" :
        "<a href=\"download.php?d=sup&f=siup.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[siupIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
    $text .= empty($r[tdpIdentity_file]) ? "" :
        "<a href=\"download.php?d=sup&f=tdp.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[tdpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
    $text .= empty($r[idIdentity_file]) ? "" :
        "<a href=\"download.php?d=sup&f=id.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[idIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
    $text .= empty($r[npwpIdentity_file]) ? "" :
        "<a href=\"download.php?d=sup&f=id.$r[kodeSupplier]\"><img src=\"" . getIcon($dFile . "/" . $r[npwpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "
					</div>
				</p>
			</td>
		</tr>
	</table>
</div>";
    # TAB CONTACT
    $text .= "<div id=\"contact\" class=\"subcontent\" $dContact >
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th style=\"min-width:175px;\">Posisi</th>
			<th style=\"min-width:175px;\">Nama</th>
			<th width=\"150\">Email</th>
			<th width=\"100\">HP</th>
			<th width=\"100\">Tlp Kantor</th>
		</tr>
	</thead>
	<tbody>";
    $sql = "select * from dta_supplier_contact where kodeSupplier='$par[kodeSupplier]' order by kodeContact";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
			<td>$no.</td>
			<td>$r[jabatanContact]</td>
			<td>$r[namaContact]</td>
			<td>$r[emailContact]</td>
			<td>$r[teleponContact]</td>
			<td>$r[kantorContact]</td>
		</td>
	</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
	<tr>
		<td>&nbsp;</td>								
		<td>&nbsp;</td>
		<td>&nbsp;</td>								
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>";
    }
    $text .= "
</tbody>
</table>
</div>";
    # TAB BANKING
    $text .= "
<div id=\"banking\" class=\"subcontent\" $dBanking >
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>
				<th>Nama Bank</th>
				<th width=\"150\">No Akun</th>							
				<th>Nama Akun</th>
			</tr>
		</thead>
		<tbody>";
    $sql = "select * from dta_supplier_bank t1 join mst_data t2 on (t1.kodeBank=t2.kodeData) where t1.kodeSupplier='$par[kodeSupplier]' order by t1.kodeBank";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "
				<tr>
					<td>$no.</td>
					<td>$r[namaBank]</td>
					<td>$r[rekeningBank]</td>
					<td>$r[pemilikBank]</td>
				</tr>";
        $no++;
    }
    if ($no == 1) {
        $text .= "
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";
    }
    $text .= "
		</tbody>
	</table>
</div>
</form>";
    return $text;
}

function getProduk()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    if (empty($par[kodeProduk])) $par[kodeProduk] = getField("select kodeProduk from dta_produk where statusProduk='t' order by namaProduk limit 1");
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>" . comboData("select * from dta_produk where statusProduk='t' order by namaProduk", "kodeProduk", "namaProduk", "par[kodeProduk]", "", $par[kodeProduk], "", "250px") . "</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
						<td>
							<input type=\"hidden\" name=\"par[mode]\" value=\"$par[mode]\">
							<input type=\"hidden\" name=\"par[kodeOpname]\" value=\"$par[kodeOpname]\">
							<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/>
						</td>
					</tr>
				</table>	
			</div>		
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th style=\"width:20px;\">No.</th>
					<th style=\"min-width:100px;\">Tipe</th>					
					<th style=\"min-width:300px;\">Produk</th>
					<th style=\"width:75px;\">Satuan</th>
					<th style=\"width:50px;\">Control</th>
				</tr>				
			</thead>
			<tbody>";
    $filter = "where kodeProduk='$par[kodeProduk]' and kodeKategori>0";
    if (!empty($par[filter]))
        $filter .= " and (			
				lower(tipeKategori) like '%" . strtolower($par[filter]) . "%'
				or lower(namaKategori) like '%" . strtolower($par[filter]) . "%'
				)";
    $satuanProduk = getField("select namaData from mst_data where kodeData='" . getField("select kodeSatuan from dta_produk where kodeProduk='$par[kodeProduk]'") . "'");
    $sql = "select * from dta_produk_kategori $filter order by kodeKategori";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        list($freeStok, $markingStok, $bookingStok, $kanibalStok, $cacatStok) = explode("\t", $arrJumlah["$r[kodeBarang]"]);
        $no++;
        $text .= "
					<tr>
						<td>$no.</td>								
						<td>$r[tipeKategori]</td>
						<td>$r[namaKategori]</td>
						<td>" . $satuanProduk . "</td>
						<td align=\"center\">
							<a href=\"#\" title=\"Select Data\" class=\"check\" onclick=\"setProduk('$r[kodeProduk]','$r[kodeKategori]', '" . getPar($par, "mode,kodeProduk,kodeKategori") . "');\"><span>Detail</span></a>
						</td>
					</tr>";
    }
    $text .= "
			</tbody>
		</table>			
	</div>
</div>";
    return $text;
}

function getJasa()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    if (empty($par[kodeProduk])) $par[kodeProduk] = getField("select kodeProduk from dta_jasa where statusProduk='t' order by namaProduk limit 1");
    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>" . comboData("select * from dta_jasa where statusProduk='t' order by namaProduk", "kodeProduk", "namaProduk", "par[kodeProduk]", "", $par[kodeProduk], "", "250px") . "</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
						<td>
							<input type=\"hidden\" name=\"par[mode]\" value=\"$par[mode]\">
							<input type=\"hidden\" name=\"par[kodeOpname]\" value=\"$par[kodeOpname]\">
							<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/>
						</td>
					</tr>
				</table>	
			</div>		
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th style=\"width:20px;\">No.</th>
					<th style=\"min-width:100px;\">Tipe</th>					
					<th style=\"min-width:300px;\">Produk</th>
					<th style=\"width:75px;\">Satuan</th>
					<th style=\"width:50px;\">Control</th>
				</tr>				
			</thead>
			<tbody>";
    $filter = "where kodeProduk='$par[kodeProduk]' and kodeKategori>0";
    if (!empty($par[filter]))
        $filter .= " and (			
				lower(tipeKategori) like '%" . strtolower($par[filter]) . "%'
				or lower(namaKategori) like '%" . strtolower($par[filter]) . "%'
				)";
    $satuanProduk = getField("select namaData from mst_data where kodeData='" . getField("select kodeSatuan from dta_produk where kodeProduk='$par[kodeProduk]'") . "'");
    $sql = "select * from dta_produk_kategori $filter order by kodeKategori";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        list($freeStok, $markingStok, $bookingStok, $kanibalStok, $cacatStok) = explode("\t", $arrJumlah["$r[kodeBarang]"]);
        $no++;
        $text .= "
					<tr>
						<td>$no.</td>								
						<td>$r[tipeKategori]</td>
						<td>$r[namaKategori]</td>
						<td>" . $satuanProduk . "</td>
						<td align=\"center\">
							<a href=\"#\" title=\"Select Data\" class=\"check\" onclick=\"setProduk('$r[kodeProduk]','$r[kodeKategori]', '" . getPar($par, "mode,kodeProduk,kodeKategori") . "');\"><span>Detail</span></a>
						</td>
					</tr>";
    }
    $text .= "
			</tbody>
		</table>			
	</div>
</div>";
    return $text;
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess, $fFile, $dFile, $cID;
    switch ($par[mode]) {
        case "cek":
            $text = cek();
            break;
        case "kta":
            $text = kota();
            break;
        case "subk":
            $text = subkat();
            break;
        case "lst":
            $text = lData();
            break;
        case "geo":
            $text = getField("select namaData from mst_data where kodeData='$par[kodeKota]'");
            break;
        case "delNote":
            if (isset($menuAccess[$s]["delete"])) $text = hapusNote(); else $text = lihat();
            break;
        case "editNote":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formNote() : ubahNote(); else $text = lihat();
            break;
        case "addNote":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formNote() : tambahNote(); else $text = lihat();
            break;
        case "delBank":
            if (isset($menuAccess[$s]["delete"])) $text = hapusBank(); else $text = lihat();
            break;
        case "editBank":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formBank() : ubahBank(); else $text = lihat();
            break;
        case "addBank":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formBank() : tambahBank(); else $text = lihat();
            break;
        case "delContact":
            if (isset($menuAccess[$s]["delete"])) $text = hapusContact(); else $text = lihat();
            break;
        case "editContact":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formContact() : ubahContact(); else $text = lihat();
            break;
        case "addContact":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formContact() : tambahContact(); else $text = lihat();
            break;
        case "getProduk":
            $text = getProduk();
            break;
        case "setProduk":
            $text = setProduk();
            break;
        case "delFProduct":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFProduct(); else $text = lihat();
            break;
        case "delProduct":
            if (isset($menuAccess[$s]["delete"])) $text = hapusProduct(); else $text = lihat();
            break;
        case "editProduct":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formProduct() : ubahProduct(); else $text = lihat();
            break;
        case "addProduct":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formProduct() : tambahProduct(); else $text = lihat();
            break;
        case "getjasa":
            $text = getJasa();
            break;
        case "setjasa":
            $text = setJasa();
            break;
        case "delFjasa":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFJasa(); else $text = lihat();
            break;
        case "deljasa":
            if (isset($menuAccess[$s]["delete"])) $text = hapusJasa(); else $text = lihat();
            break;
        case "editjasa":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formJasa() : ubahJasa(); else $text = lihat();
            break;
        case "addjasa":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formJasa() : tambahJasa(); else $text = lihat();
            break;
        case "delAddress":
            if (isset($menuAccess[$s]["delete"])) $text = hapusAddress(); else $text = lihat();
            break;
        case "editAddress":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAddress() : ubahAddress(); else $text = lihat();
            break;
        case "addAddress":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formAddress() : tambahAddress(); else $text = lihat();
            break;
        case "update":
            if (isset($menuAccess[$s]["edit"])) $text = ubah("update");
            break;
        case "delNpwp":
            if (isset($menuAccess[$s]["edit"])) $text = hapusNpwp(); else $text = lihat();
            break;
        case "delId":
            if (isset($menuAccess[$s]["edit"])) $text = hapusId(); else $text = lihat();
            break;
        case "delTdp":
            if (isset($menuAccess[$s]["edit"])) $text = hapusTdp(); else $text = lihat();
            break;
        case "delSiup":
            if (isset($menuAccess[$s]["edit"])) $text = hapusSiup(); else $text = lihat();
            break;
        case "delLogo":
            if (isset($menuAccess[$s]["edit"])) $text = hapusLogo(); else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
            break;
        case "add":
            $text = isset($menuAccess[$s]["add"]) ? tambah() : lihat();
            break;
        case "det":
            $text = detail();
            break;
        default:
            $sql = "select * from dta_supplier where namaSupplier='' and createBy='$cID'";
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                if (file_exists($fFile . $r[logoSupplier]) and $r[logoSupplier] != "") unlink($fFile . $r[logoSupplier]);
                $sql_ = "select * from dta_supplier_identity where kodeSupplier='$r[kodeSupplier]'";
                $res_ = db($sql_);
                $r_ = mysql_fetch_array($res_);
                if (file_exists($dFile . $r_[siupIdentity_file]) and $r_[siupIdentity_file] != "") unlink($dFile . $r_[siupIdentity_file]);
                if (file_exists($dFile . $r_[tdpIdentity_file]) and $r_[tdpIdentity_file] != "") unlink($dFile . $r_[tdpIdentity_file]);
                if (file_exists($dFile . $r_[idIdentity_file]) and $r_[idIdentity_file] != "") unlink($dFile . $r_[idIdentity_file]);
                if (file_exists($dFile . $r_[npwpIdentity_file]) and $r_[npwpIdentity_file] != "") unlink($dFile . $r_[npwpIdentity_file]);
                db("delete from dta_supplier where kodeSupplier='$r[kodeSupplier]'");
                db("delete from dta_supplier_address where kodeSupplier='$r[kodeSupplier]'");
                db("delete from dta_supplier_produk where kodeSupplier='$r[kodeSupplier]'");
                db("delete from dta_supplier_identity where kodeSupplier='$r[kodeSupplier]'");
                db("delete from dta_supplier_contact where kodeSupplier='$r[kodeSupplier]'");
                db("delete from dta_supplier_bank where kodeSupplier='$r[kodeSupplier]'");
            }
            $text = lihat();
            break;
    }
    return $text;
}

?>