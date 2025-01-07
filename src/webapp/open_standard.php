<?php

require "utils/session_utils.php";
require 'db/db_config.php';
require_once 'int/config.php';
require_once 'db/Database.php';
require_once 'utils/utils.php';

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

# Check if user can acces project
# TODO: maybe put somewhere else to call
$user_project = $database->select("SELECT id fROM userproject u WHERE idUser = ? AND idProject = ?",["ii", [$_SESSION['userid'], $_GET['idProject']]]);

if (count($user_project) == 0 && !$_SESSION['is_admin']) {
	http_response_code(403);
	die('Forbidden');
}

# Load neccesary data

$project = $database->select("SELECT name FROM `project` WHERE id = ?", ["i", [$_GET['idProject']]]);
$standard = $database->select("SELECT id, name, `desc` FROM standard WHERE id = ?", ["i", [$_GET['idStandard']]]);

$userrole = $database->select("SELECT idRole FROM userproject WHERE idProject = ? AND idUser = ?", ["ii", [$_GET["idProject"], $_SESSION['userid']]]);
$id_role = count($userrole) > 0 ? $userrole[0]['idRole'] : 5;

# TC Header, TM Header, APIDs

$headers = $database->select("SELECT ps.`type`, count(*) as cnt " .
							 "FROM " .
							 "`parameter` AS p INNER JOIN `parametersequence` AS ps ON p.id = ps.idParameter " .
							 "WHERE " .
							 "p.idStandard = ? " .
							 "AND (p.kind = 1 OR p.kind = 0) " .
							 "Group BY ps.`type` " .
							 "ORDER BY `type` ", ["i", [$_GET['idStandard']]]);

# Card 1
$tc_header_cnt = get_type_count($headers, 0);
$tm_header_cnt = get_type_count($headers, 1);
$apid_cnt = $database->select("SELECT count(*) as cnt FROM `process` WHERE idProject = ?", ["i", [$_GET['idProject']]]);

# Card 2
$services_cnt = $database->select("SELECT count(*) as cnt FROM `service` WHERE idStandard = ?", ["i", [$_GET['idStandard']]]);
$packets_cnt = $database->select("SELECT count(*) as cnt FROM `packet` WHERE `type` IS NOT NULL AND idStandard = ?", ["i", [$_GET['idStandard']]]);
$derived_packets_cnt = $database->select("SELECT count(*) as cnt " .
										 "FROM " .
										 "(SELECT p.id FROM `packet` p INNER JOIN `packet` pa ON p.id = pa.idParent " .
										 "WHERE p.idStandard = ? GROUP BY p.id " .
										 ") q ", ["i", [$_GET['idStandard']]]);
$base_packets_cnt = $database->select("SELECT count(*) as cnt " .
									  "FROM `packet` p INNER JOIN `parametersequence` ps ON p.id = ps.idPacket " .
									  "WHERE p.idStandard = ? AND ps.role = 3", ["i", [$_GET['idStandard']]]);
$params_packets_cnt = $database->select("SELECT count(*) as cnt FROM `packet` WHERE `type` IS NOT NULL AND idStandard = ?",
										["i", [$_GET['idStandard']]]);

# Card 3

$consts_cnt = $database->select("SELECT count(*) as cnt FROM constants WHERE idStandard = ?", ["i", [$_GET['idStandard']]]);
$types_cnt = $database->select("SELECT count(*) as cnt FROM `type` WHERE idStandard = ?", ["i", [$_GET['idStandard']]]);
$json_enum_cnt = $database->select("SELECT count(*) as cnt FROM `type` " .
								   "WHERE idStandard = ? AND JSON_CONTAINS_PATH(setting, 'one', '$.Enumerations') = 1",
								   ["i", [$_GET['idStandard']]]);
$db_enum_cnt = $database->select("SELECT count(*) as cnt FROM ".
								 "(SELECT t.id FROM `type` t INNER JOIN `enumeration` e ON t.id = e.idType " .
								 "WHERE t.idStandard = ? GROUP BY t.id) q", ["i", [$_GET['idStandard']]]);

# Card 4

$datapool_cnt = $database->select("SELECT count(*) as cnt " .
								  "FROM `parameter` p LEFT JOIN `type` t ON p.idType = t.id " .
								  "WHERE p.idStandard = ? AND p.kind IN (3, 4, 5, 6)", ["i", [$_GET['idStandard']]]);

$params_cnt = $database->select("SELECT count(*) as cnt ".
								"FROM `parameter` AS p INNER JOIN `type` AS t ON p.idType = t.id " .
								"WHERE p.idStandard = ? AND p.kind IN (0, 1, 2)", ["i", [$_GET['idStandard']]]);

$limits_cnt = $database->select("SELECT count(*) as cnt " .
								"FROM (SELECT DISTINCT t.id FROM `parameter` t LEFT JOIN `limit` e ON t.id = e.idParameter " .
								"WHERE  t.idStandard = ? AND e.idParameter IS NOT NULL) q " .
								"UNION " .
								"SELECT count(*) as cnt " .
								"FROM (SELECT DISTINCT t.id FROM `parameter` t LEFT JOIN `limit` e ON t.id = e.idParameter " .
								"WHERE t.idStandard = ? AND e.idParameter IS NULL) q",
								["ii", [$_GET['idStandard'], $_GET['idStandard']]]);

$params_calib = $database->select("SELECT count(*) as cnt FROM `parameter` t " .
								  "WHERE JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') AND t.idStandard = ?",
								  ["i", [$_GET['idStandard']]]);

$params_no_calib = $database->select("SELECT count(*) as cnt FROM `parameter` t " .
									 "WHERE (JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') IS NULL " .
									 "	OR JSON_CONTAINS_PATH(t.setting, 'one', '$.calcurve') = '' ".
									 "	OR t.setting = '' OR t.setting = '{}') " .
									 "	AND t.idStandard = ?",
									 ["i", [$_GET['idStandard']]]);

$calib = $database->select("SELECT count(*) as cnt FROM `calibration` WHERE idStandard = ?", ["i", [$_GET['idStandard']]]);

# POST actions
if (isset($_POST['export']))
	list($message, $errors) = export_standard($_GET['idStandard'], $python_settings);


# Template settings
$sidebar_actions = [ ["label" => "Back", "link" => "open_project.php?id=" . $_GET["idProject"] ], ["label" => "Home", "link" => "index.php"]  ];
$pagetitle = "Standard " . $standard[0]["name"];
# $site_css = "layout/open_project.css";
# $site_js = "js/open_project.js";
$tpl = "open_standard.tpl.php";
include "template.php";

# TODO: maybe put export function to central place and make the script a variable
function export_standard($idStandard, $python_settings)
{
	$cmd = $python_settings["cmd"] . " " .
		   $python_settings["script_path"] .
		   "export_csv.py standard " . $idStandard . " 2>&1";

	$file = shell_exec($cmd);
	$folder_to_delete = pathinfo($file, PATHINFO_DIRNAME);
	$file = substr($file, 0, strlen($file)-1);
	$message = "";
	$errors = [];
	
	if (file_exists($file)) {
		$message = $file;
		lib_dwnFile(true, $file);
		rmdirr($fileToDelete);
	} else {
		$errors[]  = "Error: Consistency check failed!\n";
		$errors[] .= "Please correct the errors as listed hereafter:\n";
		$errors[] .= $file;
	}

	return array($message, $errors);
}


function get_type_count($header, $type)
{
	foreach($header as $t) {
		if ($t['type'] == $type)
			return $t['cnt'];
	}

	return 0;
}

?>



