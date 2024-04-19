<?php

class FunctionController extends BaseController {
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	private function calc_header_size($standard_id, $packet_id)
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

	public function get_header_size($standard_id, $packet_id)
	{
		

		$result = $this->calc_header_size($standard_id, $packet_id);
		
		$this->send_output(json_encode($result), array('HTTP/1.1 200 OK'));
	}

	public function get_parent_size($standard_id, $packet_id)
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

	public function set_calibration_curve_to_parameter($param_id, $calibration_curve_id)
	{
		if ($calibration_curve_id == 0)
			$value = "";
		else
			$value = '{ "calcurve": ' . $calibration_curve_id . '}';

		$this->database->execute_non_query("UPDATE `parameter` SET setting = ? WHERE id = ?",
										   ["si", [$value, $param_id]]);
		$this->send_output("", array("HTTP/1.1 200 OK"));
	}
}

/*public function get_packet_size($standard_id, $packet_id)
   {
   $params = $this->database->select("SELECT p.id, p.kind as packet_type, ps.`type`, ps.`order`, ps.`role`, " .
   "ps.`group`, ps.repetition, pm.name, t.size " .
   "FROM packet p " .
   "	 LEFT JOIN parametersequence ps ON ps.idPacket = p.id " .
   "	 LEFT JOIN `parameter` pm ON pm.id = ps.idParameter " .
   "	 LEFT JOIN `type` t ON t.id = pm.idType " .
   "WHERE p.id = ? " .
   "ORDER BY ps.`order`", ["i", [$packet_id]]);

   
   $header = $this->database->select("SELECT p.name, p.domain, p.multiplicity, p.`size` as param_size, " .
   "  t.id as type_id, t.`size` as type_size " .
   "FROM `parameter` p " .
   "	INNER JOIN parametersequence ps ON ps.idParameter = p.id " .
   "	LEFT JOIN `type` t ON t.id = p.idType " .
   "WHERE p.kind IN (0,1) AND ps.`type` = ? AND p.idStandard  = ? " .
   "ORDER BY ps.`order` ", ["ii", [$params[0]["packet_type"], $standard_id]]);


   if ($params[0]["packet_type"] == 0 ) {
   $header_name = "TC Header";
   } else  if ($params[0]["packet_type"] == 1 ) {
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
   }
   
   if ($header_elem["domain"] == "predefined") {
   $header_sum += $header_elem["param_size"] * $mult;
   } else  if ($header_elem["type_id"] >= 101 AND $header_elem["type_id"] < 200) {
   $header_sum += $header_elem["param_size"] * $mult;
   } else {
   $header_sum += $header_elem["type_size"] * $mult;
   }
   }

   $result = [ "name" =>  $header_name, "size" => $header_sum / 8 ];

   foreach($params as $param) {
   $result += [ "name" => $param["name"], "size" => $param["param_size"] / 8 ];
   }
   

   $this->send_output(json_encode($result));
   }*/

?>
