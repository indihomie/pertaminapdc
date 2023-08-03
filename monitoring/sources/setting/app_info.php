<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/info/";

function hapusFile()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$fileInfo = getField("select fileInfo from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $fileInfo) and $fileInfo != "") unlink($fFile . $fileInfo);

	$sql = "update app_info set fileInfo='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function hapusLogo()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$logoInfo = getField("select logoInfo from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $logoInfo) and $logoInfo != "") unlink($fFile . $logoInfo);

	$sql = "update app_info set logoInfo='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function hapusBg()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$loginBackgroundInfo = getField("select loginBackgroundInfo from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $loginBackgroundInfo) and $loginBackgroundInfo != "") unlink($fFile . $loginBackgroundInfo);

	$sql = "update app_info set loginBackgroundInfo='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function hapusLeft()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$loginLeftInfo = getField("select loginLeftInfo from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $loginLeftInfo) and $loginLeftInfo != "") unlink($fFile . $loginLeftInfo);

	$sql = "update app_info set loginLeftInfo='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function hapusSupport()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$loginSupportInfo = getField("select loginSupportInfo from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $loginSupportInfo) and $loginSupportInfo != "") unlink($fFile . $loginSupportInfo);

	$sql = "update app_info set loginSupportInfo='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function hapusMobile()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$logoMobile = getField("select logoMobile from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $logoMobile) and $logoMobile != "") unlink($fFile . $logoMobile);

	$sql = "update app_info set logoMobile='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}
function hapusTop()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$logoTopMobile = getField("select logoTopMobile from app_info where kodeInfo='$par[kodeInfo]'");
	// var_dump($logoTopMobile);
	// die();
	if (file_exists($fFile . $logoTopMobile) and $logoTopMobile != "") unlink($fFile . $logoTopMobile);

	$sql = "update app_info set logoTopMobile='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}
function hapusQR()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$logoQR = getField("select logoQR from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $logoQR) and $logoQR != "") unlink($fFile . $logoQR);

	$sql = "update app_info set logoQR='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}
function hapusMbackground()
{
	global $s, $inp, $par, $fFile, $cUsername;
	$backgroundMobileLogin = getField("select backgroundMobileLogin from app_info where kodeInfo='$par[kodeInfo]'");
	if (file_exists($fFile . $backgroundMobileLogin) and $backgroundMobileLogin != "") unlink($fFile . $backgroundMobileLogin);

	$sql = "update app_info set backgroundMobileLogin='' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}
function ubah()
{
	global $s, $inp, $par, $acc, $arrAkses, $fFile, $cUsername;

	$fileIcon = $_FILES["fileInfo"]["tmp_name"];
	$fileIcon_name = $_FILES["fileInfo"]["name"];
	if (($fileIcon != "") and ($fileIcon != "none")) {
		fileUpload($fileIcon, $fileIcon_name, $fFile);
		$fileInfo = "img-" . $par[kodeInfo] . "." . getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $fileInfo);
	}
	if (empty($fileInfo)) $fileInfo = getField("select fileInfo from app_info where kodeInfo='$par[kodeInfo]'");

	$fileLogo = $_FILES["logoInfo"]["tmp_name"];
	$fileLogo_name = $_FILES["logoInfo"]["name"];
	if (($fileLogo != "") and ($fileLogo != "none")) {
		fileUpload($fileLogo, $fileLogo_name, $fFile);
		$logoInfo = "logo-" . $par[kodeInfo] . "." . getExtension($fileLogo_name);
		fileRename($fFile, $fileLogo_name, $logoInfo);
	}
	if (empty($logoInfo)) $logoInfo = getField("select logoInfo from app_info where kodeInfo='$par[kodeInfo]'");

	$loginBackgroundInfo = $_FILES["loginBackgroundInfo"]["tmp_name"];
	$loginBackgroundInfo_name = $_FILES["loginBackgroundInfo"]["name"];
	if (($loginBackgroundInfo != "") and ($loginBackgroundInfo != "none")) {
		fileUpload($loginBackgroundInfo, $loginBackgroundInfo_name, $fFile);
		$bgInfo = "bfront-" . $par[kodeInfo] . "." . getExtension($loginBackgroundInfo_name);
		fileRename($fFile, $loginBackgroundInfo_name, $bgInfo);
	}
	if (empty($bgInfo)) $bgInfo = getField("select loginBackgroundInfo from app_info where kodeInfo='$par[kodeInfo]'");

	$loginLeftInfo = $_FILES["loginLeftInfo"]["tmp_name"];
	$loginLeftInfo_name = $_FILES["loginLeftInfo"]["name"];
	if (($loginLeftInfo != "") and ($loginLeftInfo != "none")) {
		fileUpload($loginLeftInfo, $loginLeftInfo_name, $fFile);
		$leftInfo = "bleft-" . $par[kodeInfo] . "." . getExtension($loginLeftInfo_name);
		fileRename($fFile, $loginLeftInfo_name, $leftInfo);
	}
	if (empty($leftInfo)) $leftInfo = getField("select loginLeftInfo from app_info where kodeInfo='$par[kodeInfo]'");

	$loginSupportInfo = $_FILES["loginSupportInfo"]["tmp_name"];
	$loginSupportInfo_name = $_FILES["loginSupportInfo"]["name"];
	if (($loginSupportInfo != "") and ($loginSupportInfo != "none")) {
		fileUpload($loginSupportInfo, $loginSupportInfo_name, $fFile);
		$supportInfo = "bsupport-" . $par[kodeInfo] . "." . getExtension($loginSupportInfo_name);
		fileRename($fFile, $loginSupportInfo_name, $supportInfo);
	}
	if (empty($supportInfo)) $supportInfo = getField("select loginSupportInfo from app_info where kodeInfo='$par[kodeInfo]'");

	// logoMobile
	$logoMobile = $_FILES["logoMobile"]["tmp_name"];
	$logoMobile_name = $_FILES["logoMobile"]["name"];
	if (($logoMobile != "") and ($logoMobile != "none")) {
		fileUpload($logoMobile, $logoMobile_name, $fFile);
		$mobileInfo = "bmobile-" . $par[kodeInfo] . "." . getExtension($logoMobile_name);
		fileRename($fFile, $logoMobile_name, $mobileInfo);
	}
	if (empty($mobileInfo)) $mobileInfo = getField("select logoMobile from app_info where kodeInfo='$par[kodeInfo]'");
	// logoTopMobile
	$logoTopMobile = $_FILES["logoTopMobile"]["tmp_name"];
	$logoTopMobile_name = $_FILES["logoTopMobile"]["name"];
	if (($logoTopMobile != "") and ($logoTopMobile != "none")) {
		fileUpload($logoTopMobile, $logoTopMobile_name, $fFile);
		$logoTopMobileTemp = "topmobile-" . $par[kodeInfo] . "." . getExtension($logoTopMobile_name);
		fileRename($fFile, $logoTopMobile_name, $logoTopMobileTemp);
	}
	if (empty($logoTopMobileTemp)) $logoTopMobileTemp = getField("select logoTopMobile from app_info where kodeInfo='$par[kodeInfo]'");


	$logoQR = $_FILES["logoQR"]["tmp_name"];
	$logoQR_name = $_FILES["logoQR"]["name"];
	if (($logoQR != "") and ($logoQR != "none")) {
		fileUpload($logoQR, $logoQR_name, $fFile);
		$logoQRtemp = "logoQR-" . $par[kodeInfo] . "." . getExtension($logoQR_name);
		fileRename($fFile, $logoQR_name, $logoQRtemp);
	}
	if (empty($logoQRtemp)) $logoQRtemp = getField("select logoQR from app_info where kodeInfo='$par[kodeInfo]'");

	$backgroundMobileLogin = $_FILES["backgroundMobileLogin"]["tmp_name"];
	$backgroundMobileLogin_name = $_FILES["backgroundMobileLogin"]["name"];
	if (($backgroundMobileLogin != "") and ($backgroundMobileLogin != "none")) {
		fileUpload($backgroundMobileLogin, $backgroundMobileLogin_name, $fFile);
		$backgroundMobileLogintemp = "backgroundMobileLogin-" . $par[kodeInfo] . ".png";
		fileRename($fFile, $backgroundMobileLogin_name, $backgroundMobileLogintemp);
	}
	if (empty($backgroundMobileLogintemp)) $backgroundMobileLogintemp = getField("select backgroundMobileLogin from app_info where kodeInfo='$par[kodeInfo]'");


	repField();

	$sql = "update app_info set namaInfo='$inp[namaInfo]', keteranganInfo='$inp[keteranganInfo]', fileInfo='$fileInfo', logoInfo='$logoInfo', loginBackgroundInfo = '$bgInfo', loginLeftInfo = '$leftInfo', loginSupportInfo = '$supportInfo',logoQR = '$logoQRtemp',logoMobile = '$mobileInfo',logoTopMobile = '$logoTopMobileTemp',backgroundMobileLogin = '$backgroundMobileLogintemp', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeInfo='$par[kodeInfo]'";
	db($sql);

	echo "<script>
				alert('update success !!');
				window.location='?" . getPar($par, "mode") . "';
			</script>";
}

function form()
{
	global $s, $inp, $par, $fFile, $arrSite, $arrAkses, $arrTitle, $menuAccess;
	$par[kodeInfo] = 1;

	$sql = "select * from app_info where kodeInfo='$par[kodeInfo]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	setValidation("is_null", "inp[namaInfo]", "you must fill title");
	setValidation("is_null", "inp[keteranganInfo]", "you must fill text");
	$text = getValidation();

	$text .= "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
					" . getBread() . "
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				 <div style=\"top:13px; right:35px; position:absolute\">
     <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"UPDATE\"/>
    </div>
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Title</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaInfo]\" name=\"inp[namaInfo]\"  value=\"$r[namaInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Text</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[keteranganInfo]\" name=\"inp[keteranganInfo]\"  value=\"$r[keteranganInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Header</label>
						<div class=\"field\">";
	$text .= empty($r[fileInfo]) ?
		"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileInfo\" name=\"fileInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[fileInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delIco" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Login</label>
						<div class=\"field\">";
	$text .= empty($r[logoInfo]) ?
		"<input type=\"text\" id=\"logoTemp\" name=\"logoTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"logoInfo\" name=\"logoInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.logoTemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[logoInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delImg" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Background Login</label>
						<div class=\"field\">";
	$text .= empty($r[loginBackgroundInfo]) ?
		"<input type=\"text\" id=\"bgTemp\" name=\"bgTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"loginBackgroundInfo\" name=\"loginBackgroundInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.bgTemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[loginBackgroundInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delLoginBg" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Left Logo Login</label>
						<div class=\"field\">";
	$text .= empty($r[loginLeftInfo]) ?
		"<input type=\"text\" id=\"leftTemp\" name=\"leftTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"loginLeftInfo\" name=\"loginLeftInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.leftTemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[loginLeftInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delLoginLeftBg" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Support Logo Login</label>
						<div class=\"field\">";
	$text .= empty($r[loginSupportInfo]) ?
		"<input type=\"text\" id=\"supportTemp\" name=\"supportTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"loginSupportInfo\" name=\"loginSupportInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.supportTemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[loginSupportInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delLoginSupportBg" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo QR</label>
						<div class=\"field\">";
	$text .= empty($r[logoQR]) ?
		"<input type=\"text\" id=\"logoQRtemp\" name=\"logoQRtemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"logoQR\" name=\"logoQR\" class=\"realupload\" size=\"50\" onchange=\"this.form.logoQRtemp.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[logoQR] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delLogoQR" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
					</div>
				
				<div id=\"general\" class=\"subcontent\">	
					<p>
						<label class=\"l-input-small\">Logo Top Mobile</label>
								<div class=\"field\">";
	$text .= empty($r[logoTopMobile]) ?
		"<input type=\"text\" id=\"logoMobileTop\" name=\"logoMobileTop\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
										<div class=\"fakeupload\">
											<input type=\"file\" id=\"logoTopMobile\" name=\"logoTopMobile\" class=\"realupload\" size=\"50\" onchange=\"this.form.logoMobileTop.value = this.value;\" />
										</div>" :
		"<img src=\"" . $fFile . "" . $r[logoTopMobile] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
										<a href=\"?par[mode]=delLogoTopMobile" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
										<br clear=\"all\">";
	$text .= "</div>
					</p>

					<p>
					
						<label class=\"l-input-small\">Logo Mobile</label>
						<div class=\"field\">";
	$text .= empty($r[logoMobile]) ?
		"<input type=\"text\" id=\"mobileLogo\" name=\"mobileLogo\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"logoMobile\" name=\"logoMobile\" class=\"realupload\" size=\"50\" onchange=\"this.form.mobileLogo.value = this.value;\" />
									</div>" :
		"<img src=\"" . $fFile . "" . $r[logoMobile] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delLogoMobile" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>

					<p>
						<label class=\"l-input-small\">Background Login</label>
						<div class=\"field\">";
	$text .= empty($r[backgroundMobileLogin]) ?
		"<input type=\"text\" id=\"backgroundLoginMobile\" name=\"backgroundLoginMobile\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"backgroundMobileLogin\" name=\"backgroundMobileLogin\" class=\"realupload\" size=\"50\" onchange=\"this.form.backgroundLoginMobile.value = this.value;\" />
								</div>" :
		"<img src=\"" . $fFile . "" . $r[backgroundMobileLogin] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delMobileBackground" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
	$text .= "</div>
					</p>
				</div>
			</form>";
	return $text;
}

function lihat()
{
	global $s, $inp, $par, $fFile, $arrSite, $arrAkses, $arrTitle, $menuAccess;
	$par[kodeInfo] = 1;

	$sql = "select * from app_info where kodeInfo='$par[kodeInfo]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$text .= "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
					" . getBread() . "
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Title</label>
						<span class=\"field\">$r[namaInfo]&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Text</label>
						<span class=\"field\">$r[keteranganInfo]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Login</label>
						<span class=\"field\">";
	if (!empty($r[fileInfo]))
		$text .= "<img src=\"" . $fFile . "" . $r[fileInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
	$text .= "&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Header</label>
						<span class=\"field\">";
	if (!empty($r[logoInfo]))
		$text .= "<img src=\"" . $fFile . "" . $r[logoInfo] . "\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
	$text .= "&nbsp;</span>
					</p>
				</div>
			</form>";
	return $text;
}

function getContent($par)
{
	global $s, $_submit, $menuAccess;
	switch ($par[mode]) {
		case "delIco":
			if (isset($menuAccess[$s]["edit"])) $text = hapusFile();
			else $text = lihat();
			break;
		case "delImg":
			if (isset($menuAccess[$s]["edit"])) $text = hapusLogo();
			else $text = lihat();
			break;
		case "delLoginBg":
			if (isset($menuAccess[$s]["edit"])) $text = hapusBg();
			else $text = lihat();
			break;
		case "delLoginLeftBg":
			if (isset($menuAccess[$s]["edit"])) $text = hapusLeft();
			else $text = lihat();
			break;
		case "delLoginSupportBg":
			if (isset($menuAccess[$s]["edit"])) $text = hapusSupport();
			else $text = lihat();
			break;
			//TODO
		case "delLogoTopMobile":
			if (isset($menuAccess[$s]["edit"])) $text = hapusTop();
			else $text = lihat();
			break;


		case "delLogoMobile":
			if (isset($menuAccess[$s]["edit"])) $text = hapusMobile();
			else $text = lihat();
			break;

		case "delLogoQR":
			if (isset($menuAccess[$s]["edit"])) $text = hapusQR();
			else $text = lihat();
			break;
		case "delMobileBackground":
			if (isset($menuAccess[$s]["edit"])) $text = hapusMbackground();
			else $text = lihat();
			break;

		default:
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
			else $text = lihat();
			break;
	}
	return $text;
}
