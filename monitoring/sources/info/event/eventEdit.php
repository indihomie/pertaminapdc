<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

$fFoto = "files/event/";


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


$sql = "SELECT tbl_event.*,app_user.namaUser from tbl_event INNER JOIN app_user ON app_user.idPegawai=tbl_event.updateBy where `kodeEvent` = '$par[kodeEvent]'";

$res = db($sql);

$row = mysql_fetch_array($res);


$t = $row[status] == 't' ? "checked" : "";

$f = $row[status] == 'f' ? "checked" : "";

$default = empty($row[status]) ? "checked" : "";



empty($row['tanggalEvent']) ? $row['tanggalEvent'] = date('d/m/Y') : $row['tanggalEvent'] = getTanggal($row[tanggalEvent]);



setValidation("is_null", "inp[judulEvent]", "Anda belum mengisi Judul Event");

setValidation("is_null", "inp[waktuEvent]", "Anda belum mengisi Waktu Event");

setValidation("is_null", "inp[tanggalEvent]", "Anda belum mengisi Tanggal Event");

setValidation("is_null", "inp[lokasiEvent]", "Anda belum mengisi Lokasi Event");


// setValidation("is_null", "inp[isiEvent]", "Anda belum mengisi Isi Event");


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

			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeEvent") ?>';" />

		</div>
		<fieldset>
			<legend>EDIT DATA</legend>
			<p>
				<label class="l-input-small">Judul Event</label>
				<div class="field">
					<input type="text" id="inp[judulEvent]" name="inp[judulEvent]" class="smallinput" value="<?= $row[judulEvent] ?>" style="width: 95%;" required />
				</div>
			</p>
			<p>
				<label class="l-input-small">Tanggal</label>
				<div class="field">
					<input type="text" id="inp[tanggalEvent]" name="inp[tanggalEvent]" class="smallinput hasDatePicker" value="<?= $row[tanggalEvent] ?>" />
				</div>
			</p>
			<p>
				<label class="l-input-small">Waktu</label>
				<div class="field">
					<input type="text" id="inp[waktuEvent]" name="inp[waktuEvent]" class="smallinput" value="<?= $row[waktuEvent] ?>" required />
				</div>
			</p>
			<p>
				<label class="l-input-small">Lokasi</label>
				<div class="field">
					<textarea class="smallinput" style="height: 50px" id="inp[lokasiEvent]" name="inp[lokasiEvent]" required><?= $row[lokasiEvent] ?></textarea>
				</div>
			</p>
			<p>
				<label class="l-input-small">Penyelenggara</label>
				<div class="field">
					<input type="text" id="inp[penyelenggaraEvent]" name="inp[penyelenggaraEvent]" class="smallinput" value="<?= $row[penyelenggaraEvent] ?>" required />
				</div>
			</p>
			<p>
				<textarea id="isiEvent" name="inp[isiEvent]"><?= $row[isiEvent] ?></textarea>
			</p>
			<p>
				<label class="l-input-small">Foto</label>
				<div class="field">
					<?php
					if (empty($row[fotoEvent])) {
					?>
						<input type="text" id="fileTemp" name="fileTemp" class="smallinput">
						<div class="fakeupload" style="padding-left: 40px;">
							<input type="file" id="file" name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" required>
						</div>

					<?php } else { ?>
						<img src="<?= $fFoto . $row[fotoEvent] ?>" width="50px">
						<br>
						<a href="?par[mode]=delFoto<?= getPar($par, "mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>
					<?php } ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kategori Event</label>
				<div class="field">
					<?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='MDE'", "kodeData", "namaData", "inp[kategoriEvent]", "Kategori Event", "$row[kategoriEvent]", "", "200px", "chosen-select") ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field">
					<div class="sradio" style="padding-top:5px;padding-left:8px;">
						<input type="radio" name="inp[statusEvent]" value="t" <?= $t . " " . $default ?>> <span style="padding-right:10px;">Tampil</span>
						<input type="radio" name="inp[statusEvent]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>
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

		$('#isiEvent').tinymce({

			script_url: 'plugins/TinyMCE/tiny_mce.js',

			theme: "advanced",

			skin: "themepixels",

			width: "100%",

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

			table_row_limit: 10,

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
			var textArea = tinymce.activeEditor.getContent({
				format: 'text'
			}).length;
			var kategori = $('#inp\\[kategoriEvent\\]').val();
			if (textArea == 0) {
				alert('Isi Event Belum Diisi');
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
	repField();
	$first = array("-");
	$end = array("");
	$curdate = str_replace($first, $end, date('Y-m'));
	$fileId = "event_" . $curdate;

	if ($params == "insert") {
		$kodeEvent = getField("SELECT `kodeEvent` FROM `tbl_event` ORDER BY `kodeEvent` DESC LIMIT 1") + 1;
		$fileId .=  $kodeEvent;
		$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");
		$sql = "
            INSERT INTO 
            `tbl_event` (`kodeEvent`, `tanggalEvent`, `judulEvent`, `lokasiEvent`, `isiEvent`, `fotoEvent`, `waktuEvent`, `statusEvent`,`kategoriEvent`,`penyelenggaraEvent`, `createBy`, `createTime`)
            VALUES ('$kodeEvent', '" . setTanggal($inp[tanggalEvent]) . "', '$inp[judulEvent]', '$inp[lokasiEvent]', '$inp[isiEvent]', '$file', '$inp[waktuEvent]', '$inp[statusEvent]','$inp[kategoriEvent]','$inp[penyelenggaraEvent]', '$cID', '" . date('Y-m-d H:i:s)') . "');";
	} else {
		$cek_fotoEvent = getField("SELECT fotoEvent from tbl_event where kodeEvent ='$par[kodeEvent]'");
		if (empty($cek_fotoEvent)) {
			$fileId .= $par['kodeEvent'];
			$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");
			$sql = "UPDATE `tbl_event` SET
			`tanggalEvent`     = '" . setTanggal($inp[tanggalEvent]) . "',
			`judulEvent`       = '$inp[judulEvent]',
			`lokasiEvent`      = '$inp[lokasiEvent]',
			`isiEvent`         = '$inp[isiEvent]',
			`fotoEvent`        = '$file',
            `waktuEvent`       = '$inp[waktuEvent]',
			`statusEvent`      = '$inp[statusEvent]',
			`kategoriEvent`	   = '$inp[kategoriEvent]',
			`penyelenggaraEvent`= '$inp[penyelenggaraEvent]',
			`updateBy`         = '$cUsername',
			`updateTime`       = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeEvent`  = '$par[kodeEvent]'
			";
			$kodeEvent = $par['kodeEvent'];
		} else {
			$sql = "UPDATE `tbl_event` SET
			`tanggalEvent`     = '" . setTanggal($inp[tanggalEvent]) . "',
			`judulEvent`       = '$inp[judulEvent]',
			`lokasiEvent`      = '$inp[lokasiEvent]',
			`isiEvent`         = '$inp[isiEvent]',
            `waktuEvent`       = '$inp[waktuEvent]',
			`statusEvent`      = '$inp[statusEvent]',
			`kategoriEvent`	   = '$inp[kategoriEvent]',
			`penyelenggaraEvent`= '$inp[penyelenggaraEvent]',
			`updateBy`         = '$cUsername',
			`updateTime`       = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeEvent`  = '$par[kodeEvent]'
			";
			$kodeEvent = $par['kodeEvent'];
		}
	}
	db($sql);
	echo "
		<script>
			alert('DATA BERHASIL DISIMPAN');
			window.location = '?par[mode]=edit&par[kodeEvent]=$kodeEvent" . getPar($par, "mode") . "';
		</script>
		";
}
?>