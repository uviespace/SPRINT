<?php

require_once "utils/session_utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';
require_once 'int/config.php';

session_start();
check_session();

if (!isset($_GET["idProject"]) || !isset($_GET["idStandard"])) {
	http_response_code(403);
	die('Forbidden');
}

if (!check_user_can_access_project($_GET["idProject"])) {
	http_response_code(403);
	die('Forbidden');
}

$database = new Database();

# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "APIDs";
# $site_css = "layout/open_project.css";
$site_js = "js/view_apid.js";
$tpl = "view_apid.tpl.php";
include "template.php";

?>
