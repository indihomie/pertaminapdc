<?php
include "global.php";
global $cUsername;

db("update app_user set logoutUser='".date('Y-m-d H:i:s')."' where username='$cUsername'");
session_start();
setcookie("cUsername","");
setcookie("cPassword","");
setcookie("cGroup","");	
setcookie("cNama","");
setcookie("cFoto","");	
setcookie("cSession","");
setcookie("cID","");

session_destroy();
header('Location: login');
?>