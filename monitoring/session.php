<?php
	include "global.php";
	if (!empty($cUsername)) {		
	  $nSession = date('Y-m-d H:i:s');
	  echo "session : ".abs(selisihMenit($cSession, $nSession));
	  if (!empty($cSession) && abs(selisihMenit($cSession, $nSession)) > 30){
		echo "<script>
					alert('sesi berakhir, silahkan login kembali');
					parent.window.location='logout.php';
				</script>";
	  }
	  setcookie("cSession", date('Y-m-d H:i:s'));
	}
?>