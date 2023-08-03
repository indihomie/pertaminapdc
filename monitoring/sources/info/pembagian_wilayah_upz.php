<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";
$folder_upload = "../files/putusan/";
function getContent($par)
{
  global $s, $_submit, $menuAccess;
  switch ($par[mode]) {
    case "kota":
      $text = kota();
      break;

    case "lst":
      $text = lData();
      break;

    case "detail":
      $text = detail();
      break;

    case "delete_file":
      $text = delete_file();
      break;

    case "delete":
      if (isset($menuAccess[$s]["delete"])) $text = hapus();
      else $text = lihat();
      break;

    case "edit":
      if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
      else $text = lihat();
      break;

    case "add":
      if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
      else $text = lihat();
      break;

    default:
      $text = lihat();
      break;
  }
  return $text;
}

function kota()
{
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeCategory = 'KTA' and kodeInduk = '$par[provinsi]' order by namaData");
  return implode("\n", $data);
}

function lihat()
{
  global $s, $inp, $par, $arrTitle, $menuAccess, $arrColor;
  $cols = 7;
  $text = table($cols, array($cols, ($cols - 1), ($cols - 2), ($cols - 3), ($cols - 4), ($cols - 5), ($cols - 6)));
  $text .= "
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread() . "
    <span class=\"pagedesc\">&nbsp;</span>
  </div> 

  <p style=\"position: absolute; right: 20px; top: 10px;\">

  </p>   

  <div id=\"contentwrapper\" class=\"contentwrapper\">
   <form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
     <div id=\"pos_l\" style=\"float:left;\">
        <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"" . $_GET['fSearch'] . "\" style=\"width:200px;\"/> 
        " . comboData("SELECT * from mst_data WHERE kodeCategory ='WIL' order by namaData", "kodeData", "namaData", "bSearch", "All Wilayah", "$bSearch", "", "200px", "chosen-select", "") . "
        " . comboData("SELECT * from cabang_data WHERE status ='1' order by cabang", "id_cabang", "cabang", "mSearch", "All Cabang", "$mSearch", "", "200px", "chosen-select", "") . "
    </div>

    

    <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
      <a href=\"#\" id=\"btnExport2\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>";
  if (isset($menuAccess[$s]["add"])) {
    $text .= "
       <a href=\"index.php?par[mode]=add" . getPar($par, "mode") . "\" id=\"\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
       ";
  }
  $text .= "
   </div>	
 </form>
 <br clear=\"all\" />
 <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
   <thead>
    <tr>
     <th width=\"20\">No.</th>
     <th width=\"150\">UPZ</th>
     <th width=\"100\">Kode</th>
     <th width=\"130\">Kota</th>
     <th width=\"130\">Cabang</th>
     <th width=\"70\">Status</th>
     <th width=\"20\">Kontrol</th>
   </tr>
 </thead>
 <tbody></tbody>
</table>
";
  if ($par[mode] == "xls") {
    xls();
    $text .= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
  }

  $text .= "
<script>
 jQuery(\"#btnExport\").live('click', function(e){
  e.preventDefault();
  window.location.href=\"?par[mode]=xls\"+\"" . getPar($par, "mode") . "\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
});
</script>
";
  return $text;
}


function lData()
{
  global $s, $par, $fFile, $menuAccess, $cUsername, $sUser, $sGroup, $arrTitle, $arrParam, $folder_upload;
  if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
    $sLimit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);

  $sWhere = " WHERE id_upz is not null";

  if (!empty($_GET['fSearch']))
    $sWhere .= " and (				
 lower(upz) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
 )";

  if (!empty($_GET['bSearch']))
    $sWhere .= " and t2.id_wilayah = $_GET[bSearch]";

  if (!empty($_GET['mSearch']))
    $sWhere .= " and t1.id_cabang = $_GET[mSearch]";

  $arrOrder = array(
    "t1.kode",
    "t1.upz",
    "t1.created_date",
    "t1.upz",
    "t1.upz"
  );

  $orderBy = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];

  $sql = "select t1.*
 from cabang_upz t1 join cabang_data t2 on (t1.id_cabang=t2.id_cabang)
 $sWhere order by $orderBy $sLimit";

  $res = db($sql);
  $json = array(
    "iTotalRecords" => mysql_num_rows($res),
    "iTotalDisplayRecords" => getField("select count(t1.id_upz) from cabang_upz t1 join cabang_data t2 on (t1.id_cabang=t2.id_cabang) $sWhere"),
    "aaData" => array(),
  );

  $no = intval($_GET['iDisplayStart']);
  while ($r = mysql_fetch_array($res)) {
    $no++;
    $r[status] = ($r[status] == 1 ? "<img src=\"styles/images/t.png\">" : "<img src=\"styles/images/f.png\">");
    $r[id_cabang] = getField("SELECT cabang from cabang_data where id_cabang = '$r[id_cabang]'");
    $data = array(
      "<div align=\"center\">$no</div>",
      "<div align=\"left\">" . $r[upz] . "</div>",
      "<div align=\"center\">" . $r[kode] . "</div>",
      "<div align=\"left\">" . namaData($r[kota]) . "</div>",
      "<div align=\"left\">" . $r[id_cabang] . "</div>",
      "<div align=\"center\">" . $r[status] . "</div>",
      "<div align=\"center\">
    <a href='?par[mode]=edit&par[id_upz]=$r[id_upz]" . getPar($par, "mode") . "' class='edit' title='Edit Data'></a>

    <a href='?par[mode]=delete&par[id_upz]=$r[id_upz]" . getPar($par, "mode") . "' class='delete' title='Hapus Data' onclick=\"return confirm('are you sure to delete data ?');\"></a>
  </div>",
    );
    $json['aaData'][] = $data;
  }
  return json_encode($json);
}

function detail()
{
  global $s, $inp, $par, $arrTitle, $menuAccess, $arrParam;
  $sql = db("SELECT * FROM data_putusan WHERE id = '$par[id]'");
  $r = mysql_fetch_array($sql);
  $text .= "
  <style>
        #inp_kodeRekening__chosen{
    min-width:250px;
  }
</style>
<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread(ucwords("import data")) . "
    <span class=\"pagedesc\">&nbsp;</span> 
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
      <fieldset>
        <legend>Detail Data</legend>
        <p>
          <label class=\"l-input-small\">Date Post</label>
          <span class=\"field\">
            &nbsp;" . getTanggal2($r[created_date], "d/m/Y") . "
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Kategori Putusan</label>
          <span class=\"field\">
            &nbsp;" . $r[kategori_putusan] . "
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Nama Putusan</label>
          <span class=\"field\">
            &nbsp;" . $r[nama_putusan] . "
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Nomor</label>
          <span class=\"field\">
            &nbsp;" . $r[nomor] . "
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Tahun</label>
          <span class=\"field\">
            &nbsp;" . $r[tahun] . "
          </span>
        </p>
      </fieldset>
    </form>
  </div>
</div>";
  return $text;
}

function form()
{
  global $s, $inp, $par, $arrTitle, $menuAccess, $arrParam, $folder_upload;

  $sql = db("SELECT * FROM cabang_upz WHERE id_upz ='$par[id_upz]'");
  $r = mysql_fetch_array($sql);

  if (empty($r[status])) {
    $default = "checked";
  }

  setValidation("is_null", "inp[nama_putusan]", "Anda belum mengisi nama nama_putusan");
  setValidation("is_null", "inp[nomor]", "Anda belum mengisi nomor");
  echo getValidation();
  $text .= "
  <style>
        #inp_id_wilayah__chosen, #inp[id_wilayah]{
          min-width:250px;
        }
</style>
<div class=\"pageheader\">
  <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
  " . getBread(ucwords("import data")) . "
  <span class=\"pagedesc\">&nbsp;</span> 
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
  <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    <fieldset>
      <legend>AREA</legend>
      <div style=\"position:absolute; right:20px; top:14px;\">
        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"window.location='index.php?" . getPar($par, "mode") . "';\"/>

      </div>
      <p>
        <label class=\"l-input-small\">Nama Cabang</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[upz]\" name=\"inp[upz]\"  value=\"$r[upz]\" class=\"smallinput\" style=\"width:300px;\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Kode</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[kode]\" name=\"inp[kode]\"  value=\"$r[kode]\" class=\"smallinput\" style=\"width:90px;\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Region</label>
        <div class=\"field\" style='width:300px;'>
         " . comboData("SELECT * from cabang_data WHERE status ='1'", "id_cabang", "cabang", "inp[id_cabang]", "Pilih Cabang", "$r[id_cabang]", "", "200px", "chosen-select", "") . "
       </div>
     </p>
     <p>
      <label class=\"l-input-small\">Alamat</label>
      <div class=\"field\">
        <textarea rows=\"5\" style=\"width:300px;\" class=\"mediuminput\" name=\"inp[alamat]\">$r[alamat]</textarea>
      </div>
    </p>
    <p>
      <label class=\"l-input-small\">Provinsi</label>
      <div class=\"field\" style='width:300px;'>
       " . comboData("SELECT * from mst_data WHERE kodeCategory ='S02'", "kodeData", "namaData", "inp[provinsi]", "Pilih Provinsi", "$r[provinsi]", "onchange=\"getKota('" . getPar($par, "mode,inp[provinsi]") . "');\"", "200px", "chosen-select", "") . "
     </div>
   </p>
   <p>
    <label class=\"l-input-small\">Kota</label>
    <div class=\"field\" style='width:300px;'>
     " . comboData("SELECT * from mst_data WHERE kodeCategory ='S03' ", "kodeData", "namaData", "inp[kota]", "Pilih Kota", "$r[kota]", "", "200px", "chosen-select", "") . "
   </div>
 </p>
 <p>
  <label class=\"l-input-small\">Email</label>
  <div class=\"field\">
    <input type=\"text\" id=\"inp[email]\" name=\"inp[email]\"  value=\"$r[email]\" class=\"smallinput\" style=\"width:220px;\"/>
  </div>
</p>
<p>
  <label class=\"l-input-small\">No Telepon</label>
  <div class=\"field\">
    <input type=\"text\" id=\"inp[telp]\" name=\"inp[telp]\"  value=\"$r[telp]\" class=\"smallinput\" style=\"width:220px;\"/>
  </div>
</p>
<p>
  <label class=\"l-input-small\">Status</label>
  <div class=\"field\">
    <div class=\"fradio\">
      <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" style=\"width:300px;\" " . ($r[status] == '1' ? "checked" : '') . " $default/> Aktif
      
      <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"2\" style=\"width:300px;\" " . ($r[status] == '2' ? "checked" : '') . "/> Tidak Aktif
    </div>
  </div>
</p>
</fieldset>
<br>
<fieldset>
  <legend>PIC</legend>
  <p>
    <label class=\"l-input-small\">Pimpinan</label>
    <div class=\"field\">
      <input type=\"text\" id=\"inp[pimpinan]\" name=\"inp[pimpinan]\"  value=\"$r[pimpinan]\" class=\"smallinput\" style=\"width:220px;\"/>
    </div>
  </p>
  <p>
    <label class=\"l-input-small\">Wakil</label>
    <div class=\"field\">
      <input type=\"text\" id=\"inp[wakil]\" name=\"inp[wakil]\"  value=\"$r[wakil]\" class=\"smallinput\" style=\"width:220px;\"/>
    </div>
  </p>
</fieldset>
<br>
<div align=\"right\">
  <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
  <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"window.location='index.php?" . getPar($par, "mode") . "';\"/>
</div>
</form>
</div>

";

  return $text;
}

function tambah()
{
  global $s, $inp, $par, $cID, $arrParam, $folder_upload;
  repField($inp);
  $lastID = getField("SELECT id_upz FROM cabang_upz ORDER BY id_upz DESC LIMIT 1") + 1;

  $sql = "INSERT INTO cabang_upz (id_upz, id_cabang, upz, kode, alamat, provinsi, kota, email, telp, pimpinan, wakil, status, created_date, created_by) VALUES ('$lastID','$inp[id_cabang]','$inp[upz]','$inp[kode]','$inp[alamat]','$inp[provinsi]','$inp[kota]','$inp[email]','$inp[telp]','$inp[pimpinan]','$inp[wakil]','$inp[status]',now(),'$cID')";

  /*var_dump($sql);
    die();*/

  db($sql);
  echo "<script>alert('Data berhasil disimpan');</script>";
  echo "<script>window.location.href='index.php?" . getPar($par, "mode,id_upz") . "';</script>";
}

function ubah()
{
  global $s, $inp, $par, $arrParam, $folder_upload, $cID;
  repField();

  $sql = "UPDATE cabang_upz SET id_cabang = '$inp[id_cabang]', upz = '$inp[upz]', kode = '$inp[kode]', alamat = '$inp[alamat]', provinsi = '$inp[provinsi]', kota = '$inp[kota]', email = '$inp[email]', telp = '$inp[telp]', pimpinan = '$inp[pimpinan]', wakil = '$inp[wakil]', status = '$inp[status]', updated_date = now(), updated_by = '$cID' WHERE id_upz = '$par[id_upz]'";


  /*var_dump($sql);
    die();*/

  db($sql);
  echo "<script>alert('Data berhasil diubah');</script>";
  echo "<script>window.location.href='index.php?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
  global $s, $inp, $par, $folder_upload;
  $sql = "DELETE FROM cabang_upz WHERE id_upz = '$par[id_upz]'";
  db($sql);
  echo "<script>window.location.href='index.php?" . getPar($par, "mode,id_upz") . "';</script>";
}

function delete_file()
{
  global $s, $inp, $par, $folder_upload, $cUsername;
  $file = getField("SELECT file FROM data_putusan WHERE id ='$par[id]'");
  if (file_exists($folder_upload . $file) and $file != "")
    unlink($folder_upload . $file);

  $sql = "UPDATE data_putusan SET file='' WHERE id='$par[id]'";
  db($sql);

  echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}
