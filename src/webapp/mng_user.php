<?php

require "utils/session_utils.php";

session_start();
check_session();

if (!$_SESSION['is_admin']) {
	http_response_code(403);
	die('Forbidden');
}

$sidebar_actions = [ ["label" => "Back", "link" => "index.php"],
					 ["label" => "Home", "link" => "index.php"]];

$pagetitle = "Users";
$site_js = "js/mng_user.js";
$tpl = "mng_user.tpl.php";
include "template.php";

?>
