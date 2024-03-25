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

$parameters = $database->select(
	"SELECT p.id, p.`domain`, p.name, count(l.id) as limit_count, l.id > 0 as limit_exists " .
	"FROM `parameter` p LEFT JOIN `limit` l ON p.id = l.idParameter " .
	"WHERE p.idStandard = ? " .
	"GROUP BY p.id, p.`domain`, p.name " .
	"ORDER BY limit_exists DESC, p.`domain`, p.name ",
	["i", [$_GET["idStandard"]]]);

$sidebar_actions = [
	[ "label" => "Back",
	  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
			  "&idStandard=" . $_GET["idStandard"]  ],
	[ "label" => "Home", "link" => "index.php" ]
];

$pagetitle = "Limits";
$site_css = "layout/standard_additions.css";
$site_js = "js/sel_parameter-limit.js";
$tpl = "sel_parameter-limit.tpl.php";
include "template.php";

?>
