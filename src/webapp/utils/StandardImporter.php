<?php

require_once 'db/db_config.php';
require_once 'db/Database.php';

class StandardImporter
{
	private $database;

	private int $import_standard_id;
	private int $own_standard_id;


	public $import_msg;
	public $import_results;
	
	
	public function __construct(int $import_standard_id, int $own_standard_id)
	{
		$this->database = new Database();
		$this->import_msg = [];
		$this->import_results = [];
		
		$this->import_standard_id = $import_standard_id;
		$this->own_standard_id = $own_standard_id;

		
	}


	public function import_headers($tc_header, $tm_header)
	{
		try {
			$this->database->begin_transaction();

			if ($tc_header)
				$this->import_header_type(0);

			if ($tm_header)
				$this->import_header_type(1);

			$this->database->commit();
			//$this->database->rollback();
		} catch(mysqli_sql_exception $e) {
			$this->database->rollback();
			array_push($this->import_msg, "Error while trying to import headers");
			array_push($this->import_msg, $e->getMessage());
		}
	}


	public function import_services($services, $selected_services, $selected_subservices)
	{
		try {
			$this->database->begin_transaction();

			
			// Import services
			foreach ($selected_services as $s_id) {

				// Only import if service type does not already exist
				if (!$this->service_exists($services[$s_id]['type'])) {

					$this->database->insert(
						"INSERT INTO `service` " .
						"(idStandard, name, `desc`, `type`)" .
						"VALUES (?,?,?,?)",
						["issi", [$this->own_standard_id, $services[$s_id]['name'], $services[$s_id]['desc'], $services[$s_id]['type']]]);
					
				} else {
					array_push($this->import_msg, "Service of type " . $services[$s_id]['type'] .
												  " already exists in standard and won't be imported");
				}

				// Import Subservices
				foreach ($services[$s_id]['sub_services'] as $sub_s) {
					if (!$this->subservice_exists($sub_s['type'], $sub_s['subtype'])) {
						$id  = $this->database->insert(
							"INSERT INTO packet (idStandard, kind, `type`, subtype, discriminant, domain, name, shortDesc, " .
							"`desc`, descParam, descDest, code) " .
							"VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
							["iiiissssssss", [$this->own_standard_id, $sub_s['kind'], $sub_s['type'], $sub_s['subtype'],
											  $sub_s['discriminant'], $sub_s['domain'], $sub_s['name'], $sub_s['shortDesc'],
											  $sub_s['desc'], $sub_s['descParam'],
											  $sub_s['descDest'], $sub_s['code']]]);

						// Inserting parameters
						$param_sequence = $this->database->select(
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
								$type_id = $this->database->insert(
									"INSERT INTO `type` (idStandard, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, " .
									"    `setting`, `schema`) " .
									"VALUES (?,?,?,?,?,?,?,?,?)",
									["issssiiss",
									 [ $this->own_standard_id, $param['type_domain'], $param['type_name'], $param['type_nativeType'],
									   $param['type_desc'], $param['type_size'], $param['type_value'], $param['type_setting'],
									   $param['type_schema'] ]]);
							}


							// Insert parameter

							$param_id = $this->database->insert(
								"INSERT INTO parameter (`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, " .
								"    `size`, `unit`, `multiplicity`, `setting`, `role`) " .
								"VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
								["iiissssiisiss", [ $this->own_standard_id, $type_id, $param["param_kind"], $param["param_domain"],
													$param['param_name'], $param['param_shortDesc'], $param['param_desc'],
													$param['param_value'], $param['param_size'],
													$param['param_unit'], $param['param_multiplicity'], $param['param_setting'],
													$param['param_role'] ]]);

							// Insert parametersequence

							$param_seq_id = $this->database->insert(
								"INSERT INTO parametersequence (`idStandard`, `idParameter`, `idPacket`, `type`, `role`, `order`, `group`, " .
								"    `repetition`, `value`, `desc`, `setting`) " .
								"VALUES (?,?,?,?,?,?,?,?,?,?,?)",
								["iiiiiiiiiss", [ $this->own_standard_id, $param_id, $id, $param["param_seq_type"],
												  $param['param_seq_role'], $param['param_seq_order'], $param['param_seq_group'],
												  $param['param_seq_repitition'], $param['param_seq_value'], $param['param_seq_desc'],
												  $param['param_seq_setting'] ]]);


							
							array_push($this->import_results, [
								"service" => $services[$s_id]['name'],
								"sub_service" => $sub_s['name'],
								"param_id" => $param_id,
								"param_name" => $param['param_name']
							]);
						}
						
					} else {
						array_push($this->import_msg, "Subservice with type " . $sub_s['type'] . " and subservice type " . $sub_s['subtype'] .
													  " already exists in standard and won't be imported");
					}
					
				}
				
				
				
			}

			$this->database->commit();
			//$this->database->rollback();

		} catch (mysqli_sql_exception $e) {

			$this->database->rollback();
			array_push($this->import_msg, "Error while trying to import standard");
			array_push($this->import_msg, $e->getMessage());
		}
	}
	

	private function service_exists($type)
	{
		$result = $this->database->select("SELECT id FROM service WHERE idStandard = ? AND `type` = ?",
										  ["ii", [$this->own_standard_id, $type]]);

		return count($result) > 0;
	}

	private function subservice_exists($type, $subtype)
	{
		$result = $this->database->select("SELECT id FROM packet WHERE idStandard = ? AND `type` = ? AND subtype = ?",
										  ["iii", [$this->own_standard_id, $type, $subtype]]);

		return count($result) > 0;
	}
	
	
	

	private function import_header_type($header_type)
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


		$header = $this->database->select($sql, ["ii", [$this->import_standard_id, $header_type]]);

		foreach($header as $param) {
			// Insert user type or use standard type
			$type_id = -1;
			if ($param['type_standard_id'] == NULL) {
				// Type is a standard type and can be used directly
				$type_id = $param['type_id'];
			} else {
				// Type is a unique type to the standard and has to be inserted
				$type_id = $this->database->insert(
					"INSERT INTO `type` (idStandard, `domain`, `name`, `nativeType`, `desc`, `size`, `value`, " .
					"    `setting`, `schema`) " .
					"VALUES (?,?,?,?,?,?,?,?,?)",
					["issssiiss",
					 [ $this->own_standard_id, $param['type_domain'], $param['type_name'], $param['type_native_type'],
					   $param['type_desc'], $param['type_size'], $param['type_value'], $param['type_setting'],
					   $param['type_schema'] ]]);
			}

			// Insert parameter

			$param_id = $this->database->insert(
				"INSERT INTO parameter (`idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, " .
				"    `size`, `unit`, `multiplicity`, `setting`, `role`) " .
				"VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
				["iiissssiisiss", [ $this->own_standard_id, $type_id, $param["param_kind"], $param["param_domain"],
									$param['param_name'], $param['param_short_desc'], $param['param_desc'],
									$param['param_value'], $param['param_size'],
									$param['param_unit'], $param['param_multiplicity'], $param['param_setting'],
									$param['param_role'] ]]);

			// Insert parametersequence

			$param_seq_id = $this->database->insert(
				"INSERT INTO parametersequence (`idStandard`, `idParameter`, `type`, `role`, `order`, `group`, " .
				"    `repetition`, `value`, `desc`, `setting`) " .
				"VALUES (?,?,?,?,?,?,?,?,?,?)",
				["iiiiiiiiss", [ $this->own_standard_id, $param_id, $param["seq_type"],
								  $param['seq_role'], $param['seq_order'], $param['seq_group'],
								  $param['seq_repetition'], $param['seq_value'], $param['seq_desc'],
								  $param['seq_setting'] ]]);


			array_push($this->import_results, [
				"service" => ($header_type == 0 ? "TC " : "TM ") . "Header",
				"sub_service" => "",
				"param_id" => $param_id,
				"param_name" => $param['param_name']
			]);
		}
	}
	
	
	
	
}



?>
