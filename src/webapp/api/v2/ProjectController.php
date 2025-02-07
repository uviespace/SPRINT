<?php

require_once "BaseController.php";
require_once "CrudController.php";
require_once "../../db/Database.php";

class ProjectController extends BaseController implements CrudController {

	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT p.id, p.name, p.`desc`, u.id as user_id, u.name as owner, u.email as email, p.isPublic " .
			"FROM project p " .
			"	INNER JOIN userproject up ON p.id = up.idProject " .
			"	INNER JOIN `user` u ON u.id = up.idUser " .
			"WHERE up.idRole = 2 AND (up.idUser = ? OR ? = 1001 or ? = 1)",
			["iii", [$_SESSION['userid'], $_SESSION['userid'], $_SESSION['userid']]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($route_ids, $id)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM project WHERE id = ?", ["i", [$id]]);
		$this->send_output(json_encode($data));
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO project(name, `desc`, isPublic) VALUES (?, ?, ?)",
									  ["ssi", [ $item->name, $item->desc, $item->isPublic ]]);

		$item->id = $id;

		$email = $this->database->select("SELECT email FROM `user` WHERE id = ?", ["i", [$item->user_id]]);

		$id = $this->database->insert("INSERT INTO userproject(idUser, idProject, idRole, email) " .
									  "VALUES(?,?,2,?)",
									  ["iis", [$item->user_id, $item->id, $email[0]["email"]]]);

		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$result = $this->database->select("SELECT count(*) as app_cnt FROM application WHERE idProject = ?",
										  ["i", [$item_id]]);

		if ($result[0]["app_cnt"] > 0)
			throw new Exception("Project already contains an application");


		$result = $this->database->select("SELECT count(*) as stn_cnt FROM standard WHERE idProject = ?",
										  ["i", [$item_id]]);

		if ($result[0]["stn_cnt"] > 0)
			throw new Exception("Project already contains a standard");

		$this->database->execute_non_query("DELETE FROM project WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query("UPDATE project SET name = ?, `desc` = ?, isPublic = ? WHERE id = ?",
										   ["ssii", [$item->name, $item->desc, $item->isPublic, $item->id]]);

		$this->send_output('', array('HTTP/1.1 200 OK'));
	}
}


class StandardsController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM standard WHERE idProject = ?", ["i", [$route_ids["project_id"]]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO standard(idProject, name, `desc`) VALUES(?, ?, ?)",
									  ["iss", [$route_ids["project_id"], $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM standard WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, name, `desc` FROM application WHERE idProject = ?", ["i", [$route_ids["project_id"]]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO application(idProject, name, `desc`) VALUES(?,?,?)",
									  ["iss", [$route_ids["project_id"], $item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM application WHERE id = ?", ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
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
										"ORDER BY ps.order", ["i", [$route_ids["standard_id"]]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
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
													$route_ids["standard_id"]]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `parametersequence` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
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
										"ORDER BY ps.order", ["i", [$route_ids["standard_id"]]]);
		$this->send_output(json_encode($data));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
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
													$route_ids["standard_id"]]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `parametersequence` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
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


	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, address, name, `desc` " .
										"FROM `process` " .
										"WHERE idProject = ?", ["i", [$route_ids["project_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO `process` (idProject, address, name, `desc`) " .
									  "VALUES (?,?,?,?)",
									  ["isss", [$route_ids["project_id"], $item->address,
												$item->name, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `process` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, `type`, `name`, `desc` " .
										"FROM service " .
										"WHERE idStandard = ?", ["i", [$route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO service (idStandard, `type`, `name`, `desc`) " .
									  "VALUES (?, ?, ?, ?)",
									  ["isss", [$route_ids["standard_id"], $item->type,
												$item->name, $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM service WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output('', array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, kind, type, subtype, discriminant, " .
										"domain, name, shortDesc, `desc`, idProcess, " .
										"descParam, descDest, code " .
										"FROM packet " .
										"WHERE type IS NOT NULL AND idStandard = ? " .
										"ORDER BY type, subtype",
										["i", [$route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO packet (idStandard, idProcess, kind, type, subtype, " .
									  "  domain, name, shortDesc, `desc`, " .
									  "  descParam, descDest, code) " .
									  "VALUE (?,?,?,?,?,?,?,?,?,?,?,?)" ,
									  ["iiiiisssssss", [ $route_ids["standard_id"],
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

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM packet WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT id, domain, name, value, `desc` " .
										"FROM constants " .
										"WHERE idStandard = ?",
										["i", [$route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO constants (idStandard, domain, name, value, `desc`) " .
									  "VALUES (?,?,?,?,?)",
									  ["issss", [$route_ids["standard_id"], $item->domain,
												 $item->name, $item->value,
												 $item->desc]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM constants WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{
		/*
		   $data = $this->database->select("SELECT id, domain, name, nativeType, `desc`, " .
		   "size, value, setting, " .
		   "concat('PTC/PFC: ', json_value(`setting`, '$.PUS.ptc'), '/', " .
		   "json_value(`setting`, '$.PUS.pfc')) AS pusparamtype, " .
		   "json_value(`setting`, '$.PUS.type') as `pusdatatype` " .
		   "FROM `type` " .
		   "WHERE idStandard = ? " .
		   "ORDER BY domain, name", ["i", [$route_ids["standard_id"]]]);
		 */

		$data = $this->database->select("SELECT t.id, t.domain, t.name, t.nativeType, t.`desc`, " .
										"t.size, t.value, t.setting, " .
										"COUNT(r.id) as ref_count, " .
										"CONCAT('PTC/PFC: ', json_value(t.`setting`, '$.PUS.ptc'), '/', " .
										"JSON_VALUE(t.`setting`, '$.PUS.pfc')) AS pusparamtype, " .
										"JSON_VALUE(t.`setting`, '$.PUS.type') AS `pusdatatype` " .
										"FROM `type` t "  .
										"LEFT JOIN parameter r ON r.idType = t.id " .
										"WHERE t.idStandard = ? " .
										"GROUP BY t.id, t.`domain`, t.name " .
										"ORDER BY t.domain, t.name", ["i", [$route_ids["standard_id"]]]);


		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO `type`(domain, name, nativeType, size, " .
									  "  value, `desc`, idStandard, setting)" .
									  "VALUES(?,?,?,?,?,?,?,?)",
									  ["sssissis", [$item->domain, $item->name,
													$item->nativeType, $item->size, $item->value,
													$item->desc, $route_ids["standard_id"], $item->setting]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `type` WHERE id = ?",
										   ["i", [$item_id]]);
		$this->send_output("", array('HTTP/1.1 200 OK'));

	}


	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query("UPDATE `type` " .
										   "SET domain = ?, name = ?, nativeType = ?, " .
										   "  size = ?, value = ?, `desc`= ? , setting = ? " .
										   "WHERE id = ?",
										   ["sssiissi", [ $item->domain, $item->name,
														  $item->nativeType, $item->size,
														  $item->value, $item->desc,
														  $item->setting, $item->id ]]);

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

	public function get_items($route_ids)
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
										["i", [$route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO parameter(idStandard, domain, name, " .
									  "  kind, shortDesc, idType, multiplicity, value, " .
									  "  unit)" .
									  "VALUES (?,?,?,?,?,?,?,?,?)",
									  ["issisiiss", [$route_ids["standard_id"], $item->domain,
													 $item->name, $item->kind,
													 $item->shortDesc, $item->idType,
													 $item->multiplicity, $item->value,
													 $item->unit]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parameter WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

	public function get_items($route_ids)
	{

		$data = $this->database->select(
			"SELECT p.id, p.domain, p.name, p.kind, " .
			"p.shortDesc, p.idType, concat(t.domain, ' / ', t.name) AS datatype, " .
			"p.role, p.multiplicity, p.value, p.unit, " .
			"COUNT(ps.id) as ref_count, r.idReferenceParameter as ref_param_id ".
			"FROM parameter p INNER JOIN type t ON t.id = p.idType " .
			"  LEFT JOIN parameter_deduced_relation r ON r.idParameter = p.id " .
			"  LEFT JOIN parametersequence ps ON ps.idParameter = p.id " .
			"WHERE p.idStandard = ? AND p.kind IN (0, 1, 2) " .
			"GROUP BY p.id, p.domain, p.name, p.kind, p.shortDesc, p.idType, datatype, " .
	        "  p.role, p.multiplicity, p.value, p.unit, ref_param_id ".
			"ORDER BY p.domain, p.name ", ["i", [$route_ids["standard_id"]]]);
		
		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO parameter(idStandard, domain, name, " .
									  "  kind, shortDesc, idType, multiplicity, value, " .
									  "  unit)" .
									  "VALUES (?,?,?,?,?,?,?,?,?)",
									  ["issisiiss", [$route_ids["standard_id"], $item->domain,
													 $item->name, $item->kind,
													 $item->shortDesc, $item->idType,
													 $item->multiplicity, $item->value,
													 $item->unit]]);

		$item->id = $id;

		if ($item->ref_param_id != NULL) {
			$this->database->insert("INSERT INTO parameter_deduced_relation (idParameter, idReferenceParameter) " .
									"VALUES (?, ?)", ["i", [$item->id, $item->ref_param_id]]);
		}
		
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parameter WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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

		if ($item->ref_param_id != NULL) {
			$this->database->execute_non_query("DELETE FROM parameter_deduced_relation WHERE idParameter = ? ",
											   ["i", [$item->id]]);
			$this->database->insert("INSERT INTO parameter_deduced_relation (idParameter, idReferenceParameter) " .
									"VALUES (?, ?)", ["ii", [$item->id, $item->ref_param_id]]);
		} else {
			$this->database->execute_non_query("DELETE FROM parameter_deduced_relation WHERE idParameter = ? ",
											   ["i", [$item->id]]);
		}

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


	public function get_items($route_ids)
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
										"ORDER BY ps.`order` ASC ", ["i", [$route_ids["packet_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO parametersequence(idStandard, idParameter, idPacket, `role`, `order`, `group`, " .
									  "  repetition, value, `desc`, `type`) " .
									  "VALUES (?,?,?,?,?,?,?,?,?, 0)",
									  ["iiiiiiiss", [ $route_ids["standard_id"], $item->parameter_id, $route_ids["packet_id"], $item->role,
													  $item->order, $item->group, $item->repetition, $item->value, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parametersequence WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
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


class DerivedPacketController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select("SELECT p.id, p.discriminant, p.name, p.shortDesc, p.`desc`, p.descParam, p.descDest, p.code,  " .
										"   count(ps.id) as param_count " .
										"FROM packet p LEFT JOIN parametersequence ps ON ps.idPacket = p.id " .
										"WHERE idParent = ? " .
										"GROUP BY p.id, p.discriminant, p.name, p.shortDesc, p.`desc`, p.descParam, p.descDest, p.code " .
										"ORDER BY discriminant", ["i", [$route_ids["packet_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert("INSERT INTO packet(idStandard, idParent, kind, type, subtype, name, " .
									  "    discriminant, shortDesc, `desc`, descParam, descDest, code) " .
									  "VALUES (?,?,0,0,0,?,?,?,?,?,?,?)",
									  ["iisssssss", [$route_ids["standard_id"], $route_ids["packet_id"], $item->name,
													 $item->discriminant, $item->shortDesc, $item->desc, $item->descParam,
													 $item->descDest, $item->code]]);
		$item->id = $id;
		$this->send_output(json_encode($item), array('Http/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM packet WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query("UPDATE packet " .
										   "SET discriminant = ?, `name` = ?, shortDesc = ?, `desc` = ?, " .
										   "    descParam = ?, descDest = ?, code = ? " .
										   "WHERE id = ?",
										   ["sssssssi", [$item->discriminant, $item->name, $item->shortDesc,
														 $item->desc, $item->descParam, $item->descDest, $item->code,
														 $item->id]]);

		$this->send_output("", array("HTTP/1.1 200 OK"));
	}
}


class DerivedPacketParameterController extends BaseController implements CrudController
{

	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT ps.id, ps.idParameter, " .
			"    CONCAT(p.`domain`, ' / ', p.name) as parameter,  ps.`order` , ps.`role` , " .
			"    ps.`group`, ps.repetition, ps.value, ps.`desc`, p.name, t.size " .
			"FROM parametersequence ps " .
			"    INNER JOIN `parameter` p ON p.id = ps.idParameter " .
			"    INNER JOIN `type` t ON t.id = p.idType " .
			"WHERE ps.idPacket = ? AND ps.idStandard = ? " .
			"ORDER BY ps.`order`",
			["ii", [$route_ids["child_id"], $route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}


	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}


	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert(
			"INSERT INTO parametersequence(idStandard, idParameter, idPacket, `role`, `order`, `group`, " .
			"  repetition, value, `desc`, `type`) " .
			"VALUES (?,?,?,?,?,?,?,?,?, 0)",
			["iiiiiiiss", [ $route_ids["standard_id"], $item->idParameter, $route_ids["child_id"], $item->role,
							$item->order, $item->group, $item->repetition, $item->value, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}


	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM parametersequence WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}


	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query(
			"UPDATE parametersequence " .
			"SET idParameter = ?, `role` = ?, `order` = ?, `group` = ?, " .
			"  repetition = ?, `value` = ?, `desc` = ? " .
			"WHERE id = ?",
			["iiiiissi", [$item->idParameter, $item->role, $item->order, $item->group,
						  $item->repetition, $item->value, $item->desc, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}

class EnumerationController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT id, name, value, `desc` " .
			"FROM enumeration " .
			"WHERE idType = ? " .
			"ORDER BY value", ["i", [$route_ids["datatype_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert(
			"INSERT INTO enumeration (idType, name, value, `desc`) " .
			"VALUES (?,?,?,?) ",
			["isis", [$route_ids["datatype_id"], $item->name, $item->value, $item->desc]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM enumeration WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query(
			"UPDATE enumeration " .
			"SET name = ?, value = ?, `desc` = ? " .
			"WHERE id = ?",
			["sisi", [$item->name, $item->value, $item->desc, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}


class LimitController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT id, `type`, lvalue, hvalue, setting " .
			"FROM `limit` " .
			"WHERE idParameter = ? " .
			"ORDER BY id", ["i", [$route_ids["parameter_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));

	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert(
			"INSERT INTO `limit` (idParameter, type, lvalue, hvalue, setting) " .
			"VALUES (?,?,?,?,?)",
			["iisss", [$route_ids["parameter_id"], $item->type, $item->lvalue, $item->hvalue, $item->setting]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM `limit` WHERE id = ?",
										   ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query(
			"UPDATE `limit` " .
			"SET `type` = ?, lvalue = ?, hvalue = ?, setting = ? " .
			"WHERE id = ?", ["isssi", [$item->type, $item->lvalue, $item->hvalue, $item->setting, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

}


class ContributorController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT u.id, u.idUser, u.idRole, r.name as role_name, u.email " .
			"FROM userproject u INNER JOIN `role` r ON r.id = u.idRole " .
			"WHERE idProject = ? AND idRole IN (3, 4)", ["i", [$route_ids["project_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$this->not_found();
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert(
			"INSERT INTO userproject (idUser, idProject, idRole, email) " .
			"VALUES (?,?,?,?)", ["iiis", [$item->idUser, $route_ids["project_id"], $item->idRole, $item->email]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM userproject WHERE id = ?", ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query(
			"UPDATE userproject " .
			"SET idUser = ?, idRole = ?, email = ? " .
			"WHERE id = ?", ["iisi", [$item->idUser, $item->idRole, $item->email, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}


class CalibrationController extends BaseController implements CrudController
{
	private $database;

	public function __construct()
	{
		$this->database = new Database();
	}

	public function get_items($route_ids)
	{
		$data = $this->database->select(
			"SELECT id, `type`, name, shortDesc, setting " .
			"FROM calibration WHERE idStandard = ?", ["i", [$route_ids["standard_id"]]]);

		$this->send_output(json_encode($data), array('HTTP/1.1 200 OK'));
	}

	public function get_item($route_ids, $id)
	{
		$data = $this->database->select(
			"SELECT id, `type`, name, shortDesc, setting " .
			"FROM calibration WHERE id = ?", ["i", [$id]]);

		$this->send_output(json_encode($data[0]), array('HTTP/1.1 200 OK'));
	}

	public function create_item($route_ids, $item)
	{
		$id = $this->database->insert(
			"INSERT INTO calibration (idStandard, `type`, name, shortDesc, setting) " .
			"VALUES (?,?,?,?,?)",
			["iisss", [$route_ids["standard_id"], $item->type, $item->name, $item->shortDesc, $item->setting]]);

		$item->id = $id;
		$this->send_output(json_encode($item), array('HTTP/1.1 200 OK'));
	}

	public function delete_item($route_ids, $item_id)
	{
		$this->database->execute_non_query("DELETE FROM calibration WHERE id = ?", ["i", [$item_id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}

	public function put_item($route_ids, $item)
	{
		$this->database->execute_non_query(
			"UPDATE calibration " .
			"SET `type` = ?, name = ?, shortDesc = ?, setting = ? " .
			"WHERE id = ?", ["isssi", [$item->type, $item->name, $item->shortDesc, $item->setting, $item->id]]);

		$this->send_output("", array('HTTP/1.1 200 OK'));
	}
}


?>
