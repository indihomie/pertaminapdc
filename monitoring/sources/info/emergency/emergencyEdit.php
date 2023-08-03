<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

$fFoto = "files/emergency/";


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


$sql = "SELECT tbl_emergency.*,app_user.namaUser from tbl_emergency INNER JOIN app_user ON app_user.idPegawai=tbl_emergency.updateBy where `kodeEmergency` = '$par[kodeEmergency]'";

$res = db($sql);

$row = mysql_fetch_array($res);


$t = $row[status] == 't' ? "checked" : "";

$f = $row[status] == 'f' ? "checked" : "";

$default = empty($row[status]) ? "checked" : "";



empty($row['tanggalEmergency']) ? $row['tanggalEmergency'] = date('d/m/Y') : $row['tanggalEmergency'] = getTanggal($row[tanggalEmergency]);



setValidation("is_null", "inp[judulEmergency]", "Anda belum mengisi Judul Emergency");

setValidation("is_null", "inp[tanggalEmergency]", "Anda belum mengisi Tanggal Emergency");

setValidation("is_null", "inp[ringkasanEmergency]", "Anda belum mengisi Ringkasan Emergency");
setValidation("is_null", "inp[file]", "Anda belum Upload Foto ");


// setValidation("is_null", "inp[isiEmergency]", "Anda belum mengisi Isi Emergency");


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

			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeEmergency") ?>';" />

		</div>



		<fieldset>

			<legend>EDIT DATA</legend>



			<p>

				<label class="l-input-small">Judul Emergency</label>

				<div class="field">

					<input type="text" id="inp[judulEmergency]" name="inp[judulEmergency]" class="smallinput" value="<?= $row[judulEmergency] ?>" />

				</div>

			</p>



			<p>

				<label class="l-input-small">Tanggal</label>

				<div class="field">

					<input type="text" id="inp[tanggalEmergency]" name="inp[tanggalEmergency]" class="smallinput hasDatePicker" value="<?= $row[tanggalEmergency] ?>" />

				</div>

			</p>



			<p>

				<label class="l-input-small">Ringkasan</label>

				<div class="field">

					<textarea class="smallinput" style="height: 50px" id="inp[ringkasanEmergency]" name="inp[ringkasanEmergency]"><?= $row[ringkasanEmergency] ?></textarea>

				</div>

			</p>



			<p>

				<textarea id="isiEmergency" name="inp[isiEmergency]"><?= $row[isiEmergency] ?></textarea>

			</p>



			<p>

				<label class="l-input-small">Foto</label>

				<div class="field">



					<?php

					if (empty($row[fotoEmergency])) {

					?>

						<input type="text" id="fileTemp" name="fileTemp" class="smallinput">

						<div class="fakeupload" style="padding-left: 40px;">

							<input type="file" id="file" name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" required>

						</div>


					<?php } else { ?>


						<img src="<?= $fFoto . $row[fotoEmergency] ?>" width="50px">


						<a href="?par[mode]=delFoto<?= getPar($par, "mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>


					<?php } ?>



				</div>

			</p>

			<p>
				<label class="l-input-small">Kategori Emergency</label>
				<div class="field">
					<?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='KIP'", "kodeData", "namaData", "inp[kategoriEmergency]", "Kategori Emergency", "$row[kategoriEmergency]", "", "200px", "chosen-select") ?>

				</div>
			</p>

			<p>

				<label class="l-input-small">Status</label>

				<div class="field">

					<div class="sradio" style="padding-top:5px;padding-left:8px;">

						<input type="radio" name="inp[statusEmergency]" value="t" <?= $t . " " . $default ?>> <span style="padding-right:10px;">Tampil</span>

						<input type="radio" name="inp[statusEmergency]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>

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

		$('#isiEmergency').tinymce({

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
			var kategori = $('#inp\\[kategoriEmergency\\]').val();
			if (textArea == 0) {
				alert('Isi Info Belum Diisi');
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
	$fileId = "emergency_" . $curdate;
	if ($params == "insert") {
		$kodeEmergency = getField("SELECT `kodeEmergency` FROM `tbl_emergency` ORDER BY `kodeEmergency` DESC LIMIT 1") + 1;
		$fileId .=  $kodeEmergency;
		$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");
		$sql = "
            INSERT INTO 
            `tbl_emergency` (`kodeEmergency`, `tanggalEmergency`, `judulEmergency`, `ringkasanEmergency`, `isiEmergency`, `fotoEmergency`, `statusEmergency`,`kategoriEmergency`, `createBy`, `createTime`)
            VALUES ('$kodeEmergency', '" . setTanggal($inp[tanggalEmergency]) . "', '$inp[judulEmergency]', '$inp[ringkasanEmergency]', '$inp[isiEmergency]', '$file', '$inp[statusEmergency]','$inp[kategoriEmergency]', '$cID', '" . date('Y-m-d H:i:s)') . "');";
	} else {
		$cek_fotoEmergency = getField("SELECT fotoEmergency from tbl_emergency where kodeEmergency ='$par[kodeEmergency]'");
		if (empty($cek_fotoEmergency)) {
			$fileId .= $par['kodeEmergency'];
			$file = uploadImages("$fileId", "file", "$fFoto", "" . $arrParam[$s] . "", "", "");
			$sql = "UPDATE `tbl_emergency` SET
			`tanggalEmergency`     = '" . setTanggal($inp[tanggalEmergency]) . "',
			`judulEmergency`       = '$inp[judulEmergency]',
			`ringkasanEmergency`   = '$inp[ringkasanEmergency]',
			`isiEmergency`         = '$inp[isiEmergency]',
			`fotoEmergency`        = '$file',
			`statusEmergency`      = '$inp[statusEmergency]',
			`kategoriEmergency`		= '$inp[kategoriEmergency]',
			`updateBy`          = '$cID',
			`updateTime`        = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeEmergency`  = '$par[kodeEmergency]'
			";
			$kodeEmergency = $par['kodeEmergency'];
		} else {
			$sql = "UPDATE `tbl_emergency` SET
			`tanggalEmergency`     = '" . setTanggal($inp[tanggalEmergency]) . "',
			`judulEmergency`       = '$inp[judulEmergency]',
			`ringkasanEmergency`   = '$inp[ringkasanEmergency]',
			`isiEmergency`         = '$inp[isiEmergency]',
			`statusEmergency`      = '$inp[statusEmergency]',
			`kategoriEmergency`		= '$inp[kategoriEmergency]',
			`updateBy`          = '$cID',
			`updateTime`        = '" . date('Y-m-d H:i:s') . "'
			WHERE `kodeEmergency`  = '$par[kodeEmergency]'
			";
			$kodeEmergency = $par['kodeEmergency'];
		}
	}
	// var_dump($sql);
	// die();
	db($sql);
	echo "
		<script>
			alert('DATA BERHASIL DISIMPAN');
			window.location = '?par[mode]=edit&par[kodeEmergency]=$kodeEmergency" . getPar($par, "mode") . "';
		</script>
		";
}
?>