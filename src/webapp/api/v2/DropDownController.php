<?php

require_once "BaseController.php";

class DropDownController extends BaseController {
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_datatypes(int $standard_id)
	{
		$data_types = $this->database->select("SELECT id, domain, name " .
											  "FROM `type` " .
											  "WHERE idStandard = ? OR idStandard IS NULL " .
											  "ORDER BY domain, name",
											  ["i", [$standard_id]]);
		$result = array();
		foreach ($data_types as $type) {
			array_push($result, [ "value" => $type["id"],
								  "label" => $type["domain"] . " / " . $type["name"] . " (" . $type["id"] . ")"]);
		}
		
		$this->send_output(json_encode($result), array("HTTP/1.1 200 OK"));		
	}
}

?>
