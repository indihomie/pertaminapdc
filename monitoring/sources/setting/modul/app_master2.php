<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/menu/";

function order()
{
	global $inp, $par;

	$result = getField("select urutanData from mst_data where kodeInduk='$inp[kodeInduk]' and kodeCategory='$par[kodeCategory]' order by urutanData desc limit 1") + 1;

	return $result;
}

function cek()
{
	global $inp, $par;

	if (getField("select kodeCategory from mst_category where kodeCategory='$inp[kodeCategory]' and kodeCategory!='$par[kodeCategory]'"))
		return "sorry, code \" $inp[kodeCategory] \" already exist";
}

function chk()
{
	global $par;

	if (getField("select kodeCategory from mst_category where kodeInduk='$par[kodeCategory]'") || getField("select kodeCategory from mst_data where kodeCategory='$par[kodeCategory]'"))
		return "sorry, data has been use";
}

function chkDet()
{
	global $par;

	if (getField("select kodeData from mst_data where kodeInduk='$par[kodeData]'"))
		return "sorry, data has been use";
}

function hapusDet()
{
	global $par;

	$sql = "delete from mst_data where kodeData='$par[kodeData]'";
	db($sql);

	echo "<script>alertSave('Data berhasil disimpan', 'success', '?par[mode]=det" . getPar($par, "mode, kodeData") . "');</script>";
}

function ubahDet()
{
	global $inp, $par, $cUsername;
	repField();

	$sql = "update mst_data set kodeInduk='$inp[kodeInduk]', namaData='$inp[namaData]', keteranganData='$inp[keteranganData]', urutanData='" . setAngka($inp[urutanData]) . "', kodeMaster = '$inp[kodeMaster]', statusData='$inp[statusData]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeData='$par[kodeData]'";
	db($sql);

	echo "<script>alertSave('Data berhasil disimpan', 'success', '?par[mode]=det" . getPar($par, "mode, kodeData") . "');</script>";
}

function tambahDet()
{
	global $inp, $par, $cUsername;

	$kodeData = getField("select kodeData from mst_data order by kodeData desc limit 1") + 1;

	repField();
	$sql = "insert into mst_data (kodeData, kodeInduk, kodeMenu, kodeReport, kodeCategory, namaData, keteranganData, urutanData, kodeMaster, statusData, createBy, createTime) values ('$kodeData', '$inp[kodeInduk]', '$kodeMenu', '$kodeReport', '$par[kodeCategory]', '$inp[namaData]', '$inp[keteranganData]', '" . setAngka($inp[urutanData]) . "', '$inp[kodeMaster]', '$inp[statusData]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
	db($sql);

	echo "<script>alertSave('Data berhasil disimpan', 'success', '?par[mode]=det" . getPar($par, "mode,kodeData") . "');</script>";
}

function hapusIcon()
{
	global $par, $fFile;

	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");
	$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");
	if (file_exists($fFile . $iconMenu) and $iconMenu != "") unlink($fFile . $iconMenu);

	$sql = "update app_menu set iconMenu='' where kodeMenu='$kodeMenu'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "'</script>";
}

function hapus()
{
	global $par, $fFile;

	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");
	$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");
	if (file_exists($fFile . $iconMenu) and $iconMenu != "") unlink($fFile . $iconMenu);

	$sql = "delete from app_menu where kodeMenu='$kodeMenu'";
	db($sql);
	$sql = "delete from mst_category where kodeCategory='$par[kodeCategory]'";
	db($sql);

	echo "<script>window.location='?par[mode]=view" . getPar($par, "mode,kodeCategory") . "';</script>";
}

function ubah()
{
	global $inp, $par, $cUsername;

	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");

	repField();

	$sql = "update app_menu set namaMenu='$inp[namaCategory]', iconMenu='$inp[iconMenu]', urutanMenu='" . setAngka($inp[urutanCategory]) . "', statusMenu='$inp[statusCategory]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeMenu='$kodeMenu'";
	db($sql);

	$sql = "update mst_category set kodeCategory='$inp[kodeCategory]', kodeInduk='$inp[kodeInduk]', namaCategory='$inp[namaCategory]', keteranganCategory='$inp[keteranganCategory]', urutanCategory='" . setAngka($inp[urutanCategory]) . "', statusCategory='$inp[statusCategory]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeCategory='$par[kodeCategory]'";
	db($sql);

	$sql = "update mst_data set kodeCategory='$inp[kodeCategory]' where kodeCategory='$par[kodeCategory]'";
	db($sql);

	$sql = "update mst_category set kodeInduk='$inp[kodeCategory]' where kodeInduk='$par[kodeCategory]'";
	db($sql);

	$sql = "update app_parameter set nilaiParameter='$inp[kodeCategory]' where nilaiParameter='$par[kodeCategory]'";
	db($sql);

	echo "<script>alertSave('Data berhasil disimpan', 'success', '?par[mode]=view" . getPar($par, "mode,kodeCategory,kodeModul,kodeSite") . "');</script>";
}

function tambah()
{
	global $s, $inp, $par, $fFile, $cUsername;

	$kodeMenu = getField("select kodeMenu from app_menu order by kodeMenu desc") + 1;
	$sql = "select * from app_menu where kodeMenu='$s'";
	$res = db($sql);
	$r = mysql_fetch_array($res);
	$levelMenu = $r[levelMenu] + 1;

	$fileIcon = $_FILES["iconMenu"]["tmp_name"];
	$fileIcon_name = $_FILES["iconMenu"]["name"];
	if (($fileIcon != "") and ($fileIcon != "none")) {
		fileUpload($fileIcon, $fileIcon_name, $fFile);
		$iconMenu = "ico-" . $kodeMenu . "." . getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconMenu);
	}

	repField();

	echo $sql = "insert into app_menu (kodeMenu, kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, aksesMenu, iconMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime) values ('$kodeMenu', '$par[kodeModul]', '$par[kodeSite]', '$r[kodeMenu]', '$inp[namaCategory]', '$r[targetMenu]', '$r[aksesMenu]', '$iconMenu', '" . setAngka($inp[urutanCategory]) . "', '$inp[statusCategory]', '$levelMenu', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
	db($sql);
	$sql = "insert into mst_category (kodeCategory, kodeModul, kodeInduk, kodeMenu, namaCategory, keteranganCategory, urutanCategory, statusCategory, createBy, createTime) values ('$inp[kodeCategory]', '$par[kodeModul]', '$inp[kodeInduk]', '$kodeMenu', '$inp[namaCategory]', '$inp[keteranganCategory]', '" . setAngka($inp[urutanCategory]) . "', '$inp[statusCategory]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
	db($sql);

	echo "<script>alertSave('Data berhasil disimpan', 'success', '?par[mode]=view" . getPar($par, "mode,kodeCategory") . "');</script>";
}

function formDet()
{
	global $s, $par, $arrTitle, $ui;

	$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='$par[kodeCategory]'");
	$namaCategory = getField("select namaCategory from mst_category where kodeCategory='$kodeCategory'");

	$sql = "select * from mst_data where kodeData='$par[kodeData]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$queryCategory = "SELECT kodeData id, namaData description from mst_data where kodeCategory='$kodeCategory' and statusData='t' order by urutanData";

	if (empty($r[urutanData])) $r[urutanData] = getField("select urutanData from mst_data where kodeInduk='$par[kodeInduk]' and kodeCategory='$par[kodeCategory]' order by urutanData desc limit 1") + 1;
	if (empty($r[kodeInduk])) $r[kodeInduk] = $par[kodeInduk];

	$false =  $r[statusData] == "f" ? "checked=\"checked\"" : "";
	$true =  empty($false) ? "checked=\"checked\"" : "";

	if (!empty($kodeCategory))
		setValidation("is_null", "inp[kodeInduk]", "you must fill " . strtolower($namaCategory));
	setValidation("is_null", "inp[namaData]", "you must fill name");
	setValidation("is_null", "inp[urutanData]", "you must fill order");
	echo getValidation();
	?>

	<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			<h4 class="modal-title"><?= $arrTitle[$s] ?></h4>
		</div>
		<div class="modal-body">
			<div class="scroller" style="height:auto" data-always-visible="1" data-rail-visible1="1">
				<fieldset>
					<div class="row">
						<div class="col-md-12">
							<div class="form-horizontal">
								<div class="form-body">
									<?php if (!empty($kodeCategory)) $ui->createComboData($namaCategory, $queryCategory, "id", "description", "inp[kategoriModul]", $r[kategoriModul], "", "", "", " ", "onchange=\"order('" . getPar($par, "mode") . "');\"") ?>
									<?= $ui->createField("Nama", "inp[namaData]", $r[namaData], "t") ?>
									<?= $ui->createField("Deskripsi", "inp[keteranganData]", $r[keteranganData]) ?>
									<?= $ui->createSmallField("Urutan", "inp[urutanData]", $r[urutanData]) ?>
									<?= $ui->createSmallField("Kode", "inp[kodeMaster]", $r[kodeMaster]) ?>
									<?= $ui->createRadio("Status", "inp[statusData]", array("t" => "Active", "f" => "Not Active"), $r[statusData]) ?>
									<input type="hidden" id="count" name="count" value="<?= getField("select count(*) from mst_data where kodeInduk='$par[kodeData]'") ?> ">
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn btn-primary" name="btnSimpan" value="Save" />
		</div>
	</form>
<?php
}

function form()
{
	global $c, $s, $par, $arrTitle, $ui;

	$sql = "select t1.*, t2.iconMenu from mst_category t1 join app_menu t2 on (t1.kodeMenu=t2.kodeMenu) where t1.kodeCategory='$par[kodeCategory]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$par[kodeModul] = $c;

	$queryParent = "SELECT kodeCategory id, namaCategory description from mst_category where kodeModul='" . $par[kodeModul] . "' and kodeCategory!='$par[kodeCategory]' and statusCategory='t' order by urutanCategory";

	if (empty($r[urutanCategory])) $r[urutanCategory] = getField("select urutanCategory from mst_category where kodeModul='" . $par[kodeModul] . "' order by urutanCategory desc limit 1") + 1;

	setValidation("is_null", "inp[kodeCategory]", "you must fill code");
	setValidation("is_null", "inp[namaCategory]", "you must fill category");
	echo getValidation();
	?>

	<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			<h4 class="modal-title"><?= $arrTitle[$s] ?></h4>
		</div>
		<div class="modal-body">
			<div class="scroller" style="height:auto" data-always-visible="1" data-rail-visible1="1">
				<fieldset>
					<div class="row">
						<div class="col-md-12">
							<div class="form-horizontal">
								<div class="form-body">
									<?= $ui->createField("Code", "inp[kodeCategory]", $r[kodeCategory], "t") ?>
									<?= $ui->createComboData("Parent", $queryParent, "id", "description", "inp[kategoriModul]", $r[kategoriModul], "", "", "", " ") ?>
									<?= $ui->createField("Category", "inp[namaCategory]", $r[namaCategory], "t") ?>
									<?= $ui->createField("Description", "inp[keteranganCategory]", $r[keteranganCategory]) ?>
									<?= $ui->createField("Icon", "inp[iconMenu]", $r[iconMenu]) ?>
									<?= $ui->createSmallField("Order", "inp[urutanCategory]", $r[urutanCategory]) ?>
									<?= $ui->createRadio("Status", "inp[statusCategory]", array("t" => "Active", "f" => "Not Active"), $r[statusCategory]) ?>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn btn-primary" name="btnSimpan" value="Save" />
		</div>
	</form>
<?php
}

function detail()
{
	global $s, $par, $arrTitle, $menuAccess;

	$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='$par[kodeCategory]'");
	?>
	<h1 class="page-title"><?= $arrTitle[$s] ?></h1>
	<div class="row">
		<div class="col-md-12">
			<form action="" method="post">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<?php
								if (!empty($kodeCategory)) echo comboData("select * from mst_data where kodeCategory='$kodeCategory' and statusData='t' order by urutanData", "kodeData", "namaData", "par[kodeInduk]", "All", $par[kodeInduk], "", "250px");
								?>
							<input class="form-control" type="text" placeholder="Search" id="par[filter]" name="par[filter]" value="<?= $par[filter] ?>" />
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<input type="submit" value="GO" class="btn btn-primary" />
						</div>
					</div>
					<?php
						if (isset($menuAccess[$s]["add"])) {
							?>
						<div class="col-md-3 pull-right">
							<div class="form-group pull-right">
								<a onclick="openBox('popup.php?par[mode]=addDet<?= getPar($par, 'mode,kodeModul') ?>');" class="btn btn-primary"><i class="fa fa-plus"></i> Add Data</a>
							</div>
						</div>
					<?php
						}
						?>
				</div>
			</form>
			<br clear="all" />
			<table class="table table-striped table-bordered table-hover" id="tableStandart">
				<thead>
					<tr>
						<th width="20">No.</th>
						<th width="*">Name</th>
						<th width="50">Order</th>
						<th width="50">Status</th>
						<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
					</tr>
				</thead>
				<tbody>
					<?php
						$filter = "where kodeCategory='$par[kodeCategory]'";
						if (!empty($par[kodeInduk])) $filter .= " and kodeInduk='$par[kodeInduk]'";
						if (!empty($par[filter]))
							$filter .= " and (lower(namaData) like '%" . strtolower($par[filter]) . "%')";
						$sql = "select * from mst_data $filter order by kodeInduk, urutanData";
						$res = db($sql);
						$no = 0;
						while ($r = mysql_fetch_assoc($res)) {
							$no++;
							$statusData = $r[statusData] == "t" ? "<i class=\"fa fa-circle font-green-jungle\" title=\"Active\"></i>" : "<i class=\"fa fa-circle font-red-thunderbird\" title=\"Not Active\"></i>";
							if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
								$control = "<td align=\"center\">
								<div style=\"text-align:center\" class=\"btn-group btn-group-xs btn-group-solid\">";
								if (isset($menuAccess[$s]["edit"]))
									$control .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"btn blue\" type=\"button\" onclick=\"openBox('popup.php?par[mode]=editDet&par[kodeData]=$r[kodeData]" . getPar($par, "mode,kodeData") . "',825,375);\"><i class=\"fa fa-pencil-square-o\"></i></a>";
								if (isset($menuAccess[$s]["delete"]))
									$control .= "<a href=\"#Delete\" onclick=\"delDet('$r[kodeData]','" . getPar($par, "mode,kodeData") . "')\" type=\"button\" title=\"Delete\" class=\"btn red\"><i class=\"fa fa-trash\"></i></a>";
								$control .= "</td>
								</div>";
							}
							?>
						<tr>
							<td><?= $no ?>.</td>
							<td><?= $r[namaData] ?></td>
							<td align="right"><?= getAngka($r[urutanData]) ?></td>
							<td align="center"><?= $statusData ?></td>
							<?= $control ?>
						</tr>
					<?php
						}
						?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}

function lihat()
{
	global $c, $p, $m, $s, $par, $arrTitle, $menuAccess;

	$par[kodeModul] = $c;
	$par[kodeSite] = $p;

	?>
	<h1 class="page-title"><?= $arrTitle[$s] ?></h1>
	<div class="row">
		<div class="col-md-12">
			<form action="" method="post">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input class="form-control" type="text" placeholder="Search" id="par[filter]" name="par[filter]" value="<?= $par[filter] ?>" />
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<input type="submit" value="GO" class="btn btn-primary" />
						</div>
					</div>
					<?php
						if (isset($menuAccess[$s]["add"])) {
							?>
						<div class="col-md-3 pull-right">
							<div class="form-group pull-right">
								<a onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode,kodeModul') ?>');" class="btn btn-primary"><i class="fa fa-plus"></i> Add Data</a>
							</div>
						</div>
					<?php
						}
						?>
				</div>
			</form>
			<table class="table table-striped table-bordered table-hover" id="tableStandart">
				<thead>
					<tr>
						<th width="20">No.</th>
						<th width="100">Code</th>
						<th width="*">Category</th>
						<th width="50">Order</th>
						<th width="50">Status</th>
						<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
					</tr>
				</thead>
				<tbody>
					<?php
						$filter = "where kodeModul='" . $par[kodeModul] . "'";
						if (!empty($par[filter]))
							$filter .= "and (lower(kodeCategory) like '%" . strtolower($par[filter]) . "%' or lower(namaCategory) like '%" . strtolower($par[filter]) . "%')";
						$sql = "select * from mst_category $filter order by urutanCategory";
						$res = db($sql);
						$no = 0;
						while ($r = mysql_fetch_assoc($res)) {
							$no++;
							$statusCategory = $r[statusCategory] == "t" ? "<i class=\"fa fa-circle font-green-jungle\" title=\"Active\"></i>" : "<i class=\"fa fa-circle font-red-thunderbird\" title=\"Not Active\"></i>";
							if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
								$control = "
								<td align=\"center\">
									<div style=\"text-align:center\" class=\"btn-group btn-group-xs btn-group-solid\">";
								if (isset($menuAccess[$s]["edit"]))
									$control .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"btn blue\" type=\"button\" onclick=\"openBox('popup.php?par[mode]=edit&par[kodeCategory]=$r[kodeCategory]" . getPar($par, "mode,kodeCategory") . "',825,500);\"><i class=\"fa fa-pencil-square-o\"></i></a>";
								if (isset($menuAccess[$s]["delete"]))
									$control .= "<a href=\"#Delete\" onclick=\"del('$r[kodeCategory]','" . getPar($par, "mode,kodeCategory") . "')\" type=\"button\" title=\"Delete\" class=\"btn red\"><i class=\"fa fa-trash\"></i></a>";
								$control .= "
									</div>
								</td>";
							}
							?>
						<tr>
							<td><?= $no ?>.</td>
							<td><?= $r[kodeCategory] ?></td>
							<td><?php isset($menuAccess["$r[kodeMenu]"]["view"]) ? "<a href=\"?c=$c&p=$p&m=$m&s=$r[kodeMenu]&par[mode]=det&par[kodeCategory]=$r[kodeCategory]\" class=\"detil\">$r[namaCategory]</a>" : "$r[namaCategory]"; ?></td>
							<td align="right"><?= getAngka($r[urutanCategory]) ?></td>
							<td align="center"><?= $statusCategory ?></td>
							<?= $control ?>
						</tr>
					<?php
						}
						?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}

function icon()
{
	global $s, $par, $arrTitle, $c, $p, $m;

	$_SESSION['kodeKategori'] = $par[kategoriModul];
	$par[kodeModul] = $c;
	$par[kodeSite] = $p;

	?>
	<div class="row">
		<div class="col-md-10">
			<h1 class="page-title"><?= $arrTitle[$s] ?></h1>
		</div>
		<div class="col-md-2">
			<a href="?par[mode]=view<?= getPar($par, "mode") ?>"><i class="fa fa-bars"></i></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="tiles">
				<?php
					$filter = "where t1.kodeModul='" . $par[kodeModul] . "'";
					if (!empty($par[kategoriModul]))
						$filter .= " and kategoriModul='$par[kategoriModul]'";
					if (!empty($par[filter]))
						$filter .= " and (lower(namaModul) like '%" . strtolower($par[filter]) . "%')";
					$sql = "select * from mst_category t1 join app_menu t2 on (t1.kodeMenu=t2.kodeMenu) $filter order by t1.urutanCategory";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_assoc($res)) {
						?>
					<a href="<?= "?c=$c&p=$p&m=$m&s=$r[kodeMenu]&par[mode]=det&par[kodeCategory]=$r[kodeCategory]"; ?>">
						<div class="tile bg-red-flamingo">
							<div class="tile-body">
								<i class="<?= $r[iconMenu] ?>"></i>
							</div>
							<div class="tile-object">
								<div class="name" style="right: 0; display: block; text-align: center;"><strong><?= $r[namaCategory] ?></strong></div>
							</div>
						</div>
					</a>
				<?php
					}
					?>
			</div>
		</div>
	</div>
<?php
}

function getContent($par)
{
	global $s, $_submit, $menuAccess;
	switch ($par[mode]) {
		case "order":
			$text = order();
			break;

		case "cek":
			$text = cek();
			break;
		case "chk":
			$text = chk();
			break;
		case "chkDet":
			$text = chkDet();
			break;

		case "delDet":
			if (isset($menuAccess[$s]["delete"])) $text = hapusDet();
			else $text = detail();
			break;
		case "editDet":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formDet() : ubahDet();
			else $text = detail();
			break;
		case "addDet":
			if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDet() : tambahDet();
			else $text = detail();
			break;
		case "det":
			$text = detail();
			break;

		case "delIco":
			if (isset($menuAccess[$s]["edit"])) $text = hapusIcon();
			else $text = lihat();
			break;
		case "del":
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
		case "view":
			$text = lihat();
			break;
		default:
			$text = icon();
			break;
	}
	return $text;
}
?>