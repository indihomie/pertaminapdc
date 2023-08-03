<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

$fFoto = "files/program/";


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


$sql = "SELECT * FROM `tbl_program` WHERE `kodeProgram` = '$par[kodeProgram]'";

$res = db($sql);

$row = mysql_fetch_array($res);


$t = $row[status] == 't' ? "checked" : "";

$f = $row[status] == 'f' ? "checked" : "";

$default = empty($row[status]) ? "checked" : "";



empty($row['urutanProgram']) ? $row['urutanProgram'] = getField("select max(urutanProgram) from tbl_program") + 1 : $row['urutanProgram'] = getAngka($row[urutanProgram]);



setValidation("is_null", "inp[judulProgram]", "Anda belum mengisi Judul");

setValidation("is_null", "inp[urutanProgram]", "Anda belum mengisi Urutan");

setValidation("is_null", "inp[ringkasanProgram]", "Anda belum mengisi Ringkasan");

setValidation("is_null", "inp[isiProgram]", "Anda belum mengisi Isi");

echo getValidation();

?>


<script src="sources/js/default.js"></script>


<div class="pageheader">

	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>

	<?= getBread(ucwords($par[mode]." data")) ?>

	<span class="pagedesc">&nbsp;</span>

</div>



<div id="contentwrapper" class="contentwrapper">



	<form class="stdform" method="POST" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" id="form" enctype="multipart/form-data">



		<div style="position:absolute; top: 15px; right: 20px;">

			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />

			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par,"mode,kodeProgram") ?>';" />

		</div>



		<fieldset>

			<legend>EDIT DATA</legend>



			<p>

				<label class="l-input-small">Judul</label>

				<div class="field">

					<input type="text" id="inp[judulProgram]" name="inp[judulProgram]" class="smallinput" style="width:95%" value="<?= $row[judulProgram] ?>" />

				</div>

            </p>
            

			<p>

				<label class="l-input-small">Ringkasan</label>

				<div class="field">

					<textarea class="smallinput" style="height: 50px; width:95%" id="inp[ringkasanProgram]" name="inp[ringkasanProgram]"><?= $row[ringkasanProgram] ?></textarea>

				</div>

			</p>



			<p>

				<textarea id="isiProgram" name="inp[isiProgram]" ><?= $row[isiProgram] ?></textarea>

			</p>



			<p>

				<label class="l-input-small">Foto</label>

				<div class="field">



					<?php

					if(empty($row[fotoProgram])){

						?>

						<input type="text" id="fileTemp" name="fileTemp" class="smallinput">

						<div class="fakeupload" style="padding-left: 40px;">

							<input type="file" id="file" name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;">

						</div>


						<?php }else{ ?>


						<img src="<?= $fFoto.$row[fotoProgram] ?>" width="50px">


						<a href="?par[mode]=delFoto<?= getPar($par,"mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>


						<?php } ?>



					</div>

				</p>


				<p>

					<label class="l-input-small">Urutan</label>

					<div class="field">

						<input type="text" id="inp[urutanProgram]" name="inp[urutanProgram]" class="smallinput" style="text-align:right; width:50px;" onkeyup="cekAngka(this);" value="<?= $row[urutanProgram] ?>" />

					</div>

				</p>


				<p>

					<label class="l-input-small">Status</label>

					<div class="field">

						<div class="sradio" style="padding-top:5px;padding-left:8px;">

							<input type="radio" name="inp[statusProgram]" value="t" <?= $t." ".$default ?>> <span style="padding-right:10px;">Tampil</span>

							<input type="radio" name="inp[statusProgram]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>

						</div>

					</div>

				</p>


			</fieldset>

		</form>



	</div>



	<script type="text/javascript" src="plugins/TinyMCE/jquery.tinymce.js"></script>



	<script type="text/javascript">

		jQuery(document).ready(function () {

			jQuery('#isiProgram').tinymce({

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

				setup: function (ed) {

					ed.onKeyDown.add(function (ed, evt) {

						if (evt.keyCode === 9) {

							ed.execCommand('mceInsertRawHTML', false, '\x09');

							evt.preventDefault();

							evt.stopPropagation();

							return false;

						}

					});

				}

			});

		});

	</script>



	<?php



	function save($params = ""){

		global $s, $inp, $par, $cUsername, $fFoto, $cID;

		repField();


		$first = array("-"," ",":");

		$end = array("","","");	

		$curdate = str_replace($first, $end, date('Y-m-d H:i'));



		$file = $_FILES["file"]["tmp_name"];

		$file_name = $_FILES["file"]["name"];

		if(($file!="") and ($file!="none")){						

			fileUpload($file, $file_name, $fFoto);		

			$file = "program_".uniqid()."_".$curdate.".".getExtension($file_name);

			fileRename($fFoto, $file_name, $file);

		}


		if(empty($file)) $file = getField("SELECT `fotoProgram` FROM `tbl_program` WHERE `kodeProgram` = '$par[kodeProgram]'");



		$kodeProgram = getField("SELECT `kodeProgram` FROM `tbl_program` ORDER BY `kodeProgram` DESC LIMIT 1")+1;	



		if ($params == "insert") {

			$sql = "
            INSERT INTO 
            `tbl_program` (`kodeProgram`, `urutanProgram`, `judulProgram`, `ringkasanProgram`, `isiProgram`, `fotoProgram`, `statusProgram`, `createBy`, `createTime`)
            VALUES ('$kodeProgram', '".setAngka($inp[urutanProgram])."', '$inp[judulProgram]', '$inp[ringkasanProgram]', '$inp[isiProgram]', '$file', '$inp[statusProgram]', '$cID', '".date('Y-m-d H:i:s)')."');";


		}else{			

			$sql = "UPDATE `tbl_program` SET

			`urutanProgram`     = '".setAngka($inp[urutanProgram])."',

			`judulProgram`       = '$inp[judulProgram]',

			`ringkasanProgram`   = '$inp[ringkasanProgram]',

			`isiProgram`         = '$inp[isiProgram]',

			`fotoProgram`        = '$file',

			`statusProgram`      = '$inp[statusProgram]',

			`updateBy`          = '$cID',

			`updateTime`        = '".date('Y-m-d H:i:s')."'

			WHERE `kodeProgram`  = '$par[kodeProgram]'
			";

			$kodeProgram = $par['kodeProgram'];

		}

		db($sql);
		echo "

		<script>

			alert('DATA BERHASIL DISIMPAN');

			window.location = '?par[mode]=edit&par[kodeProgram]=$kodeProgram".getPar($par,"mode")."';

		</script>

		";
	}
?>