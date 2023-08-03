<?php

use Illuminate\Support\Carbon;

define('DIR_IMG_NEWS', "files/news/");
define('DIR_IMG_EVENT', "files/events/");
define('DIR_IMG_FORUM', "files/forums/");

$arr_status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
$arr_gender = [1 => 'Laki - Laki', 0 => 'Perempuan'];
$arr_status_image = [1 => "<img src='" . APP_URL . "/styles/images/t.png' title='Aktif'>", 0 => "<img src='" . APP_URL . "/styles/images/f.png' title='Tidak Aktif'>"];
$arr_status_image2 = ['t' => "<img src='" . APP_URL . "/styles/images/t.png' title='Aktif'>", 'f' => "<img src='" . APP_URL . "/styles/images/f.png' title='Tidak Aktif'>"];

$arr_text_apprv = [
    't' => "Diterima",
    'f' => "Ditolak",
    'r' => "Diperbaiki",
    'p' => "Pending"
];

$arr_status_apprv = [
    't' => "<img src='" . APP_URL . "/styles/images/t.png' title='Diterima'>",
    'f' => "<img src='" . APP_URL . "/styles/images/f.png' title='Ditolak'>",
    'r' => "<img src='" . APP_URL . "/styles/images/r.png' title='Diperbaiki'>",
    'p' => "<img src='" . APP_URL . "/styles/images/s.png' title='Pending'>"
];

$path_import = "files/imports/";
$path_export = "files/exports/";


function user()
{
    global $cID;

    return getRow("SELECT * FROM `app_user` WHERE `id` = '$cID'");
}

// QUERY HELPER
function getRow($sql)
{

    $res = db($sql);

    return $res ? mysql_fetch_assoc($res) : false;
}

function getRows($sql, $index = "")
{

    $res = db($sql);

    $_index = 0;
    $result = [];

    while ($row = mysql_fetch_assoc($res)) {

        $result[!empty($index) ? $row[$index] : $_index] = $row;

        $_index++;
    }

    return $result;
}

function getMaster($column, $code)
{

    return getField("SELECT `$column` FROM `mst_data` WHERE `kodeData` = '$code'") ?: "-";
}

// END QUERY HELPER


// STRING HELPER

function prettyText($text)
{
    return empty($text) ? "" : ucwords(strtolower($text));
}

// END STRING HELPER

// UPLOAD HELPER

function customeUpload($file, $file_rename = "", $directory)
{

    if (!empty($file['tmp_name'])) {

        if (!is_dir($directory)) mkdir($directory, 0755, true);

        $file_temp = $file['tmp_name'];
        $file_name = $file['name'];

        $extension = explode(".", $file_name);
        $file_renamed = empty($file_rename) ? $file_name : $file_rename . "." . end($extension);

        $file_renamed = str_replace("/", ".", $file_renamed);

        move_uploaded_file($file_temp, $directory . "/" . $file_renamed);

        return $file_renamed;
    }

    return false;
}

// END UPLOAD HELPER

function urlViewer($target)
{
    $url = str_replace("https://", "http://", APP_URL);

    $extension = pathinfo($target, PATHINFO_EXTENSION);
    $native = [
        "jpg",
        "jpeg",
        "png",
        "gif",
    ];
    $document = [
        "pdf",
        "doc",
        "docx",
        "xls",
        "xlsx",
        "ppt",
        "pptx"
    ];

    if (in_array($extension, $native)) {
        return $url . $target;
    }

    if (in_array($extension, $document)) {
        return "https://docs.google.com/viewer?embedded=true&url={$url}{$target}";
    }

    return $target;
}

function customeUploadFile($file, $file_rename = "", $directory = "", $last_file = null)
{

    if (!empty($file['tmp_name'])) {

        if (!is_dir($directory)) mkdir($directory, 0755, true);

        $file_temp = $file['tmp_name'];
        $file_name = $file['name'];

        $extension = explode(".", $file_name);
        $file_renamed = empty($file_rename) ? $file_name : $file_rename . "." . end($extension);

        $file_renamed = str_replace("/", ".", $file_renamed);

        move_uploaded_file($file_temp, $directory . "/" . $file_renamed);

        return $file_renamed;
    }

    return $last_file;
}

function uploadXLS()
{
    global $s, $arrTitle, $path_import, $file_log;

    $file_name = $_FILES['file_import']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!empty($file_ext) && ($file_ext == "xls" || $file_ext == "xlsx")) {

        // static name, dont extravagant
        $file_name = customeUploadFile($_FILES['file_import'], md5($arrTitle[$s]), $path_import);

        if (empty($file_name)) {

            return json_encode([
                'message' => "Terjadi masalah file!",
                'result' => null
            ]);
        }

        if (file_exists($file_log))
            unlink($file_log);

        $log = fopen($file_log, "a+");

        fwrite($log, "UPLOADED \t: " . date("d/m/Y H:i:s") . "\n");
        fclose($log);

        return decodeXLS($file_name);
    }

    return json_encode([
        'message' => "file harus dalam format .xls atau .xlsx",
        'result' => null
    ]);
}

function decodeXLS($file_name)
{
    global $path_import, $file_log;

    require_once('plugins/PHPExcel/IOFactory.php');

    try {

        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);

    } catch (Exception $e) {

        file_put_contents($file_log, "ERROR \t: " . date("d/m/Y H:i:s") . "Terjadi kesalahan sistem: " . $e->getMessage() . "\n", FILE_APPEND);

        if (file_exists($path_import . $file_name))
            unlink($path_import . $file_name);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name",
            'return' => null
        ]);
    }

    if (file_exists($path_import . $file_name))
        unlink($path_import . $file_name);

    file_put_contents($file_log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n", FILE_APPEND);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();

    $data = [];

    for ($n = 1; $n <= $sheet_size_y; $n++) {

        // AZ is last column record (conditional by data length)
        $row = $sheet->rangeToArray("A" . $n . ":AZ" . $n, null, true, true);
        $row = $row[0];

        if (!is_numeric($row[0])) continue;

        array_push($data, $row);
    }

    return json_encode([
        'message' => "Decoded",
        'result' => $data
    ]);
}

function carbon($datetime): Carbon
{
    return $datetime ? Carbon::create($datetime) : Carbon::now();
}