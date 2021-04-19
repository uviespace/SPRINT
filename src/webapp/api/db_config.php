<?php
	define ("DB_USER", "user");
	define ("DB_PASSWORD", "pwd");
	define ("DB_DATABASE", "db");
	define ("DB_HOST", "localhost");
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
?>