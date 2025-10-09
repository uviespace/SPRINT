<?php

require_once "BaseController.php";

class FunctionController extends BaseController {
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	private function calc_header_size(int $standard_id, int $packet_id): array
	{
		$packet = $this->database->select("SELECT p.kind as packet_type FROM packet p WHERE id = ?",
										  ["i", [$packet_id]]);

		$header = $this->database->select("SELECT p.name, p.domain, p.multiplicity, p.`size` as param_size, " .
										  "  t.id as type_id, t.`size` as type_size " .
										  "FROM `parameter` p " .
										  "	INNER JOIN parametersequence ps ON ps.idParameter = p.id " .
										  "	LEFT JOIN `type` t ON t.id = p.idType " .
										  "WHERE p.kind IN (0,1) AND ps.`type` = ? AND p.idStandard  = ? " .
										  "ORDER BY ps.`order` ", ["ii", [$packet[0]["packet_type"], $standard_id]]);

		if ($packet[0]["packet_type"] == 0 ) {
			$header_name = "TC Header";
		} else  if ($packet[0]["packet_type"] == 1 ) {
			$header_name = "TM Header";
		} else {
			$header_name = "Uknown Header type";
		}

		$header_sum = 0;
		foreach ($header as $header_elem) {
			$mult = 1;
			// who writes string "null" into the database and why is multiplicity a string?
			if ($header_elem["multiplicity"] != NULL AND $header_elem["multiplicity"] != "null") {
				$mult = $header_elem["multiplicity"];
				if ($mult ==  0)
					$mult = 1;
			}
			
			if ($header_elem["domain"] == "predefined") {
				$header_sum += $header_elem["param_size"] * $mult;
			} else  if ($header_elem["type_id"] >= 101 AND $header_elem["type_id"] < 200) {
				$header_sum += $header_elem["param_size"] * $mult;
			} else {
				$header_sum += $header_elem["type_size"] * $mult;
			}
		}

		return [ "name" =>  $header_name, "size" => $header_sum / 8, "color" => "#6495ED" ];
	}

	public function get_header_size(int $standard_id, int $packet_id): void
	{
		$result = $this->calc_header_size($standard_id, $packet_id);
		
		$this->send_output(json_encode($result), array('HTTP/1.1 200 OK'));
	}

	public function get_parent_size(int $standard_id, int $packet_id): void
	{
		$parent = $this->database->select("SELECT p.name, t.`size`, ps.role " .
										  "FROM packet pa " .
										  "	INNER JOIN parametersequence ps ON ps.idPacket = pa.id " .
										  "	INNER JOIN `parameter` p ON p.id = ps.idParameter " .
										  "	INNER JOIN `type` t ON t.id = p.idType " .
										  "WHERE pa.id = ?", ["i", [$packet_id]]);

		$result = [ "header" => $this->calc_header_size($standard_id, $packet_id),
					"parent" => $parent ];

		$this->send_output(json_encode($result), array('HTTP/1.1 200 OK'));
	}

	public function set_calibration_curve_to_parameter(int $param_id, int $calibration_curve_id): void
	{
		if ($calibration_curve_id == 0)
			$value = "";
		else
			$value = '{ "calcurve": ' . $calibration_curve_id . '}';

		$this->database->execute_non_query("UPDATE `parameter` SET setting = ? WHERE id = ?",
										   ["si", [$value, $param_id]]);
		$this->send_output("", array("HTTP/1.1 200 OK"));
	}

	public function set_component_settings(int $application_id, int $component_id, string $settings): void
	{
		$this->database->execute_non_query("UPDATE applicationcomponent SET setting = ? WHERE idApplication = ? AND idComponent = ?",
										   ["sii", [$settings, $application_id, $component_id]]);
		$this->send_output($settings, array("HTTP/1.1 200 OK"));
	}

	public function check_datapool_id(int $project_id, int $param_id, int $datapool_id): void
	{
		$result = $this->database->select(
			"SELECT idParameter, nrParameter FROM datapoolidentifier 
             WHERE idProject = ? AND idParameter <> ? AND nrParameter = ?",
			["iii", [$project_id, $param_id, $datapool_id]]);

		$this->send_output(json_encode([ "datapool_id_count" => count($result) ]), array("HTTP/1.1 200 OK"));
	}

	public function get_next_datapool_id(int $project_id): void
	{
		$result = $this->database->select(
			"SELECT max(nrParameter) AS max_id FROM datapoolidentifier
			 WHERE idProject = ?",
			["i", [$project_id]]);

		$this->send_output(json_encode([ "next_datapool_id" => $result[0]["max_id"] + 1 ]), array("HTTP/1.1 200 OK"));
	}

	public function is_variable_monitored(int $standard_id, int $param_id): void
	{
		$result = $this->database->select(
			"SELECT count(*) as param_count FROM `parameter` p
             WHERE p.idstandard = ? AND name LIKE CONCAT((SELECT name FROM `parameter` p2 WHERE id = ?), '%')",
			["ii", [$standard_id, $param_id]]);

		$this->send_output(json_encode([ "variable_monitored" => $result[0]["param_count"] > 1 ]), array("HTTP/1.1 200 OK"));
	}
	
}

?>
