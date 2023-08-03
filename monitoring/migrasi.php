<?php
include "global.php";

include 'plugins/PHPExcel/IOFactory.php';
$path_import = "migrasi/";

function migrasi_proyek()
{
    global $path_import;

    $file_name = "proyek.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();

    $masterSBU = arrayQuery("select namaData, kodeData from mst_data where kodeCategory = 'KSBU'");

    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":M" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $project_no = trim($r[1]);
        $project_name = trim($r[2]);
        $no_kontrak = trim($r[7]);
        $wbs = trim($r[8]);
        $sbu = trim($r[5]);
        $sbu = $masterSBU[$sbu];
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO
                  `proyek_data`
                SET
                  `proyek` = '$project_name',
                  `nomor` = '$project_no',
                  `kontrak` = '$no_kontrak',
                  `wbs` = '$wbs',
                  `keterangan` = '',
                  `sbu` = '$sbu',
                  `customer` = '',
                  `status` = '1',
                  `created_at` = '$created_at',
                  `created_by` = '$created_by'";

        if (db($sql)) {
            echo "Migrasi Proyek - $r[0] - $project_no -$project_name Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - $project_no -$project_name Gagal : $sql<br />";
        }
    }
}

function migrasi_gl_account()
{
    global $path_import;

    $file_name = "glacc.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;



        $kode = trim($r[2]);
        $grup = trim($r[9]);
        $judul = trim($r[3]);
        $keterangan = trim($r[4]);
        $cocd = trim($r[5]);
        $currency = trim($r[6]);
        $level = trim($r[17]);
        $fbi = trim($r[18]);
        $fst = trim($r[19]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

      //  $check = getField("SELECT id from account_gl where grup = '$grup'");
//        if (empty($check))
//        {
            $sql = "INSERT INTO
                  `account_gl`
                SET
                  `kode` = '$kode',
                  `grup` = '$grup',
                  `judul` = '$judul',
                  `keterangan` = '$keterangan',
                  `cocd` = '$cocd',
                  `currency` = '$currency',
                  `level` = '$level',
                  `fbi` = '$fbi',
                  `fst` = '$fst',
                  `status` = '1',
                  `created_at` = '$created_at',
                  `created_by` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Proyek - $r[0] - $kode -$judul Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - $kode -$judul Gagal : $sql<br />";
            }
 //       }


    }
}

function migrasi_vendor()
{
    global $path_import;

    $file_name = "vendorsheet1.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $namaSupplier = trim($r[1]);
        $namaRekening = trim($r[2]);
        //  $address = trim($r[2]);
        //  $pic = trim($r[3]);
        //  $notelp = trim($r[4]);
        $norek = trim($r[3]);
        $bank = trim($r[4]);
        //$npwp = trim($r[7]);
        //$area = trim($r[8]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $sql = "INSERT INTO
                  `dta_supplier`
                SET
                  `tipe` = 'supplier',
                  `namaSupplier` = '$namaSupplier',
                  `statusSupplier` = 't',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

        if (db($sql)) {
            echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Gagal : $sql<br />";
        }

        $kodeSupplier = getField("select kodeSupplier from dta_supplier where namaSupplier = '". $namaSupplier ."'");

        $sql2 = "INSERT INTO
                  `dta_supplier_bank`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaBank` = '$bank',
                  `rekeningBank` = '$norek',
                  `pemilikBank` = '$namaRekening',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

        if (db($sql2)) {
            echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Gagal : $sql2<br />";
        }

        $sql3 = "INSERT INTO
                  `dta_supplier_address`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `alamatAddress` = '$address',
                  `teleponAddress` = '$notelp',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

        if (db($sql3)) {
            echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Gagal : $sql3<br />";
        }

        $sql4 = "INSERT INTO
                  `dta_supplier_identity`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `npwpIdentity` = '$npwp',
                  `alamatIdentity` = '$area',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

        if (db($sql4)) {
            echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Gagal : $sql4<br />";
        }

        $sql5 = "INSERT INTO
                  `dta_supplier_contact`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaContact` = '$pic',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

        if (db($sql5)) {
            echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Gagal : $sql5<br />";
        }
    }
}


function migrasi_vendor1()
{
    global $path_import;

    $file_name = "vendorsheet1.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $namaSupplier = trim($r[4]);
        $namaRekening = trim($r[2]);
        $address = trim($r[30]);
        $pic = trim($r[42]);
        $notelp = trim($r[28]);
        $norek = trim($r[19]);
        $bank = trim($r[18]);
        $npwp = trim($r[10]);
        $email = trim($r[29]);

        //$area = trim($r[8]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $ceknama = getField("select kodeSupplier from dta_supplier where namaSupplier = '$namaSupplier'");

        if (empty($ceknama)){
            $sql = "INSERT INTO
                  `dta_supplier`
                SET
                  `tipe` = 'supplier',
                  `namaSupplier` = '$namaSupplier',
                  `statusSupplier` = 't',
                  `emailSupplier` = '$email',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Gagal : $sql<br />";
            }

            $kodeSupplier = getField("select kodeSupplier from dta_supplier where namaSupplier = '". $namaSupplier ."'");

            $sql2 = "INSERT INTO
                  `dta_supplier_bank`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaBank` = '$bank',
                  `rekeningBank` = '$norek',
                  `pemilikBank` = '$namaRekening',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql2)) {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Gagal : $sql2<br />";
            }

            $sql3 = "INSERT INTO
                  `dta_supplier_address`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `alamatAddress` = '$address',
                  `teleponAddress` = '$notelp',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql3)) {
                echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Gagal : $sql3<br />";
            }

            $sql4 = "INSERT INTO
                  `dta_supplier_identity`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `npwpIdentity` = '$npwp',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql4)) {
                echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Gagal : $sql4<br />";
            }

            $sql5 = "INSERT INTO
                  `dta_supplier_contact`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaContact` = '$pic',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql5)) {
                echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Gagal : $sql5<br />";
            }
        }


    }
}

function migrasi_vendor2()
{
    global $path_import;

    $file_name = "vendorsheet2.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $namaSupplier = trim($r[1]);
        $namaRekening = trim($r[3]);
        $address = trim($r[2]);
        $pic = trim($r[3]);
        $notelp = trim($r[4]);
        $norek = trim($r[6]);
        $bank = trim($r[5]);
        $npwp = trim($r[7]);

        //$area = trim($r[8]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $ceknama = getField("select kodeSupplier from dta_supplier where namaSupplier = '$namaSupplier'");

        if (empty($ceknama)){
            $sql = "INSERT INTO
                  `dta_supplier`
                SET
                  `tipe` = 'supplier',
                  `namaSupplier` = '$namaSupplier',
                  `statusSupplier` = 't',
                  `alamatSupplier` = '$address',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Gagal : $sql<br />";
            }

            $kodeSupplier = getField("select kodeSupplier from dta_supplier where namaSupplier = '". $namaSupplier ."'");

            $sql2 = "INSERT INTO
                  `dta_supplier_bank`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaBank` = '$bank',
                  `rekeningBank` = '$norek',
                  `pemilikBank` = '$namaRekening',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql2)) {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Gagal : $sql2<br />";
            }

            $sql3 = "INSERT INTO
                  `dta_supplier_address`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `alamatAddress` = '$address',
                  `teleponAddress` = '$notelp',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql3)) {
                echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'alamat' -$namaSupplier Gagal : $sql3<br />";
            }

            $sql4 = "INSERT INTO
                  `dta_supplier_identity`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `npwpIdentity` = '$npwp',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql4)) {
                echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'identitas' -$namaSupplier Gagal : $sql4<br />";
            }

            $sql5 = "INSERT INTO
                  `dta_supplier_contact`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaContact` = '$pic',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql5)) {
                echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'kontak' -$namaSupplier Gagal : $sql5<br />";
            }

        }


    }
}

function migrasi_vendor3()
{
    global $path_import;

    $file_name = "vendorsheet3.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $namaSupplier = trim($r[1]);
        $namaRekening = trim($r[2]);
        $norek = trim($r[3]);
        $bank = trim($r[4]);

        //$area = trim($r[8]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $ceknama = getField("select kodeSupplier from dta_supplier where namaSupplier = '$namaSupplier'");

        if (empty($ceknama)){
            $sql = "INSERT INTO
                  `dta_supplier`
                SET
                  `tipe` = 'supplier',
                  `namaSupplier` = '$namaSupplier',
                  `statusSupplier` = 't',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'supplier' -$namaSupplier Gagal : $sql<br />";
            }

            $kodeSupplier = getField("select kodeSupplier from dta_supplier where namaSupplier = '". $namaSupplier ."'");

            $sql2 = "INSERT INTO
                  `dta_supplier_bank`
                SET
                  `kodeSupplier` = '$kodeSupplier',
                  `namaBank` = '$bank',
                  `rekeningBank` = '$norek',
                  `pemilikBank` = '$namaRekening',
                  `createTime` = '$created_at',
                  `createBy` = '$created_by'";

            if (db($sql2)) {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Berhasil <br />";
            } else {
                echo "Migrasi Proyek - $r[0] - 'bank' -$namaSupplier Gagal : $sql2<br />";
            }

        }


    }
}

function migrasi_pegawai()
{
    global $path_import;

    $file_name = "pegawai.xls";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $nama = trim($r[3]);
        $namaRekening = trim($r[7]);
        $alamat = trim($r[6]);
        $jabatan = trim($r[4]);
        $norek = trim($r[9]);
        $bank = strtoupper(trim($r[8]));
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $ceknama = getField("select id from pegawai_data where nama = '$nama'");

        if (empty($ceknama)){
            $kodeBank = getField("select kodeData from mst_data where kodeCategory = 'BNK' and namaData = '$bank'");
            if (empty($kodeBank)){

                db("INSERT INTO `mst_data`
                            SET    
                            `kodeCategory` = 'BNK',
                            `namaData` = '$bank',
                            `statusData` = 't',
                            `createBy` = 'migrasi',
                            `createTime` = '$created_at'");

                $kodeBank = getField("select kodeData from mst_data where kodeCategory = 'BNK' and createBy = 'migrasi' order by kodeData desc limit 1");

            }

            $sql = "INSERT INTO
                  `pegawai_data`
                SET
                  `nama` = '$nama',
                  `jabatan` = '$jabatan',
                  `alamat` = '$alamat',
                  `norek` = '$norek',
                  `nama_pemilik` = '$namaRekening',
                  `bank` = '$kodeBank',
                  `created_at` = '$created_at',
                  `created_by` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Pegawai - $r[0] - $nama : Berhasil <br />";
            } else {
                echo "Migrasi Pegawai - $r[0] - $nama : Gagal : $sql<br />";
            }
        }


    }
}

function migrasi_pegawai2()
{
    global $path_import;

    $file_name = "pegawai2.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $nama = trim($r[2]);
        $nik = trim($r[1]);
        $namaRekening = trim($r[15]);
        $alamat = trim($r[6]);
        $jabatan = trim($r[5]);
        $norek = trim($r[13]);
        $nohp = trim($r[11]);
        $bank = strtoupper(trim($r[14]));
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $ceknama = getField("select id from pegawai_data where nama = '$nama'");

        if (empty($ceknama)){
            $kodeBank = getField("select kodeData from mst_data where kodeCategory = 'BNK' and namaData = '$bank'");
            if (empty($kodeBank)){

                db("INSERT INTO `mst_data`
                            SET    
                            `kodeCategory` = 'BNK',
                            `namaData` = '$bank',
                            `statusData` = 't',
                            `createBy` = 'migrasi',
                            `createTime` = '$created_at'");

                $kodeBank = getField("select kodeData from mst_data where kodeCategory = 'BNK' and createBy = 'migrasi' order by kodeData desc limit 1");

            }

            $sql = "INSERT INTO
                  `pegawai_data`
                SET
                  `nama` = '$nama',
                  `nik` = '$nik',
                  `nohp` = '$nohp',
                  `jabatan` = '$jabatan',
                  `alamat` = '$alamat',
                  `norek` = '$norek',
                  `nama_pemilik` = '$namaRekening',
                  `bank` = '$kodeBank',
                  `created_at` = '$created_at',
                  `created_by` = '$created_by'";

            if (db($sql)) {
                echo "Migrasi Pegawai - $r[0] - $nama : Berhasil <br />";
            } else {
                echo "Migrasi Pegawai - $r[0] - $nama : Gagal : $sql<br />";
            }
        }

    }
}

function migrasi_proyek2()
{
    global $path_import;

    $file_name = "proyek.xlsx";

    $file_log = "log.txt";

    $log = fopen($file_log, "a+");

    try {
        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);
    } catch (Exception $e) {
        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . " Terjadi kesalahan sistem: " . $e->getMessage() . "\n\n");
        fclose($log);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name \n\n",
            'return' => null
        ]);
    }

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();


    for ($n = 1; $n <= $sheet_size_y; $n++) {
        $r = $sheet->rangeToArray("A" . $n . ":Z" . $n, null, true, true);
        $r = $r[0];

        if (!is_numeric($r[0])) continue;

        $project_no = trim($r[1]);
        $project_name = trim($r[2]);
        $no_kontrak = trim($r[7]);
        $wbs = trim($r[8]);
        $cost = trim($r[3]);
        $profit = trim($r[4]);
        $customer = trim($r[6]);
        $created_by = '1';
        $created_at = date('Y-m-d H:i:s');

        $cekcc = getField("select id from costcenter_data where cost = '$cost' and profit = '$profit'");
        $cekcustomer = getField("select kodeSupplier from dta_supplier where namaSupplier = '$customer' and tipe = 'customer'");

        $sql = "INSERT INTO
                  `proyek_data`
                SET
                  `proyek` = '$project_name',
                  `nomor` = '$project_no',
                  `kontrak` = '$no_kontrak',
                  `wbs` = '$wbs',
                  `keterangan` = '',
                  `costcenter` = '$cekcc',
                  `customer` = '$cekcustomer',
                  `status` = '1',
                  `created_at` = '$created_at',
                  `created_by` = '$created_by'";

        if (db($sql)) {
            echo "Migrasi Proyek - $r[0] - $project_no -$project_name Berhasil <br />";
        } else {
            echo "Migrasi Proyek - $r[0] - $project_no -$project_name Gagal : $sql<br />";
        }
    }
}

migrasi_proyek2();