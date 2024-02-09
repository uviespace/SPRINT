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

$role_values = get_roles_for_header($database);
$parameter_values = get_parameter_values_for_header($database, $_GET['idStandard']);


/*$role_values = $database->select("SELECT id, name FROM parameterrole WHERE filter IN (0, 1) ORDER BY id", null);

$parameter_values = $database->select("SELECT p.id, concat(p.domain, '/', p.name) as parameter " .
									  "FROM parameter p " .
									  "WHERE idStandard = ? AND p.kind IN (0, 1) " .
									  "ORDER BY p.domain, p.name",
									  ["i", [$_GET['idStandard']]]);

*/

# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "TM Header";
# $site_css = "layout/open_project.css";
$site_js = "js/view_tmheader.js";
$tpl = "view_tmheader.tpl.php";
include "template.php";

?>
