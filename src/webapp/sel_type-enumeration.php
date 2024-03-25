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


$enums = $database->select(
	"SELECT t.id, t.`domain`, t.name, count(e.id) as enum_count, e.id > 0 as enum_exists " .
	"FROM `type` t LEFT JOIN enumeration e ON e.idType = t.id " .
	"WHERE t.idStandard = ? " .
	"GROUP BY t.id, t.`domain`, t.name " .
	"ORDER BY enum_exists DESC, t.`domain`, t.name",
	["i", [$_GET["idStandard"]]]);


$sidebar_actions = [
	[ "label" => "Back",
	  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
			  "&idStandard=" . $_GET["idStandard"]  ],
	[ "label" => "Home", "link" => "index.php" ]
];


$pagetitle = "Enumerations";
$site_css = "layout/standard_additions.css";
$site_js = "js/sel_type-enumeration.js";
$tpl = "sel_type-enumeration.tpl.php";
include "template.php";

?>
