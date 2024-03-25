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

if (!isset($_GET["idParent"])) {
	http_response_code(500);
	die('idParent needed');
}


$database = new Database();

$project_name = $database->select("SELECT name FROM project WHERE id = ?", ["i", [$_GET["idProject"]]])[0]["name"];
$standard_name = $database->select("SELECT name FROM standard WHERE id = ?", ["i", [$_GET["idStandard"]]])[0]["name"];

$header_info = $database->select("SELECT CASE par.kind " .
								 "	        WHEN 0 THEN 'TC' " .
								 "	        WHEN 1 THEN 'TM' " .
								 "	        ELSE 'Unknown' " .
								 "       END AS kind, par.`type`, par.subtype, par.name, " .
								 "    p.discriminant " . 
								 "FROM packet p INNER JOIN packet par ON p.idParent = par.id ". 
								 "WHERE p.id = ?", ["i", [$_GET["idPacket"]]]);

$parameter_values = get_parameter_values($database, $_GET['idStandard']);
$role_values = get_roles($database, 2);


$sidebar_actions = [
	["label" => "Back",
	 "link" => "sel_packet-derived.php?idProject=" . $_GET["idProject"] .
			 "&idStandard=" . $_GET["idStandard"] . "&open=" . $_GET["idParent"] ],
	["label" => "Home", "link" => "index.php" ]
];

$site_js = "js/view_packet-params-derived.js";
$pagetitle = "Packet Parameters";
$tpl = "view_packet-params-derived.tpl.php";
include "template.php";

?>
