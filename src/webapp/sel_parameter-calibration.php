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

$cal_curves = $database->select("SELECT c.id, c.name, c.shortDesc, c.`type`, " .
								"CASE `type` " .
								"	WHEN 0 THEN 'NUM' " .
								"   WHEN 1 THEN 'POL' " .
								"	WHEN 2 THEN 'LOG' " .
								"	ELSE 'UNKNOWN' " .
								"END AS type_name, " .
								"count(p.id) as param_count " .
								"FROM calibration c " .
								"    LEFT JOIN `parameter` p ON c.id = JSON_VALUE(p.setting, '$.calcurve') " .
								"WHERE c.idStandard = ? " .
								"GROUP BY id, name, shortDesc, `type`, type_name " .
								"ORDER BY name", ["i", [$_GET["idStandard"]]]);

$params = $database->select("SELECT p.id, p.`domain`, p.name, JSON_VALUE(p.setting, '$.calcurve') as calcurve, " .
							"   c.id as curve_id,  c.name as curve_name " .
							"FROM `parameter` p " .
							"	LEFT JOIN calibration c ON c.id = JSON_VALUE(p.setting, '$.calcurve') " .
							"WHERE p.idStandard = ? " .
							"ORDER BY CASE WHEN curve_id IS NULL THEN 1 ELSE 0 END, p.`domain`, p.name ", ["i", [$_GET["idStandard"]]]);

$select_curves = $database->select("SELECT id, name, " .
								   "	CASE `type`	" .
								   "		WHEN 0 THEN 'NUM' " .
								   "		WHEN 1 THEN 'POL' " .
								   "		WHEN 2 THEN 'LOG' " .
								   "		ELSE 'UNKNOWN' " .
								   "	END AS type_name " .
								   "FROM calibration c " .
								   "WHERE idStandard = ? " .
								   "ORDER BY name",["i", [$_GET["idStandard"]]]);


$sidebar_actions = [
	[ "label" => "Back",
	  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
			  "&idStandard=" . $_GET["idStandard"] ],
	["label" => "Home", "link" => "index.php" ]
];

$site_js = "js/sel_parameter-calibration.js"; 
$pagetitle = "Calibration Curve";
$tpl = "sel_parameter-calibration.tpl.php";
include "template.php";


?>
