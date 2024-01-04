<?php

function check_session() {
	if (!isset($_SESSION['userid'])) {
		// TODO: send with returnUrl to login to redirect back afterwards
		header("refresh:0,url=login.php");
		die('');
	}
}


?>
