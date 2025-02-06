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

$datatypes = $database->select("SELECT id, domain, name " .
							   "FROM `type` " .
							   "WHERE idStandard = ? OR idStandard IS NULL " .
							   "ORDER BY domain, name",
							   ["i", [$_GET["idStandard"]]]);

$parameters = $database->select(
	"SELECT id, domain, name " .
	"FROM `parameter` " .
	"WHERE idStandard = ? " .
	"ORDER BY domain, name",
	["i", [$_GET["idStandard"]]]
);

$role_values = get_roles($database, 3);

# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "Parameters";
$site_js = "js/view_parameter.js";
$tpl = "view_parameter.tpl.php";
include "template.php";




?>
