<?php
	define ("DB_USER", "user");
	define ("DB_PASSWORD", "pass");
	define ("DB_DATABASE", "testdb");
	define ("DB_HOST", "dev_mariadb_1");
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
?>
