<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

$fFoto = "files/parameter/";


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


$sql = "SELECT * FROM `tbl_parameter` WHERE `kodeParameter` = '$par[kodeParameter]'";

$res = db($sql);

$row = mysql_fetch_array($res);

if(empty($row[urutanParameter]))
	$row[urutanParameter] = getField("select max(urutanParameter) from tbl_parameter where tipeParameter='".$arrParam[$s]."'") + 1;

$t = $row[status] == 't' ? "checked" : "";

$f = $row[status] == 'f' ? "checked" : "";

$default = empty($row[status]) ? "checked" : "";



empty($row['tanggalParameter']) ? $row['tanggalParameter'] = date('d/m/Y') : $row['tanggalParameter'] = getTanggal($row[tanggalParameter]);



setValidation("is_null", "inp[isiParameter]", "Anda belum mengisi Keterangan");

setValidation("is_null", "inp[urutanParameter]", "Anda belum mengisi Urutan");


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

			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par,"mode,kodeParameter") ?>';" />

		</div>



		<fieldset>

			<legend>EDIT DATA</legend>


			<p>

				<label class="l-input-small">Keterangan</label>

				<div class="field">

					<textarea class="smallinput" style="height: 50px" id="inp[isiParameter]" name="inp[isiParameter]"><?= $row[isiParameter] ?></textarea>

				</div>

			</p>


			<p>

				<label class="l-input-small">Urutan</label>

				<div class="field">

					<input type="text" id="inp[urutanParameter]" name="inp[urutanParameter]" class="smallinput" value="<?= getAngka($row[urutanParameter]) ?>" style="text-align:right; width:50px;" onkeyup="cekAngka(this);" />

				</div>

			</p>


				<p>

					<label class="l-input-small">Status</label>

					<div class="field">

						<div class="sradio" style="padding-top:5px;padding-left:8px;">

							<input type="radio" name="inp[statusParameter]" value="t" <?= $t." ".$default ?>> <span style="padding-right:10px;">Tampil</span>

							<input type="radio" name="inp[statusParameter]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>

						</div>

					</div>

				</p>


			</fieldset>

		</form>



	</div>



	<script type="text/javascript" src="plugins/TinyMCE/jquery.tinymce.js"></script>



	<script type="text/javascript">

		jQuery(document).ready(function () {

			jQuery('#isiParameter').tinymce({

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

							evt.prparameterDefault();

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

		global $s, $inp, $par, $cUsername, $arrParam, $fFoto, $cID;

		repField();


		$first = array("-"," ",":");

		$end = array("","","");	

		$curdate = str_replace($first, $end, date('Y-m-d H:i'));

		$kodeParameter = getField("SELECT `kodeParameter` FROM `tbl_parameter` ORDER BY `kodeParameter` DESC LIMIT 1")+1;	



		if ($params == "insert") {

			$sql = "
            INSERT INTO 
            `tbl_parameter` (`kodeParameter`, `tipeParameter`, `isiParameter`, `urutanParameter`, `statusParameter`, `createBy`, `createTime`)
            VALUES ('$kodeParameter', '".$arrParam[$s]."', '$inp[isiParameter]', '".getAngka($inp[urutanParameter])."', '$inp[statusParameter]', '$cID', '".date('Y-m-d H:i:s)')."');";


		}else{			

			$sql = "UPDATE `tbl_parameter` SET

			`isiParameter`         = '$inp[isiParameter]',
			
            `urutanParameter`      = '".getAngka($inp[urutanParameter])."',

			`statusParameter`      = '$inp[statusParameter]',

			`updateBy`          = '$cID',

			`updateTime`        = '".date('Y-m-d H:i:s')."'

			WHERE `kodeParameter`  = '$par[kodeParameter]'
			";

			$kodeParameter = $par['kodeParameter'];

		}

		db($sql);
		echo "

		<script>

			alert('DATA BERHASIL DISIMPAN');

			window.location = '?par[mode]=edit&par[kodeParameter]=$kodeParameter".getPar($par,"mode")."';

		</script>

		";
	}
?>