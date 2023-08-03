<?php
include "global.php";	

if(!getUser()){
	echo "<script>window.location='logout'</script>";
	exit();
}	

$kodeInfo=1;
$sql="select * from app_info where kodeInfo='$kodeInfo'";
$res=db($sql);
$r=mysql_fetch_array($res);	

list($namaUser, $fotoUser) = explode("\t", getField("select concat(namaUser, '\t', fotoUser) from app_user where username='$cUsername'"));

list($namaGroup, $keteranganGroup) = explode("\t", getField("select concat(namaGroup, '\t', keteranganGroup) from app_group where kodeGroup='$cGroup'"));

$fPengumuman = "files/pengumuman/";
$fManual = "files/manual/";
$fAplikasi = "files/aplikasi/";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $r[keteranganInfo]; ?></title>
	<link href="favicon.ico" rel="shortcut icon" />  
	<link rel="stylesheet" href="styles/styles.css" type="text/css" />    
	<link rel="stylesheet" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">
	<script type="text/javascript" src="scripts/jquery.js"></script>	
	<script type="text/javascript" src="scripts/custom.js"></script>
	<script type="text/javascript" src="scripts/cookie.js"></script>
	<script type="text/javascript" src="scripts/data.js"></script>
	<script type="text/javascript" src="scripts/uniform.js"></script>
	<script type="text/javascript" src="scripts/time.js"></script>
	<script type="text/javascript" src="scripts/chosen.js"></script>
	<script type="text/javascript" src="scripts/general.js"></script>
	<script type="text/javascript" src="scripts/tables.js"></script>
	<script type="text/javascript" src="scripts/tinybox.js"></script>   
	<script type="text/javascript" src="scripts/jquery.autocomplete.min.js"></script> 	
</head>

<style>
	.container { width: 800px; margin: 0 auto; }

	.autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
	.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
	.autocomplete-no-suggestion { padding: 2px 5px;}
	.autocomplete-selected { background: #F0F0F0; }
	.autocomplete-suggestions strong { font-weight: bold; color: #000; }
	.autocomplete-group { padding: 2px 5px; }
	.autocomplete-group strong { font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }

	.stdtable{
		border: 2px solid #c0c0c0;
	}
</style>

<script>
function startTime() {
  var today = new Date();
  var h = today.getHours();
  var m = today.getMinutes();
  var s = today.getSeconds();
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById('jam').innerHTML =
  h + ":" + m + ":" + s;
  var t = setTimeout(startTime, 1000);
}
function checkTime(i) {
  if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
  return i;
}
</script>
<body class="loginpage" onload="startTime()"> 
	<?php
	valPar($par);	
	?>

	<div class="bodywrapper">
		<div class="logintop"></div>
		<div class="loginbottom"></div>
		<div class="topheader">
			<div class="companyinfof">
				<?php			
				$arrHari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu");
				echo $arrHari[date('w')].", ".getTanggal(date('Y-m-d'),"t");			
				?>,
                <span id="jam"></span>
			</div>		
			<div class="userinfof">
				<span><?php 			
					$fotoUser = empty($fotoUser) ? "styles/images/foto.png" : "images/user/".$fotoUser;
					echo "<table style=\"width:100%;\">
					<tr>
						<td align=\"center\" style=\"vertical-align:top; padding-right:5px;\"><img src=\"$fotoUser\" height=\"20\" ></td>
						<td style=\"vertical-align:top; padding-right:10px;\"><a href=\"#\" class=\"menu\" onclick=\"openBox('popup.php?par[mode]=profile',825,390);\">".$namaUser."</a></td>
						<td style=\"vertical-align:top;\">|</td>
						<td style=\"vertical-align:top; padding-left:10px;\"><a href=\"logout\" class=\"menu\" onclick=\"return confirm('are you sure to logout ?');\"><strong>LOGOUT</strong></a></td>
					</tr>	
				</table>";

				?></span>
			</div><!--userinfo-->	
			<div class="left">
				<h1 class="logo" style="background: url(images/info/<?php echo $r[fileInfo]?>) no-repeat; ">
					<?php
				/*$arrInfo = explode(" ", $r[namaInfo]);
				if (is_array($arrInfo)) {
				  ksort($arrInfo);
				  reset($arrInfo);
				  while (list($id, $namaInfo) = each($arrInfo)) {
					echo $id % 2 < 1 ? "<span>" . $namaInfo . "</span> " : $namaInfo . " ";
				  }
				}*/
				echo "<span>".$r[namaInfo]."</span>";	
				?>&nbsp;
			</h1>
			<br clear="all" />
			<span class="slogan"><?php echo $r[keteranganInfo]; ?></span>
		</div><!--left-->

	</div><!--topheader-->		
	<div class="header">
	</div><!--header-->       

	<div class="loginbox-wide">
		<div class="loginboxinner">
			<div class="splitter">
				<div class="left" style="width: 26%;">
					
					<div class="logo">
						<h1><span>PROFILE</span></h1>
					</div><!--logo-->

					<div class="logincontent" style="height: 140px">
						<fieldset style="padding: 10px;">
							<a href="#" onclick="openBox('popup.php?par[mode]=profile',825,500);"><img src="<?php echo $fotoUser ?>" height="100" style="max-width: 100px;" align="left" alt=""></a>
							<div style="margin-left: 110px; margin-top: 8px">
								<a href="#" onclick="openBox('popup.php?par[mode]=profile',825,500);"><h4><?php echo $cNama; ?></h4></a>
								<hr style="border-color: #fff"/>
								<p style="color: #959595"><?php echo $namaGroup; ?></p>
								<?php echo $keteranganGroup; ?>
							</div>
						</fieldset>
					</div>		

					<div class="logo">
						<h1><span>PENGUMUMAN</span></h1>
					</div><!--logo-->

					<div class="logincontent" style="height: auto">
						<fieldset style="padding: 10px;">
									<?php 								
									$sql = "select * from dta_pengumuman where statusPengumuman = 't' order by tanggalPengumuman desc limit 1";
									$res = db($sql);
									while($r = mysql_fetch_array($res)){
										?>									
											<div class="info">
												<a href="#"><b><?php echo $r[judulPengumuman] ?></b></a> <br>
												<font size='1'>by. <?php echo $r[sumberPengumuman] ?> | <?php echo getTanggal($r[tanggalPengumuman],"t") ?> <br /></font>
												<?php echo $r[resumePengumuman] ?>
												<?php
												if(!empty($r[filePengumuman])){
													echo "<hr style=\"border-color: #fff\"/>";
													echo "<img src=\"".getIcon($r[filePengumuman],"")."\" align=\"left\" height=\"16\" style=\"padding-right:5px; padding-bottom:5px;\"> &nbsp; <a href=\"".$fPengumuman.$r[filePengumuman]."\" download>Download File</a>";
												}
												?>
											</div><!--info-->
										<?php
									}
									?>
						</fieldset>
					</div>		

					<br clear="all">

					<div class="logo">
						<h1><span>MANUAL BOOK</span></h1>
					</div>

					<div class="logincontent" style="height: auto">
						<?php
						$sql = "select * from dta_manual where statusManual = 't' order by idManual desc limit 1";
						$res = db($sql);
						while($r = mysql_fetch_array($res)){

							echo "<fieldset style=\"padding:10px;\"><img src=\"".getIcon($r[fileManual],"")."\" align=\"left\" style=\"padding: 2px\" alt=\"\" />
							<div style=\"margin-left: 42px;\">
								<a href=\"".$fManual.$r[fileManual]."\" download><h5>$r[judulManual]</h5></a>
								<p>by. ".getField("select namaUser from app_user where username='$r[createBy]'")."</p>
							</div></fieldset>";

						}							
						?>
					</div>

				</div>
				
			<div class="right" style="width: 20%;">
					<div class="logo">
						<h1><span>LAST LOGIN</span></h1>
					</div><!--logo-->

					<div class="logincontent" style="height: auto;font-size: 11px">
						<fieldset style="padding: 10px;">
							<?php										
							$sql="SELECT namaUser, loginUser,fotoUser FROM app_user WHERE jenisUser = '1' and loginUser !='0000-00-00 00:00:00' ORDER BY loginUser DESC LIMIT 5";
										$res=db($sql);
										while($r=mysql_fetch_array($res)){
											echo"	<img src=\"".APP_URL."/images/user/".$r[fotoUser]."\" height=\"40\" width=\"35\" align=\"left\" alt=\"\">&nbsp;
														<a class=\"news\" style=\"font-weight:normal;text-transform:uppercase; \">".$r[namaUser]."</a><br>
														&nbsp;&nbsp;<a class=\"news\" style=\"font-weight:normal;\">".$r[loginUser]."</a>
														<hr style='border:1px solid #f5f5f5;'/>
											";

										}
							?>
							
						</fieldset>
					</div>					
				    <?php
                    $cek = getField("select jenisUser from app_user where username='$cUsername'");
                    if($cek == 0)
                    {
                        ?>
                        <br clear="all">
    					<div class="logo" style="margin-top:15px;">
    						<h1><span>TOTAL USER</span></h1>
    					</div>
    					<div class="logincontent" style="height: auto">
    						<table style="width:100%; background:#f5f5f5;">
    						<?php
    						$arrJumlah=arrayQuery("select kodeGroup, count(*) from app_user group by 1");
    						$sql="select * from app_group where kodeGroup != '1' order by kodeGroup";
    						$res=db($sql);
    						while($r=mysql_fetch_array($res)){
    							echo"
                                <tr>
        							<td style=\"text-align:left; padding:5px; padding-left:15px; border-bottom:solid 1px #f3f3f3;\">
        								$r[namaGroup]
        							</td>
        							<td style=\"text-align:right; padding:5px; padding-right:15px; border-bottom:solid 1px #f3f3f3;\">
        								".getAngka($arrJumlah["$r[kodeGroup]"])."
        							</td>
    							</tr>";
    						}		
    						?>
                            </table>
    					</div>	
                        <?php
                    }
                    ?>
					
                    			
				</div>
				<div class="right" style="width: 50%;border: 0px #ccc solid; border-top: 0; border-bottom: 0; border-right: 0; padding-right: 29px;">
					<div class="logo">
						<h1><span>MODULES</span></h1>
					</div><!--logo-->	
					<div class="logincontent" style="height: 140px">

						<ul class="shortcuts">
							<?php											
							$sql="select * from app_modul t1 join app_group_modul t2 on (t1.kodeModul=t2.kodeModul) join mst_data t3 on t3.kodeData = t1.kategoriModul where t2.kodeGroup='".$cGroup."' order by t3.urutanData,t1.urutanModul";
							$res=db($sql);
							$no=1;
							while($r=mysql_fetch_array($res)){						
								$kategori = ".$r[kategoriModul].";
								$kategoriModul2= getField("select namaData from mst_data where kodeData = '".$r[kategoriModul]."'");
 
								if ($kategori!=$kategorix and $no != 0)
								{
									$kategorix=$kategori;
									$no=0;
									echo "</ul><h4 style='width:100%;border-bottom:1px solid #c0c0c0;margin-bottom:20px;float: left'>".$kategoriModul2."</h4><ul class='shortcuts'>";
								};
		

							$no++;

								$iconModul = is_file("images/menu/".$r[iconModul]) ? "images/menu/".$r[iconModul] : "styles/images/folder.png";
                                
                                if($r[statusLink] == 'p')
                                {
                                    //$link = $r[modul_link];
									$modul_link = str_replace("index.php?", "", $r[modul_link]);
									$modul_link = str_replace("c=", "", $modul_link);
									$modul_link = str_replace("p=", "", $modul_link);
									$modul_link = str_replace("m=", "", $modul_link);
									$modul_link = str_replace("s=", "", $modul_link);
									$modul_link = str_replace("&", "-", $modul_link);
									
									$cek_link = explode("-",$modul_link);
									for($c=4; $c > count($cek_link); $c--)
										$modul_link.="-0";
									
									$link = encode($modul_link);
                                }
                                else
                                {
                                    //$link = "index.php?c=".$r[kodeModul]."";
									$link = encode("$r[kodeModul]-0-0-0");
                                }
                                
								echo "<li>
        								<a href=\"$link\">
        									<span style=\"display: block; background: url(".$iconModul.") no-repeat center center;  background-size:48px 48px;\">$r[namaModul]</span>
        								</a>
        							</li>";

						    }
						    ?>							
					</ul>

					
				</div>
			</div>
		</div><!--loginboxinner-->
	</div><!--loginbox-->

</div><!--bodywrapper-->
<!--<iframe src="session.php" style="display:none"></iframe>-->
<br><br>
<br><br>
</body>
</html>
