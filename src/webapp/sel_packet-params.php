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


$packets = $database->select("SELECT p.id, p.`domain`, p.`type`, p.subtype, p.name, count(ps.id) as param_count, " .
							 "  CASE p.kind " .
							 "    WHEN 0 THEN 'TC' " .
							 "    WHEN 1 THEN 'TM' " .
							 "    ELSE 'Unknown' " .
							 "  END as kind " .
							 "FROM packet p LEFT JOIN parametersequence ps ON p.id = ps.idPacket " .
							 "WHERE p.idStandard = ? AND (p.discriminant = '' OR p.discriminant IS NULL) " .
							 "GROUP BY id, `domain`, `type`, subtype, kind, name " .
							 "ORDER BY p.`domain`, p.`type`, p.subtype ", ["i", [$_GET["idStandard"]]]);


$role_values = get_roles($database, 2);
$parameter_values = get_parameter_values($database, $_GET['idStandard']);


# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "Packet Parameters";
$site_js = "js/sel_packet-params.js";
$site_css = "layout/standard_additions.css";
$tpl = "sel_packet-params.tpl.php";
include "template.php";

?>
