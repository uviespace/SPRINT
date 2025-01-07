<?php

require_once "utils/session_utils.php";
require_once 'db/db_config.php';
require_once 'db/Database.php';
require_once 'utils/utils.php';
require_once 'utils/StandardImporter.php';
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
$user = $database->select("SELECT id, name, email FROM user WHERE id = ?", ["i", [$_SESSION['userid']]]);
$current_project = $database->select("SELECT name, `desc` FROM project WHERE id = ?", ["i", [$_GET['idProject']]]);
$current_standard = $database->select("SELECT name, `desc` FROM standard WHERE id = ?", ["i", [$_GET['idStandard']]]);

$selected_project = -1;
$projects = [ [ 'id' => '-1', 'name' => 'Please select a project' ] ];
$projects = array_merge($projects, $database->select(
	"SELECT p.id, p.name " .
	"FROM project p INNER JOIN userproject u ON u.idProject = p.id " .
	"WHERE (u.idUser  = ? OR u.email = ?) AND u.idRole < 4", ["is", [$user[0]['id'], $user[0]['email']]]));

$selected_standard = -1;
$standards = [ [ 'id' => -1, 'name' => 'Please select a standard' ] ];
if (isset($_POST['project']) && $_POST['project'] > -1) {
	$selected_project = $_POST['project'];
	$standards = array_merge($standards, $database->select("SELECT id, name FROM `standard` WHERE idProject = ?",
														   ["i", [$_POST['project']]]));
}

$services = [];
$tc_header_cnt = -1;
$tm_header_cnt = -1;
$tc_header_selected = isset($_POST['tc_header_check']) and $_POST['tc_header_check'] == 1 ? 1 : 0;
$tm_header_selected = isset($_POST['tm_header_check']) and $_POST['tm_header_check'] == 1 ? 1 : 0;
if (isset($_POST['standard']) && $_POST['standard'] > -1) {
	$tc_header_cnt_result = $database->select("SELECT count(*) as cnt
                                               FROM `parameter` AS p INNER JOIN `parametersequence` AS ps ON p.id = ps.idParameter
	                                           WHERE p.idStandard = ?
	                                           AND (p.kind = 1 OR p.kind = 0) AND ps.`type` = 0", ["i", [$_GET['idStandard']]]);
	$tc_header_cnt = count($tc_header_cnt_result) > 0 ? $tc_header_cnt_result[0]['cnt'] : 0;

	$tm_header_cnt_result = $database->select("SELECT count(*) as cnt
                                               FROM `parameter` AS p INNER JOIN `parametersequence` AS ps ON p.id = ps.idParameter
	                                           WHERE p.idStandard = ?
	                                           AND (p.kind = 1 OR p.kind = 0) AND ps.`type` = 1", ["i", [$_GET['idStandard']]]);
	$tm_header_cnt = count($tc_header_cnt_result) > 0 ? $tm_header_cnt_result[0]['cnt'] : 0;
	
	$selected_standard = $_POST['standard'];
	$result = $database->select(
		"SELECT s.id, s.name, s.`type`, s.`desc`, sub.id as service_exists
         FROM service s
	         LEFT JOIN service sub ON sub.`type` = s.`type` AND sub.idStandard = ?
         WHERE s.idStandard = ?
         ORDER BY `type`, sub.idStandard ", ["ii", [$_GET['idStandard'], $selected_standard]]);
		
	foreach($result as $r)
		$services[$r['id']] = $r;

}

if (isset($_POST['sel_all_services'])) {
	$_POST['sel_services'] = [];
	foreach($services as $s)
		array_push($_POST['sel_services'], $s['id']);
}

if (isset($_POST['sel_services'])) {
	foreach($_POST['sel_services'] as $s) {
		$services[$s]['sub_services'] = $database->select(
			"SELECT * FROM packet p WHERE idStandard = ? AND `type` = ?",
			["ii", [$selected_standard, $services[$s]['type']]]);
	}
}


$import_msg = [];
$import_results = [];
if (isset($_POST['import'])) {
	//list($import_msg, $import_results) = import_services($database, $import_msg, $import_results, $services,
	//													 $_POST['sel_services'], $_POST['sel_sub_services'], $_GET['idStandard']);

	$importer = new StandardImporter($selected_standard, $_GET['idStandard']);

	$importer->import_headers($tc_header_selected, $tm_header_selected);

	if (isset($_POST['sel_services']) && isset($_POST['sel_sub_services']))
		$importer->import_services($services, $_POST['sel_services'], $_POST['sel_sub_services']);

	$import_msg = $importer->import_msg;
	$import_results = $importer->import_results;
}



$sidebar_actions = [
	[ "label" => "Back", "link" => "open_standard.php?idProject=" . $_GET['idProject'] . "&idStandard=" . $_GET['idStandard']],
	[ "label" => "Home", "link" => "index.php"]
];

$pagetitle = "Import to Standard " . $current_standard[0]['name'];
$tpl = "view_standard-import.tpl.php";
include "template.php";



function import_header_type($database, $import_msg, $import_results, $header_type, $import_standard_id, $own_standard_id)
{
	$sql = "SELECT p.id as param_id, p.kind as param_kind, p.`domain` as param_domain, p.name as param_name,
	                p.shortDesc as param_short_desc, p.`desc` as param_desc, p.value as param_value, p.`size` as param_size,
	                p.unit as param_unit, p.multiplicity as param_multiplicity, p.setting as param_setting, p.role as param_role,
	                ps.id seq_id, ps.`type` as seq_type, ps.`role` as seq_role, ps.`order` as seq_order, ps.`group` as seq_group,
	                ps.repetition as seq_repetition, ps.value as seq_value, ps.`desc` as seq_desc, ps.setting as seq_setting,
	                t.id as type_id, t.idStandard as type_standard_id, t.`domain` as type_domain, t.name as type_name,
                    t.nativeType as type_native_type, t.`desc` as type_desc, t.`size` as type_size, t.value as type_value,
                    t.setting as type_setting, t.`schema` as type_schema
               FROM `parameter` p 
	                INNER JOIN parametersequence ps ON ps.idParameter = p.id 
	                INNER JOIN `type` t ON t.id = p.idType 
               WHERE p.idStandard = ? AND p.kind IN (0, 1) AND ps.`type` = ?
               ORDER BY ps.`order` ";


	$header = $database->select($sql, ["ii", [$import_standard_id, $header_type]]);

	foreach($header as $param) {
		// Insert user type or use standard type
		$type_id = -1;
		if ($param['type_standard_id'] == NULL) {
			// Type is a standard type and can be used directly
			$type_id = $param['type_id'];
		} else {
			// Type is a unique type to the standard and has to be inserted
			$type_id = $database->insert(
				"INSERT INTO `type` (idStandard, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, " .
				"    `setting`, `schema`) " .
				"VALUES (?,?,?,?,?,?,?,?,?)",
				["issssiiss",
				 [ $own_standard_id, $param['type_domain'], $param['type_name'], $param['type_nativeType'],
				   $param['type_desc'], $param['type_size'], $param['type_value'], $param['type_setting'],
				   $param['type_schema'] ]]);
		}

		// Insert parameter

		$param_id = $database->insert(
			"INSERT INTO parameter (`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, " .
			"    `size`, `unit`, `multiplicity`, `setting`, `role`) " .
			"VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
			["iiissssiisiss", [ $own_standard_id, $type_id, $param["param_kind"], $param["param_domain"],
								$param['param_name'], $param['param_shortDesc'], $param['param_desc'],
								$param['param_value'], $param['param_size'],
								$param['param_unit'], $param['param_multiplicity'], $param['param_setting'],
								$param['param_role'] ]]);

		// Insert parametersequence

		$param_seq_id = $database->insert(
			"INSERT INTO parametersequence (`idStandard`, `idParameter`, `idPacket`, `type`, `role`, `order`, `group`, " .
			"    `repetition`, `value`, `desc`, `setting`) " .
			"VALUES (?,?,?,?,?,?,?,?,?,?,?)",
			["iiiiiiiiiss", [ $own_standard_id, $param_id, $id, $param["seq_type"],
							  $param['seq_role'], $param['seq_order'], $param['seq_group'],
							  $param['seq_repitition'], $param['seq_value'], $param['seq_desc'],
							  $param['seq_setting'] ]]);
	}


	
	return array($import_msg, $import_results);
}




function import_headers($database, $import_msg, $import_results, $tc_header_selected, $tm_header_selected,
						$import_standard_id, $own_standard_id)
{
	try {

		$database->begin_transaction();

		
		if ($tc_header_selected)
			import_header_type($database, $import_msg, $import_results, 0, $import_standard_id, $own_standard_id);

		if ($tm_header_selected) 
			import_header_type($database, $import_msg, $import_results, 1, $import_standard_id, $own_standard_id);
		


		$database->rollback();
	} catch(mysqli_sql_exception $e) {
		$database->rollback();
	}


	return array($import_msg, $import_results);
}


function import_services($database, $import_msg, $import_results, $services, $selected_services, $selected_subservices, $own_standard_id)
{
	
	try {
		$database->begin_transaction();

		
		// Import services
		foreach ($selected_services as $s_id) {

			// Only import if service type does not already exist
			if (!service_exists($database, $own_standard_id, $services[$s_id]['type'])) {

				$database->insert(
					"INSERT INTO `service` " .
					"(idStandard, name, `desc`, `type`)" .
					"VALUES (?,?,?,?)",
					["issi", [$own_standard_id, $services[$s_id]['name'], $services[$s_id]['desc'], $services[$s_id]['type']]]);
				
			} else {
				array_push($import_msg, "Service of type " . $services[$s_id]['type'] . " already exists in standard and won't be imported");
			}

			// Import Subservices
			foreach ($services[$s_id]['sub_services'] as $sub_s) {
				if (!subservice_exists($database, $own_standard_id, $sub_s['type'], $sub_s['subtype'])) {
					$id  = $database->insert(
						"INSERT INTO packet (idStandard, kind, `type`, subtype, discriminant, domain, name, shortDesc, " .
						"`desc`, descParam, descDest, code) " .
						"VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
						["iiiissssssss", [$own_standard_id, $sub_s['kind'], $sub_s['type'], $sub_s['subtype'], $sub_s['discriminant'],
										  $sub_s['domain'], $sub_s['name'], $sub_s['shortDesc'], $sub_s['desc'], $sub_s['descParam'],
										  $sub_s['descDest'], $sub_s['code']]]);

					// Inserting parameters
					$param_sequence = $database->select(
						"SELECT ps.id as param_seq_id, p.id as param_id, pa.id as packet_id,
                         	pa.name, p.name,
                         	ps.`type` as param_seq_type, ps.`role` as param_seq_role, ps.`order` as param_seq_order,
                             ps.`group` as param_seq_group, 
                         	ps.repetition param_seq_repitition, ps.value param_seq_value, ps.`desc` param_seq_desc,
                             ps.setting as param_seq_setting,  
                         	p.kind as param_kind, p.`domain` as param_domain, p.name as param_name,
                             p.shortDesc as param_shortDesc, 
                         	p.`desc` as param_desc, p.value as param_value, p.`size` as param_size, p.unit as param_unit, 
                         	p.multiplicity as param_multiplicity,  p.setting as param_setting, p.`role` as param_role,  
                         	t.id as type_id, t.idStandard as type_standard_id, t.`domain` as type_domain,
                             t.name as type_name, t.setting as type_setting,
                         	t.nativeType as type_nativeType, t.`desc` as type_desc, 
                         	t.`size` as type_size, t.value as type_value, t.`schema` as type_schema 
                         FROM parametersequence ps 
                         	INNER JOIN `parameter` p ON p.id = ps.idParameter
                         	INNER JOIN packet pa ON pa.id = ps.idPacket
                         	INNER JOIN `type` t ON t.id = p.idType 
                         WHERE ps.idPacket = ? 
                         ORDER BY ps.`order`",
						["i", [$sub_s['id']]]);


					foreach ($param_sequence as $param) {
						// Insert user type or use standard type
						$type_id = -1;
						if ($param['type_standard_id'] == NULL) {
							// Type is a standard type and can be used directly
							$type_id = $param['type_id'];
						} else {
							// Type is a unique type to the standard and has to be inserted
							$type_id = $database->insert(
								"INSERT INTO `type` (idStandard, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, " .
								"    `setting`, `schema`) " .
								"VALUES (?,?,?,?,?,?,?,?,?)",
								["issssiiss",
								 [ $own_standard_id, $param['type_domain'], $param['type_name'], $param['type_nativeType'],
								   $param['type_desc'], $param['type_size'], $param['type_value'], $param['type_setting'],
								   $param['type_schema'] ]]);
						}


						// Insert parameter

						$param_id = $database->insert(
							"INSERT INTO parameter (`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, " .
							"    `size`, `unit`, `multiplicity`, `setting`, `role`) " .
							"VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
							["iiissssiisiss", [ $own_standard_id, $type_id, $param["param_kind"], $param["param_domain"],
												$param['param_name'], $param['param_shortDesc'], $param['param_desc'],
												$param['param_value'], $param['param_size'],
												$param['param_unit'], $param['param_multiplicity'], $param['param_setting'],
												$param['param_role'] ]]);

						// Insert parametersequence

						$param_seq_id = $database->insert(
							"INSERT INTO parametersequence (`idStandard`, `idParameter`, `idPacket`, `type`, `role`, `order`, `group`, " .
							"    `repetition`, `value`, `desc`, `setting`) " .
							"VALUES (?,?,?,?,?,?,?,?,?,?,?)",
							["iiiiiiiiiss", [ $own_standard_id, $param_id, $id, $param["param_seq_type"],
											  $param['param_seq_role'], $param['param_seq_order'], $param['param_seq_group'],
											  $param['param_seq_repitition'], $param['param_seq_value'], $param['param_seq_desc'],
											  $param['param_seq_setting'] ]]);


						
						array_push($import_results, [
							"service" => $services[$s_id]['name'],
							"sub_service" => $sub_s['name'],
							"param_id" => $param_id,
							"param_name" => $param['param_name']
						]);
					}
					
				} else {
					array_push($import_msg, "Subservice with type " . $sub_s['type'] . " and subservice type " . $sub_s['subtype'] .
											" already exists in standard and won't be imported");
				}
				
			}
			
			
			
		}

		$database->commit();
		//$database->rollback();

	} catch (mysqli_sql_exception $e) {

		$database->rollback();
		array_push($import_msg, "Error while trying to import standard");
		array_push($import_msg, $e->getMessage());
	}



	return array($import_msg, $import_results);
}


function service_exists($database, $own_standard_id, $type)
{
	$result = $database->select("SELECT id FROM service WHERE idStandard = ? AND `type` = ?", ["ii", [$own_standard_id, $type]]);

	return count($result) > 0;
}

function subservice_exists($database, $own_standard_id, $type, $subtype)
{
	$result = $database->select("SELECT id FROM packet WHERE idStandard = ? AND `type` = ? AND subtype = ?",
								["iii", [$own_standard_id, $type, $subtype]]);

	return count($result) > 0;
}



?>
