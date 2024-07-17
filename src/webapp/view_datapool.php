<?php

require_once "utils/session_utils.php";
require_once "utils/utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';
require_once 'int/config.php';

const COL_KIND = 0;
const COL_DOMAIN = 1;
const COL_NAME = 2;
const COL_SHORT_DESC = 3;
const COL_DESC = 4;
const COL_VALUE = 5;
const COL_SIZE = 6;
const COL_UNIT = 7;
const COL_MULTIPLICITY = 8;
const COL_TYPE = 9;

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
$result_msg = "";

if(isset($_POST['import'])) {
	$result_msg = import_datapool($database);
}


if(isset($_POST['export'])) {
	$datapool_export = $database->select(
		"SELECT CASE p.kind " . 
		"        WHEN 0 THEN 'predefined' " . 
		"        WHEN 1 THEN 'header' " .
		"        WHEN 2 THEN 'body' " .
		"        WHEN 3 THEN 'par' " .
		"        WHEN 4 THEN 'var' " .
		"        WHEN 5 THEN 'par' " .
		"        WHEN 6 THEN 'var' " .
		"        ELSE 'kind not recognized' " .
		"    END as kind, " .
		"    p.domain, p.name, p.shortDesc, p.`desc`, p.value, p.size, p.unit, p.multiplicity, CONCAT(t.domain, '/', t.name) " .
		"FROM parameter p JOIN type t ON p.idType=t.id " .
		"WHERE p.kind IN (3,4,5,6) AND p.idStandard = ? " .
		"ORDER BY p.kind, p.domain, p.name", ["i", [$_GET["idStandard"]]]);

	$tmp_name = tempnam(sys_get_temp_dir(), 'datapool');
	$file = fopen($tmp_name, "w");
	foreach($datapool_export as $row)
		fputcsv($file, $row, "|");
	fclose($file);
	lib_dwnFile(true, $tmp_name, "application/csv");
	
}


$datatypes = $database->select("SELECT id, domain, name " .
							   "FROM `type` " .
							   "WHERE idStandard = ? OR idStandard IS NULL " .
							   "ORDER BY domain, name",
							   ["i", [$_GET["idStandard"]]]);

# Template settings
$sidebar_actions = [ ["label" => "Back",
					  "link" => "open_standard.php?idProject=" . $_GET["idProject"] .
							  "&idStandard=" . $_GET["idStandard"] ],
					 ["label" => "Home", "link" => "index.php"]  ];

$pagetitle = "Datapool";
$site_css = "layout/standard_additions.css";
$site_js = "js/view_datapool.js";
$tpl = "view_datapool.tpl.php";
include "template.php";


function import_datapool($db) {
	if(!isset($_FILES['import_file']))
		return "No file selected. Please choose a file first!";

	if (!file_exists($_FILES['import_file']['tmp_name'])) {
		return "No file selected. Please choose a file first!";
	}

	// read data and do preliminary checks
	$row_num = 0;
	$data = [];
	if ( ($file = fopen($_FILES['import_file']['tmp_name'], "r")) !== FALSE) {
		while ( ($row = fgetcsv($file, null, "|")) !== FALSE) {
			$num = count($row);

			if ($num != 10)
				return "Csv file has the wrong format on line " . $row_num;

			$data[] = $row;
			$row_num++;
		}
	}
	fclose($file);


	$types = $db->select("SELECT t.id, CONCAT(t.domain, '/', t.name) as type_name from type t " .
						 "WHERE t.idStandard IS NULL OR t.idStandard = ?", ["i", [$_GET['idStandard']]]);
	$types_map = [];
	foreach($types as $type)
		$types_map[$type["type_name"]] = $type["id"];

	$kind_map = array("par" => 3, "var" => 4);

	$msg = [];
	
	// import data in database
	foreach($data as $row) {
		if (array_key_exists($row[COL_TYPE], $types_map))
			$idType = $types_map[$row[COL_TYPE]];
		else
			$idType = null;
		
		$id = $db->insert(
			"INSERT INTO `parameter`(idStandard, idType, kind, `domain`, `name`, shortDesc, `desc`, `value`, `size`, `unit`, `multiplicity`) " .
			"VALUES(?,?,?,?,?,?,?,?,?,?,?)",
			["iiisssssisi", [$_GET['idStandard'], $idType, $kind_map[$row[COL_KIND]],
							 $row[COL_DOMAIN], $row[COL_NAME], $row[COL_SHORT_DESC],
							 $row[COL_DESC], $row[COL_VALUE], $row[COL_SIZE],
							 $row[COL_UNIT], $row[COL_MULTIPLICITY]]]);

		$msg[] = "Imported Datapool item with id " . $id;
	}

	return join("\n", $msg);
}


?>
