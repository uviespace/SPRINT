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


$packets = $database->select("SELECT p.id, p.`domain`, p.`type`, p.subtype, p.name, " .
							 "	CASE p.kind " .
							 "		WHEN 0 THEN 'TC' " .
							 "		WHEN 1 THEN 'TM' " .
							 "	ELSE 'Unknown' " .
							 "	END AS kind, count(p2.id) AS packet_count " .
							 "FROM packet p " .
							 "	INNER JOIN parametersequence ps ON p.id = ps.idPacket " .
							 "	LEFT JOIN packet p2 ON p.id = p2.idParent " .
							 "WHERE p.idStandard = ? AND ps.`role` = 3 " .
							 "GROUP BY p.id, p.`domain`, p.`type`, p.subtype, p.name, kind " .
							 "ORDER BY p.`domain` , p.subtype", ["i", [$_GET["idStandard"]]]); 


$discriminants = $database->select("SELECT e.idType, e.name " .
								   "FROM enumeration e INNER JOIN `type` t ON t.id = e.idType " .
								   "WHERE t.idStandard = ? ". 
								   "ORDER BY name", ["i", [$_GET["idStandard"]]]);


$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"] ];



$pagetitle = "Derived Packets";
$site_css = "layout/standard_additions.css";
$site_js = "js/sel_packet-derived.js";
$tpl = "sel_packet-derived.tpl.php";
include "template.php";
	
?>
