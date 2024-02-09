<?php

require_once "utils/session_utils.php";
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


$apids = $database->select("SELECT id, address, name FROM `process` WHERE idProject = ? ORDER BY address",
						   ["i", [$_GET["idProject"]]]);

$services = $database->select("SELECT id, name, type FROM `service` WHERE idStandard = ? ORDER BY type",
							  ["i", [$_GET["idStandard"]]]);


# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "Packets";
$site_js = "js/view_packet.js";
$tpl = "view_packet.tpl.php";
include "template.php";

?>
