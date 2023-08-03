<?php
include "global.php";

$data = getRow("SELECT 
                
                c.tanggal as spk_tanggal,
                c.nomor as spk_nomor,
                c.id_supplier as spk_supplier,
                c.judul as spk_judul,
                c.catatan as spk_catatan,
                c.target_realisasi as spk_tgl_mulai,
                c.target_realisasi_selesai as spk_tgl_selesai,
                c.nilai as spk_nilai,
                c.alamat as spk_alamat,
                c.ppn as spk_ppn,
                c.pph as spk_pph,
                c.diskon as spk_diskon,
                c.total as spk_grand_total,
                c.nilai_tahapan_fullpayment,
                c.nilai_tahapan_termin,
                c.nilai_tahapan_bertahap,
                
                c.uang_muka as pembayaran_uang_muka,
                c.retensi as pembayaran_retensi,
                c.nilai_uang_muka,
                c.nilai_retensi,
                g.dokumen as dokumen_pendukung,
                h.namaData as tahapan_tagihan,
                
                b.termin as term_termin,
                b.persen as term_besaran,
                b.nilai as term_senilai,
                b.nilai_ppn as term_nilai_ppn,
                b.nilai_total as term_nilai_total,
                b.target as term_target,
                b.catatan as term_catatan,

                b.id,
                a.no_invoice, 
                b.target, 
                b.termin, 
                b.persen,
                b.nilai, 
                a.tgl_terima,
                a.approval_date,
                a.tiket_tanggal,
                a.tiket_nomor,
                
                d.nama as relasi_cc,
                e.proyek as relasi_proyek,
                f.namaData as relasi_kategori
                
                
                
                
                FROM tagihan_data AS a
                JOIN tagihan_termin AS b ON (b.id = a.id_termin)
                JOIN tagihan_spk AS c ON (c.id = a.id_spk)
                JOIN costcenter_data AS d ON (d.id = c.id_cc)
                JOIN proyek_data AS e ON (e.id = c.id_proyek)
                JOIN mst_data AS f ON (f.kodeData = c.jenis_permohonan)
                left join dokumen_pendukung as g on (g.kategori = c.id_jenis)
                left join mst_data as h on (h.kodeMaster = c.tahapan_tagihan)
                WHERE b.pengajuan_no_tiket = '{$_POST['no_invoice']}'");

$data['spk_supplier'] = getField("select namaSupplier from dta_supplier where kodeSupplier = '".$data["spk_supplier"]."'");
$data['dokumen'] = getField("select ba_verifikasi_date from tagihan_syarat where id_termin = '".$data['id']."' and ba_verifikasi_status = 't' order by id desc limit 1");

$data[pembayaran_uang_muka] == '1' ? $data[pembayaran_uang_muka] = "Ya" : $data[pembayaran_uang_muka] = "Tidak";
$data[pembayaran_retensi] == '1' ? $data[pembayaran_retensi] = "Ya" : $data[pembayaran_retensi] = "Tidak";


if ($data[tahapan_tagihan] == 'TR') {
$data['tahapan_tagihan'] = $data[nilai_tahapan_termin];
}
elseif ($data[tahapan_tagihan] == 'FP') {
$data['tahapan_tagihan'] = $data[nilai_tahapan_fullpayment];
}
elseif ($data[tahapan_tagihan] == 'BT') {
$data['tahapan_tagihan'] = $data[nilai_tahapan_bertahap];
}

echo json_encode($data);
?>