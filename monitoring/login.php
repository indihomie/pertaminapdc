<?php
include "global.php";

if(!empty($username) && !empty($password)){

	$username=mysql_real_escape_string($username);
	$password=mysql_real_escape_string($password);

	$pengacak = "UzFuM3JHMV9DNV9EM1ZsMHAzUg==";
	$pengacak2 = "8eb98b33c777a27ab57a35ee1dc3a389";

	$password = md5($pengacak2.$pengacak.md5($password).$pengacak.$pengacak2.$pengacak.$pengacak2);

	$sql = "select * from app_user where username='$username' and password='".$password."' and statusUser='t'";
	$res=db($sql);
	$r= mysql_fetch_array($res);
	if($r[password]!=""){
		setcookie("cUsername",$r[username]);
		setcookie("cPassword",$r[password]);
		setcookie("cGroup",$r[kodeGroup]);		
		setcookie("cNama",$r[namaUser]);
		setcookie("cFoto",$r[fotoUser]);
		setcookie("cID",$r[id]);
		setcookie("cIDPegawai",$r[idPegawai]);
		setcookie("cJenisUser",$r[cJenisUser]);

		db("update app_user set loginUser='".date('Y-m-d H:i:s')."' where username='$username'");
        
        $kodeLog = getField("select kodeLog from log_access order by kodeLog desc limit 1") + 1;
        $kodeModul = empty($c) ? 0 : $c;
        $kodeSite = empty($p) ? 0 : $p;
        $kodeMenu = empty($s) ? 0 : $s;
        $ip = $_SERVER['REMOTE_ADDR'];
        $getip = 'http://extreme-ip-lookup.com/json/' . $ip;
        $curl  = curl_init();
        curl_setopt($curl, CURLOPT_URL, $getip);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($curl);
        curl_close($curl);
        $details       = json_decode($content);
        $country_code  = $details->countryCode;
        $nama_kota     = $details->city;
        $nama_provinsi = $details->region;
        $sqlLog = "insert into log_access (kodeLog, kodeModul, kodeSite, kodeMenu, aktivitasLog,lokasi,ip_address, createBy, createTime,kodeTipe) values ('$kodeLog', '$kodeModul', '$kodeSite', '$kodeMenu', 'login','" . $nama_kota . "','" . $ip . "', '$cUsername', '" . date('Y-m-d H:i:s') . "','0')";
        db($sqlLog, "false");
        
        
		header('Location: main');
	}else{
		$message="username / password was wrong";
	}
}else{
	$message="you must fill username & password";
}


$kodeInfo=1;
$sql="select * from app_info where kodeInfo='$kodeInfo'";
$res=db($sql);
$r=mysql_fetch_array($res);	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $r[keteranganInfo]; ?></title>
	<link href="favicon.ico" rel="shortcut icon" />  
	<link rel="stylesheet" href="styles/styles.css" type="text/css" />    
	
	<script type="text/javascript" src="scripts/general.js"></script>
	<script type="text/javascript" src="scripts/jquery.js"></script>	
	<script type="text/javascript" src="scripts/custom.js"></script>
	<script type="text/javascript" src="scripts/cookie.js"></script>
	<script type="text/javascript" src="scripts/data.js"></script>
	<script type="text/javascript" src="scripts/uniform.js"></script>
	<script type="text/javascript" src="scripts/time.js"></script>
	<script type="text/javascript" src="scripts/chosen.js"></script>    
	<script type="text/javascript" src="scripts/tinybox.js"></script>
	<script type="text/javascript">
		function message(){
			<?php
			if(!empty($lgn))
				echo "alert('".$message."')";
			?>	
		}
	</script>
</head>
<body class="loginpage" onload="message()";>
	<center>
		<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" style="background:url('images/info/<?php echo $r[loginBackgroundInfo] ?>') top center no-repeat; background-size: cover;background-repeat: no-repeat;background-position: center center;">
			<tr><td height="420" align="left" valign="top">&nbsp;</td></tr>
			<tr>
				<td height="150" valign="top" style='background: transparent linear-gradient(to bottom, #fd0318 10%, #ef1d2f 31%, #e0192a 100%) repeat scroll 0% 0%;opacity: 0.9; filter: alpha(opacity=90);'>
					<div style="position:normal;margin-top:30px;">
						<table cellpadding="3" cellspacing="2" border="0">
							<tr>
								<td align="right" style="padding-right:30px;font-weight:bold;color:#fff;width:630px;padding-top:15px;"> 
									<font size="6"><?php echo $r[namaInfo]; ?></font><br>
									<font size="2"><?php echo $r[keteranganInfo]; ?></font><br>
								</td>
								<td class="note" style="border-left:1px solid #fff;width:20px">&nbsp;</td>
								<td class="note" style="padding-top:-20px;">
									<div class="loginbox">
										<form id="login" action="login" method="post">       
											<div class="username">
												<div class="usernameinner">
													<input type="text" name="username" id="username" placeholder="Username" />
												</div>
											</div>                
											<div class="password">
												<div class="passwordinner">
													<input type="password" name="password" id="password" placeholder="Password" />
												</div>
											</div>
											<input type="hidden" id="lgn" name="lgn" value="t">
											<button>Login</button>  
										</form> 
									</div> 
									<div style='font-size:10px;float:left;margin-left:-120px;color:#ccc;'>If you are not registered, please contact the administrator </div>
								</td>
							</td>
						</tr>
					</table>				
				</div>
			</td>
		</tr>
		<tr><td height="4" style='background:#f9b133;'></td></tr>
		<tr>
			<td  valign="top" style='background:#fff;'>
				<div style='font-family:arial;font-size:10px;margin:5px 0 0 30px;color:#b9b9b9;'>
					<?php
					if(!empty($r[loginLeftInfo])){
						?>
						<img src='images/info/<?php echo $r[loginLeftInfo] ?>' align='left' height="60" style='margin-right:20px;'>
						<?php
					}
					?>
				</div>
				<div style='align:right'>
					<img src='images/info/<?php echo $r[loginSupportInfo] ?>' align='right' style='margin-right:20px;'>
				</div>
			</td>
		</tr>
	</table>
</center>		
</body>
</html>