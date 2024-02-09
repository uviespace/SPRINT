<?php

require_once "BaseController.php";
require_once "CrudController.php";
require_once "../../db/Database.php";

class ProjectController extends BaseController {

	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	public function get_project($id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM project WHERE id = ?", ["i", [$id]]);
		$this->send_output(json_encode($data));
	}
}


class StandardsController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($project_id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM standard WHERE idProject = ?", ["i", [$project_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	public function create_item($project_id, $item)
	{
		$id = $this->database->insert("INSERT INTO standard(idProject, name, `desc`) VALUES(?, ?, ?)",
									  ["iss", [$item->idProject, $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($project_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM standard WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($project_id, $item)
	{
		$this->database->execute_non_query("UPDATE standard SET name = ?, `desc` = ? WHERE id = ?",
										   ["ssi", [$item->name, $item->desc, $item->id ]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class ApplicationController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($project_id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM application WHERE idProject = ?", ["i", [$project_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	public function create_item($project_id, $item)
	{
		$id = $this->database->insert("INSERT INTO application(idProject, name, `desc`) VALUES(?,?,?)",
									  ["iss", [$item->idProject, $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($project_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM application WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($project_id, $item)
	{
		$this->database->execute_non_query("UPDATE application SET name = ?, `desc` = ? WHERE id = ?",
										   ["ssi", [$item->name, $item->desc, $item->id ]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

class TCHeaderController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT ps.id, p.id AS idParameter, " .
										"  concat(p.domain, '/', p.name) as parameter, " .
										"  ps.order, ps.role, ps.group, ps.repetition, " .
										"  ps.value, ps.desc " .
										"FROM `parameter` p " .
										"INNER JOIN `parametersequence` ps " .
										"  ON ps.idParameter = p.id " .
										"WHERE p.idStandard = ? AND p.kind IN (0, 1) " .
										"  AND ps.type = 0 " .
										"ORDER BY ps.order", ["i", [$standard_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO `parametersequence` (idParameter, " .
									  "`order`, `role`, `group`, `repetition`, " .
									  "`value`, `desc`, idStandard, `type`)" .
									  "VALUES(?,?,?,?,?,?,?,?,0)",
									  ["iiiiiisi", [$item->idParameter,
													$item->order,
													$item->role,
													$item->group,
													$item->repetition,
													$item->value,
													$item->desc,
													$standard_id]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `parametersequence` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE `parametersequence` " .
										   "SET idParameter = ?, `order` = ?, " .
										   "`role` = ?, `group` = ?, `repetition` = ?, " .
										   "`value` = ?, `desc` = ? " .
										   "WHERE id = ?",
										   ["iiiiiisi", [$item->idParameter,
														 $item->order,
														 $item->role,
														 $item->group,
														 $item->repetition,
														 $item->value,
														 $item->desc,
														 $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class TMHeaderController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT ps.id, p.id AS idParameter, " .
										"  concat(p.domain, '/', p.name) as parameter, " .
										"  ps.order, ps.role, ps.group, ps.repetition, " .
										"  ps.value, ps.desc " .
										"FROM `parameter` p " .
										"INNER JOIN `parametersequence` ps " .
										"  ON ps.idParameter = p.id " .
										"WHERE p.idStandard = ? AND p.kind IN (0, 1) " .
										"  AND ps.type = 1 " .
										"ORDER BY ps.order", ["i", [$standard_id]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO `parametersequence` (idParameter, " .
									  "`order`, `role`, `group`, `repetition`, " .
									  "`value`, `desc`, idStandard, `type`)" .
									  "VALUES(?,?,?,?,?,?,?,?,1)",
									  ["iiiiiisi", [$item->idParameter,
													$item->order,
													$item->role,
													$item->group,
													$item->repetition,
													$item->value,
													$item->desc,
													$standard_id]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `parametersequence` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE `parametersequence` " .
										   "SET idParameter = ?, `order` = ?, " .
										   "`role` = ?, `group` = ?, `repetition` = ?, " .
										   "`value` = ?, `desc` = ? " .
										   "WHERE id = ?",
										   ["iiiiiisi", [$item->idParameter,
														 $item->order,
														 $item->role,
														 $item->group,
														 $item->repetition,
														 $item->value,
														 $item->desc,
														 $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class ApidController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}


	public function get_items($project_id)
	{
		$data = $this->database->select("SELECT id, address, name, `desc` " .
										"FROM `process` " .
										"WHERE idProject = ?", ["i", [$project_id]]);
		
		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	public function create_item($project_id, $item)
	{
		$id = $this->database->insert("INSERT INTO `process` (idProject, address, name, `desc`) " .
									  "VALUES (?,?,?,?)",
									  ["isss", [$project_id, $item->address,
												$item->name, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($project_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `process` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($project_id, $item)
	{
		$this->database->execute_non_query("UPDATE `process` " .
										   "SET address = ?,
										     name = ?,
										     `desc` = ? " .
										   "WHERE id = ?",
										   ["sssi", [$item->address, $item->name, $item->desc, $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class ServiceController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT id, `type`, `name`, `desc` " .
										"FROM service " .
										"WHERE idStandard = ?", ["i", [$standard_id]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	
	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO service (idStandard, `type`, `name`, `desc`) " .
									  "VALUES (?, ?, ?, ?)",
									  ["isss", [$standard_id, $item->type,
												$item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM service WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE service " .
										   "SET `type` = ?, `name` = ?, `desc` = ? " .
										   "WHERE id = ?",
										   ["sssi", [$item->type, $item->name, $item->desc, $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

class PacketController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT id, kind, type, subtype, discriminant, " .
										"  domain, name, shortDesc, `desc`, idProcess, " .
										"  descParam, descDest, code " .
										"FROM packet " .
										"WHERE type IS NOT NULL AND idStandard = ? " .
										"ORDER BY type, subtype",
										["i", [$standard_id]]);
		
		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	
	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO packet (idStandard, idProcess, kind, type, subtype, " .
									  "  domain, name, shortDesc, `desc`, " .
									  "  descParam, descDest, code) " .
									  "VALUE (?,?,?,?,?,?,?,?,?,?,?,?)" ,
									  ["iiiiisssssss", [ $standard_id,
														 $item->idProcess,
														 $item->kind,
														 $item->type,
														 $item->subtype,
														 $item->domain,
														 $item->name,
														 $item->shortDesc,
														 $item->desc,
														 $item->descParam,
														 $item->descDest,
														 $item->code]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}
	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM packet WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE packet " .
										   "SET idProcess = ?, kind = ?, type = ?, " .
										   "  subtype = ?, domain = ?, " .
										   "  name = ?, shortDesc = ?, `desc` = ?, " .
										   "  descParam = ?, descDest = ?, code = ? " .
										   "WHERE id = ?",
										   ["iiiisssssssi", [$item->idProcess,
															 $item->kind,
															 $item->type,
															 $item->subtype,
															 $item->domain,
															 $item->name,
															 $item->shortDesc,
															 $item->desc,
															 $item->descParam,
															 $item->descDest,
															 $item->code,
															 $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

class ConstantController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT id, domain, name, value, `desc` " .
										"FROM constants " .
										"WHERE idStandard = ?",
										["i", [$standard_id]]);
		
		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	
	public function get_item($project_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO constants (idStandard, domain, name, value, `desc`) " .
									  "VALUES (?,?,?,?,?)",
									  ["issss", [$standard_id, $item->domain,
												 $item->name, $item->value,
												 $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM constants WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE constants " .
										   "SET domain = ?, name = ?, " .
										   "  value = ?, `desc` = ? " .
										   "WHERE id = ?",
										   ["ssssi", [$item->domain, $item->name,
													  $item->value, $item->desc,
													  $item->id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}

class DatatypesController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT id, domain, name, nativeType, `desc`, " .
										"  size, value, " .
										"  concat('PTC/PFC: ', json_value(`setting`, '$.PUS.ptc'), '/', " .
										"json_value(`setting`, '$.PUS.pfc')) AS pusparamtype, " .
										"json_value(`setting`, '$.PUS.type') as `pusdatatype` " .

										"FROM `type` " .
										"WHERE idStandard = ?", ["i", [$standard_id]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO `type`(domain, name, nativeType, size, " .
									  "  value, `desc`, idStandard)" .
									  "VALUES(?,?,?,?,?,?,?)",
									  ["sssissi", [$item->domain, $item->name,
												   $item->nativeType, $item->size, $item->value,
												   $item->desc, $standard_id]]);
		
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `type` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
		
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE `type` " .
										   "SET domain = ?, name = ?, nativeType = ? " .
										   "  size = ?, value = ?, desc = ? " .
										   "WHERE id = ?",
										   ["sssissi", [ $item->domain, $item->name,
														 $item->nativeType, $item->size,
														 $item->size, $item->value,
														 $item->desc, $item->id ]]);

		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class DatapoolController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT  p.id, p.domain, p.name, p.kind, " .
										"  p.shortDesc, p.idType, " .
										"concat(COALESCE(t.domain,  'None'), ' / ', " .
										"       COALESCE(t.name, 'None')) AS datatype, " .
										"p.multiplicity, p.value, p.unit " .
										"FROM `parameter` p LEFT JOIN `type` t " .
										"  ON p.idType = t.id " .
										"WHERE p.idStandard  = ? " .
										"  AND p.kind IN (3, 4, 5, 6)" .
										"ORDER BY domain, kind, name",
										["i", [$standard_id]]);
		
		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	
	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO parameter(idStandard, domain, name, " .
									  "  kind, shortDesc, idType, multiplicity, value, " .
									  "  unit)" .
									  "VALUES (?,?,?,?,?,?,?,?,?)",
									  ["issisiiss", [$standard_id, $item->domain,
													 $item->name, $item->kind,
													 $item->shortDesc, $item->idType,
													 $item->multiplicity, $item->value,
													 $item->unit]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parameter WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE parameter " .
										   "SET domain = ?, name = ?, kind = ?, " .
										   "  shortDesc = ?, idType = ?, multiplicity = ?, " .
										   "  value = ?, unit = ? " .
										   "WHERE id = ?",
										   ["ssisiissi", [$item->domain, $item->name,
														  $item->kind, $item->shortDesc,
														  $item->idType, $item->multiplicity,
														  $item->value, $item->unit,
														  $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}

class ParameterController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	public function get_items($standard_id)
	{
		$data = $this->database->select("SELECT p.id, p.domain, p.name, p.kind, p.shortDesc, " .
										"  p.idType, concat(t.domain, ' / ', t.name) AS datatype, " .
										"  p.role, p.multiplicity, p.value, p.unit " .
										"FROM parameter p INNER JOIN type t ON t.id = p.idType " .
										"WHERE p.idStandard = ? AND p.kind IN (0, 1, 2) " .
										"ORDER BY p.domain, p.name ", ["i", [$standard_id]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}
	
	public function get_item($standard_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($standard_id, $item)
	{
		$id = $this->database->insert("INSERT INTO parameter(idStandard, domain, name, " .
									  "  kind, shortDesc, idType, multiplicity, value, " .
									  "  unit)" .
									  "VALUES (?,?,?,?,?,?,?,?,?)",
									  ["issisiiss", [$standard_id, $item->domain,
													 $item->name, $item->kind,
													 $item->shortDesc, $item->idType,
													 $item->multiplicity, $item->value,
													 $item->unit]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($standard_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parameter WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($standard_id, $item)
	{
		$this->database->execute_non_query("UPDATE parameter " .
										   "SET domain = ?, name = ?, kind = ?, " .
										   "  shortDesc = ?, idType = ?, multiplicity = ?, " .
										   "  value = ?, unit = ? " .
										   "WHERE id = ?",
										   ["ssisiissi", [$item->domain, $item->name,
														  $item->kind, $item->shortDesc,
														  $item->idType, $item->multiplicity,
														  $item->value, $item->unit,
														  $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}



class PacketParameterController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}
	
	
	public function get_items($packet_id)
	{
		$data = $this->database->select("SELECT ps.id, concat(p.domain, ' / ', p.name) AS parameter, " .
										"  p.id AS parameter_id, " .
										"  ps.`order`, ps.`role`, ps.`group`, ps.repetition, " .
										"  ps.value, ps.`desc`, t.size, p.name, " .
										"  pa.idStandard as standard_id " .
										"FROM `packet` AS pa " .
										"  INNER JOIN parametersequence ps ON pa.id = ps.idPacket " .
										"  INNER JOIN `parameter` p ON p.id = ps.idParameter " .
										"  INNER JOIN `type` t ON t.id = p.idType " .
										"WHERE ps.idPacket = ? AND p.kind <> 1 " .
										"ORDER BY ps.`order` ASC ", ["i", [$packet_id]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	
	public function get_item($packet_id, $id)
	{
		$this->not_found();
	}

	
	public function create_item($packet_id, $item)
	{
		$id = $this->database->insert("INSERT INTO parametersequence(idStandard, idParameter, idPacket, role, order, group, " .
									  "  repetition, value, desc) " .
									  "VALUES (?,?,?,?,?,?,?,?,?)",
									  ["iiiiiiiss", [ $item->standard_id, $item->parameter_id, $packet_id, $item->role, $item->order,
													  $item->group, $item->reptition, $item->value, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	
	public function delete_item($packet_id, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parameter WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	
	public function put_item($packet_id, $item)
	{
		$this->database->execute_non_query("UPDATE parametersequence " .
										   "SET idParameter = ?, `role` = ?, `order` = ?, `group` = ?, " .
										   "  repetition = ?, `value` = ?, `desc` = ? " .
										   "WHERE id = ?",
										   ["iiiiissi", [$item->parameter_id, $item->role, $item->order, $item->group,
														$item->repetition, $item->value, $item->desc, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}



?>
