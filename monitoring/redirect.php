<?php

include "global.php";

$user = getRow("select * from app_user where id = 1");

setcookie("cUsername", $user[username]);
setcookie("cPassword", $user[password]);
setcookie("cGroup", $user[kodeGroup]);
setcookie("cNama", $user[namaUser]);
setcookie("cFoto", $user[fotoUser]);
setcookie("cID", $user[id]);
setcookie("cIDPegawai", $user[idPegawai]);
setcookie("cJenisUser", $user[jenisUser]);

switch ($_GET[target]) {

    case "print_tagihan":
        header("Location: ajax.php?c=26&p=73&m=675&s=675&par[mode]=print_dokumen&par[id_tagihan]={$_GET[id_tagihan]}");
        break;

}
