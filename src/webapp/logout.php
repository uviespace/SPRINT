<?php
session_start();

// Delete session variables
$_SESSION = array();

// Delete also cookie if it was used
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), "", time() - 42000, $params["path"],
			  $params["domain"], $params["secure"], $params["httponly"]);
}

// destroy session at the end
session_destroy();

header( "refresh:8;url=login.php" );

$sidebar_actions = [ ["label" => "Login", "link" => "login.php"] ];

$pagetitle = "Logout";
$tpl = "logout.tpl.php";
include "template.php";
?>

