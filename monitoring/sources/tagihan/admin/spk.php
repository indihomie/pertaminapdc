<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dirFile = "files/tagihan_spk/";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $arrTitle;

    switch ($par[mode]) {

        default:
            $text = lihat();
            break;

        case "lst":
            $text = lData();
            break;

        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpan();
            else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan();
            else $text = lihat();
            break;

        case "delete":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;

        case "delFile":
            if (isset($menuAccess[$s]["delete"])) $text = delFile(); else $text = lihat();
            break;

        case "approval":
            if (isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form_approval() : simpan_approval();
            else $text = lihat();
            break;

        case "detailSPK":
            $text = view_permohonan($arrTitle[$s], $par['id_spk'], $par['pop_up']);
            break;

        case "getProyek":
            $text = getProyek();
            break;

        case "getPemohon":
            $text = getPemohon();
            break;
            
        case "getFilter":
            $text = getFilter();
            break;
    }

    return $text;
}

function getProyek()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where costcenter = '" . $par['id_cc'] . "'");
    echo json_encode($getData);
}

function getPemohon()
{
    global $par;

    $idUnit = getField("SELECT sbu from costcenter_data where id = '".$par['id_cc']."'");
    $getData = getRows("SELECT * from pegawai_data where unit = '" . $idUnit . "'");
    echo json_encode($getData);
}

function getFilter()
{
    global $par;

    $getData = getRows("SELECT * from proyek_data where sbu = '" . $par['kodeData'] . "'");
    echo json_encode($getData);
}

function delFile()
{
    global $par, $dirFile;

    $file = getField("select file_spk from tagihan_spk where id = '".$par['id']."'");
    db("update tagihan_spk set file_spk = '' where id = '".$par['id']."'");
    unlink($dirFile.$file);

    echo "<script>closeBox(); alert('File berhasil dihapus!'); reloadPage(); </script>";
}

function xls()
{
    global $par, $arrTitle, $s, $arrParam;

    $direktori = "files/export/";
    $namaFile = ucwords(strtolower($arrTitle[$s])) . ".xls";
    $judul = $arrTitle[$s];

    $field = [
        "NO",
        "Tanggal",
        "Judul - Nomor",
        "Pemohon",
        "Nilai",
        "APPROVAL"
    ];

    $where = " WHERE id_jenis = '".$arrParam[$s]."' ";

    if (!empty($par['fSearch'])) {
        $where .= " and (     
            lower(a.nomor) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
            or
            lower(a.judul) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
            or
            lower(b.namaSupplier) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
            or
            lower(c.nama) like '%" . mysql_real_escape_string(strtolower($par['fSearch'])) . "%'
            )";
    }



    if (!empty($par['combo1'])) $where .= " and month(a.tanggal) = '" . $par['combo1'] . "'";
    if (!empty($par['combo2'])) $where .= " and year(a.tanggal) = '" . $par['combo2'] . "'";
    if (!empty($par['combo3'])) $where .= " and id_supplier = '$par[combo3]'";
    //if (!empty($par['combo4'])) $where .= " and nilai BETWEEN = '$par[combo4]'"; //agregat (belum selesai)
    if (!empty($par['combo5'])){
        $where .= " and id_cc = '$par[combo5]'";
        
        if (!empty($par['combo6'])){
            $where .= " and id_proyek = '$par[combo6]'";
        }
    }
        
    
    

    $order = "id DESC";

    $sql = "SELECT a.*, b.namaSupplier, c.nama
            from tagihan_spk as a
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS c ON (c.id = a.id_supplier)
            $where order by $order";

    $res = queryAssoc($sql);

    $no = 0;
    foreach ($res as $r) {

        $no++;

        $appr = "Menunggu Persetujuan";
        if ($r["approve_status"] == "t") $appr = "Setuju";
        if ($r["approve_status"] == "f") $appr = "Tolak";
        if ($r["approve_status"] == "p") $appr = "Pending";

        $pemohon = ($arrParam[$s] == '1048') ? "namaSupplier" : "nama";

        $data[] = [
            $no . "\t center",
            getTanggal($r["tanggal"]) . "\t center",
            $r["judul"] . "\n - \n" . $r["nomor"] . " \t left",
            $r["$pemohon"] . "\t left",
            getAngka($r["total"]) . "\t right",
            $appr . "\t center"
        ];
    }

    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}

function sync_doc($idSpk, $idTermin, $jenis)
{
    global $cID, $arrParam, $s;

    $dokumen = getRows("SELECT * FROM dokumen_pendukung WHERE kategori = '$arrParam[$s]' AND jenis = '$jenis' ORDER BY urut asc");
    foreach ($dokumen as $dk) {
        $sql = "INSERT
                    `tagihan_syarat`
                SET
                    `id_spk` = '".$idSpk."',
                    `id_termin` = '".$idTermin."',
                    `judul` = '".$dk["dokumen"]."',
                    `jenis_dokumen` = '".$dk["lembar"]."',
                    `mandatory` = '".$dk["mandatory"]."',
                    `catatan` = '".$dk["keterangan"]."',
                    `created_at` = now(),
                    `created_by` = '".$cID."'
                ";
        db($sql);
    }

}

function simpan_approval()
{
    global $s, $inp, $par, $cID, $arrParam;

    $par_uang_muka = getField("select nilaiParameter from app_parameter where namaParameter = 'uang_muka'");
    $par_retensi = getField("select nilaiParameter from app_parameter where namaParameter = 'retensi'");
    $par_termin = getField("select nilaiParameter from app_parameter where namaParameter = 'termin'");
    $par_reimbursement = getField("select nilaiParameter from app_parameter where namaParameter = 'reimbursement'");
    $par_dinas = getField("select nilaiParameter from app_parameter where namaParameter = 'dinas'");

    $sql = "UPDATE
              `tagihan_spk`
            SET
              `approve_date` = '" . setTanggal($inp["approve_date"]) . "',
              `approve_by` = '" . $cID . "',
              `approve_desc` = '" . $inp["approve_desc"] . "',
              `approve_status` = '" . $inp["approve_status"] . "'
            WHERE `id` = '" . $par["id"] . "'
            ";
    db($sql);

    if ($inp["approve_status"] == 't'){

        $spk = getRow("SELECT * FROM tagihan_spk WHERE id = '$par[id]'");


        if ($arrParam[$s] != '1048'){ // reiburstment or dinas

            if ($arrParam[$s] == '1049') $parPlusDay = $par_reimbursement;
            if ($arrParam[$s] == '1050') $parPlusDay = $par_dinas;

            $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi] ) ) . "+$parPlusDay day" ) );

            $sql = "INSERT
                      `tagihan_termin`
                    SET
                      `id_spk` = '" . $par["id"] . "',
                      `termin` = 'Pembayaran',
                      `persen` = '100',
                      `nilai` = '" . $spk["nilai"] . "',
                      `nilai_ppn` = '" . $spk["nilai_ppn"] . "',
                      `nilai_plus_ppn` = '" . $spk["nilai_plus_ppn"] . "',
                      `nilai_total` = '" . $spk["total"] . "',
                      `target` = '" . $target . "',
                      `created_at` = now(),
                      `created_by` = '" . $cID . "'
                    ";
            db($sql);

            $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
            sync_doc($spk[id], $idTermin, $spk[dokumen_pendukung]);

            db("update tagihan_spk set persen_termin = '100' where id = '".$par["id"]."'");

        } else { // vendor

            if ($spk['tahapan_tagihan'] == 'FP') {

                $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi] ) ) . "+$par_uang_muka day" ) );

                $sql = "INSERT
                          `tagihan_termin`
                        SET
                          `id_spk` = '" . $par["id"] . "',
                          `termin` = 'Pembayaran',
                          `persen` = '100',
                          `nilai` = '" . $spk["nilai"] . "',
                          `nilai_ppn` = '" . $spk["nilai_ppn"] . "',
                          `nilai_plus_ppn` = '" . $spk["nilai_plus_ppn"] . "',
                          `nilai_total` = '" . $spk["total"] . "',
                          `target` = '" . $target . "',
                          `created_at` = now(),
                          `created_by` = '" . $cID . "'
                        ";
                db($sql);

                $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                sync_doc($spk[id], $idTermin, $spk[dokumen_pendukung]);

                db("update tagihan_spk set persen_termin = '100' where id = '".$par["id"]."'");

            }

            if ($spk['tahapan_tagihan'] == 'TR') {

                $persen_uang_muka = 0;

                $target = $spk[target_realisasi];

                if ($spk['uang_muka'] == 1) {

                    //$incBulan++;

                    $persen_uang_muka = $spk['nilai_uang_muka'];
                    $nilai_uang_muka = $spk["nilai"] * ($persen_uang_muka / 100);
                    $nilai_uang_muka = round($nilai_uang_muka);
                    $ppn_uang_muka = $nilai_uang_muka * ($spk["ppn"] / 100);
                    $ppn_uang_muka = round($ppn_uang_muka);
                    $total_uang_muka = $nilai_uang_muka + $ppn_uang_muka;
                    $total_uang_muka = round($total_uang_muka);

                    $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi] ) ) . "+$par_uang_muka day" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Uang Muka',
                              `persen` = '$persen_uang_muka',
                              `nilai` = '" . $nilai_uang_muka . "',
                              `nilai_ppn` = '" . $ppn_uang_muka . "',
                              `nilai_plus_ppn` = '$total_uang_muka',
                              `nilai_total` = '" . $total_uang_muka . "',
                              `target` = '" . $target . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, 1129);

                }

                $persen_retensi = 0;
                if ($spk['retensi'] == 1) {

                    $persen_retensi = $spk['nilai_retensi'];
                    $nilai_retensi = $spk["nilai"] * ($persen_retensi / 100);
                    $nilai_retensi = round($nilai_retensi);
                    $ppn_retensi = $nilai_retensi * ($spk["ppn"] / 100);
                    $ppn_retensi = round($ppn_retensi);
                    $total_retensi = $nilai_retensi + $ppn_retensi;
                    $total_retensi = round($total_retensi);

                }

                $sisa_persen = 100 - $persen_uang_muka - $persen_retensi;
                $persen_tahapan = $sisa_persen / $spk['nilai_tahapan_termin'];
                $persen_tahapan = round($persen_tahapan);

                //$incBulan = 0;

                for ($i = 1; $i <= $spk['nilai_tahapan_termin']; $i++)
                {
                    //$incBulan++;

                    $nilai_tahapan = $spk["nilai"] * ($persen_tahapan / 100);
                    $nilai_tahapan = round($nilai_tahapan);
                    $ppn_tahapan = $nilai_tahapan * ($spk["ppn"] / 100);
                    $ppn_tahapan = round($ppn_tahapan);
                    $total_tahapan = $nilai_tahapan + $ppn_tahapan;
                    $total_tahapan = round($total_tahapan);

                    $targetTermin = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $target ) ) . "+".($i)." month" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Termin $i',
                              `persen` = '$persen_tahapan',
                              `nilai` = '" . $nilai_tahapan . "',
                              `nilai_ppn` = '" . $ppn_tahapan . "',
                              `nilai_plus_ppn` = '$total_tahapan',
                              `nilai_total` = '" . $total_tahapan . "',
                              `target` = '" . $targetTermin . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, $spk[dokumen_pendukung]);
                }

                if ($spk['retensi'] == 1) {

                    $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi_selesai] ) ) . "+".($par_retensi)." day" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Retensi',
                              `persen` = '$persen_retensi',
                              `nilai` = '" . $nilai_retensi . "',
                              `nilai_ppn` = '" . $ppn_retensi . "',
                              `nilai_plus_ppn` = '$total_retensi',
                              `nilai_total` = '" . $total_retensi . "',
                              `target` = '" . $target . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, 1130);

                }

                git branch -M main   $updatePersen = getField("SELECT SUM(persen) FROM tagihan_termin WHERE id_spk = '".$par["id"]."'");
                db("update tagihan_spk set persen_termin = '$updatePersen' where id = '".$par["id"]."'");

            }

            if ($spk['tahapan_tagihan'] == 'BT') {

                $target = $spk[target_realisasi];

                if ($spk['nilai_tahapan_bertahap'] == 'BL') {

                    $d1 = new DateTime($spk['target_realisasi']);
                    $d2 = new DateTime($spk['target_realisasi_selesai']);

                    $interval = $d2->diff($d1);

                    $jumlahTermin = $interval->format('%m months');

                }
                if ($spk['nilai_tahapan_bertahap'] == 'TR') $jumlahTermin = 3;
                if ($spk['nilai_tahapan_bertahap'] == 'SM') $jumlahTermin = 6;

                $persen_uang_muka = 0;
                //$incBulan = 0;
                if ($spk['uang_muka'] == 1) {

                    //$incBulan++;

                    $persen_uang_muka = $spk['nilai_uang_muka'];
                    $nilai_uang_muka = $spk["nilai"] * ($persen_uang_muka / 100);
                    $nilai_uang_muka = round($nilai_uang_muka);
                    $ppn_uang_muka = $nilai_uang_muka * ($spk["ppn"] / 100);
                    $ppn_uang_muka = round($ppn_uang_muka);
                    $total_uang_muka = $nilai_uang_muka + $ppn_uang_muka;
                    $total_uang_muka = round($total_uang_muka);

                    $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi] ) ) . "+$par_uang_muka day" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Uang Muka',
                              `persen` = '$persen_uang_muka',
                              `nilai` = '" . $nilai_uang_muka . "',
                              `nilai_ppn` = '" . $ppn_uang_muka . "',
                              `nilai_plus_ppn` = '$total_uang_muka',
                              `nilai_total` = '" . $total_uang_muka . "',
                              `target` = '" . $target . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, 1129);

                }

                $persen_retensi = 0;
                if ($spk['retensi'] == 1) {

                    $persen_retensi = $spk['nilai_retensi'];
                    $nilai_retensi = $spk["nilai"] * ($persen_retensi / 100);
                    $nilai_retensi = round($nilai_retensi);
                    $ppn_retensi = $nilai_retensi * ($spk["ppn"] / 100);
                    $ppn_retensi = round($ppn_retensi);
                    $total_retensi = $nilai_retensi + $ppn_retensi;
                    $total_retensi = round($total_retensi);

                }

                $sisa_persen = 100 - $persen_uang_muka - $persen_retensi;
                $persen_tahapan = $sisa_persen / $jumlahTermin;
                $persen_tahapan = round($persen_tahapan);

                for ($i = 1; $i <= $jumlahTermin; $i++)
                {
                    //$incBulan++;

                    $nilai_tahapan = $spk["nilai"] * ($persen_tahapan / 100);
                    $nilai_tahapan = round($nilai_tahapan);
                    $ppn_tahapan = $nilai_tahapan * ($spk["ppn"] / 100);
                    $ppn_tahapan = round($ppn_tahapan);
                    $total_tahapan = $nilai_tahapan + $ppn_tahapan;
                    $total_tahapan = round($total_tahapan);

                    $targetTermin = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $target ) ) . "+".($i)." month" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Termin $i',
                              `persen` = '$persen_tahapan',
                              `nilai` = '" . $nilai_tahapan . "',
                              `nilai_ppn` = '" . $ppn_tahapan . "',
                              `nilai_plus_ppn` = '$total_tahapan',
                              `nilai_total` = '" . $total_tahapan . "',
                              `target` = '" . $targetTermin . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, $spk[dokumen_pendukung]);
                }

                if ($spk['retensi'] == 1) {

                    $target = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( $spk[target_realisasi_selesai] ) ) . "+".($par_retensi)." day" ) );

                    $sql = "INSERT
                              `tagihan_termin`
                            SET
                              `id_spk` = '" . $par["id"] . "',
                              `termin` = 'Retensi',
                              `persen` = '$persen_retensi',
                              `nilai` = '" . $nilai_retensi . "',
                              `nilai_ppn` = '" . $ppn_retensi . "',
                              `nilai_plus_ppn` = '$total_retensi',
                              `nilai_total` = '" . $total_retensi . "',
                              `target` = '" . $target . "',
                              `created_at` = now(),
                              `created_by` = '" . $cID . "'
                            ";
                    db($sql);

                    $idTermin = getField("select id from tagihan_termin where `created_by` = '" . $cID . "' order by id desc limit 1");
                    sync_doc($spk[id], $idTermin, 1130);

                }

                $updatePersen = getField("SELECT SUM(persen) FROM tagihan_termin WHERE id_spk = '".$par["id"]."'");
                db("update tagihan_spk set persen_termin = '$updatePersen' where id = '".$par["id"]."'");

            }

            $cekPersenTermin = getField("select persen_termin from tagihan_spk where id = '$par[id]'");
            $lastTermin = getRow("select * from tagihan_termin where termin like '%Termin%' and created_by = '$cID' order by id desc limit 1");

            if ($cekPersenTermin > 100) {
                $selisih = $cekPersenTermin - 100;
                $updatePersenLast = $lastTermin[persen] - $selisih;
            } else {
                $selisih = 100 - $cekPersenTermin;
                $updatePersenLast = $lastTermin[persen] + $selisih;
            }

            $nilai_tahapan = $spk["nilai"] * ($updatePersenLast / 100);
            $nilai_tahapan = round($nilai_tahapan);
            $ppn_tahapan = $nilai_tahapan * ($spk["ppn"] / 100);
            $ppn_tahapan = round($ppn_tahapan);
            $total_tahapan = $nilai_tahapan + $ppn_tahapan;
            $total_tahapan = round($total_tahapan);

            db("update tagihan_termin set `persen` = '$updatePersenLast',
                                          `nilai` = '" . $nilai_tahapan . "',
                                          `nilai_ppn` = '" . $ppn_tahapan . "',
                                          `nilai_plus_ppn` = '$total_tahapan',
                                          `nilai_total` = '" . $total_tahapan . "'
                                           where id = '$lastTermin[id]'");

            $updatePersen = getField("SELECT SUM(persen) FROM tagihan_termin WHERE id_spk = '".$par["id"]."'");
            db("update tagihan_spk set persen_termin = '$updatePersen' where id = '".$par["id"]."'");

        }

    }
    else {
        db("delete from tagihan_termin where id_spk = '" . $par["id"] . "'");
        db("delete from tagihan_syarat where id_spk = '" . $par["id"] . "'");
        db("update tagihan_spk set persen_termin = '0' where id = '".$par["id"]."'");
    }

    echo "<script>closeBox(); alert(\"Data berhasil disimpan.\"); reloadPage();</script>";
}

function form_approval()
{
    global $par, $cID, $menuAccess, $s;

    $r = getRow("select * from tagihan_spk where id='" . $par["id"] . "'");

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">APPROVAL</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
				    <!--
				    " . (($r["approve_status"] != "t" and isset($menuAccess[$s]["apprlv1"])) ? "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>" : "") . "
				    -->
				    <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>
				</p>
			</div>
			<fieldset>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Tanggal</label>
                    <div class=\"field\">
                        ";
                        $r["approve_date"] = $r["approve_date"] ?: date('Y-m-d');
                        $text .= "
                        <input type=\"text\" id=\"inp[approve_date]\" name=\"inp[approve_date]\"  value=\"" . getTanggal($r["approve_date"]) . "\" class=\"hasDatePicker\" maxlength=\"150\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Nama</label>
                    <div class=\"field\">
                        ";
                        $r["approve_by"] = (empty($r["approve_by"])) ? getField("select namaUser from app_user where id = '$cID'") : getField("select namaUser from app_user where id = '" . $r["approve_by"] . "'");
                        $text .= "
                        <input type=\"text\" readonly id=\"inp[approve_by]\" style=\"width:300px;\" name=\"inp[approve_by]\" size=\"10\" maxlength=\"150\" value=\"" . $r["approve_by"] . "\" class=\"vsmallinput\"/>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Keterangan</label>
                    <div class=\"field\">
                        <textarea name=\"inp[approve_desc]\" style=\"width:300px;\" id=\"inp[approve_desc]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >" . nl2br($r["approve_desc"]) . "</textarea>
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\" style=\"padding-left:10px;\">Status</label>
                    <div class=\"fradio\">
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"t\" " . (($r["approve_status"] == "t" or empty($r["approve_status"])) ? "checked" : "") . "/> <span class=\"sradio\">Setuju</span>
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"p\" " . ($r["approve_status"] == "p" ? "checked" : "") . " /> <span class=\"sradio\">Pending</span>
                        <input type=\"radio\" id=\"inp[approve_status]\" name=\"inp[approve_status]\" value=\"f\" " . ($r["approve_status"] == "f" ? "checked" : "") . " /> <span class=\"sradio\">Tolak</span>
                    </div>
                </p>
            </fieldset>
		</form>
	</div>";

    return $text;
}

function hapus()
{
    global $par;

    db("delete from tagihan_spk where id = '" . $par["id"] . "'");

    echo "<script>alert('Data berhasil dihapus.')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function lData()
{
    global $s, $par, $menuAccess, $arrParam, $cID;

    if ($_GET[json] == 1) {
        header("Content-type: application/js on");
    }

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
        $limit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
    }

    $jenisUser = getField("select jenisUser from app_user where id = '".$cID."' ");

    if ($jenisUser == 1){
        $whereAccess = " AND id_cc IN (SELECT id_cc FROM app_user_akses WHERE id_user = '".$cID."')";
    }

    $where = " WHERE id_jenis = '".$arrParam[$s]."' $whereAccess";

   if (!empty($_GET['fSearch'])) {
        $where .= " and (     
            lower(a.nomor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            or
            lower(a.judul) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            or
            lower(b.namaSupplier) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            or
            lower(c.nama) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
            )";
    }

    if (!empty($_GET['combo1'])) $where .= " and month(tanggal) = '".$_GET['combo1']."'";
    if (!empty($_GET['combo2'])) $where .= " and year(tanggal) = '".$_GET['combo2']."'";
    if (!empty($_GET['combo3'])) $where .= " and id_supplier = '$_GET[combo3]'";

    if(!empty($_GET['combo4'])){
         $range = explode(" - ", $_GET['combo4']);
         $start = str_replace(',', '', $range[0]);
         $end = str_replace(',', '', $range[1]);
         $where .= " and nilai between '$start' and '$end'";
    }

    if (!empty($_GET['combo5'])){ 
        $where .= " and id_cc = '$_GET[combo5]'";

        if (!empty($_GET['combo6'])){
            $where .= " and id_proyek = '$_GET[combo6]'";
        }
    }

    $arrOrder = array("", "tanggal", "judul");

    if (!empty($_GET[sSortDir_0]) && !empty($_GET[iSortCol_0])) $order = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    else $order = "id DESC";


    $sql = "SELECT a.*
            from tagihan_spk as a
            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
            LEFT JOIN pegawai_data AS c ON (c.id = a.id_supplier)
            $where order by $order $limit";

    $res = db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("SELECT count(a.id) from tagihan_spk as a
                                            LEFT JOIN dta_supplier AS b ON (b.kodeSupplier = a.id_supplier)
                                            LEFT JOIN pegawai_data AS c ON (c.id = a.id_supplier) $where"),
        "aaData" => array()
    );

    $no = intval($_GET['iDisplayStart']);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $view = $r['file_spk'] ? "<a href=\"#\" onclick=\"openBox('view.php?doc=fileTagihanSpk&par[id]=$r[id]".getPar($par, "mode, id")."',900,500);\" class=\"detail\"><span>Detail</span></a>" : "";
        $download = $r['file_spk'] ? "<a href=\"download.php?d=fileTagihanSpk&f=$r[id]".getPar($par, "mode, id")."\"><img src=\"".getIcon($r['file_spk'])."\" height=\"20\"></a>" : "";

        if ($r["approve_status"] == "t"){ #ini kalau setuju
            $background = "class=\"labelStatusHijau\"";
        } elseif ($r["approve_status"] == "p") {
            $background = "class=\"labelStatusBiru\""; #ini kalau pending
        } elseif ($r["approve_status"] == "f") { #kalo ditolak
            $background = "class=\"labelStatusMerah\"";
        } else { #kalau belum
            $background = "class=\"labelStatusKuning\"";
        }

        $appr = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[id]=" . $r["id"] . getPar($par, "mode, id") . "', 600, 300);\" style=\"text-decoration: none; color:black;\">Belum</a>";

        if ($r["approve_status"] == "t") $appr = "Setuju";
        if ($r["approve_status"] == "f") $appr = "Tolak";
        if ($r["approve_status"] == "p") $appr = "Pending";

        if (isset($menuAccess[$s]["apprlv1"])) {
            $approval = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=approval&par[id]=" . $r["id"] . getPar($par, "mode, id") . "', 600, 300);\" style=\"text-decoration: none; color:black;\">$appr</a>";
        } else {
            $approval = $appr;
        }

        $kontrol = "";
        if (isset($menuAccess[$s]["edit"])) $kontrol .= "<a href=\"?par[mode]=edit&par[id]=" . $r["id"] . "" . getPar($par, "mode, id") . "\" class=\"edit\"><span>Edit</span></a>";
        if (isset($menuAccess[$s]["delete"]) && $r["approve_status"] == '') $kontrol .= "<a href=\"?par[mode]=delete&par[id]=" . $r["id"] . getPar($par, "mode, id") . "\" onclick=\"return confirm('Delete data?')\" class=\"delete\"><span>Delete</span></a>";

        $pemohon = ($r[id_jenis] == '1048') ? getField("select namaSupplier from dta_supplier where kodeSupplier = $r[id_supplier]") : getField("select nama from pegawai_data where id = $r[id_supplier]");

        $data = array(
            "<div align=\"center\">" . $no . "</div>",
            "<div align=\"center\">" . getTanggal($r["tanggal"]) . "</div>",
            "<div align=\"left\">
                $r[judul]
                <br>
                <a style=\"text-decoration: none;\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=detailSPK&par[pop_up]=1&par[id_spk]=".$r["id"]."" . getPar($par, "mode, id_spk") . "',  980, 500);\">".$r["nomor"]."</a>
            </div>",
            "<div align=\"left\">" . $pemohon . "</div>",
            "<div align=\"right\">" . getAngka($r["nilai_plus_ppn"]) . "</div>",
            "<div align=\"center\">". $view ."</div>",
            "<div align=\"center\">". $download ."</div>",
            "<div align=\"center\" $background>" . $approval . "</div>",
            "<div align=\"center\">" . $kontrol . "</div>",
        );

        $json['aaData'][] = $data;
    }

    return json_encode($json);
}

function simpan()
{
    global $inp, $par, $cID, $s, $arrParam, $dirFile;
    
    $fileIcon = $_FILES["fileUpload"]["tmp_name"];
    $fileIcon_name = $_FILES["fileUpload"]["name"];
    if (($fileIcon != "") and ($fileIcon != "none"))
    {
        fileUpload($fileIcon, $fileIcon_name, $dirFile);
        $fileDokumen = "tagihan_".time().".".getExtension($fileIcon_name);
        fileRename($dirFile, $fileIcon_name, $fileDokumen);
        $updateFile .= "file_spk = '".$fileDokumen."',";
    }

    $nilai    = setAngka($inp["nilai"]);
    $ppn      = setAngka($inp["ppn"]);
    //$pph      = setAngka($inp["pph"]);
    $nilaiPPN = $nilai * ($ppn / 100);
    $nilaiPPN = round($nilaiPPN);
    //$nilaiPPH = $nilai * ($pph / 100);
    //$diskon   = setAngka($inp["diskon"]);
    //$total    = ($nilai + $nilaiPPN + $nilaiPPH) - $diskon;
    $total    = $nilai + $nilaiPPN;
    $total    = round($total);

    if ($arrParam[$s] != '1048') {
        $inp["tahapan_tagihan"] = 'FP';
        $inp["nilai_tahapan_fullpayment"] = 100;
    }

    $nilaiTahapan = "";
    if ($inp["tahapan_tagihan"] == "TR") {
        $nilaiTahapan = "`nilai_tahapan_termin` = '" . setAngka($inp["nilai_tahapan_termin"]) . "',";
    }

    if ($inp["tahapan_tagihan"] == "FP") {
        $nilaiTahapan = "`nilai_tahapan_fullpayment` = '" . setAngka($inp["nilai_tahapan_fullpayment"]) . "',";
    }

    if ($inp["tahapan_tagihan"] == "BT") {
        $nilaiTahapan = "`nilai_tahapan_bertahap` = '".$inp["nilai_tahapan_bertahap"]."',";
    }

    $setData = "`id_supplier` = '" . $inp["id_supplier"] . "',
                  `tanggal` = '" . setTanggal($inp["tanggal"]) . "',
                  `nomor` = '" . nomorSPK() . "',
                  `judul` = '" . $inp["judul"] . "',
                  `id_jenis` = '" . $arrParam[$s] . "',
                  `id_cc` = '" . $inp["id_cc"] . "',
                  `id_proyek` = '" . $inp["id_proyek"] . "',
                  `jenis_permohonan` = '" . $inp["jenis_permohonan"] . "',
                  `catatan` = '" . $inp["catatan"] . "',
                  `target_realisasi` = '" . setTanggal($inp["target_realisasi"]) . "',
                  `target_realisasi_selesai` = '" . setTanggal($inp["target_realisasi_selesai"]) . "',
                  `alamat` = '" . $inp["alamat"] . "',
                  `ppn` = '" . $ppn . "',
                  `pph` = '" . $pph . "',
                  `nilai` = '" . $nilai . "',
                  `nilai_ppn` = '" . setAngka($nilaiPPN) . "',
                  `nilai_plus_ppn` = '" . setAngka($total) . "',
                  `total` = '" . setAngka($total) . "',
                  $updateFile
                  
                  `uang_muka` = '" . $inp["uang_muka"] . "',
                  `retensi` = '" . $inp["retensi"] . "',
                  `nilai_uang_muka` = '" . $inp["nilai_uang_muka"] . "',
                  `nilai_retensi` = '" . $inp["nilai_retensi"] . "',
                  `tahapan_tagihan` = '" . $inp["tahapan_tagihan"] . "',
                  `dokumen_pendukung` = '" . $inp["dokumen_pendukung"] . "',
                  $nilaiTahapan
                  ";
                  

    if (empty($par["id"])) {

        $sql = "INSERT
                  `tagihan_spk`
                SET
                  $setData
                  `created_at` = now(),
                  `created_by` = '" . $cID . "'
                ";
    } else {

        $sql = "UPDATE
                  `tagihan_spk`
                SET
                  $setData
                  `updated_at` = now(),
                  `updated_by` = '" . $cID . "'
                WHERE `id` = '" . $par["id"] . "'
                ";
    }

    db($sql);

    echo "<script>alert(\"Data berhasil disimpan.\");</script>";
    echo "<script>window.location=\"?".getPar($par, 'mode, id')."\"</script>";
}

function nomorSPK()
{
    $nomor = "SUBSTRING_INDEX(SUBSTRING_INDEX(nomor, '/', 1), '/', -1)";
    $kode  = "SUBSTRING_INDEX(SUBSTRING_INDEX(nomor, '/', 2), '/', -1)";
    $bulan = "SUBSTRING_INDEX(SUBSTRING_INDEX(nomor, '/', 3), '/', -1)";
    $tahun = "SUBSTRING_INDEX(SUBSTRING_INDEX(nomor, '/', 4), '/', -1)";

    $code   = "DP";
    $month  = getRomawi(date('m'));
    $year   = date('Y');

    $getlastNumber = getField("SELECT $nomor FROM tagihan_spk WHERE $kode = '$code' and $bulan = '$month' and $tahun = '$year' ORDER BY $nomor DESC LIMIT 1");

    $str    = empty($getlastNumber) ? "000" : $getlastNumber;
    $incNum = str_pad($str + 1, 3, "0", STR_PAD_LEFT);

    $kode = $incNum . "/" . $code . "/" . $month . "/" . $year;

    return $kode;
}

function form()
{
    global $s, $par, $arrTitle, $dirFile, $arrParam, $cID;


    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '$par[id]'");
    $ppn = getField("select nilaiParameter from app_parameter where namaParameter = 'ppn'");
    $pph = getField("select nilaiParameter from app_parameter where namaParameter = 'pph'");
    $jenis = getField("select * from mst_data where kodeCategory = 'KDJP' and urutanData = 1");

    $r["jenis_permohonan"] = empty($r["jenis_permohonan"]) ? $jenis : $r["jenis_permohonan"];

    $r["ppn"] = empty($r["id"]) ? setAngka($ppn) :  $r["ppn"];
    $r["pph"] = empty($r["id"]) ? setAngka($pph) :  $r["pph"];

    setValidation("is_null", "inp[tanggal]", "anda harus mengisi tanggal");
    setValidation("is_null", "inp[judul]", "anda harus mengisi judul");
    setValidation("is_null", "inp[target_realisasi]", "anda harus mengisi target realisasi");
    setValidation("is_null", "inp[id_cc]", "anda harus mengisi cost center");
    setValidation("is_null", "inp[id_proyek]", "anda harus mengisi proyek");
    setValidation("is_null", "inp[id_supplier]", "anda harus mengisi supplier");
    $text = getValidation();

    if ($arrParam[$s] == 1048)
    {
        $dependent = "onchange=\"getProyek(this.value, '" . getPar($par, "mode") . "')\"";
    } else {
        $dependent = "onchange=\"getProyek(this.value, '" . getPar($par, "mode") . "');getPemohon(this.value, '" . getPar($par, "mode") . "')\"";
    }

    $text .= "
    <div class=\"pageheader\">
		<h1 class=\"pagetitle\">". strtoupper($arrTitle[$s]) ."</h1>
		".getBread(ucwords(str_replace("Detail", "", $par["mode"])))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
					";
                    if ($r["approve_status"] != 't' && $r["approve_status"] != 'f') $text .= "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
                    $text .= "
                    <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id, id_spk, id_tagihan, id_termin, id_pajak") . "';\"/>
				</p>
			</div>
			<br>
			<fieldset>
            <legend>Dasar Permohonan</legend>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Tanggal Input</label>
							<div class=\"fieldA\">  
							    ";
                                $r["tanggal"] = empty($r["tanggal"]) ? date("Y-m-d") : $r["tanggal"];
                                $text .= "
								<input type=\"text\" id=\"inp[tanggal]\" name=\"inp[tanggal]\"  value=\"" . getTanggal($r["tanggal"]) . "\" class=\"hasDatePicker\"/>
							</div>
						</p>
						
					</td>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Nomor Dokumen</label>
							<div class=\"fieldA\">
							    ";
                                $r["nomor"] = empty($r["nomor"]) ? nomorSPK() : $r["nomor"];
                                $text .= "
								<input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"" . $r["nomor"] . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
							</div>
						</p>
					</td>
				</tr>
			</table>

			<p>
				<label class=\"l-input-small\" >Judul Pekerjaan</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"" . $r["judul"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" >Uraian</label>
				<div class=\"field\">
					<textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:600px;\">" . $r["catatan"] . "</textarea>
				</div>
			</p>
			
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Tanggal Mulai</label>
							<div class=\"fieldA\">  
                                ";
                                $r["target_realisasi"] = empty($r["target_realisasi"]) ? date("Y-m-d") : $r["target_realisasi"];
                                $text .= "
                                <input type=\"text\" id=\"inp[target_realisasi]\" name=\"inp[target_realisasi]\"  value=\"" . getTanggal($r["target_realisasi"]) . "\" class=\"hasDatePicker\"/>
							</div>
						</p>
					</td>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Tanggal Selesai</label>
							<div class=\"fieldA\">  
                                ";
                                $r["target_realisasi_selesai"] = empty($r["target_realisasi_selesai"]) ? date("Y-m-d") : $r["target_realisasi_selesai"];
                                $text .= "
                                <input type=\"text\" id=\"inp[target_realisasi_selesai]\" name=\"inp[target_realisasi_selesai]\"  value=\"" . getTanggal($r["target_realisasi_selesai"]) . "\" class=\"hasDatePicker\"/>
							</div>
						</p>
					</td>
				</tr>
			</table>
            <p>
            <label class=\"l-input-small\">File Perintah Kerja</label>
            <div class=\"field\">";
                $text .= empty($r['file_spk'])
                ?
                "<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
                <div class=\"fakeupload\">
                    <input type=\"file\" id=\"fileUpload\" name=\"fileUpload\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
                </div>"
                :
                "<a href=\"".$dirFile.$r['file_spk']."\" download><img src=\"".getIcon($r['file_spk'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                ".($r['tiket_status'] != "t" ? "<a href=\"?par[mode]=delFile&par[id]=".$r['id'].getPar($par,"mode, id")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                <br clear=\"all\">";
                $text.="
            </div>
        </p>
			
        <p>
            <label class=\"l-input-small\" >Lokasi Pelaksanaan</label>
            <div class=\"field\">
                <textarea id=\"inp[alamat]\" name=\"inp[alamat]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:600px;\">" . $r["alamat"] . "</textarea>
            </div>
        </p>
        
        <!-- <p>
				<label class=\"l-input-small\" >Control Balance</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[control_balance]\" name=\"inp[control_balance]\"  value=\"" . $r["control_balance"] . "\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"50\"/>
				</div>
		</p> -->
        
        <table style=\"width:100%\">
            <tr>
                <td style=\"width:50%\">
                    <p>
                        <label class=\"l-input-small2\">Nilai DPP</label>
                        <div class=\"fieldA\">  
                            <input type=\"text\" id=\"inp[nilai]\" name=\"inp[nilai]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["nilai"]) . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                        </div>
                    </p>
                    
                </td>
                <td style=\"width:50%\">
                    <p>
                        <label class=\"l-input-small2\">PPN</label>
                        <div class=\"fieldA\">
                                ";
                                $r["ppn"] = empty($r["ppn"]) ? '11' : $r["ppn"];
                                $text .= "
                            <input type=\"text\" id=\"inp[ppn]\" name=\"inp[ppn]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["ppn"]) . "\" style='width:50px;'/> %
                        </div>
                    </p>
                </td>
            </tr>
        </table>
        
        <p>
            <label class=\"l-input-small\">Grand Total</label>
            <div class=\"field\">
                <input type=\"text\" readonly id=\"inp[total]\" name=\"inp[total]\" onkeyup=\"cekAngka(this)\" value=\"" . getAngka($r["total"]) . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
            </div>
        </p>
        
        </fieldset>

            <br>

            <fieldset>
                <legend>Relasi Data</legend>
                <p>
                    <label class=\"l-input-small\">Cost Center</label>
                    <div class=\"field\">
                        " . comboData("select * from costcenter_data order BY nama asc", "id", "nama", "inp[id_cc]", "- Pilih Cost Center -", $r["id_cc"], $dependent, "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_id_cc__chosen{ min-width:210px; }
                    </style>
                </p>
                <p>
                    <label class=\"l-input-small\">Proyek</label>
                    <div class=\"field\">
                        " . comboData("select *, concat(nomor, ' - ', proyek) as namaProyek from proyek_data where costcenter = '" . $r['id_cc'] . "' order by proyek asc", "id", "namaProyek", "inp[id_proyek]", "- Pilih Proyek -", $r["id_proyek"], "", "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_id_proyek__chosen{ min-width:410px; }
                    </style>
                </p>
                <p>
                    ";
                    if ($arrParam[$s] == 1048) {

                        $text .= "<label class=\"l-input-small\">Vendor</label>
                        <div class=\"field\">
                            " . comboData("select * from dta_supplier where tipe = 'supplier' order by namaSupplier asc", "kodeSupplier", "namaSupplier", "inp[id_supplier]", "- Pilih Vendor -", $r["id_supplier"], "", "410px", "chosen-select") . "
                        </div>
                        <style>
                            #inp_id_supplier__chosen{ min-width:410px; }
                        </style>";

                    } else {
                        $idUnit = getField("SELECT sbu from costcenter_data where id = '".$r['id_cc']."'");

                        $text .= "<label class=\"l-input-small\">Pemohon</label>
                        <div class=\"field\">
                            " . comboData("select * from pegawai_data where unit = ". $idUnit ." order by nama asc", "id", "nama", "inp[id_supplier]", "- Pilih Pemohon -", $r["id_supplier"], "", "410px", "chosen-select") . "
                        </div>
                        <style>
                            #inp_id_supplier__chosen{ min-width:410px; }
                        </style>";

                    }
                    $text.="
                </p>
                <p>
                    <label class=\"l-input-small\">Jenis Permohonan</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'KDJP' order by urutanData asc", "kodeData", "namaData", "inp[jenis_permohonan]", "", $r["jenis_permohonan"], "", "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_jenis_permohonan__chosen{ min-width:410px; }
                    </style>
                </p>
            </fieldset>
            
            <br>
            
            
            <fieldset>
                <legend>Pembayaran</legend>
                
                ";
                if ($arrParam[$s] == 1048)
                {
                    $text.="
                    
                    <table width='80%'>
                        <tr>
                            <td width='50%'>  
                                <p>
                                    <label class=\"l-input-small2\" style=\"min-width: 245px !important;\">Uang Muka</label>
                                    <div class=\"fieldA\" style=\"min-width: 80px !important;\">  
                                         <input type=\"checkbox\" ".($r[uang_muka] == '1' ? "checked=\"checked\"" : "")." onclick=\"checkUangMuka();\" id=\"inp[uang_muka]\" name=\"inp[uang_muka]\" value=\"1\"/> Ya
                                    </div>
                                </p>
                                
                                <p>
                                    <label class=\"l-input-small2\" style=\"min-width: 245px !important;\">Retensi</label>
                                    <div class=\"fieldA\" style=\"min-width: 80px !important;\">  
                                         <input type=\"checkbox\" ".($r[retensi] == '1' ? "checked=\"checked\"" : "")." onclick=\"checkRetensi();\" id=\"inp[retensi]\" name=\"inp[retensi]\" value=\"1\"/> Ya
                                    </div>
                                </p>
                                
                            </td>
                            <td>
                                <p>
                                    <label class=\"l-input-small2\" style=\"min-width: 50px !important;\">Nilai</label>
                                    <div class=\"fieldA\" style=\"min-width: 100px !important;\">
                                        <input type=\"text\" id=\"inp[nilai_uang_muka]\" name=\"inp[nilai_uang_muka]\" onkeyup=\"cekAngka(this);\" value=\"" . getAngka($r["nilai_uang_muka"]) . "\" class=\"mediuminput\" style=\"width:50px;\" maxlength=\"100\"> %
                                    </div>
                                </p>
                                
                                <p>
                                    <label class=\"l-input-small2\" style=\"min-width: 50px !important;\">Nilai</label>
                                    <div class=\"fieldA\" style=\"min-width: 100px !important;\">
                                        <input type=\"text\" id=\"inp[nilai_retensi]\" name=\"inp[nilai_retensi]\" onkeyup=\"cekAngka(this);\" value=\"" . getAngka($r["nilai_retensi"]) . "\" class=\"mediuminput\" style=\"width:50px;\" maxlength=\"100\"> %
                                    </div>
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                    
                    <p>
                        <label class=\"l-input-small\">Tahapan Tagihan</label>
                        <div class=\"field\">
                             " . comboData("select * from mst_data where kodeCategory = 'THT' order by urutanData asc", "kodeMaster", "namaData", "inp[tahapan_tagihan]", "- Pilih Tahapan -", $r["tahapan_tagihan"], "onchange=\"setTahapan(this.value);\"", "260px", "chosen-select") . "
                             
                             ";
                             if ($r[tahapan_tagihan] == 'TR') {
                                $display_full = "none";
                                $display_termin = "block";
                                $display_bertahap = "none";
                             }
                             elseif ($r[tahapan_tagihan] == 'FP') {
                                $display_full = "block";
                                $display_termin = "none";
                                $display_bertahap = "none";
                             }
                             elseif ($r[tahapan_tagihan] == 'BT') {
                                $display_full = "none";
                                $display_termin = "none";
                                $display_bertahap = "block";
                             }
                             else {
                                $display_full = "none";
                                $display_termin = "none";
                                $display_bertahap = "none";
                             }
                             $text.="
                             <span id='nilai_tahapan_fullpayment' style=\"display: $display_full; ".(($par[mode] == 'edit' && $r[tahapan_tagihan] == 'FP') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")." \">
                                &nbsp;
                                ";
                                $r["nilai_tahapan_fullpayment"] = empty($r["nilai_tahapan_fullpayment"]) ? 100 : $r["nilai_tahapan_fullpayment"];
                                $text.="
                                <input style=\"width:50px;\" type=\"text\" readonly id=\"inp[nilai_tahapan_fullpayment]\" name=\"inp[nilai_tahapan_fullpayment]\" onkeyup=\"cekAngka(this);\" value=\"" . getAngka($r["nilai_tahapan_fullpayment"]) . "\" class=\"mediuminput\" maxlength=\"100\"> %
                             </span>
                             
                             <span id='nilai_tahapan_termin' style=\"display: $display_termin; ".(($par[mode] == 'edit' && $r[tahapan_tagihan] == 'TR') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")."\">
                                &nbsp;
                                <input style=\"width:50px;\" type=\"text\" id=\"inp[nilai_tahapan_termin]\" name=\"inp[nilai_tahapan_termin]\" onkeyup=\"cekAngka(this);\" value=\"" . getAngka($r["nilai_tahapan_termin"]) . "\" class=\"mediuminput\" style=\"width:50px;\" maxlength=\"100\"> Kali
                             </span>
                             
                             <span id='nilai_tahapan_bertahap' style=\"display: $display_bertahap; ".(($par[mode] == 'edit' && $r[tahapan_tagihan] == 'BT') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")."\">
                                &nbsp;
                                " . comboData("select * from mst_data where kodeCategory = 'TBH' order by urutanData asc", "kodeMaster", "namaData", "inp[nilai_tahapan_bertahap]", "", $r["nilai_tahapan_bertahap"], "", "100px", "chosen-select") . "
                                <style>
                                    #inp_nilai_tahapan_bertahap__chosen{ min-width:100px; }
                                </style>
                             </span>
                        </div>
                    </p>
                    
                    ";

                }
                else
                {
                    $r["tahapan_tagihan"] = empty($r["tahapan_tagihan"]) ? "FP" : $r["tahapan_tagihan"];

                    $text.="
                    <p>
                        <label class=\"l-input-small\">Tahapan Tagihan</label>
                        <div class=\"field\">
                             " . comboData("select * from mst_data where kodeCategory = 'THT' order by urutanData asc", "kodeMaster", "namaData", "inp[tahapan_tagihan]", "- Pilih Tahapan -", $r["tahapan_tagihan"], "onchange=\"setTahapan(this.value);\"", "260px", "chosen-select", "disabled") . "
                             
                             
                             <span id='nilai_tahapan_fullpayment' style=\"display: $display_full; ".(($par[mode] == 'edit' && $r[tahapan_tagihan] == 'FP') ? "position: relative; margin-left: 268px; margin-top: -32px;" : "")." \">
                                &nbsp;
                                ";
                                $r["nilai_tahapan_fullpayment"] = empty($r["nilai_tahapan_fullpayment"]) ? 100 : $r["nilai_tahapan_fullpayment"];
                                $text.="
                                <input style=\"width:50px;\" type=\"text\" readonly id=\"inp[nilai_tahapan_fullpayment]\" name=\"inp[nilai_tahapan_fullpayment]\" onkeyup=\"cekAngka(this);\" value=\"" . getAngka($r["nilai_tahapan_fullpayment"]) . "\" class=\"mediuminput\" maxlength=\"100\"> %
                             </span>
                             
                             
                        </div>
                    </p>
                    
                    ";
                }
                $text.="
                
                
                
                <p>
                    <label class=\"l-input-small\">Dokumen Pendukung</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeInduk = '$arrParam[$s]' order by urutanData asc", "kodeData", "namaData", "inp[dokumen_pendukung]", "- Pilih Dokumen -", $r["dokumen_pendukung"], "", "260px", "chosen-select") . "
                    </div>
                </p>
                
            </fieldset>
            
            ";
             if ($par[mode] == 'edit')
             {
                 $text .= "
                <br>
                
                <fieldset>
                <legend>History</legend>
                        <table style=\"width:100%\">
                                <tr>
                                    <td style=\"width:50%\">
                                        <p>
                                            <label class=\"l-input-small2\">Created Date</label>
                                            <span class=\"fieldA\">
                                                ";
                                                $r["created_at"] = empty($r["created_at"]) ? date("Y-m-d") : $r["created_at"];
                                                $text .= "
                                                " . getWaktu($r["created_at"]) . "
                                            </span>
                                        </p>
                                        
                                    </td>
                                    <td style=\"width:50%\">
                                        <p>
                                            <label class=\"l-input-small2\">Update Date</label>
                                            <span class=\"fieldA\">  
                                                ";
                                                $r["updated_at"] = empty($r["updated_at"]) ? date("Y-m-d") : $r["target_realisasi"];
                                                $text .= "
                                                " . getWaktu($r["created_at"]) . "
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                        </table>
                        <table style=\"width:100%\">
                                <tr>
                                    <td style=\"width:50%\">
                                        <p>
                                            <label class=\"l-input-small2\">Created By</label>
                                            <span class=\"fieldA\">
                                                ";
                                                $r["created_by"] = (empty($r["created_by"])) ? getField("select namaUser from app_user where id = '$cID'") : getField("select namaUser from app_user where id = '" . $r["created_by"] . "'");
                                                $text .= "
                                                " . $r["created_by"] . "
                                            </span>
                                        </p>
                                        
                                    </td>
                                    <td style=\"width:50%\">
                                        <p>
                                            <label class=\"l-input-small2\">Update By</label>
                                            <span class=\"fieldA\">
                                                ";
                                                $r["updated_by"] = (empty($r["updated_by"])) ? getField("select namaUser from app_user where id = '$cID'") : getField("select namaUser from app_user where id = '" . $r["updated_by"] . "'");
                                                $text .= "
                                                " . $r["updated_by"] . "
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                        </table>
                </fieldset>";
             }
             $text .= "
		</form>
	</div>";

    return $text;
}

function form_()
{
    global $s, $par, $arrTitle, $dirFile, $arrParam, $cID;


    $r = getRow("SELECT * FROM tagihan_spk WHERE id = '$par[id]'");
    $judul = strtoupper($arrTitle[$s]);

    
    $ppn = getField("select nilaiParameter from app_parameter where namaParameter = 'ppn'");
    $pph = getField("select nilaiParameter from app_parameter where namaParameter = 'pph'");
    $jenis = getField("select * from mst_data where kodeCategory = 'KDJP' and urutanData = 1");

    $r["jenis_permohonan"] = empty($r["jenis_permohonan"]) ? $jenis : $r["jenis_permohonan"];

    $r["ppn"] = empty($r["id"]) ? setAngka($ppn) :  $r["ppn"];
    $r["pph"] = empty($r["id"]) ? setAngka($pph) :  $r["pph"];

    setValidation("is_null", "inp[tanggal]", "anda harus mengisi tanggal");
    setValidation("is_null", "inp[judul]", "anda harus mengisi judul");
    setValidation("is_null", "inp[target_realisasi]", "anda harus mengisi target realisasi");
    setValidation("is_null", "inp[id_cc]", "anda harus mengisi sbu");
    setValidation("is_null", "inp[id_proyek]", "anda harus mengisi proyek");
    setValidation("is_null", "inp[id_supplier]", "anda harus mengisi supplier");
    $text = getValidation();

    if ($arrParam[$s] == 1048){
        $vendor = "<label class=\"l-input-small\">Vendor</label>
        <div class=\"field\">
            " . comboData("select * from dta_supplier order by namaSupplier asc", "kodeSupplier", "namaSupplier", "inp[id_supplier]", "- Pilih Vendor -", $r["id_supplier"], "", "410px", "chosen-select", $arrParam[$s] != 1048) . "
        </div>
        <style>
            #inp_id_supplier__chosen{ min-width:410px; }
        </style>";
    } else {
        $vendor = "<label class=\"l-input-small\">Pemohon</label>
        <div class=\"field\">
            " . comboData("select * from pegawai_data where unit = ". $r['id_cc'] ." order by nama asc", "id", "nama", "inp[id_supplier]", "- Pilih Pemohon -", $r["id_supplier"], "", "410px", "chosen-select") . "
        </div>
        <style>
            #inp_id_supplier__chosen{ min-width:410px; }
        </style>";
    }

    if ($arrParam[$s] == 1048)
    {
        $dependent = "onchange=\"getProyek(this.value, '" . getPar($par, "mode") . "')\"";
    } else {
        $dependent = "onchange=\"getProyek(this.value, '" . getPar($par, "mode") . "');getPemohon(this.value, '" . getPar($par, "mode") . "')\"";
    }

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">". $judul ."</h1>
		<br>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
					";
                    if ($r["approve_status"] != 't') $text .= "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('" . getPar($par, "mode") . "');\"/>";
                    $text .= "
				</p>
			</div>
			<fieldset>
            <legend>Dasar Permohonan</legend>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Tanggal</label>
							<div class=\"fieldA\">  
							    ";
                                $r["tanggal"] = empty($r["tanggal"]) ? date("Y-m-d") : $r["tanggal"];
                                $text .= "
								<input type=\"text\" id=\"inp[tanggal]\" name=\"inp[tanggal]\"  value=\"" . getTanggal($r["tanggal"]) . "\" class=\"hasDatePicker\"/>
							</div>
						</p>
						
					</td>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Nomor</label>
							<div class=\"fieldA\">
							    ";
                                $r["nomor"] = empty($r["nomor"]) ? nomorSPK() : $r["nomor"];
                                $text .= "
								<input type=\"text\" readonly id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"" . $r["nomor"] . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
							</div>
						</p>
					</td>
				</tr>
			</table>

			<p>
				<label class=\"l-input-small\" >Judul</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"" . $r["judul"] . "\" class=\"mediuminput\" style=\"width:600px;\" maxlength=\"50\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" >Catatan</label>
				<div class=\"field\">
					<textarea id=\"inp[catatan]\" name=\"inp[catatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:600px;\">" . $r["catatan"] . "</textarea>
				</div>
			</p>
			
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Target Realisasi</label>
							<div class=\"fieldA\">  
							";
                            $r["target_realisasi"] = empty($r["target_realisasi"]) ? date("Y-m-d") : $r["target_realisasi"];
                            $text .= "
                                    <input type=\"text\" id=\"inp[target_realisasi]\" name=\"inp[target_realisasi]\"  value=\"" . getTanggal($r["target_realisasi"]) . "\" class=\"hasDatePicker\"/>
							</div>
						</p>
					</td>
					<td style=\"width:50%\">
					    <p>
							<label class=\"l-input-small2\">Total Nilai</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[nilai]\" name=\"inp[nilai]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["nilai"]) . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
							</div>
						</p>
					</td>
				</tr>
			</table>
            <p>
                    <label class=\"l-input-small\">File</label>
                    <div class=\"field\">";
                        $text .= empty($r['file_spk'])
                        ?
                        "<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
                        <div class=\"fakeupload\">
                            <input type=\"file\" id=\"fileUpload\" name=\"fileUpload\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
                        </div>"
                        :
                        "<a href=\"".$dirFile.$r['file_spk']."\" download><img src=\"".getIcon($r['file_spk'])."\" width='16' style=\"padding-right:5px; padding-top:10px;\"></a>
                        ".($r['tiket_status'] != "t" ? "<a href=\"?par[mode]=delFile&par[id]=".$r['id'].getPar($par,"mode, id")."\" onclick=\"return confirm('Hapus file?')\" class=\"action delete\"><span>Delete</span></a>" : "")."
                        <br clear=\"all\">";
                        $text.="
                    </div>
                </p>
			
			<p>
				<label class=\"l-input-small\" >Lokasi Pelaksanaan</label>
				<div class=\"field\">
					<textarea id=\"inp[alamat]\" name=\"inp[alamat]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:600px;\">" . $r["alamat"] . "</textarea>
				</div>
			</p>
			
			 <!-- <table style=\"width:100%\">
				<tr>
					<td style=\"width:33%\"> -->
					    <p>
							<label class=\"l-input-small\">PPN</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[ppn]\" name=\"inp[ppn]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["ppn"]) . "\" style='width:50px;'/> %
							</div>
						</p>
					<!-- </td>
					 <td>
					    <p>
							<label class=\"l-input-small2\" style=\"width:90px;\">PPH</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[pph]\" name=\"inp[pph]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["pph"]) . "\" style='width:50px;'/> %
							</div>
						</p>
					</td>
					<td>
					    <p>
							<label class=\"l-input-small\">Diskon</label>
							<div class=\"field\">
                                <input type=\"text\" id=\"inp[diskon]\" name=\"inp[diskon]\" onkeyup=\"cekAngka(this); getTotal();\" value=\"" . getAngka($r["diskon"]) . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">							
                            </div>
						</p>
					</td> 
				</tr>
			</table> -->
			
			<p>
                <label class=\"l-input-small\">Grand Total</label>
                <div class=\"field\">
                    <input type=\"text\" readonly id=\"inp[total]\" name=\"inp[total]\" onkeyup=\"cekAngka(this)\" value=\"" . getAngka($r["total"]) . "\" class=\"mediuminput\" style=\"width:140px;\" maxlength=\"100\">
                </div>
            </p>
			
			</fieldset>

            <br>

            <fieldset>
            <legend>Relasi Data</legend>
                <p>
                    <label class=\"l-input-small\">Nama SBU</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'KSBU' order BY urutanData asc", "kodeData", "namaData", "inp[id_cc]", "- Pilih SBU -", $r["id_cc"], $dependent, "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_id_cc__chosen{ min-width:210px; }
                    </style>
                </p>
                <p>
                    <label class=\"l-input-small\">Nama Proyek</label>
                    <div class=\"field\">
                        " . comboData("select * from proyek_data where sbu = '" . $r['id_cc'] . "' order by proyek asc", "id", "proyek", "inp[id_proyek]", "- Pilih Proyek -", $r["id_proyek"], "", "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_id_proyek__chosen{ min-width:410px; }
                    </style>
                </p>
                <p>
                    ". $vendor ."
                </p>
                <p>
                    <label class=\"l-input-small\">Jenis Permohonan</label>
                    <div class=\"field\">
                        " . comboData("select * from mst_data where kodeCategory = 'KDJP' order by urutanData asc", "kodeData", "namaData", "inp[jenis_permohonan]", "", $r["jenis_permohonan"], "", "410px", "chosen-select") . "
                    </div>
                    <style>
                        #inp_jenis_permohonan__chosen{ min-width:410px; }
                    </style>
                </p>
            </fieldset>
		</form>
	</div>";

    return $text;
}

function lihat()
{
    global $s, $par, $arrTitle, $arrParam;

    $text = table(9, array(4, 5, 6, 7, 8, 9));

    $combo1 = empty($combo1) ? date("m") : $combo1;
    $combo2 = empty($combo2) ? date("Y") : $combo2;

    $yearStart = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) asc limit 1");
    $yearEnd = getField("SELECT DISTINCT(YEAR(tanggal)) FROM tagihan_spk ORDER BY YEAR(tanggal) desc limit 1");

    if($arrParam[$s] == 1048){
        $pemohon = 'Pemohon';

    } else{
        $pemohon = 'Bisnis Unit';
    }

    $text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left; width:750px; display:flex;\">
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"" . $fSearch . "\" style=\"width:250px;\"/>
                &nbsp;
                " . comboMonth("combo1", $combo1, "", "120px", "ALL") . "&nbsp;
                " . comboYear("combo2", $combo2, "", "", "60px", "ALL", $yearStart, $yearEnd) . "
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
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>EXPORT</span></a>
                <a href=\"?par[mode]=add".getPar($par, "mode")."\" class=\"btn btn1 btn_document\"><span>TAMBAH</span></a>
            </div>

            <div id=\"dFilter\" style=\"display:none;\">
                <br clear=\"all\" />
                <fieldset>
                    <table style=\"width:100%\">
                        <tr>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Vendor</label>
                                    <div class=\"field\">
                                        " . comboData("select * from dta_supplier where kodeSupplier in (SELECT DISTINCT(id_supplier) from tagihan_spk where id_jenis = '".$arrParam[$s]."') order by namaSupplier asc", "kodeSupplier", "namaSupplier", "combo3", "Semua Vendor", $combo3, "", "200px", "chosen-select", $arrParam[$s] != 1048) . "
                                    </div>
                                    <style>
                                            #combo3_chosen{ min-width:210px; }
                                    </style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Nilai</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'FNL' order by urutanData", "namaData", "namaData", "combo4", "Semua Nilai", $combo4, "", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo4_chosen{ min-width:210px; }
                                    </style>
                                </p>
                            </td>
                            <td style=\"width:50%\">
                                <p>
                                    <label class=\"l-input-small\">Bisnis Unit</label>
                                    <div class=\"field\">
                                        " . comboData("select * from mst_data where kodeCategory = 'KSBU' order by urutanData", "kodeData", "namaData", "combo5", "Semua SBU", $combo5, "onchange=\"getFilter(this.value, '" . getPar($par, "mode") . "')\"", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo5_chosen{ min-width:210px; }
                                    </style>
                                </p>
                                <p>
                                    <label class=\"l-input-small\">Proyek</label>
                                    <div class=\"field\">
                                        " . comboData("select * from proyek_data where sbu = '" . $r['id_cc'] . "' order by proyek asc", "id", "proyek", "combo6", "Semua Proyek", $combo6, "", "200px", "chosen-select") . "
                                    </div>
                                    <style>
                                            #combo6_chosen{ min-width:210px; }
                                    </style>
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
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"20\">No</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">Tanggal Input</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"*\">Judul - Nomor</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"200\">Pemohon</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">Nilai</th>
                    <th colspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">File</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">APPROVAL</th>
                    <th rowspan=\"2\" style=\"vertical-align: middle;\" width=\"40\">Kontrol</th>
                </tr>
                <tr>
                    <th width=\"40\">View</th>
                    <th width=\"40\">D/L</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
        
	</div>
	";

    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=" . ucwords(strtolower($arrTitle[$s])) . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    $text .= "
    <script>
    	jQuery(\"#btnExport\").live('click', function(e){
    		e.preventDefault();
    		window.location.href=\"?par[mode]=xls" . getPar($par, "mode, fSearch, combo1, combo2, combo3, combo4, combo5, combo6") . "&par[fSearch]=\"+jQuery(\"#fSearch\").val() + \"&par[combo1]=\"+jQuery(\"#combo1\").val() + \"&par[combo2]=\"+jQuery(\"#combo2\").val() + \"&par[combo3]=\"+jQuery(\"#combo3\").val() + \"&par[combo4]=\"+jQuery(\"#combo4\").val() + \"&par[combo5]=\"+jQuery(\"#combo5\").val() + \"&par[combo6]=\"+jQuery(\"#combo6\").val() ;
    	});
    </script>
    ";

    return $text;
}

// function detail(){

//     global $s, $_submit, $menuAccess, $arrTitle, $arrParam, $par;

//     $judul = strtoupper($arrti);

//     $text .= "
// 	<div class=\"pageheader\">
// 		<h1 class=\"pagetitle\">". $judul ."</h1>
// 		<br>
// 	</div>

// 	<div id=\"contentwrapper\" class=\"contentwrapper\">";

//     $text .= view_permohonan($par['id_spk'], $par['pop_up'], false);
//     $text .= "</div>";
//     return $text;
// }