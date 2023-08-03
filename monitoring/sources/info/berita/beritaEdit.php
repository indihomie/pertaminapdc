<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

$fFoto = "files/berita/";

if (isset($_POST["btnSimpan"])) {
	switch ($par[mode]) {
		case "add":
			save('insert');
			die();
			break;
		case "edit":
			save('update');
			die();
			break;
	}
}

$sql = "SELECT tbl_berita.*,app_user.namaUser from tbl_berita INNER JOIN app_user ON app_user.idPegawai=tbl_berita.updateBy where `kodeBerita` = '$par[kodeBerita]'";
$res = db($sql);
$row = mysql_fetch_array($res);
$t = $row[status] == 't' ? "checked" : "";
$f = $row[status] == 'f' ? "checked" : "";
$default = empty($row[status]) ? "checked" : "";
$queryGroup = "SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory = 'GC'";

empty($row['tanggalBerita']) ? $row['tanggalBerita'] = date('d/m/Y') : $row['tanggalBerita'] = getTanggal($row[tanggalBerita]);
setValidation("is_null", "inp[judulBerita]", "Anda belum mengisi Judul Berita");
setValidation("is_null", "inp[sumberBerita]", "Anda belum mengisi Sumber Berita");
setValidation("is_null", "inp[tanggalBerita]", "Anda belum mengisi Tanggal Berita");
setValidation("is_null", "inp[ringkasanBerita]", "Anda belum mengisi Ringkasan Berita");
// setValidation("is_null", "inp[isiBerita]", "Anda belum mengisi Isi Berita");
echo getValidation();
?>
<script src="sources/js/default.js"></script>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread(ucwords($par[mode] . " data")) ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<div id="contentwrapper" class="contentwrapper">
	<form class="stdform" method="POST" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" id="form" enctype="multipart/form-data">
		<div style="position:absolute; top: 15px; right: 20px;">
			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" id="submit" />
			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeBerita") ?>';" />
		</div>
		<fieldset>
			<legend>EDIT DATA</legend>
			<p>
				<label class="l-input-small">Judul Berita</label>
				<div class="field">
					<input type="text" id="inp[judulBerita]" name="inp[judulBerita]" value="<?= $row[judulBerita] ?>" style="width: 95%;" required />
				</div>
			</p>
			<p>
				<label class="l-input-small">Sumber</label>
				<div class="field">
					<input type="text" id="inp[sumberBerita]" name="inp[sumberBerita]" class="smallinput" value="<?= $row[sumberBerita] ?>" required />
				</div>
			</p>
			<p>
				<label class="l-input-small">Tanggal</label>
				<div class="field">
					<input type="text" id="inp[tanggalBerita]" name="inp[tanggalBerita]" class="smallinput hasDatePicker" value="<?= $row[tanggalBerita] ?>" required />
				</div>
			</p>
			<p>
				<label class="l-input-small">Author Berita</label>
				<div class="field">
					<input type="text" id="inp[authorBerita]" name="inp[authorBerita]" value="<?= $row[authorBerita] ?>" style="width: 102px;" required />
				</div>
			</p>

			<p>
				<label class="l-input-small">Ringkasan</label>
				<div class="field">
					<textarea id="inp[ringkasanBerita]" name="inp[ringkasanBerita]" style="width: 95%;" required rows="4"><?= $row[ringkasanBerita] ?></textarea>
				</div>
			</p>
			<p>
				<label class="l-input-small">Index Tag</label>
				<div class="field">
					<input type="text" id="inp[indexTagBerita]" name="inp[indexTagBerita]" value="<?= $row[indexTagBerita] ?>" style="width: 95%;" required />
				</div>
			</p>
			<p>
				<textarea id="inp[isiBerita]" name="inp[isiBerita]" class="tinyMCE"><?= $row[isiBerita] ?></textarea>
			</p>
			<p>
				<label class="l-input-small">Foto</label>
				<div class="field">
					<?php
					if (empty($row[fotoBerita])) {
					?>
						<input type="text" id="fileTemp" name="fileTemp" class="smallinput">
						<div class="fakeupload" style="padding-left: 40px;">
							<input type="file" id="file" name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" required>
						</div>
					<?php } else { ?>
						<img src="<?= $fFoto . $row[fotoBerita] ?>" width="50px">
						<br>
						<a href="?par[mode]=delFoto<?= getPar($par, "mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>
					<?php } ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kategori Berita</label>
				<div class="field">
					<?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MDB'", "kodeData", "namaData", "inp[kategoriBerita]", "All Kategori", "$row[kategoriBerita]", "", "200px", "chosen-select") ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field">
					<div class="sradio" style="padding-top:5px;padding-left:8px;">
						<input type="radio" name="inp[statusBerita]" value="t" <?= $t . " " . $default ?>> <span style="padding-right:10px;">Tampil</span>
						<input type="radio" name="inp[statusBerita]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>
					</div>
				</div>
			</p>
		</fieldset>
		<?= show_history($row) ?>

	</form>
</div>
<script type="text/javascript" src="plugins/TinyMCE/jquery.tinymce.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('textarea.tinyMCE').tinymce({
			script_url: 'plugins/TinyMCE/tiny_mce.js',
			theme: "advanced",
			skin: "themepixels",
			width: "100%",
			height: "300px",
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
			table_row_limit: 5,
			table_col_limit: 5,
			setup: function(ed) {
				ed.onKeyDown.add(function(ed, evt) {
					if (evt.keyCode === 9) {
						ed.execCommand('mceInsertRawHTML', false, '\x09');
						evt.preventDefault();
						evt.stopPropagation();
						return false;
					}
				});
			}
		});
		$("#submit").click(function() {
			// if (tinymce.EditorManager.get('textarea.tinyMCE').getContent() === '') {
			// 	alert('Blog Description can not be empty.');
			// 	return false;
			// }
			var textArea = tinymce.activeEditor.getContent({
				format: 'text'
			}).length;
			var kategori = $('#inp\\[kategoriBerita\\]').val();
			if (textArea == 0) {
				alert('Isi Berita Belum Diisi');
				return false;
			} else if (kategori == "") {
				alert('Kategori Harus Dipilih');
				return false;
			} else {
				return true;
			}
		});
	});
</script>
<?php
function show_history($r = null)
{
	global $menuAccess, $s, $par;
	$result = "";
	if ($par[mode] == "edit") {
		$result = "
        <fieldset>
            <legend>History</legend>
            <table width=\"100%\" >
                <tr>
                    <td width=\"50%\">
                    <p>
                        <label class=\"l-input-small2\" >Created Date</label>
                        <span class=\"field\" id=\"created_date\">
                        " . $r[createTime] . "

                        </span>
                    </p>	
                    </td> 
                    <td >
                        <p>
                            <label class=\"l-input-small2\" >Created By</label>
                            <span class=\"field\" id=\"created_by\">
                            " . $r[namaUser] . "

                            </span>
                        </p>	
                    </td> 
                <tr>
                <tr>
                    <td width=\"50%\">
                    <p>
                        <label class=\"l-input-small2\" >Update Date</label>
                        <span class=\"field\" id=\"update_date\">
                        " . $r[updateTime] . "

                        </span>
                    </p>	
                    </td> 
                    <td >
                        <p>
                            <label class=\"l-input-small2\" >Update By</label>
                            <span class=\"field\" id=\"update_by\">
                            " . $r[namaUser] . "

                            </span>
                        </p>	
                    </td> 
                <tr>
            </table>
        </fieldset>";
	} else {
		$result = "";
	}
	return $result;
}
function save($params = "")
{
	global $arrParam, $s, $inp, $par, $cUsername, $fFoto, $cID;
	repField('judulBerita');
	repField('ringkasanBerita');
	repField('isiBerita');

	$first = array("-");
	$end = array("");
	$curdate = str_replace($first, $end, date('Y-m'));
	$fileId = "berita_" . $curdate;

	if ($params == "insert") {
		$kodeBerita = getField("SELECT `kodeBerita` FROM `tbl_berita` ORDER BY `kodeBerita` DESC LIMIT 1") + 1;
		$fileId .=  $kodeBerita;
		$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");

		$sql = "
            INSERT INTO 
            `tbl_berita` (`kodeBerita`, `tanggalBerita`, `judulBerita`, `ringkasanBerita`, `isiBerita`, `fotoBerita`, `sumberBerita`, `statusBerita`,`kategoriBerita`,`authorBerita`,`indexTagBerita`, `createBy`, `createTime`)
            VALUES ('$kodeBerita', '" . setTanggal($inp[tanggalBerita]) . "', '$inp[judulBerita]', '$inp[ringkasanBerita]', '$inp[isiBerita]', '$file', '$inp[sumberBerita]', '$inp[statusBerita]','$inp[kategoriBerita]','$inp[authorBerita]','$inp[indexTagBerita]', '$cUsername', '" . date('Y-m-d H:i:s)') . "');";
	} else {
		$cek_fotoBerita = getField("SELECT fotoBerita from tbl_berita where kodeBerita ='$par[kodeBerita]'");
		if (empty($cek_fotoBerita)) {
			$fileId .= $par['kodeBerita'];
			$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");
			$sql = "UPDATE `tbl_berita` SET
			`tanggalBerita`     = '" . setTanggal($inp[tanggalBerita]) . "',
			`judulBerita`       = '$inp[judulBerita]',
			`ringkasanBerita`   = '$inp[ringkasanBerita]',
			`isiBerita`         = '$inp[isiBerita]',
			`fotoBerita`        = '$file',
            `sumberBerita`      = '$inp[sumberBerita]',
			`statusBerita`      = '$inp[statusBerita]',
			`kategoriBerita`	= '$inp[kategoriBerita]',
			`authorBerita`		= '$inp[authorBerita]',
			`indexTagBerita`    = '$inp[indexTagBerita]',
			`updateBy`          = '$cUsername',
			`updateTime`        = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeBerita`  = '$par[kodeBerita]'
			";
			$kodeBerita = $par['kodeBerita'];
		} else {
			$sql = "UPDATE `tbl_berita` SET
			`tanggalBerita`     = '" . setTanggal($inp[tanggalBerita]) . "',
			`judulBerita`       = '$inp[judulBerita]',
			`ringkasanBerita`   = '$inp[ringkasanBerita]',
			`isiBerita`         = '$inp[isiBerita]',
            `sumberBerita`      = '$inp[sumberBerita]',
			`statusBerita`      = '$inp[statusBerita]',
			`kategoriBerita`	= '$inp[kategoriBerita]',
			`authorBerita`		= '$inp[authorBerita]',
			`indexTagBerita`    = '$inp[indexTagBerita]',
			`updateBy`          = '$cUsername',
			`updateTime`        = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeBerita`  = '$par[kodeBerita]'
			";
			$kodeBerita = $par['kodeBerita'];
		}
	}

	db($sql);
	echo "
		<script>
			alert('DATA BERHASIL DISIMPAN');
			window.location = '?par[mode]=edit&par[kodeBerita]=$kodeBerita" . getPar($par, "mode") . "';
		</script>
		";
}


?>