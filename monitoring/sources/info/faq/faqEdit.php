<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;


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


$sql = "SELECT tbl_faq.*,app_user.namaUser from tbl_faq INNER JOIN app_user ON app_user.idPegawai=tbl_faq.updateBy where `kodeFaq` = '$par[kodeFaq]'";

$res = db($sql);

$row = mysql_fetch_array($res);


$t = $row[status] == 't' ? "checked" : "";

$f = $row[status] == 'f' ? "checked" : "";

$default = empty($row[status]) ? "checked" : "";



empty($row['urutanFaq']) ? $row['urutanFaq'] = getAngka(getField("select max(urutanFaq) from tbl_faq") + 1) : $row['urutanFaq'] = getAngka($row[urutanFaq]);


setValidation("is_null", "inp[pertanyaanFaq]", "Anda belum mengisi Pertanyaan");


setValidation("is_null", "inp[jawabanFaq]", "Anda belum mengisi Jawaban");

setValidation("is_null", "inp[urutanFaq]", "Anda belum mengisi Urutan");

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
			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
			<input type="button" class="cancel radius2" value="Kembali" onclick="location.href='?<?= getPar($par, "mode,kodeFaq") ?>';" />
		</div>



		<fieldset>

			<legend>EDIT DATA</legend>
			<p>
				<label class="l-input-small">Pertanyaan</label>
				<div class="field">
					<textarea class="smallinput" style="height: 50px; width:95%;" id="inp[pertanyaanFaq]" name="inp[pertanyaanFaq]"><?= $row[pertanyaanFaq] ?></textarea>
				</div>
			</p>
			<p>
				<label class="l-input-small">Jawaban</label>
				<div class="field">
					<textarea class="smallinput" style="height: 50px;width:95%;" id="inp[jawabanFaq]" name="inp[jawabanFaq]"><?= $row[jawabanFaq] ?></textarea>
				</div>
			</p>
			<p>

				<label class="l-input-small">Urutan</label>

				<div class="field">
					<input type="text" id="inp[urutanFaq]" name="inp[urutanFaq]" class="smallinput" value="<?= $row[urutanFaq] ?>" style="text-align:right; width:50px;" onkeyup="cekAngka(this)" />
				</div>
			</p>
			<p>
				<label class="l-input-small">FAQ</label>
				<div class="field">
					<?= comboData("SELECT kodeData,namaData from mst_data where kodeCategory='KFQ'", "kodeData", "namaData", "inp[kategoriFaq]", "FAQ", "$row[kategoriFaq]", "", "200px", "chosen-select") ?>

				</div>
			</p>

			<p>

				<label class="l-input-small">Status</label>

				<div class="field">

					<div class="sradio" style="padding-top:5px;padding-left:8px;">

						<input type="radio" name="inp[statusFaq]" value="t" <?= $t . " " . $default ?>> <span style="padding-right:10px;">Tampil</span>

						<input type="radio" name="inp[statusFaq]" value="f" <?= $f ?>> <span style="padding-right:10px;">Tidak Tampil</span>

					</div>

				</div>

			</p>


		</fieldset>
		<?= show_history($row) ?>

	</form>



</div>



<script type="text/javascript" src="plugins/TinyMCE/jquery.tinymce.js"></script>



<script type="text/javascript">
	jQuery(document).ready(function() {

		jQuery('#jawabanFaq').tinymce({

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

	global $s, $inp, $par, $cUsername, $fFoto, $cID;

	repField();


	$first = array("-", " ", ":");

	$end = array("", "", "");

	$curdate = str_replace($first, $end, date('Y-m-d H:i'));


	$kodeFaq = getField("SELECT `kodeFaq` FROM `tbl_faq` ORDER BY `kodeFaq` DESC LIMIT 1") + 1;



	if ($params == "insert") {

		$sql = "
            INSERT INTO 
            `tbl_faq` (`kodeFaq`, `urutanFaq`, `pertanyaanFaq`, `jawabanFaq`, `statusFaq`,`kategoriFaq`, `createBy`, `createTime`)
            VALUES ('$kodeFaq', '" . setAngka($inp[urutanFaq]) . "', '$inp[pertanyaanFaq]', '$inp[jawabanFaq]', '$inp[statusFaq]','$inp[kategoriFaq]', '$cID', '" . date('Y-m-d H:i:s)') . "');";
	} else {

		$sql = "UPDATE `tbl_faq` SET

			`urutanFaq`     = '" . setAngka($inp[urutanFaq]) . "',

			`pertanyaanFaq`   = '$inp[pertanyaanFaq]',

			`jawabanFaq`         = '$inp[jawabanFaq]',

			`statusFaq`      = '$inp[statusFaq]',

			`kategoriFaq`	= '$inp[kategoriFaq]',

			`updateBy`          = '$cID',

			`updateTime`        = '" . date('Y-m-d H:i:s') . "'

			WHERE `kodeFaq`  = '$par[kodeFaq]'
			";

		$kodeFaq = $par['kodeFaq'];
	}
	// var_dump($sql);
	// die();
	db($sql);
	echo "

		<script>

			alert('DATA BERHASIL DISIMPAN');

			window.location = '?par[mode]=edit&par[kodeFaq]=$kodeFaq" . getPar($par, "mode") . "';

		</script>

		";
}
?>