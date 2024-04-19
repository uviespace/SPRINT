<?php

require_once "utils/session_utils.php";
require_once "utils/drop_down_utils.php";
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

$project_name = $database->select("SELECT name FROM project WHERE id = ?", ["i", [$_GET["idProject"]]])[0]["name"];
$standard_name = $database->select("SELECT name FROM standard WHERE id = ?", ["i", [$_GET["idStandard"]]])[0]["name"];


$sidebar_actions = [
	[ "label" => "Back",
	  "link" => "sel_parameter-calibration.php?idProject=" . $_GET["idProject"] .
			    "&idStandard=" . $_GET["idStandard"] ],
	["label" => "Home", "link" => "index.php" ]
];


$site_js = "js/open_calibration_editor.js";
$pagetitle = "Calibration Curves";
$tpl = "open_calibration_editor.tpl.php";
include "template.php";


?>
