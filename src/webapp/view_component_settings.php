<?php

require_once "utils/session_utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';
require_once 'utils/utils.php';
require_once 'int/config.php';

session_start();
check_session();

if (!isset($_GET["idProject"]) || !isset($_GET["idApplication"]) || !isset($_GET["idComponent"])) {
	http_response_code(403);
	die('Forbidden');
}


if (!check_user_can_access_project($_GET["idProject"])) {
	http_response_code(403);
	die('Forbidden');
}

$database = new Database();

$application = $database->select("SELECT name, `desc` FROM application WHERE id = ?",
								 ["i", [$_GET['idProject']]]);

$component = $database->select("SELECT shortName, name, `desc`, componentSettings FROM component WHERE id = ?",
							   ["i", [$_GET['idComponent']]]);

$component_settings = $database->select("SELECT setting FROM applicationcomponent WHERE idApplication = ? AND idComponent = ?",
										[ "ii", [ $_GET["idApplication"], $_GET['idComponent']]]);


// Template settings

$sidebar_actions = [
	[ "label" => "Back", "link" => "open_application.php?idProject=" . $_GET["idProject"] . "&idApplication=" . $_GET["idApplication"] ],
	["label" => "Home", "link" => "index.php"]
];


$site_js = "js/view_component_settings.js";
$pagetitle = "Component settings for " . $component[0]["shortName"]  . " of project " . $application[0]["name"];
$tpl = "view_component_settings.tpl.php";
include "template.php";

?>
